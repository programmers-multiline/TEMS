<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Daf;
use App\Models\DafItems;
use App\Models\Warehouse;
use App\Mail\ApproverEmail;
use App\Models\PmGroupings;
use Illuminate\Http\Request;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


class WarehouseController extends Controller
{

    public function view_warehouse()
    {

        $pg = PmGroupings::leftjoin('assigned_projects', 'assigned_projects.pm_group_id', 'pm_groupings.id')
            ->leftjoin('project_sites', 'project_sites.id', 'assigned_projects.project_id')
            ->select('project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address', )
            ->where('assigned_projects.status', 1)
            ->where('pm_groupings.status', 1)
            ->where('project_sites.status', 1)
            ->where('pm_groupings.pe_code', Auth::user()->emp_id)
            ->orwhere('pm_groupings.pm_code', Auth::user()->emp_id)
            ->get();


        $warehouses = Warehouse::where('status', 1)->get();


        $search = Route::input('search');
        $desc = Route::input('desc');

        return view('/pages/warehouse', compact('warehouses', 'search', 'desc', 'pg'));

    }


    public function add_tools(Request $request)
    {


        $request->validate([
            'itemCode' => 'required',
            'itemDescription' => 'required',
            'brand' => 'required',
            'location' => 'required',
            'status' => 'required',
        ], [
            'required' => 'Please input required fields(*).',
        ]);



        ToolsAndEquipment::create([
            'po_number' => $request->poNumber,
            'asset_code' => $request->assetCode,
            'serial_number' => $request->serialNumber,
            'item_code' => $request->itemCode,
            'item_description' => $request->itemDescription,
            'brand' => $request->brand,
            'location' => $request->location,
            'wh_ps' => 'wh',
            'tools_status' => $request->status,
            'status' => 1,
        ]);

    }


    public function fetch_tools(Request $request)
    {

        $action = '';
        $status = '';

        if ($request->warehouseId) {
            $tools = ToolsAndEquipment::leftJoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.wh_ps', 'wh')
                ->where('location', $request->warehouseId)
                ->get();
        } else {
            $tools = ToolsAndEquipment::leftJoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.wh_ps', 'wh')
                ->get();
        }


        return DataTables::of($tools)


            // ->addColumn('box', function($row){
            //     return $box = '
            //       <div class="form-check">
            //         <input class="form-check-input" style="margin-left: -10px" type="checkbox">
            //         <label class="form-check-label" for="row_1"></label>
            //         </div>
            //       ';
            // })
            ->setRowClass(function ($row) {
                if (Auth::user()->user_type_id != 2) {
                    $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
                        ->select('transfer_request_items.*')
                        ->where('transfer_requests.progress', 'ongoing')
                        ->where('transfer_requests.status', 1)->get();

                    $toolIds = collect($tool_id)->pluck('tool_id')->toArray();

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
                } 
                // else if ($user_type == 3 || $user_type == 4) {
                //     $action = '<button data-bs-toggle="modal" data-bs-target="#modalRequestWarehouse" type="button" id="requestWhBtn" class="btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                // <i class="fa fa-file-pen"></i></button>';
                // } 
                else {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                }
                return $action;
            })
            ->rawColumns(['tools_status', 'action'])
            ->toJson();
    }


    public function edit_tools(Request $request)
    {


        $tools = ToolsAndEquipment::find($request->hiddenToolsId);

        $tools->po_number = $request->updatePo;
        $tools->asset_code = $request->updateAsset;
        $tools->serial_number = $request->updateSerial;
        $tools->item_code = $request->updateItemCode;
        $tools->item_description = $request->updateItemDesc;
        $tools->brand = $request->updateBrand;
        $tools->location = $request->updateLocation;
        $tools->tools_status = $request->updateStatus;

        $tools->update();
    }


    public function delete_tools(Request $request)
    {

        $deleteTools = ToolsAndEquipment::find($request->id);

        $deleteTools->status = 0;
        $deleteTools->update();

    }


    public function request_tools(Request $request)
    {

        // return $request->idArray;


        $mail_data = [];
        $mail_approvers = [];
        $mail_Items = [];

        $prev_tn = TransferRequest::where('status', 1)->orderBy('teis_number', 'desc')->first();


        $new_teis_number = '';
        if (!$prev_tn) {
            $new_teis_number = 1000;
        } else {
            $new_teis_number = $prev_tn->teis_number + 1;
        }

        $req = TransferRequest::create([
            'teis_number' => $new_teis_number,
            'pe' => Auth::user()->id,
            'subcon' => $request->subcon,
            'customer_name' => $request->customerName,
            'project_name' => $request->projectName,
            'project_code' => $request->projectCode,
            'project_address' => $request->projectAddress,
            'date_requested' => Carbon::now(),
            'status' => 1,
        ]);


        Daf::create([
            'daf_number' => $new_teis_number,
            'user_id' => Auth::user()->id,
            'date_requested' => Carbon::now(),
            'tr_type' => 'rfteis',
        ]);

        $req = TransferRequest::orderBy('id', 'desc')->first();


        $array_id = json_decode($request->idArray);

        $array_count = count($array_id);

        for ($i = 0; $i < $array_count; $i++) {
            TransferRequestItems::create([
                'tool_id' => $array_id[$i],
                'teis_number' => $new_teis_number,
                'transfer_request_id' => $req->id,
                'pe' => Auth::user()->id,
                'status' => 1,
            ]);
        }

        for ($i = 0; $i < $array_count; $i++) {
            DafItems::create([
                'tool_id' => $array_id[$i],
                'daf_number' => $new_teis_number,
                'daf_id' => $req->id,
                'user_id' => Auth::user()->id,
            ]);
        }



        $approvers = RequestApprover::leftjoin('users', 'users.id', 'request_approvers.approver_id')
            ->select('request_approvers.*', 'users.fullname', 'users.email')
            ->where('request_approvers.status', 1)
            ->where('request_type', 1)
            ->where('request_approvers.request_id', $req->id)
            ->where('request_approvers.sequence', 0)
            ->orderBy('request_approvers.sequence', 'asc')
            ->get();

        foreach ($approvers as $approver) {
            array_push($mail_approvers, ['fullname' => $approver->fullname]);
        }

        $tools = ToolsAndEquipment::where('status', 1)->whereIn('id', $array_id)->get();

        foreach ($tools as $tool) {
            array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
        }





        foreach ($approvers as $approver) {  
            $mail_data = ['requestor_name' => Auth::user()->fullname, 'date_requested' => Carbon::today()->format('m/d/Y'), 'approver' => $approver->fullname, 'items' => json_encode($mail_Items)];

            // Mail::to($approver->email)->send(new ApproverEmail($mail_data));
        }


    }




}
