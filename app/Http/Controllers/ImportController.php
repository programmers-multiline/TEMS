<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Uploads;
use App\Models\Companies;
use App\Models\ActionLogs;
use App\Models\UploadTools;
use App\Models\ProjectSites;
use Illuminate\Http\Request;
use App\Imports\PreviewImport;
use App\Mail\ToolExtensionNotif;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\UploadToolsDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public $uploads;


    public function previewImport(Request $request)
    {

        // List of restricted item codes
        // $restrictedItems = [
        //     "TN12XBK-01",
        //     "TN12XRD-01",
        //     "TL50009-00",
        //     "RC314BK-02",
        //     "RC012BK-02",
        //     "RC010BK-03",
        //     "RC010BK-02-PF",
        //     "RC010BK-00",
        //     "10-WRC10-2C",
        //     "05-10-WRC10-2C"
        // ];

        $expectedHeaders = ["Item Code", "Description", "Qty.", "TEIS Reference"];

        $data = Excel::toCollection(new PreviewImport, $request->file('file'))->first();

        if (!$data || $data->isEmpty()) {
            return response()->json(['error' => 'Uploaded file is empty or invalid.'], 422);
        }

        // Validate headers (Ensure columns are in correct order)
        $fileHeaders = $data->first()->toArray();
        if ($fileHeaders !== $expectedHeaders) {
            return response()->json(['error' => 'Invalid file format. Columns should be in order: ' . implode(', ', $expectedHeaders)], 422);
        }

        // Remove headers row from data
        $data = $data->slice(1)->values();

        // Validate each row
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                // Check for empty cells (except '0')
                if (empty($cell) && $cell !== '0') {
                    return response()->json([
                        'error' => "Empty cell found at row " . ($rowIndex + 2) . ", column " . ($colIndex + 1)
                    ], 422);
                }

                // Check if the first column (index 0) contains a restricted item code
                // if (isset($row[0]) && in_array(trim($row[0]), $restrictedItems)) {
                //     return response()->json([
                //         'error' => "Item code '{$row[0]}' found at row " . ($rowIndex + 2) . " is measured in meters and cannot be uploaded because the system will itemize it into multiple rows. Please remove this item from the file and try again. Contact IT for the removed Tools to upload manually."
                //     ], 422);
                // }
            }

            // Validate that Column C (index 2) contains only numbers
            if (!isset($row[2]) || !is_numeric($row[2])) {
                return response()->json([
                    'error' => "Invalid value in 'Qty.' at row " . ($rowIndex + 2) . ". It should only contain numbers."
                ], 422);
            }
        }

        $file = $request->file('file');
        $excel_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $file->getClientOriginalExtension();
        $uploads = Uploads::create([
            'name' => $excel_name,
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
        ]);
        $file->move('uploads/psite_import_excel/', $excel_name);

        return response()->json([
            'data' => $data,
            'upload_id' => $uploads->id,
        ]);
    }
    public function confirmImport(Request $request)
    {
        $data = $request->input('data');
        $upload_id = $request->input('upload_id');

        // List of restricted item codes
        $restrictedItems = [
            "TN12XBK-01",
            "TN12XRD-01",
            "TL50009-00",
            "RC314BK-02",
            "RC012BK-02",
            "RC010BK-03",
            "RC010BK-02-PF",
            "RC010BK-00",
            "10-WRC10-2C",
            "05-10-WRC10-2C"
        ];


        if (!$upload_id) {
            return response()->json(['error' => 'Upload ID is missing'], 400);
        }

        $comp_id = ProjectSites::where('status', 1)->where('id', $request->psite)->value('company_id');

        $tool_upload = UploadTools::create([
            'upload_id' => $upload_id,
            'uploader_id' => $request->pe,
            'project_id' => $request->psite,
            'company_id' => $comp_id,
        ]);

        DB::transaction(function () use ($data, $tool_upload, $restrictedItems) {
            foreach ($data as $row) {

                $item_code = $row[0];
                $item_desc = $row[1];
                $quantity = (int) $row[2];
                $teis_ref = $row[3];


                // Check if item is restricted
                if (in_array($item_code, $restrictedItems)) {
                    $entryCount = max(1, floor($quantity / 75)); // 1 entry for ≤ 75, additional per 75 units
                } else {
                    $entryCount = $quantity; // Normal item, loop for each unit
                }

                for ($i = 0; $i < $entryCount; $i++) {
                    $old_te = ToolsAndEquipment::generateOldte();

                    UploadToolsDetails::create([
                        'tools_upload_id' => $tool_upload->id,
                        'item_code' => $item_code,
                        'item_description' => $item_desc,
                        'qty' => 1,
                        'teis_ref' => $teis_ref,
                        'asset_code' => $old_te,
                    ]);

                }

            }
        });

        if ($comp_id == 3) {
            $CA = User::where('status', 1)->where('comp_id', $comp_id)->where('pos_id', 12)->first();
        }

        $mail_data = [
            'pe_name' => $CA->fullname,
            'message' => Auth::user()->fullname . ' has uploaded a set of tools that require cost. Please review and add the necessary cost.',
            'type' => 'notif_acct'
        ];

        // Send an email notification
        Mail::to($CA->email)->cc('mbi_acctg1@multi-linegroup.com')->send(new ToolExtensionNotif($mail_data));

        $ps = ProjectSites::where('status', 1)->where('id', $request->psite)->value('project_name');

        ActionLogs::create([
            'user_id' => Auth::id(),
            'action' => Auth::user()->fullname . ' uploaded Tools for ' . $ps . '( uid: ' . $upload_id . ')',
            'ip_address' => request()->ip(),
        ]);

        return response()->json(['success' => 'Data imported successfully']);
    }

    public function fetch_upload_tools(Request $request)
    {
        if(Auth::user()->user_type_id == 7){
            if ($request->projectSiteId && $request->status) {
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->where('upload_tools.project_id', $request->projectSiteId)
                    ->where('upload_tools.progress', $request->status)
                    ->get();
            } elseif ($request->status) {
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->where('upload_tools.progress', $request->status)
                    ->get();
    
            } elseif ($request->projectSiteId) {
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->where('upload_tools.project_id', $request->projectSiteId)
                    ->get();
            } else {
                // no filter
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->get();
            }
        }else{
            // filter
            if ($request->projectSiteId && $request->status) {
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->where('upload_tools.project_id', $request->projectSiteId)
                    ->where('upload_tools.progress', $request->status)
                    // ->where('upload_tools.uploader_id', Auth::id())
                    ->get();
            } elseif ($request->status) {
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->where('upload_tools.progress', $request->status)
                    // ->where('upload_tools.uploader_id', Auth::id())
                    ->get();

            } elseif ($request->projectSiteId) {
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    ->where('upload_tools.project_id', $request->projectSiteId)
                    // ->where('upload_tools.uploader_id', Auth::id())
                    ->get();
            } else {
                // no filter
                $upload_tools = UploadTools::leftJoin('project_sites as ps', 'ps.id', 'upload_tools.project_id')
                    ->leftJoin('users as u', 'u.id', 'upload_tools.uploader_id')
                    ->select('upload_tools.*', 'ps.project_code', 'ps.project_name', 'u.fullname')
                    ->where('ps.status', 1)
                    ->where('u.status', 1)
                    ->where('upload_tools.status', 1)
                    // ->where('upload_tools.uploader_id', Auth::id())
                    ->get();
            }
        }

        return DataTables::of($upload_tools)

            ->addColumn('view_tools', function ($row) {

                return '<button data-id="' . $row->id . '" data-status="' . $row->progress . '" data-bs-toggle="modal" data-bs-target="#ExcelImportDetails" class="uploadToolDetails btn text-primary fs-6 d-block">View</button>';
            })

            ->addColumn('request_status', function ($row) {

                if ($row->progress === 1) {
                    return '<span class="badge bg-success">Done</span>';
                } elseif ($row->progress === 0) {
                    return '<span class="badge bg-warning">Pending</span>';
                } else {
                    return '';
                }

            })

            ->addColumn('date_upload', function ($row) {

                $carbonDate = Carbon::parse($row->created_at);
                $readableDate = $carbonDate->toDayDateTimeString();

                return $readableDate;
            })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $action = '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#trackRequestModal" data-trtype="' . $row->tr_type . '" data-requestnumber="' . $row->teis_number . '" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
            </div>
            ';
                return $action;
            })


            ->rawColumns(['view_tools', 'request_status', 'date_upload'])
            ->toJson();
    }

    public function import_tools_details(Request $request)
    {
        $import_tools = UploadToolsDetails::where('status', 1)->where('tools_upload_id', $request->id)->get();

        return DataTables::of($import_tools)

            ->addColumn('request_status', function ($row) {

                if ($row->approver_status === 1) {
                    return '<span class="badge bg-success">Done</span>';
                } elseif ($row->approver_status === 0) {
                    return '<span class="badge bg-warning">Pending</span>';
                } else {
                    return '';
                }

            })

            ->addColumn('add_price', function ($row) {

                // $is_have_value = $row->price ? 'disabled' : '';
                $cost = ToolsAndEquipment::where('status', 1)->where('item_code', $row->item_code)->orderBy('price', 'desc')->value('price');
                
    
                return '<input class="form-control price" value="' . $cost . '" data-id="' . $row->id . '" style="width: 110px;" type="number" name="price" min="1">';
            })

            ->rawColumns(['request_status', 'add_price'])
            ->toJson();
    }

    public function import_tool_add_price(Request $request)
    {

        $price_datas = json_decode($request->priceDatas);

        foreach ($price_datas as $data) {
            UploadToolsDetails::where('status', 1)->where('id', $data->id)->update([
                'cost' => $data->price,
                'approver_status' => 1
            ]);

        }

        $tools = UploadToolsDetails::where('status', 1)->where('tools_upload_id', $request->id)->pluck('approver_status')->toArray();

        $is_all_not_approved = in_array(0, $tools);

        if ($is_all_not_approved) {
            return 0;
        } else {
            $tool_request = UploadTools::where('status', 1)->where('id', $request->id)->first();

            $tool_request->progress = 1;
            $tool_request->save();

            foreach ($price_datas as $data) {
                $tool_upload_details = UploadToolsDetails::where('status', 1)->where('id', $data->id)->first();

                $loc = $tool_request->company_id == 3 ? 1 : 2;

                ToolsAndEquipment::create([
                    'current_pe' => $tool_request->uploader_id,
                    'current_site_id' => $tool_request->project_id,
                    'asset_code' => $tool_upload_details->asset_code,
                    'item_code' => $tool_upload_details->item_code,
                    'item_description' => $tool_upload_details->item_description,
                    'price' => $tool_upload_details->cost,
                    'teis_ref' => $tool_upload_details->teis_ref,
                    'location' => $loc,
                    'company_id' => $tool_request->company_id,
                    'wh_ps' => 'ps',
                    'tools_status' => 'good'
                ]);

            }

        }

        $pname = ProjectSites::where('status', 1)->where('id', $tool_request->project_id)->value('project_name');

        ActionLogs::create([
            'user_id' => Auth::id(),
            'action' => Auth::user()->fullname . ' approved outstading Tools for ' . $pname . '( id: ' . $tool_request->project_id . ')',
            'ip_address' => request()->ip(),
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ["Item Code", "Description", "Qty.", "TEIS Reference"];
        $sampleData = ["sample item code", "Hard Hat", "2", "12345"];

        foreach ($headers as $index => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        // Add sample row
        foreach ($sampleData as $index => $value) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValueByColumnAndRow($index + 1, 2, $value);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        // Set the file to download
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="tools_template.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

}
