<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\Daf;
use App\Models\User;
use App\Models\DafItems;
use App\Models\TersUploads;
use App\Mail\EmailRequestor;
use App\Models\ProjectSites;
use App\Models\TeisUploads; 
use Illuminate\Http\Request;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\PsTransferRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\TransferRequestItems; 
use App\Models\PsTransferRequestItems;


class TransferRequestController extends Controller
{
    public function ongoing_teis_request(){ 


        $request_tools = TransferRequest::select('teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address', 'date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'ongoing')
        ->where('pe', Auth::user()->id);

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'ongoing')
        ->where('user_id', Auth::user()->id);

        $unioned_tables = $request_tools->union($ps_request_tools)->get();
        
        return DataTables::of($unioned_tables)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-transfertype="'.$row->tr_type.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
        })

        ->addColumn('request_status', function($row){

            $bg_class = $row->request_status === 'approved' ? 'bg-success' : 'bg-warning';
            
            return $request_status = '<span class="badge '.$bg_class.'">'.$row->request_status.'</span>';
        })

        ->addColumn('request_type', function($row){
            
            return strtoupper($row->tr_type);
        })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action =  '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
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
        ->rawColumns(['view_tools', 'request_status', 'request_type', 'uploads', 'action'])
        ->toJson();
    }


    public function completed_teis_request(){
        $request_tools = TransferRequest::select('teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address', 'date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'completed')
        ->where('pe', Auth::user()->id);

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'completed')
        ->where('user_id', Auth::user()->id);

        $unioned_tables = $request_tools->union($ps_request_tools)->get();
        
        return DataTables::of($unioned_tables)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-transfertype="'.$row->tr_type.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
        })

        ->addColumn('request_status', function($row){

            $bg_class = $row->request_status === 'approved' ? 'bg-success' : 'bg-warning';
            
            return $request_status = '<span class="badge '.$bg_class.'">'.$row->request_status.'</span>';
        })

        ->addColumn('request_type', function($row){
            
            return strtoupper($row->tr_type);
        })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action =  '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Print" data-bs-original-title="Print"><i class="fa fa-print"></i></button>
            </div>
            ';
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
        ->rawColumns(['view_tools', 'request_status', 'request_type', 'uploads', 'action'])
        ->toJson();
    }



    public function ongoing_teis_request_modal(Request $request){

        // $tr_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
        //         ->select('transfer_request_items.*')
        //         ->where('transfer_requests.status', 1)
        //         ->where('transfer_requests.request_status', 'pending')
        //         ->get();

        //         return $tr_id;

        if($request->type){
            if($request->type == 'rfteis'){
                $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                ->select('tools_and_equipment.*','transfer_request_items.tool_id', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number')
                ->where('transfer_request_items.status', 1)
                ->where('transfer_request_items.teis_number', $request->id)
                ->get();

            }else{
                $tools = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
                ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                ->select('tools_and_equipment.*','ps_transfer_request_items.id as pstri_id', 'ps_transfer_request_items.price', 'warehouses.warehouse_name', 'ps_transfer_request_items.request_number as r_number')
                ->where('ps_transfer_request_items.status', 1)
                ->where('ps_transfer_request_items.request_number', $request->id)
                ->get();
            }
        }else{
            $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
            ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
            ->select('tools_and_equipment.*','transfer_request_items.tool_id', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number')
            ->where('transfer_request_items.status', 1)
            ->where('transfer_request_items.teis_number', $request->id)
            ->get();
        }

        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get();
        
        return DataTables::of($tools)

        ->addColumn('action', function($row) use ($request){
            $user_type = Auth::user()->user_type_id;

            if($request->type == 'rfteis'){
                $tri = TransferRequest::where('transfer_requests.status', 1)
                ->where('transfer_requests.teis_number', $row->r_number)
                ->first();

                $isPending = $tri->request_status == 'pending' ? 'disabled' : '';
            }else{
                $ps_tr = PsTransferRequests::where('ps_transfer_requests.status', 1)
                ->where('ps_transfer_requests.request_number', $row->r_number)
                ->first();
                
                $isPending = $ps_tr->request_status == 'pending' ? 'disabled' : '';
            }

            if($request->type == 'rfteis'){
                $tri_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
                ->select('transfer_request_items.*')
                ->where('transfer_requests.status', 1)
                ->where('transfer_request_items.status', 1)
                ->where('transfer_request_items.item_status', 1)
                ->where('transfer_requests.teis_number', $row->r_number)
                ->get();

                $id = collect($tri_id)->pluck('tool_id')->toArray();

                $isApproved = in_array($row->tool_id, $id) == 'pending' ? 'disabled' : '';
            }else{
                $tri_id = PsTransferRequestItems::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.request_number')
                ->select('ps_transfer_request_items.*')
                ->where('ps_transfer_requests.status', 1)
                ->where('ps_transfer_request_items.status', 1)
                ->where('ps_transfer_requests.request_number', $row->r_number)
                ->get();

                $id = collect($tri_id)->pluck('tool_id')->toArray();

                $isApproved = in_array($row->tool_id, $id) == 'pending' ? 'disabled' : '';
            }
            

            if( $user_type == 6){
                $action = '';
            }else{
                $action =  '
                <button '.$isPending.' '.$isApproved.' data-triid="'.$row->tri_id.'" data-number="'.$row->r_number.'" type="button" class="receivedBtn btn btn-sm btn-alt-success d-block mx-auto" data-bs-toggle="tooltip" aria-label="Receive Tool" data-bs-original-title="Receive Tool"><i class="fa fa-file-circle-check"></i></button>
                ';

            }

            if ($request->path == 'pages/site_to_site_transfer') {
                $action = '';
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

        $request_tools = TransferRequest::select('teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address', 'date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'ongoing')
        ->where('request_status', 'approved');

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'ongoing')
        ->where('request_status', 'approved')
        ->whereNotNull('acc');

        $unioned_tables = $request_tools->union($ps_request_tools)->get();


        // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->where('daf_status', 1)->get();

        return DataTables::of($unioned_tables)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">'.$row->teis_number.'</button>';;
        })
        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            if($row->tr_type == 'rfteis'){
                $action =  '<button data-num="'.$row->teis_number.'" data-type="'.$row->tr_type.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Upload TEIS" data-bs-original-title="Upload TEIS"><i class="fa fa-upload me-1"></i>TEIS</button>';
            }else{
                $action =  '<div class="d-flex gap-2"><button data-type="'.$row->tr_type.'" data-num="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Upload TEIS" data-bs-original-title="Upload TEIS"><i class="fa fa-upload me-1"></i>TEIS</button>
                <button data-num="'.$row->teis_number.'" data-type="'.$row->tr_type.'" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Upload TERS" data-bs-original-title="Upload TERS"><i class="fa fa-upload me-1"></i>TERS</button>
                </div>';
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
        // ->addColumn('uploads', function ($row) {
        //     $teis_uploads = TeisUploads::with('uploads')->where('teis_number', $row->id)->get()->toArray();
        // })

        ->addColumn('teis', function ($row) {
            $teis_uploads = TeisUploads::with('uploads')->where('teis_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
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
        ->addColumn('ters', function ($row) {
            $ters_uploads = TersUploads::with('uploads')->where('pullout_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
            $uploads_file = [];
            $uploads_file ='<div class="row mx-auto">';
            foreach($ters_uploads as $item) {
                
                $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="'.env('APP_URL').'uploads/ters_form/'.$item['uploads']['name'].'">
                    <img class="border border-1 border-primary" src="'.env('APP_URL').'uploads/ters_form/'.$item['uploads']['name'].'" width="30">
                    </a>
                </div>';
                
            }
            $uploads_file .= '</div>';
            return $uploads_file;
        })

        ->rawColumns(['view_tools','action','teis', 'ters'])
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
        ->where('request_type', 1)
        ->where('series', $request->series)
        ->orderBy('sequence','desc')
        ->first();


        $tools = RequestApprover::find($request->id);

        
        $tools->approver_status = 1;
        
        $tools->update();

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
              <button type="button" id="ReceivedToolsBtn" data-id="'.$row->tri_id.'" data-teis="'.$row->teis_number.'" class="receiveBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Received" data-bs-original-title="Received" '.$isApproved.'>
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

        $tr = TransferRequest::where('status', 1)->where('id', $scannedTools->transfer_request_id)->first();
        $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();


        $tools = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->first();

        $tools->wh_ps = 'ps';
        $tools->current_pe = $scannedTools->pe;
        $tools->current_site_id = $project_site->id;

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


    public function fetch_daf_approver(){

        $series = 1;

        $approver = RequestApprover::where('status', 1)
        ->where('approver_id', Auth::user()->id)
        ->where('series', $series)
        ->where('request_type', 4)
        ->first();

        

        if($approver->sequence == 1){
            // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
            $tool_approvers = RequestApprover::leftjoin('dafs', 'dafs.id', 'request_approvers.request_id')
            ->leftjoin('transfer_requests','transfer_requests.teis_number','dafs.daf_number')
            ->select('dafs.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series','transfer_requests.subcon', 'transfer_requests.customer_name','transfer_requests.project_name','transfer_requests.project_code','transfer_requests.project_address')
            ->where('dafs.status', 1)
            ->where('transfer_requests.status', 1)
            ->where('request_approvers.status', 1)
            ->where('request_approvers.approver_id', Auth::user()->id)
            ->where('series', $series)
            ->where('approver_status', 0)
            ->where('request_type', 4)
            ->get();    

        }else{

            $prev_sequence = $approver->sequence - 1;

            $prev_approver = RequestApprover::where('status', 1)
            ->where('request_id', $approver->request_id)
            ->where('sequence', $prev_sequence)
            ->where('series', $series)
            ->where('request_type', 4)
            ->first();


            if($prev_approver->approver_status == 1){
                $tool_approvers = RequestApprover::leftjoin('dafs', 'dafs.id', 'request_approvers.request_id')
                ->leftjoin('transfer_requests','transfer_requests.teis_number','dafs.daf_number')
                ->select('dafs.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series','transfer_requests.subcon', 'transfer_requests.customer_name','transfer_requests.project_name','transfer_requests.project_code','transfer_requests.project_address')
                ->where('dafs.status', 1)
                ->where('transfer_requests.status', 1)
                ->where('request_approvers.status', 1)
                ->where('request_approvers.approver_id', Auth::user()->id)
                ->where('series', $series)
                ->where('approver_status', 0)
                ->where('request_type', 4)
                ->get();    
            }
            else{
                $tool_approvers = [];
            }
        }

        
        return DataTables::of($tool_approvers)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->daf_number.'" data-bs-toggle="modal" data-bs-target="#psOngoingTeisRequestModal" class="teisNumber btn text-info fs-6 d-block">View</button>';
        })

        // ->addColumn('request_status', function($row){
            
        //     return $request_status = '<span class="badge bg-warning">'.$row->request_status.'</span>';
        // })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $price = [];
            
            $daf_tools = DafItems::where('status', 1)->where('daf_id', $row->id)->get();
            foreach ($daf_tools as $tools) {
    
                array_push($price, $tools->price);
            }

            if( Auth::user()->dept_id !== 1){
                $price = [''];
            }
    
            $has_null = in_array(null, $price, true);
    
             $has_price = $has_null ? 'disabled' : '';

            $action =  '<div class="d-flex gap-1"><button type="button" '.$has_price.' data-requestid="'.$row->request_id.'"  data-series="'.$row->series.'" data-id="'.$row->approver_id.'" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check"></i></button>
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



    public function daf_approve_tools(Request $request){

        $mail_Items = [];

        $tobeApproveTools = RequestApprover::where('status', 1)
        ->where('request_id', $request->requestId)
        ->where('series', $request->series)
        ->orderBy('sequence','desc')
        ->first();


        $tools = RequestApprover::find($request->id);

        
            $tools->approver_status = 1;
            
            $tools->update();

        if($tools->sequence == $tobeApproveTools->sequence){
            $daf_request = Daf::find($request->requestId);
            $daf_request->request_status = "approved";

            $daf_request->update();

            $transfer_request = TransferRequest::where('status', 1)->where('teis_number', $daf_request->daf_number)->first();

            $transfer_request->daf_status = 1;

            $transfer_request->update();


            $user = User::where('status', 1)->where('id', $daf_request->user_id)->first();


            $tools_approved = DafItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'daf_items.tool_id')
            ->select('tools_and_equipment.*')
            ->where('tools_and_equipment.status', 1)
            ->where('daf_items.item_status', 0)
            ->where('daf_id', $daf_request->id)
            ->get();

            foreach ($tools_approved as $tool) {
                array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
            }
            
        
            $mail_data = ['fullname' => $user->fullname, 'items' => json_encode($mail_Items)];
        
            Mail::to($user->email)->send(new EmailRequestor($mail_data));


        }
    }



    public function daf_table_modal(Request $request){

        $tools = DafItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'daf_items.tool_id')
                                     ->select('tools_and_equipment.*','daf_items.id as pstri_id', 'daf_items.price')
                                     ->where('daf_items.status', 1)
                                     ->where('daf_items.daf_number', $request->id)
                                     ->get();


        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get(); lagay ka barcode to receive btn

        
        
        return DataTables::of($tools)

        ->addColumn('action', function($row){
            if( $user_type = 6){
                $action = '';
            }else{
                $action =  '
                <button data-bs-toggle="modal" data-bs-target="#" type="button" class="receiveBtn btn btn-sm btn-alt-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-file-circle-check"></i></button>
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

        ->addColumn('add_price', function($row){

            $is_have_value = $row->price ? 'disabled' : '';
            $is_accounting = Auth::user()->dept_id != '1' ? 'disabled' : '';

            $add_price = '<input value="'.$row->price.'" data-id="'.$row->pstri_id.'" '.$is_have_value.' '.$is_accounting .' style="width: 100px;" type="number" class="price" name="price" min="1">';
            return $add_price;
        })

        ->rawColumns(['tools_status', 'action', 'add_price'])
        ->toJson();
    }

    
    public function add_price_acc_daf(Request $request){
        
        $price_datas = json_decode($request->priceDatas);

        foreach ($price_datas as $data) {
            $daf_itams = DafItems::where('status', 1)->where('id', $data->id)->first();

            $daf_itams->price = $data->price;

            $daf_itams->update();
        }
        
    }


    public function fetch_site_tools(){
        
        $series = 1;


        if(Auth::user()->user_type_id == 4){
            $ps_request_tools = PsTransferRequests::leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
            ->select('users.fullname','request_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type')
            ->where('ps_transfer_requests.status', 1)
            ->where('users.status', 1)
            ->where('progress', 'ongoing')
            ->where('current_pe', Auth::user()->id);
        }else{

            $approver = RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('series', $series)
            ->where('request_type', 2)
            ->first();
            

            if($approver->sequence == 1){
                $ps_request_tools = PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
                ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                ->select('users.fullname','request_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
                ->where('ps_transfer_requests.status', 1)
                ->where('request_approvers.status', 1)
                // ->where('current_pe', Auth::user()->id)
                ->where('request_approvers.approver_id', Auth::user()->id)
                ->where('progress', 'ongoing')
                // ->where('series', $series)
                ->where('approver_status', 0)
                ->where('request_type', 2)
                ->get();

            }else{

                $prev_sequence = $approver->sequence - 1;

                $prev_approver = RequestApprover::where('status', 1)
                ->where('request_id', $approver->request_id)
                ->where('sequence', $prev_sequence)
                ->where('series', $series)
                ->where('request_type', 2)
                ->first();


                if($prev_approver->approver_status == 1){
                    $ps_request_tools = PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
                    ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                    ->select('users.fullname','request_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
                    ->where('ps_transfer_requests.status', 1)
                    ->where('request_approvers.status', 1)
                    // ->where('current_pe', Auth::user()->id)
                    ->where('request_approvers.approver_id', Auth::user()->id)
                    ->where('progress', 'ongoing')
                    // ->where('series', $series)
                    ->where('approver_status', 0)
                    ->where('request_type', 2)
                    ->get();
                }
                else{
                    $ps_request_tools = [];
                }
            }
        }




        // if(Auth::user()->user_type_id == 4){
        //     $ps_request_tools = PsTransferRequests::select('request_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type')
        //     ->where('status', 1)
        //     ->where('progress', 'ongoing')
        //     ->where('current_pe', Auth::user()->id);
        // }else{
        //     $ps_request_tools = PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
        //     ->select('request_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
        //     ->where('ps_transfer_requests.status', 1)
        //     ->where('request_approvers.status', 1)
        //     // ->where('current_pe', Auth::user()->id)
        //     ->where('request_approvers.approver_id', Auth::user()->id)
        //     ->where('progress', 'ongoing')
        //     // ->where('series', $series)
        //     ->where('approver_status', 0)
        //     ->where('request_type', 2)
        //     ->get();
        // }


        return DataTables::of($ps_request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->request_number.'" data-transfertype="'.$row->tr_type.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
        })

        ->addColumn('request_status', function($row){

            $bg_class = $row->request_status === 'approved' ? 'bg-success' : 'bg-warning';
            
            return $request_status = '<span class="badge '.$bg_class.'">'.$row->request_status.'</span>';
        })

        ->addColumn('request_type', function($row){
            
            return strtoupper($row->tr_type);
        })

        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action = '';

            if($user_type !== 4){
                $action =  '<div class="d-flex gap-1"><button data-requestid="'.$row->request_id.'" data-id="'.$row->request_approver_id.'" data-series="'.$row->series.'" type="button" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-check"></i></button>
                </div>
                ';
            }

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
        ->rawColumns(['view_tools', 'request_status', 'request_type', 'uploads', 'action'])
        ->toJson();
    }


    public function ps_approve_tools(Request $request){

        $mail_Items = [];

        $tobeApproveTools = RequestApprover::where('status', 1)
        ->where('request_id', $request->requestId)
        ->where('request_type', 1)
        ->where('series', $request->series)
        ->orderBy('sequence','desc')
        ->first();


        $tools = RequestApprover::find($request->id);
        
        $tools->approver_status = 1;
        
        $tools->update();

        if($tools->sequence == $tobeApproveTools->sequence){
            $ps_transfer_request = PsTransferRequests::find($request->requestId);
            $ps_transfer_request->request_status = "approved";

            $ps_transfer_request->update();


            $user = User::where('status', 1)->where('id', $ps_transfer_request->user_id)->first();


            $tools_approved = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
            ->select('tools_and_equipment.*')
            ->where('tools_and_equipment.status', 1)
            // ->where('ps_transfer_request_items.item_status', 1)
            ->where('ps_transfer_request_id', $ps_transfer_request->id)
            ->get();

            foreach ($tools_approved as $tool) {
                array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
            }
            
        
            $mail_data = ['fullname' => $user->fullname, 'items' => json_encode($mail_Items)];
        
            Mail::to($user->email)->send(new EmailRequestor($mail_data));


        }
    }
  
    public function ps_approve_rttte(Request $request){
        $ps_tools = PsTransferRequests::where('status', 1)->where('request_number', $request->requestNum)->first();

        $ps_tools->acc = Carbon::now();

        $ps_tools->update();

    }

}
