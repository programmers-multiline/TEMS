<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\ApproverEmail;
use App\Models\PmGroupings;
use App\Models\PulloutLogs;
use App\Models\ProjectSites;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use App\Models\AssignedProjects;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\PulloutRequestItems;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\PsTransferRequestItems;

class MyToolsAndEquipmentController extends Controller
{

    public function view_my_te(){

        $pg = AssignedProjects::leftJoin('project_sites', 'project_sites.id', '=', 'assigned_projects.project_id')
            ->select('assigned_projects.project_id as id', 'project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address')
            ->where('assigned_projects.status', 1)
            ->where('project_sites.status', 1)
            ->where(function($query) {
                $query->where('assigned_projects.emp_id', Auth::user()->emp_id)
                    ->orWhere('assigned_projects.assigned_by', Auth::user()->id);
            })
            ->groupBy('assigned_projects.project_id', 'project_sites.customer_name', 'project_sites.project_name', 'project_sites.project_code', 'project_sites.project_address', 'project_sites.id')
            ->get();

        // return $pg;

        return view('/pages/my_te', compact('pg'));
    }


    public function fetch_my_te(Request $request){
      
        // $tools = TransferRequest::leftJoin('transfer_request_items', 'transfer_request_items.teis_number', 'transfer_requests.teis_number')
        // ->select('tools_and_equipment.*','transfer_request_items.tool_id')
        // ->where('transfer_request_items.status', 1)
        // ->where('transfer_request_items.teis_number', $request->id)
        // ->get();


        // $tools = TransferRequestItems::join('transfer_requests', 'transfer_request_items.pe', 'transfer_requests.pe')
        // ->join('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        // ->select('tools_and_equipment.*')
        // ->where('transfer_requests.pe', Auth::user()->id)
        // ->where('transfer_request_items.status', 1)
        // ->where('transfer_requests.progress', 'completed')
        // ->get();
        
        // $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        //                              ->leftJoin('transfer_requests','transfer_requests.id','transfer_request_items.transfer_request_id')
        //                              ->select('tools_and_equipment.*')
        //                             ->where('transfer_requests.pe', Auth::user()->id)
        //                             ->where('transfer_request_items.status', 1)
        //                             ->where('transfer_requests.progress', 'completed')
        //                             ->get();
        // if($request->id){

        //     $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        //     ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
        //     ->leftJoin('transfer_requests','transfer_requests.id','transfer_request_items.transfer_request_id')
        //     ->select('tools_and_equipment.*', 'transfer_request_items.teis_number', 'warehouses.warehouse_name')
        //     ->where('transfer_requests.pe', Auth::user()->id)
        //     ->where('transfer_request_items.status', 1)
        //     ->where('transfer_requests.progress', 'completed')
        //     ->where('transfer_requests.project_code', $request->id)
        //     ->get();
        // }else{
        //     $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        //     ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
        //     ->leftJoin('transfer_requests','transfer_requests.id','transfer_request_items.transfer_request_id')
        //     ->select('transfer_request_items.teis_number','tools_and_equipment.*', 'warehouses.warehouse_name')
        //     ->where('transfer_requests.pe', Auth::user()->id)
        //     ->where('transfer_request_items.status', 1)
        //     ->where('tools_and_equipment.status', 1)
        //     ->where('transfer_requests.status', 1)
        //     ->where('transfer_request_items.item_status', 1)
        //     ->get();

        // }

        /// under ng OM
        $PEs = AssignedProjects::where('status', 1)->where('assigned_by', Auth::id())->pluck('user_id')->toArray();

        /// under ng PM
        $projectIds = AssignedProjects::where('status', 1)
            ->where('user_id', Auth::id())
            ->where('pos', 'pm')
            ->pluck('project_id');

        // Get PE user IDs associated with those project IDs
        $peUserIds = AssignedProjects::where('status', 1)
            ->whereIn('project_id', $projectIds)
            ->where('pos', 'pe')
            ->pluck('user_id');

        if($request->projectId){

            if(Auth::user()->user_type_id == 5){
                $tools = ToolsAndEquipment::leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                ->whereIn('tools_and_equipment.current_pe', $PEs)
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.current_site_id', $request->projectId)
                ->get();

            }elseif(Auth::user()->user_type_id == 3){
                $tools = ToolsAndEquipment::leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                ->whereIn('tools_and_equipment.current_pe', $peUserIds)
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.current_site_id', $request->projectId)
                ->get();

            }else{
                $tools = ToolsAndEquipment::leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                ->where('tools_and_equipment.current_pe', Auth::user()->id)
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.current_site_id', $request->projectId)
                ->get();
            }
            
        }else{

            if(Auth::user()->user_type_id == 5){
                $tools = ToolsAndEquipment::leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                    ->whereIn('tools_and_equipment.current_pe', $PEs)
                    ->where('tools_and_equipment.status', 1)
                    ->get();

            }elseif(Auth::user()->user_type_id == 3){
                $tools = ToolsAndEquipment::leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                    ->whereIn('tools_and_equipment.current_pe', $peUserIds)
                    ->where('tools_and_equipment.status', 1)
                    ->whereIn('tools_and_equipment.current_site_id', $projectIds)
                    ->get();
            }else{
                $tools = ToolsAndEquipment::leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->select('tools_and_equipment.*', 'warehouses.warehouse_name')
                    ->where('tools_and_equipment.current_pe', Auth::user()->id)
                    ->where('tools_and_equipment.status', 1)
                    ->get();
            }
            
        }

        


        // return $tools[0]->teis_number;

        // fix my te error
        
        return DataTables::of($tools)

        ->setRowClass(function ($row) {
            $tool_ids = PulloutRequestItems::leftjoin('pullout_requests', 'pullout_requests.id', 'pullout_request_items.pullout_request_id')
            ->select('pullout_request_items.*')
            ->where('pullout_requests.progress', 'ongoing')
            ->where('pullout_request_items.item_status', 0)
            ->where('pullout_requests.status', 1)
            ->pluck('tool_id')
            ->toArray();

            $ps_tool_ids = PsTransferRequestItems::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.ps_transfer_request_id')
            ->select('ps_transfer_request_items.*')
            ->where('ps_transfer_requests.progress', 'ongoing')
            ->where('ps_transfer_request_items.item_status', 0)
            ->where('ps_transfer_requests.status', 1)
            ->pluck('tool_id')
            ->toArray();

            $all_tools_id = array_merge($tool_ids, $ps_tool_ids);

            return in_array($row->id, $all_tools_id) ? 'bg-gray' : '';
        })
        
        ->addColumn('tools_status', function($row){
            $status = $row->tools_status;
            if($status == 'good'){
                $status = '<span class="badge bg-success">'.$status.'</span>';
            }else if($status == 'repair'){
                $status =  '<span class="badge bg-warning">'.$status.'</span>';
            }else{
                $status =  '<span class="badge bg-danger">'.$status.'</span>';
            }
            return $status;
        })

        ->addColumn('transfer_state', function($row){
            $state = '';
            if($row->transfer_state == 1){
                $state = '<span class="btn btn-sm btn-alt-success" style="font-size: 12px;">Available to transfer</span>';
            }else if($row->transfer_state == 2){
                $state = '<span class="btn btn-sm btn-alt-primary" style="font-size: 12px;">Pullout Ongoing</span>';
            }else{
                $state =  '<span class="btn btn-sm btn-alt-danger" style="font-size: 12px;">Currently Using</span>';
            }
            return $state;
        })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;
            if($user_type == 3){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-pencil-alt"></i>
              </button>
              <button type="button" id="deleteToolsBtn" data-id="'.$row->id.'" class="btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                <i class="fa fa-times"></i>
              </button>';
            }else if($user_type == 4){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                ?</button>';
            }else{
                $action = '';
            }
            return $action;
        })

        ->addColumn('warehouse_name', function ($row){
            if($row->current_site_id){
                // $location = ProjectSites::where('status', 1)->where('id', $row->current_site_id)->first();
                return ProjectSites::where('status', 1)->where('id', $row->current_site_id)->value('project_location');
            }else{
                return $row->warehouse_name;
            }
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


        ->rawColumns(['transfer_state','tools_status','po_number','brand','action'])
        ->toJson();
    }


    public function pullout_request(Request $request){

        $project_site_id = ProjectSites::where('status', 1)->where('project_code', $request->projectCode)->value('id');
        $assigned = AssignedProjects::where('status', 1)->where('project_id', $project_site_id)->where('pos', 'pm')->first();

        if(!$assigned){
            return 1;
        }



        $type = 'P';

        // Get the current year and month in 'YYYYMM' format
        $currentYearMonth = Carbon::now()->format('Ym');

        // Fetch the latest request for the same type, year, and month
        $lastRequest = PulloutRequest::where('pullout_number', 'like', "{$type}-{$currentYearMonth}-%")
            ->orderBy('pullout_number', 'desc')
            ->first();

        // Determine the new sequence number
        $newSequence = 1; // Default to 1 if no previous request
        if ($lastRequest) {
            // Extract the last sequence number and increment it
            $lastSequence = (int)substr($lastRequest->pullout_number, strrpos($lastRequest->pullout_number, '-') + 1);
            $newSequence = $lastSequence + 1;
        }

        // Format the new request number with leading zeroes for the sequence
        $new_pullout_number = sprintf('%s-%s-%02d', $type, $currentYearMonth, $newSequence);



        /// old generation of request number
        // $prev_pn = PulloutRequest::where('status', 1)->orderBy('pullout_number', 'desc')->first();
        

        // $new_pullout_number = '';
        // if(!$prev_pn){
        //     $new_pullout_number = 1000;
        // }else{
        //     $new_pullout_number = $prev_pn->pullout_number + 1;
        // }

        $req = PulloutRequest::create([
            'pullout_number' => $new_pullout_number,
            'user_id' => Auth::user()->id,
            'client' => $request->client,
            'subcon' => $request->subcon,
            'project_name' => $request->projectName,
            'project_code' => $request->projectCode,
            'project_address' => $request->projectAddress,
            'pickup_date' => $request->dateToPick,
            'date_requested' => Carbon::now(),
            'contact_number' => $request->contact,
            'reason' => $request->reason,
            'comp_id' => Auth::user()->comp_id,
        ]);


        $approvers = [];

        $approvers[] = $assigned->user_id;
        $approvers[] = $assigned->assigned_by;

        foreach ($approvers as $key => $approver) {
            RequestApprover::create([
                'request_id' => $req->id,
                'approver_id' => $approver,
                'sequence' => $key + 1,
                'request_type' => 3,
            ]);  
        }

        $last = 0;

        if(!PulloutRequest::exists()){
            $last = 1;
        }else{
            $last = $req->id;
        }


        $array_table_data = $request->tableData;

        // return $array_table_data;

        $array_count = count($array_table_data);

        foreach ($array_table_data as $table_data) {
            PulloutRequestItems::create([
                'pullout_request_id' => $last,
                'tool_id' => $table_data['id'],
                'pullout_number' => $new_pullout_number,
                'user_id' => Auth::user()->id,
                'tools_status' => $table_data['tools_status'],
                'req_num' => $table_data['prev_req_num'],
            ]);

            $te = ToolsAndEquipment::where('status', 1)->where('id', $table_data['id'])->first();

            $te->transfer_state = 2;

            $te->update();
        }

        
        $array_id = collect($array_table_data)->pluck('id')->toArray();

        $mail_data = [];
        $mail_Items = [];

        $approvers = RequestApprover::leftjoin('users', 'users.id', 'request_approvers.approver_id')
        ->select('request_approvers.*', 'users.fullname', 'users.email')
        ->where('request_approvers.status', 1)->where('request_type', 3)
        ->where('request_approvers.request_id', $last)
        ->orderBy('request_approvers.sequence', 'asc')
        ->get();


        $tools = ToolsAndEquipment::where('status', 1)->whereIn('id',$array_id)->get();

        foreach ($tools as $tool) {
            array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
        }
        
        
        
        foreach ($approvers as $approver) {
            $mail_data = ['requestor_name' => Auth::user()->fullname, 'date_requested' => Carbon::today()->format('m/d/Y'), 'approver' => $approver->fullname, 'items' => json_encode($mail_Items)];
        
            // Mail::to($approver->email)->send(new ApproverEmail($mail_data));
        }

         PulloutLogs::create([
            'approver_name' => Auth::user()->fullname,
            'page' => 'my_te',
            'request_number' => $new_pullout_number,
            'title' => 'Request'.' '.'#'. $new_pullout_number,
            'message' => Auth::user()->fullname .' '. 'created a Pullout request.',
            'action' => 1,
        ]);
    }


   


    public function add_state(Request $request){
        $state_datas = json_decode($request->stateDatas);

        foreach ($state_datas as $data) {
            $tools = ToolsAndEquipment::where('status', 1)->where('id', $data->id)->first();

            $tools->transfer_state = $data->state;

            $tools->update();
        }
    }

}
