<?php

namespace App\Http\Controllers;

use App\Models\TeisUploads;
use App\Models\TersUploads;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\RequestApprover;
use Yajra\DataTables\DataTables;
use App\Models\PulloutRequestItems;
use App\Models\ProjectSites;
use App\Models\ToolsAndEquipment;
use Illuminate\Support\Facades\Auth;

class PullOutController extends Controller
{

    public function fetch_ongoing_pullout(){

        $series = 1;
        
        $approver = RequestApprover::where('status', 1)
        ->where('approver_id', Auth::user()->id)
        ->where('series', $series)
        ->where('request_type', 3)
        ->first();

        $pullout_tools = RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
        ->select('pullout_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series')
        ->where('pullout_requests.status', 1)
        ->where('request_approvers.status', 1)
        ->where('request_approvers.approver_id', Auth::user()->id)
        // ->where('series', $series)
        ->where('approver_status', 0)
        ->where('request_type', 3)
        ->get();  


        if(Auth::user()->user_type_id == 4){

            $pullout_tools = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->get();
        }

        
        return DataTables::of($pullout_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
        })
        ->addColumn('action', function($row){

            $tool = PulloutRequest::where('status', 1)
            ->where('pullout_requests.request_status', 'approved')
            ->get();

            // $isApproved = $row->request_status == 'approved' ? 'disabled' : '';

            $user_type = Auth::user()->user_type_id;

    
            if($user_type == 4){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                ';
            }else if($user_type == 3 || $user_type == 5){
                $action =  '<div class="d-flex"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                <button type="button" data-requestid="'.$row->request_id.'"  data-series="'.$row->series.'" data-id="'.$row->approver_id.'" class="pulloutApproveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve" data-bs-original-title="Approve"><i class="fa fa-check"></i></button>
                </div>';
            };
            return $action;
        })

        // ->setRowClass(function ($row) { 
        //     $tool = PulloutRequest::where('status', 1)->get();
        //     $status = collect($tool)->pluck('request_status')->toArray();

        //     return in_array('approved', $status) ? 'bg-gray' : '';

        // })

        ->rawColumns(['view_tools', 'action'])
        ->toJson();
    }



    public function ongoing_pullout_request_modal(Request $request){
        if($request->path == "pages/pullout_warehouse"){
            $tools = PulloutRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'pullout_request_items.tool_id')
            ->leftjoin('warehouses','warehouses.id','tools_and_equipment.location')
            ->select('tools_and_equipment.*','pullout_request_items.tool_id', 'warehouses.warehouse_name', 'pullout_request_items.tools_status as tool_status_eval', 'pullout_request_items.id as pri_id')
            ->where('pullout_request_items.status', 1)
            ->where('pullout_request_items.item_status', 0)
            ->where('pullout_request_items.pullout_number', $request->id)
            ->get();
        }else{
            $tools = PulloutRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'pullout_request_items.tool_id')
            ->leftjoin('warehouses','warehouses.id','tools_and_equipment.location')
            ->select('tools_and_equipment.*','pullout_request_items.tool_id', 'warehouses.warehouse_name', 'pullout_request_items.tools_status as tool_status_eval', 'pullout_request_items.id as pri_id')
            ->where('pullout_request_items.status', 1)
            ->where('pullout_request_items.pullout_number', $request->id)
            ->get();
        }


        
        
        return DataTables::of($tools)

        ->addColumn('action', function($row){
            $action =  '
            <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-alt-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-file-circle-check"></i></button>
            ';
            return $action;
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

        ->addColumn('new_tools_status', function($row){
            $status = $row->tool_status_eval;
            if($status == 'good'){
                $status = '<span class="badge bg-success">'.$status.'</span>';
            }else if($status == 'repair'){
                $status =  '<span class="badge bg-warning">'.$status.'</span>';
            }else{
                $status =  '<span class="badge bg-danger">'.$status.'</span>';
            }
            return $status;
        })

        ->rawColumns(['tools_status', 'action', 'new_tools_status'])
        ->toJson();
    }


    public function fetch_approved_pullout(){

        $pullout_tools = RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
        ->select('pullout_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.approved_by')
        ->where('pullout_requests.status', 1)
        ->where('request_approvers.status', 1)
        ->where('request_approvers.approved_by', Auth::user()->id)
        ->where('request_type', 3)
        ->get();  
        
        return DataTables::of($pullout_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
        })
        ->addColumn('action', function($row){

            $action = '';

            return $action;
        })

        ->rawColumns(['view_tools', 'action'])
        ->toJson();
    }




    public function fetch_pullout_request(){

        $request_tools = PulloutRequest::leftjoin('users', 'users.id', 'pullout_requests.user_id')
        ->select('pullout_requests.*', 'users.fullname')
        ->where('pullout_requests.status', 1)
        ->where('users.status', 1)
        ->where('progress', 'ongoing')
        ->where('request_status', 'approved')
        ->get();
        
        return DataTables::of($request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">view</button>';
        })
        ->addColumn('action', function($row){
            // $user_type = Auth::user()->user_type_id;

            $have_sched = $row->approved_sched_date ? 'disabled' : '';

            $action =  '<div class="d-flex align-items-center gap-2">
            <button have_sched id="addSchedBtn" data-pulloutnum="'.$row->pullout_number.'" data-pe="'.$row->fullname.'" data-location="'.$row->project_address.'" data-pickupdate="'.$row->pickup_date.'" data-bs-toggle="modal" data-bs-target="#addSched" type="button" class="btn btn-sm btn-secondary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Add Schedule" data-bs-original-title="Add Schedule"><i class="fa fa-calendar-plus"></i></button>
            <button data-pulloutnum="'.$row->pullout_number.'" data-type="pullout" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-upload"></i></button>
            ';

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
        ->addColumn('ters', function ($row) {
            $ters_uploads = TersUploads::with('uploads')->where('pullout_number', $row->pullout_number)->where('tr_type', $row->tr_type)->get()->toArray();
            $uploads_file = [];
            $uploads_file ='<div class="row mx-auto">';
            foreach($ters_uploads as $item) {
                
                $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="'.env('APP_URL').'uploads/ters_form/'.$item['uploads']['name'].'">
                    <span>TERS.pdf</span>
                    </a>
                </div>';
                
            }
            $uploads_file .= '</div>';
            return $uploads_file;
        })
        ->rawColumns(['view_tools','action','ters'])
        ->toJson();
    }

    public function tobe_approve_tools(Request $request){

        $tools = RequestApprover::find($request->id);

        $tools->approver_status = 1;
        $tools->approved_by = Auth::user()->id;
        
        $tools->update();


        $tobeApproveTools = RequestApprover::where('status', 1)
        ->where('request_id', $request->requestId)
        ->where('series', $request->series)
        ->where('request_type', 3)
        ->orderBy('approver_status', 'asc')
        ->first();

        $tobeApproveTools->approver_status = 1;
        $tobeApproveTools->update();



        $pullout_request = PulloutRequest::find($request->requestId);
        $pullout_request->request_status = "approved";

        $pullout_request->update();
    }



    public function fetch_completed_pullout(){

    $pullout_tools = PulloutRequest::where('status', 1)->where('progress', 'completed')->get();
        
        return DataTables::of($pullout_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
        })
        ->addColumn('action', function($row){

            $user_type = Auth::user()->user_type_id;

    
            if($user_type == 4){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                ';
            }else if($user_type == 3 || $user_type == 5){
                $action =  '<div class="d-flex"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                <button type="button" data-requestid="'.$row->request_id.'"  data-series="'.$row->series.'" data-id="'.$row->approver_id.'" '.$isApproved.' class="pulloutApproveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve" data-bs-original-title="Approve"><i class="fa fa-check"></i></button>
                </div>';
            };
            return $action;
        })

        ->rawColumns(['view_tools', 'action'])
        ->toJson();
    }

    public function fetch_sched_date(){
        $pullout_request = PulloutRequest::leftjoin('users','users.id','pullout_requests.user_id')
        ->select('pullout_requests.pullout_number', 'pullout_requests.project_address', 'pullout_requests.approved_sched_date','pullout_requests.contact_number', 'users.fullname', 'pullout_requests.project_name', 'pullout_requests.client')
        ->where('pullout_requests.status', 1)->whereNotNull('approved_sched_date')->get();
        return $pullout_request;
    }

    public function add_schedule(Request $request){
        $pullout_request = PulloutRequest::where('status', 1)->where('pullout_number', $request->pulloutNum)->first();

        $pullout_request->approved_sched_date = $request->pickupDate;

        $pullout_request->update();
    }

    public function received_pullout_tools(Request $request){
        if($request->multi){

            $priIds = json_decode($request->priIdArray);

            
            foreach($priIds as $pri_id){
                
                $received_tools = PulloutRequestItems::find($pri_id);

                $received_tools->item_status = 1;
        
                $received_tools->update();
        
                $tr = PulloutRequest::where('status', 1)->where('id', $received_tools->pullout_request_id)->first();
                $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();
        
        
                $tools = ToolsAndEquipment::where('status', 1)->where('id', $received_tools->tool_id)->first();
        
                $tools->wh_ps = 'wh';
                $tools->current_pe = null;
                $tools->current_site_id = null;
        
                $tools->update();

            }

        }else{
            $received_tools = PulloutRequestItems::find($request->id);

            $received_tools->item_status = 1;
    
            $received_tools->update();
    
            $tr = PulloutRequest::where('status', 1)->where('id', $received_tools->pullout_request_id)->first();
            $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();
    
    
            $tools = ToolsAndEquipment::where('status', 1)->where('id', $received_tools->tool_id)->first();
    
            $tools->wh_ps = 'wh';
            $tools->current_pe = null;
            $tools->current_site_id = null;
    
            $tools->update();  

        }

        $pri = PulloutRequestItems::where('status', 1)
        ->where('pullout_number', $received_tools->pullout_number)
        ->get();

        $item_status = collect($pri)->pluck('item_status')->toArray();

        $allStatus = array_unique($item_status);

        if(count($allStatus) == 1){
            $tool_requests = PulloutRequest::find($pri[0]->pullout_request_id);
            
            $tool_requests->progress = 'completed';
            $tool_requests->update();
        }
    }


}
