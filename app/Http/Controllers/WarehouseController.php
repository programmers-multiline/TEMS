<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Daf;
use App\Models\DafItems;
use App\Models\Warehouse;
use App\Models\RfteisLogs;
use App\Mail\ApproverEmail;
use App\Models\PmGroupings;
use App\Models\DafApprovers;
use App\Models\ProjectSites;
use Illuminate\Http\Request;
use App\Models\SetupApprover;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use App\Models\AssignedProjects;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\Scopes\CompanyScope;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


class WarehouseController extends Controller
{

    public function view_warehouse()
    {

        $pg = AssignedProjects::leftJoin('project_sites', 'project_sites.id', '=', 'assigned_projects.project_id')
            ->select('assigned_projects.project_id as id', 'project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address')
            ->where('assigned_projects.status', 1)
            ->where('project_sites.status', 1)
            ->where(function($query) {
                $query->where('assigned_projects.emp_id', Auth::user()->emp_id)
                    ->orWhere('assigned_projects.assigned_by', Auth::user()->id);
            })
            ->groupBy('assigned_projects.project_id','project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address')
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
                ->where('tools_and_equipment.tools_status', 'good')
                ->where('location', $request->warehouseId)
                // ->where('company_code', Auth::user()->company_code) //i helpers mo to kunin mo comp id sa auth tapos ibato mo sa companies table
                ->get();
        } else {
            $tools = ToolsAndEquipment::leftJoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.wh_ps', 'wh')
                ->where('tools_and_equipment.tools_status', 'good')
                // ->where('company_code', Auth::user()->company_code)
                ->get();
        }

        // return Auth::user()->company_code;
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

                    if ($row->qty == 0) {
                        return 'bg-pulse-light'; 
                    }

                    $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
                        ->select('transfer_request_items.*')
                        ->where('transfer_requests.progress', 'ongoing')
                        ->where('transfer_requests.status', 1)
                        ->where('transfer_request_items.status', 1)
                        ->get();

                    $toolIds = collect($tool_id)->pluck('tool_id')->toArray();


                    if (in_array($row->id, $toolIds)) {
                        return 'bg-gray';
                    }

                    if ($row->tagged_to) {
                        if($row->tagged_to != Auth::id()){
                            return 'bg-warning'; 
                        }else{
                            return ''; 
                        }
                        
                    }
                }
                return '';
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

            ->rawColumns(['tools_status', 'action', 'po_number', 'brand'])
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
        $project_site_id = ProjectSites::where('status', 1)->where('project_code', $request->projectCode)->value('id');
        $assigned = AssignedProjects::where('status', 1)->where('project_id', $project_site_id)->where('pos', 'pm')->first();
        $setup_approvers = SetupApprover::where('status', 1)->where('requestor', Auth::user()->id)->where('request_type', 1)->orderBy('sequence', 'asc')->get();
        $daf_approvers = SetupApprover::where('status', 1)->where('request_type', 4)->where('company_id', Auth::user()->comp_id)->orderBy('sequence', 'asc')->get();

        if($daf_approvers->isEmpty()){
            return 3;
        }

        if(!$assigned){
            return 1;
        }elseif($setup_approvers->isEmpty()){
            return 2;
        }
        $mail_data = [];
        $mail_approvers = [];
        $mail_Items = [];


        $type = 'RF';

        // Get the current year and month in 'YYYYMM' format
        $currentYearMonth = Carbon::now()->format('Ym');

        // Fetch the latest request for the same type, year, and month
        $lastRequest = TransferRequest::withoutGlobalScope(CompanyScope::class)->where('teis_number', 'like', "{$type}-{$currentYearMonth}-%")
            ->orderBy('teis_number', 'desc')
            ->first();

        // Determine the new sequence number
        $newSequence = 1; // Default to 1 if no previous request
        if ($lastRequest) {
            // Extract the last sequence number and increment it
            $lastSequence = (int)substr($lastRequest->teis_number, strrpos($lastRequest->teis_number, '-') + 1);
            $newSequence = $lastSequence + 1;
        }

        // Format the new request number with leading zeroes for the sequence
        $new_teis_number = sprintf('%s-%s-%02d', $type, $currentYearMonth, $newSequence);


        /// old generation of request number
        // $prev_tn = TransferRequest::where('status', 1)->orderBy('teis_number', 'desc')->first();

        // $new_teis_number = '';
        // if (!$prev_tn) {
        //     $new_teis_number = 1000;
        // } else {
        //     $new_teis_number = $prev_tn->teis_number + 1;
        // }

        $req = TransferRequest::create([
            'teis_number' => $new_teis_number,
            'company_id' => Auth::user()->comp_id,
            'pe' => Auth::user()->id,
            'subcon' => $request->subcon,
            'customer_name' => $request->customerName,
            'project_name' => $request->projectName,
            'project_code' => $request->projectCode,
            'project_address' => $request->projectAddress,
            'date_requested' => Carbon::now(),
            'wh_location' => $request->whLocation,
            'status' => 1,
        ]);

        /// for logs
        RfteisLogs::create([
            'approver_name' => Auth::user()->fullname,
            'page' => 'warehouse',
            'request_number' => $new_teis_number,
            'title' => 'Request'.' '.'#'. $new_teis_number,
            'message' => Auth::user()->fullname .' '. 'created a RFTEIS request.',
            'action' => 1,
        ]);


        $approvers = [];

        $approvers[] = $setup_approvers[1]->user_id; /// warehouse first
        $approvers[] = $assigned->user_id; /// PM
        $approvers[] = $assigned->assigned_by; /// OM
        $approvers[] = $setup_approvers[0]->user_id; // cnc
        
        // foreach($setup_approvers as $approver){
        //     $approvers[] = $approver->user_id;
        // }

        foreach ($approvers as $key => $approver) {
            RequestApprover::create([
                'request_id' => $req->id,
                'company_id' => Auth::user()->comp_id,
                'approver_id' => $approver,
                'sequence' => $key + 1,
                'request_type' => 1,
            ]);  
        }


        
        Daf::create([
            'daf_number' => $new_teis_number,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->comp_id,
            'date_requested' => Carbon::now(),
            'tr_type' => 'rfteis',
        ]);

        // $req = TransferRequest::orderBy('id', 'desc')->first();


        $array_id = json_decode($request->idArray);

        $array_count = count($array_id);

        for ($i = 0; $i < $array_count; $i++) {
            TransferRequestItems::create([
                'tool_id' => $array_id[$i],
                'teis_number' => $new_teis_number,
                'transfer_request_id' => $req->id,
                'pe' => Auth::user()->id,
                'duration_date' => $request->durationDate,
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


        /// lumang pag email
        // $approvers = RequestApprover::leftjoin('users', 'users.id', 'request_approvers.approver_id')
        //     ->select('request_approvers.*', 'users.fullname', 'users.email')
        //     ->where('request_approvers.status', 1)
        //     ->where('request_type', 1)
        //     ->where('request_approvers.request_id', $req->id)
        //     ->where('request_approvers.sequence', 0)
        //     ->orderBy('request_approvers.sequence', 'asc')
        //     ->get();

        // foreach ($approvers as $approver) {
        //     array_push($mail_approvers, ['fullname' => $approver->fullname]);
        // }

        // $tools = ToolsAndEquipment::where('status', 1)->whereIn('id', $array_id)->get();

        // foreach ($tools as $tool) {
        //     array_push($mail_Items, ['asset_code' => $tool->asset_code, 'item_description' => $tool->item_description, 'price' => $tool->price]);
        // }


        foreach ($daf_approvers as $approver) {
            DafApprovers::create([
                'request_id' => $req->id,
                'company_id' => Auth::user()->comp_id,
                'approver_id' => $approver->user_id,
                'sequence' => $approver->sequence,
		        'type' => 'rfteis',
            ]);  
        }


        // foreach ($approvers as $approver) {  
        //     $mail_data = ['requestor_name' => Auth::user()->fullname, 'date_requested' => Carbon::today()->format('m/d/Y'), 'approver' => $approver->fullname, 'items' => json_encode($mail_Items)];

        //     Mail::to($approver->email)->send(new ApproverEmail($mail_data));
        // }


        $approver = RequestApprover::leftjoin('users', 'users.id', 'request_approvers.approver_id')
        ->select('request_approvers.*', 'users.fullname', 'users.email')
        ->where('request_approvers.status', 1)
        ->where('request_type', 1)
        ->where('request_approvers.request_id', $req->id)
        ->orderBy('request_approvers.sequence', 'asc')
        ->first();

        $tools = ToolsAndEquipment::where('status', 1)->whereIn('id', $array_id)->get();

        foreach ($tools as $tool) {
            array_push($mail_Items, ['asset_code' => $tool->asset_code, 'item_description' => $tool->item_description, 'price' => $tool->price]);
        }

        $mail_data = ['requestor_name' => Auth::user()->fullname, 'request_number' => $new_teis_number, 'date_requested' => Carbon::today()->format('m/d/Y'), 'approver' => $approver->fullname, 'items' => json_encode($mail_Items)];

        Mail::to($approver->email)->send(new ApproverEmail($mail_data));


    }




}
