<?php

namespace App\Http\Controllers;

use App\Models\TeisUploads;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\RequestApprover;
use Yajra\DataTables\DataTables;
use App\Models\PulloutRequestItems;
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
        ->where('series', $series)
        // ->where('approver_status', 0)
        ->where('request_type', 3)
        ->get();  



        // $pullout_tools = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->get();

        
        return DataTables::of($pullout_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
        })
        ->addColumn('action', function($row){

            $tool = PulloutRequest::where('status', 1)
            ->where('pullout_requests.request_status', 'approved')
            ->get();

            $isApproved = $row->request_status == 'approved' ? 'disabled' : '';

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

        ->setRowClass(function ($row) { 
            $tool = PulloutRequest::where('status', 1)
            ->where('pullout_requests.request_status', 'approved')
            ->get();

            return 'bg-gray';
        })

        ->rawColumns(['view_tools', 'action'])
        ->toJson();
    }



    public function ongoing_pullout_request_modal(Request $request){

        $tools = PulloutRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'pullout_request_items.tool_id')
                                     ->select('tools_and_equipment.*','pullout_request_items.tool_id')
                                     ->where('pullout_request_items.status', 1)
                                     ->where('pullout_request_items.pullout_number', $request->id)
                                     ->get();

        
        
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
        ->rawColumns(['tools_status', 'action'])
        ->toJson();
    }




    public function fetch_pullout_request(){

        $request_tools = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->get();
        
        return DataTables::of($request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">view</button>';
        })
        ->addColumn('action', function($row){
            // $user_type = Auth::user()->user_type_id;

            $action =  '<div class="d-flex align-items-center gap-2">
            <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-secondary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Add Schedule" data-bs-original-title="Add Schedule"><i class="fa fa-calendar-plus"></i></button>
            <button data-pulloutnum="'.$row->pullout_number.'" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-upload"></i></button>
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
        ->addColumn('uploads', function ($row) {
            $teis_uploads = TeisUploads::with('uploads')->where('teis_number', $row->id)->get()->toArray();
        })
        ->rawColumns(['view_tools','action','uploads'])
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


}
