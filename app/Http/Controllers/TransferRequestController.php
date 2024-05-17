<?php

namespace App\Http\Controllers;

use Mail;
use App\Models\User;
use App\Mail\EmailRequestor;
use App\Models\TeisUploads; 
use Illuminate\Http\Request;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use Illuminate\Support\Facades\Auth;
use App\Models\TransferRequestItems; 

class TransferRequestController extends Controller
{
    public function ongoing_teis_request(){

        $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
        
        return DataTables::of($request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
        })

        ->addColumn('request_status', function($row){

            $bg_class = $row->request_status === 'approved' ? 'bg-success' : 'bg-warning';
            
            return $request_status = '<span class="badge '.$bg_class.'">'.$row->request_status.'</span>';
        })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action =  '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
            </div>
            ';
            // <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-alt-danger d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-barcode"></i></button>

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
            $uploads_file = [];
            $uploads_file ='<div class="row mx-auto">';
            foreach($teis_uploads as $item) {
                
                $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="'.env('APP_URL').'uploads/teis_form/'.$item['uploads']['name'].'">
                    <img class="border border-1 border-primary" src="'.env('APP_URL').'uploads/teis_form/'.$item['uploads']['name'].'" width="30">
                    </a>
                </div>';
                
            }
            $uploads_file .= '</div>';
            return $uploads_file;
        })
        ->rawColumns(['view_tools', 'request_status', 'uploads', 'action'])
        ->toJson();
    }



    public function ongoing_teis_request_modal(Request $request){

        $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                                     ->select('tools_and_equipment.*','transfer_request_items.tool_id')
                                     ->where('transfer_request_items.status', 1)
                                     ->where('transfer_request_items.teis_number', $request->id)
                                     ->get();

        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get(); lagay ka barcode to receive btn

        
        
        return DataTables::of($tools)

        ->addColumn('action', function($row){
            if( $user_type = 6){
                $action = '';
            }else{
                $action =  '
                <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-alt-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-file-circle-check"></i></button>
                ';

            }
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




    public function fetch_teis_request(){

        $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->get();
        
        return DataTables::of($request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">'.$row->teis_number.'</button>';;
        })
        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;
            $action =  '<button data-teisnum="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-upload me-1"></i>TEIS</button>';

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


    public function fetch_rfteis_approver(){


        // $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
        // ->leftJoin('transfer_request_items', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
        // ->leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        // ->select('tools_and_equipment.*', 'transfer_requests.*')
        // ->where('transfer_requests.status', 1)
        // ->where('transfer_request_items.status', 1)
        // ->where('tools_and_equipment.status', 1)
        // ->where('request_approvers.status', 1)
        // ->where('approver_id', Auth::user()->id)
        // ->get();

        $series = 1;

        $approver = RequestApprover::where('status', 1)
        ->where('approver_id', Auth::user()->id)
        ->where('series', $series)
        ->where('request_type', 1)
        ->first();
        

        if($approver->sequence == 1){
            // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
            $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
            ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series')
            ->where('transfer_requests.status', 1)
            ->where('request_approvers.status', 1)
            ->where('request_approvers.approver_id', Auth::user()->id)
            ->where('series', $series)
            ->where('approver_status', 0)
            ->where('request_type', 1)
            ->get();    

        }else{

            $prev_sequence = $approver->sequence - 1;

            $prev_approver = RequestApprover::where('status', 1)
            ->where('request_id', $approver->request_id)
            ->where('sequence', $prev_sequence)
            ->where('series', $series)
            ->where('request_type', 1)
            ->first();


            if($prev_approver->approver_status == 1){
                $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series')
                ->where('transfer_requests.status', 1)
                ->where('request_approvers.status', 1)
                ->where('approver_id', Auth::user()->id)
                ->where('series', $series)
                ->where('approver_status', 0)
                ->where('request_type', 1)
                ->get();
            }
            else{
                $tool_approvers = [];
            }
        }

        
        return DataTables::of($tool_approvers)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-info fs-6 d-block">View</button>';
        })

        // ->addColumn('request_status', function($row){
            
        //     return $request_status = '<span class="badge bg-warning">'.$row->request_status.'</span>';
        // })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action =  '<div class="d-flex gap-1"><button type="button" data-requestid="'.$row->request_id.'"  data-series="'.$row->series.'" data-id="'.$row->approver_id.'" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check"></i></button>
            </div>
            ';
            // <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-alt-danger d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-barcode"></i></button>

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
            $uploads_file = [];
            $uploads_file ='<div class="row mx-auto">';
            foreach($teis_uploads as $item) {
                
                $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="'.env('APP_URL').'uploads/teis_form/'.$item['uploads']['name'].'">
                    <img class="border border-1 border-primary" src="'.env('APP_URL').'uploads/teis_form/'.$item['uploads']['name'].'" width="30">
                    </a>
                </div>';
                
            }
            $uploads_file .= '</div>';
            return $uploads_file;
        })
        ->rawColumns(['view_tools', 'uploads', 'action'])
        ->toJson();
    }

    public function approve_tools(Request $request){

        $mail_Items = [];

        $tobeApproveTools = RequestApprover::where('status', 1)
        ->where('request_id', $request->requestId)
        ->where('series', $request->series)
        ->orderBy('sequence','desc')
        ->first();


        $tools = RequestApprover::find($request->id);

        
        // $tools->approver_status = 1;
        
        // $tools->update();

        if($tools->sequence == $tobeApproveTools->sequence){
            $transfer_request = TransferRequest::find($request->requestId);
            $transfer_request->request_status = "approved";

            $transfer_request->update();


            $user = User::where('status', 1)->where('id', $transfer_request->pe)->first();


            $tools_approved = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
            ->select('tools_and_equipment.*')
            ->where('tools_and_equipment.status', 1)
            ->where('transfer_request_items.item_status', 1)
            ->where('transfer_request_id', $transfer_request->id)
            ->get();

            foreach ($tools_approved as $tool) {
                array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
            }
            
        
            $mail_data = ['fullname' => $user->fullname, 'items' => json_encode($mail_Items)];
        
            Mail::to($user->email)->send(new EmailRequestor($mail_data));


        }
    }

    public function scanned_teis(Request $request){

        $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        ->select('tools_and_equipment.*','transfer_request_items.tool_id', 'transfer_request_items.id as tri_id','transfer_request_items.teis_number')
        ->where('transfer_request_items.status', 1)
        ->where('transfer_request_items.teis_number', $request->barcode)
        ->get();
       
        return DataTables::of($tools)
        
            ->setRowClass(function ($row) { 
                $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
                ->select('transfer_request_items.*')
                // ->where('transfer_requests.progress', 'ongoing')
                ->where('transfer_requests.status', 1)
                ->where('transfer_request_items.item_status', 1)
                ->get();
        
                $toolIds = collect($tool_id)->pluck('tool_id')->toArray();

                return in_array($row->id, $toolIds) ? 'bg-gray' : '';
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
        ->addColumn('action', function($row){

            $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
            ->select('transfer_request_items.*')
            // ->where('transfer_requests.progress', 'ongoing')
            ->where('transfer_requests.status', 1)
            ->where('transfer_request_items.item_status', 1)
            ->get();
    
            $toolIds = collect($tool_id)->pluck('tool_id')->toArray();

             $isApproved = in_array($row->id, $toolIds) ? 'disabled' : '';

                return $action =  '
              <button type="button" id="ReceivedToolsBtn" data-id="'.$row->tri_id.'" data-teis="'.$row->teis_number.'" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Received" data-bs-original-title="Received" '.$isApproved.'>
                <i class="fa fa-clipboard-check"></i>
              </button>';
            
        })
        ->rawColumns(['tools_status','action'])
        ->toJson();
    }

    public function scanned_teis_received(Request $request){
        $scannedTools = TransferRequestItems::find($request->id);

        $scannedTools->item_status = 1;

        $scannedTools->update();

        $tools = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->first();

        $tools->wh_ps = 'ps';

        $tools->update();


        $tri = TransferRequestItems::where('status', 1)
        ->where('teis_number', $request->teis_num)
        ->get();

        $item_status = collect($tri)->pluck('item_status')->toArray();

        $allStatus = array_unique($item_status);

        if(count($allStatus) == 1){
            $tool_requests = TransferRequest::find($tri[0]->transfer_request_id);
            
            $tool_requests->progress = 'completed';
            $tool_requests->update();
        }



    }


}
