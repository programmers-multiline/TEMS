<?php

namespace App\Http\Controllers;

use App\Models\RttteLogs;
use Carbon\Carbon;
use App\Models\Daf;
use App\Models\Uploads;
use App\Models\DafItems;
use App\Models\PmGroupings;
use App\Models\TeisUploads;
use App\Models\ProjectSites;
use App\Models\ToolPictures;
use Illuminate\Http\Request;
use App\Models\SetupApprover;
use Laravel\Prompts\Progress;
use App\Models\RequestApprover;
use App\Models\AssignedProjects;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\PsTransferRequests;
use App\Models\PulloutRequestItems;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use App\Models\PsTransferRequestItems;

class ProjectSiteController extends Controller
{

    public function view_project_site()
    {

        $all_pg = ProjectSites::where('status', 1)->where('progress', 'ongoing')->select('project_name', 'id')->get();

        $pg = AssignedProjects::leftjoin('project_sites', 'project_sites.id', 'assigned_projects.project_id')
            ->select('project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address')
            ->where('assigned_projects.status', 1)
            ->where('project_sites.status', 1)
            ->where(function($query) {
                $query->where('assigned_projects.emp_id', Auth::user()->emp_id)
                    ->orWhere('assigned_projects.assigned_by', Auth::user()->id);
            })
            ->groupBy('assigned_projects.project_id', 'project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address')
            ->get();

        return view('/pages/project_site', compact('all_pg', 'pg'));
    }

    public function fetch_tools_ps(Request $request)
    {

        $action = '';
        $status = '';

        // $id = [];

        // $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
        // ->select('transfer_request_items.tool_id')
        // ->where('transfer_requests.status', 1)
        // ->where('transfer_request_items.item_status', 1)
        // ->where('transfer_request_items.pe', Auth::user()->id)
        // ->get();


        // $ps_tool_id = PsTransferRequestItems::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.ps_transfer_request_id')
        // ->select('ps_transfer_request_items.tool_id')
        // ->where('ps_transfer_requests.status', 1)
        // ->where('ps_transfer_request_items.item_status', 1)
        // ->where('ps_transfer_request_items.user_id', Auth::user()->id)
        // ->get();

        // $tool_ids = $tool_id->merge($ps_tool_id);

        // foreach($tool_ids as $tool){
        //     $id[] = $tool->tool_id;
        // }

        // return $id;

        /// di nya makikita ang mga tools sa project site na owned nya
        $id = ToolsAndEquipment::where('status', 1)
            ->where("current_pe", Auth::user()->id)
            ->pluck('id')
            ->toArray();



        if ($request->projectSiteId) {
            $tools = ToolsAndEquipment::leftJoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->leftjoin('project_sites', 'project_sites.id', 'tools_and_equipment.current_site_id')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name', 'project_sites.project_location', 'project_sites.project_name')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.wh_ps', 'ps')
                ->where('project_sites.id', $request->projectSiteId)
                ->whereNotIn('tools_and_equipment.id', $id)
                ->get();
        } else {
            $tools = ToolsAndEquipment::leftJoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->leftjoin('project_sites', 'project_sites.id', 'tools_and_equipment.current_site_id')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name', 'project_sites.project_location', 'project_sites.project_name')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.wh_ps', 'ps')
                ->whereNotIn('tools_and_equipment.id', $id)
                ->get();
        }



        return DataTables::of($tools)
            // para di maselect ang tools na ni request ng iba 
            ->setRowClass(function ($row) {

                if (Auth::user()->user_type_id != 2) {
                    $tool_id = PulloutRequestItems::leftjoin('pullout_requests', 'pullout_requests.id', 'pullout_request_items.pullout_request_id')
                        ->select('pullout_request_items.*')
                        ->where('pullout_requests.progress', 'ongoing')
                        ->where('pullout_requests.status', 1)->get();

                    $psTransferRequest = PsTransferRequestItems::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.ps_transfer_request_id')
                        ->select('ps_transfer_request_items.*')
                        ->where('ps_transfer_requests.progress', 'ongoing')
                        ->where('ps_transfer_requests.status', 1)
                        ->where('ps_transfer_request_items.status', 1)
                        ->get();

                    // $tools = ToolsAndEquipment::where('status', 1)->where('transfer_state', 0)->get();

                    $toolIds = [];

                    $PulloutToolIds = collect($tool_id)->pluck('tool_id')->toArray();
                    $PsToolIds = collect($psTransferRequest)->pluck('tool_id')->toArray();
                    // $tools_id = collect($tools)->pluck('id')->toArray();

                    $toolIds = array_merge($PulloutToolIds, $PsToolIds);

                    return in_array($row->id, $toolIds) ? 'bg-gray' : '';
                }

            })

            ->addColumn('tools_status', function ($row) {
                $status = $row->tools_status;
                if ($status == 'good') {
                    $status = '<span class="badge bg-success">' . $status . '</span>';
                } else if ($status == 'repair') {
                    $status = '<span class="badge bg-warning">' . $status . '</span>';
                } else {
                    $status = '<span class="badge bg-danger">' . $status . '</span>';
                }
                return $status;
            })

            ->addColumn('transfer_state', function ($row) {
                $state = '';
                if ($row->transfer_state == 1) {
                    $state = '<span class="btn btn-sm btn-alt-success" style="font-size: 12px;">Available to transfer</span>';
                } else if ($row->transfer_state == 2) {
                    $state = '<span class="btn btn-sm btn-alt-primary" style="font-size: 12px;">Pullout Ongoing</span>';
                } else {
                    $state = '<span class="btn btn-sm btn-alt-danger" style="font-size: 12px;">Currently Using</span>';
                }
                return $state;
            })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;
                if ($user_type == 1) {
                    $action = '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="' . $row->id . '" data-po="' . $row->po_number . '" data-asset="' . $row->asset_code . '" data-serial="' . $row->serial_number . '" data-itemcode="' . $row->item_code . '" data-itemdesc="' . $row->item_description . '" data-brand="' . $row->brand . '" data-location="' . $row->location . '" data-status="' . $row->tools_status . '" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-pencil-alt"></i>
              </button>
              <button type="button" id="deleteToolsBtn" data-id="' . $row->id . '" class="btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                <i class="fa fa-times"></i>
              </button>';
                } else if ($user_type == 2) {
                    $action = '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="' . $row->id . '" data-po="' . $row->po_number . '" data-asset="' . $row->asset_code . '" data-serial="' . $row->serial_number . '" data-itemcode="' . $row->item_code . '" data-itemdesc="' . $row->item_description . '" data-brand="' . $row->brand . '" data-location="' . $row->location . '" data-status="' . $row->tools_status . '" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-pencil-alt"></i></button>';
                } else if ($user_type == 3 || $user_type == 4 || $user_type == 5) {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                }
                return $action;
            })

            ->addColumn('po_number', function($row){
                if(!$row->po_number){
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }else{
                    return $row->po_number;
                }
            })

            ->addColumn('brand', function($row){
                if(!$row->brand){
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }else{
                    return $row->brand;
                }
            })

            ->rawColumns(['transfer_state', 'tools_status', 'po_number', 'brand', 'action'])
            ->toJson();
    }


    public function ps_request_tools(Request $request)
    {
        // return $request->prevReqNum;

        $project_site_id = ProjectSites::where('status', 1)->where('project_code', $request->projectCode)->value('id');
        //?kapag pwede dalawang PM sa isang project baguhin mo ang query na ito ⬇
        $assigned = AssignedProjects::select('user_id', 'assigned_by')->where('status', 1)->where('project_id', $project_site_id)->where('pos', 'pm')->first();

        $currentOwner = AssignedProjects::select('user_id', 'assigned_by')->where('status', 1)->where('project_id', $request->currentSiteId)->where('pos', 'pm')->first();
      
        // return [$assigned , $currentOwner];
        $approvers = [];

        if(!$assigned){
            return 1;
        }elseif(!$currentOwner){
            return 2;
        }

        $prev_rn = PsTransferRequests::where('status', 1)->orderBy('request_number', 'desc')->first();



        $new_request_number = '';
        if (!$prev_rn) {
            $new_request_number = 1000;
        } else {
            $new_request_number = $prev_rn->request_number + 1;
        }

        $req = PsTransferRequests::create([
            'request_number' => $new_request_number,
            'user_id' => Auth::user()->id,
            'project_name' => $request->projectName,
            'project_code' => $request->projectCode,
            'project_address' => $request->projectAddress,
            'date_requested' => Carbon::now(),
            'current_pe' => $request->currentPe,
            'current_site_id' => $request->currentSiteId,
            'reason_for_transfer' => $request->reason,
        ]);


        $approvers[] = $assigned->user_id;
        $approvers[] = $assigned->assigned_by;
        $approvers[] = $request->currentPe;
        $approvers[] = $currentOwner->user_id;
        $approvers[] = $currentOwner->assigned_by;

        // $mergedCollection = collect([$assigned, $currentOwner]);

        // foreach($mergedCollection as $approver){
        //     $approvers[] = $approver->user_id;
        //     $approvers[] = $approver->assigned_by;
        // }

        foreach ($approvers as $key => $approver) {
            RequestApprover::create([
                'request_id' => $req->id,
                'approver_id' => $approver,
                'sequence' => $key + 1,
                'request_type' => 2,
            ]);  
        }

        Daf::create([
            'daf_number' => $new_request_number,
            'user_id' => Auth::user()->id,
            'date_requested' => Carbon::now(),
            'tr_type' => 'rttte',
        ]);

        // $req = PsTransferRequests::where('status', 1)->orderBy('id', 'desc')->first();


        $array_id = json_decode($request->idArray);

        $array_count = count($array_id);

        for ($i = 0; $i < $array_count; $i++) {
            PsTransferRequestItems::create([
                'tool_id' => $array_id[$i],
                'request_number' => $new_request_number,
                'ps_transfer_request_id' => $req->id,
                'user_id' => Auth::user()->id,
                'prev_request_num' => $request->prevReqNum,
                'status' => 1,
            ]);
        }

        for ($i = 0; $i < $array_count; $i++) {
            DafItems::create([
                'tool_id' => $array_id[$i],
                'daf_number' => $new_request_number,
                'daf_id' => $req->id,
                'user_id' => Auth::user()->id,
            ]);
        }

        /// for logs
        RttteLogs::create([
            'approver_name' => Auth::user()->fullname,
            'page' => 'view_project_site',
            'request_number' => $new_request_number,
            'title' => 'Request'.' '.'#'. $new_request_number,
            'message' => Auth::user()->fullname .' '. 'created a RTTTE request.',
            'action' => 1,
        ]);


        ///dati to nung nasa nag rerequest pa ang pag upload kala ko ganun e
        // $files = $request->file('files'); 
        // $rowIds = $request->input('row_ids');
        
        // foreach ($files as $index => $file) {
        //     $name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $file->extension();
        //     $uploads = Uploads::create([
        //         'name' => $name,
        //         'original_name' => $file->getClientOriginalName(),
        //         'extension' => $file->extension(),
        //     ]);

        //     $file->move('uploads/tool_pictures/', $name);


        //     $tool_id = $rowIds[$index];

        //     ToolPictures::create([
        //         'pstr_id' => $new_request_number,
        //         'tool_id' => $tool_id,
        //         'upload_id' => $uploads->id,
        //         'tr_type' => "rttte",
        //     ]);

        // }

    }


    public function fetch_teis_request_ps()
    {

        $request_tools = PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->whereNull('acc')->get();
        // if(Auth::user()->user_type_id == 7){
        // }
        // else{
        //     $request_tools = PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->get();
        // }


        return DataTables::of($request_tools)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->request_number . '" data-bs-toggle="modal" data-bs-target="#psOngoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';
                ;
            })
            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $price = [];

                $ps_tools = PsTransferRequestItems::where('status', 1)->where('ps_transfer_request_id', $row->id)->get();
                foreach ($ps_tools as $tools) {

                    array_push($price, $tools->price);
                }

                // if (Auth::user()->dept_id !== 1) {
                //     $price = [''];
                // }

                $has_null = in_array(null, $price, true);

                $has_price = $has_null ? 'disabled' : '';


                if ($user_type == 2) {
                    $action = '<button data-requestnum="' . $row->request_number . '" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-upload me-1"></i>TEIS</button>';
                } else if ($user_type == 7) {
                    $action = '<button data-requestnum="' . $row->request_number . '" ' . $has_price . ' type="button" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-check"></i></button>';
                } else {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                }

                // if($user_type == 1){
                //     $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                //     <i class="fa fa-pencil-alt"></i>
                //   </button>
                //   <button type="button" id="deleteToolsBtn" data-id="'.$row->id.'" class="btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                //     <i class="fa fa-times"></i>
                //   </button>';
                // }else if($user_type == 2){
                //     $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                //     <i class="fa fa-pencil-alt"></i></button>';
                // }else if($user_type == 3 || $user_type == 4){
                //     $action =  '<button data-bs-toggle="modal" data-bs-target="#modalRequestWarehouse" type="button" id="requestWhBtn" class="btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                //     <i class="fa fa-file-pen"></i></button>';
                // }
                return $action;
            })
            ->addColumn('uploads', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->id)->get()->toArray();
            })
            ->rawColumns(['view_tools', 'action', 'uploads'])
            ->toJson();
    }


    public function ps_ongoing_teis_request_modal(Request $request)
    {

        $tools = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
            ->leftjoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
            ->leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.ps_transfer_request_id')
            ->select('tools_and_equipment.*', 'ps_transfer_request_items.id as pstri_id', 'ps_transfer_request_items.price','ps_transfer_request_items.tool_id', 'ps_transfer_request_items.request_number', 'warehouses.warehouse_name', 'ps_transfer_request_items.teis_no', 'ps_transfer_requests.reason_for_transfer', 'ps_transfer_request_items.item_status', 'ps_transfer_request_items.user_id')
            ->where('ps_transfer_request_items.status', 1)
            ->where('ps_transfer_request_items.request_number', $request->id)
            ->get();

        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get(); lagay ka barcode to receive btn

        $count = 1; 
        return DataTables::of($tools)

            ->addColumn('item_no', function() use (&$count){
                return $count++;
            })

            ->addColumn('action', function ($row) {
                    if($row->item_status == 1){
                        $action = '<div class="text-center"><span class="badge bg-success text-center">Served</span></div>';
                    }elseif($row->item_status == 2){
                        $action = '<div class="text-center"><span class="badge bg-danger">Not Served</span></div>';
                    }else{
                        $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                    }
                return $action;
            })

            ->addColumn('picture', function ($row) use ($request) {
                    $picture = ToolPictures::leftjoin('uploads', 'uploads.id', 'upload_id')
                        ->select('uploads.name', 'uploads.original_name')
                        ->where('tool_pictures.status', 1)
                        ->where('pstr_id', $row->request_number)
                        ->where('tool_id', $row->tool_id)
                        ->orderBy('tool_pictures.created_at', 'desc')
                        ->first();

                        if($picture){
                            $uploads_file =
                                '<div class="row mx-auto">
                                <div class="animated fadeIn pictureContainer">
                                    <a target="_blank" class="img-link-zoom-in" href="' . asset('uploads/tool_pictures') . '/' . $picture->name . '">
                                    <span>'.$picture->original_name.'</span>
                                    </a>
                                </div>
                            </div>';
                        }else if(Auth::user()->user_type_id == 4 && Auth::user()->id != $row->user_id){
                            $uploads_file = '<button data-num="' . $row->request_number . '" data-toolid="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#uploadPicture" type="button" class="uploadPictureBtn noPicture btn btn-sm btn-success js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Upload Picture of tool" data-bs-original-title="Upload Picture of tool"><i class="fa fa-upload"></i></button>';

                        }else{
                            $uploads_file = '';
                        }


                    return $uploads_file;
            })


            ->addColumn('tools_status', function ($row) {
                $status = $row->tools_status;
                if ($status == 'good') {
                    $status = '<span class="">Good</span>';
                } else {
                    $status = '<span class="">Defective</span>';
                }
                return $status;
            })

            ->addColumn('add_price', function ($row) {

                $is_have_value = $row->price ? 'disabled' : '';

                $add_price = '<input class="form-control price" value="' . $row->price . '" data-id="' . $row->pstri_id . '" style="width: 100px;" type="number" name="price" min="1">';
                return $add_price;
            })

            ->addColumn('warehouse_name', function ($row) {
                if ($row->current_site_id) {
                    // $location = ProjectSites::where('status', 1)->where('id', $row->current_site_id)->first();
                    return ProjectSites::where('status', 1)->where('id', $row->current_site_id)->value('project_location');
                } else {
                    return $row->warehouse_name;
                }
            })

            ->rawColumns(['tools_status', 'action', 'add_price', 'picture'])
            ->toJson();
    }


    public function add_price_acc(Request $request)
    {

        $price_datas = json_decode($request->priceDatas);

        // return $price_datas;

        if($request->type == 'rfteis'){
            foreach ($price_datas as $data) {
               TransferRequestItems::where('status', 1)->where('id', $data->id)->update([
                    'price' => $data->price,
                ]);
            }
        }else{
            foreach ($price_datas as $data) {
                PsTransferRequestItems::where('status', 1)->where('id', $data->id)->update([
                    'price' => $data->price
                ]);

            }
        }
        

    }
}
