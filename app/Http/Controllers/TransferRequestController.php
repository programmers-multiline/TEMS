<?php

namespace App\Http\Controllers;

use App\Models\RttteLogs;
use Mail;
use Carbon\Carbon;
use App\Models\Daf;
use App\Models\User;
use App\Models\PeLogs;
use App\Models\Uploads;
use App\Models\DafItems;
use App\Models\RfteisLogs;
use App\Mail\ApproverEmail;
use App\Models\TeisUploads;
use App\Models\TersUploads;
use Illuminate\Support\Str;
use App\Mail\EmailRequestor;
use App\Models\ProjectSites;
use App\Models\ToolPictures;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\ReceivingProof;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use App\Models\AssignedProjects;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\PsTransferRequests;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use App\Models\ToolsAndEquipmentLogs;
use App\Models\PsTransferRequestItems;
use App\Models\ToolPictureReceivingUploads;
use Illuminate\Database\Eloquent\Factories\Sequence;


class TransferRequestController extends Controller
{
    public function ongoing_teis_request(Request $request)
    {


        $request_tools = TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'progress')
            ->where('status', 1)
            ->where('progress', 'ongoing')
            ->where('pe', Auth::user()->id);

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type','progress')
            ->where('status', 1)
            ->where('progress', 'ongoing')
            ->where('user_id', Auth::user()->id);


        if ($request->path == 'pages/request_for_receiving') {
            $request_tools = TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver', 'progress')
                ->where('status', 1)
                ->where(function($query) {
                    $query->where('progress', 'ongoing')
                        ->orWhere('progress', 'partial');
                })
                ->where('pe', Auth::user()->id)
                ->whereNotNull('is_deliver');

            $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver', 'progress')
                ->where('status', 1)
                ->where('progress', 'ongoing')
                ->where('request_status', 'approved')
                ->where('user_id', Auth::user()->id)
                ->whereNotNull('is_deliver');
        }

        $unioned_tables = $request_tools->union($ps_request_tools)->get();

        return DataTables::of($unioned_tables)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->teis_number . '" data-transfertype="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
            })

            ->addColumn('request_status', function ($row) {

                if($row->progress === 'completed'){
                    return '<span class="badge bg-success">' . $row->progress . '</span>';
                }elseif($row->progress === 'partial'){
                    return '<span class="badge bg-elegance">' . $row->progress . '</span>';
                }else{
                    return '<span class="badge bg-warning">' . $row->progress . '</span>';
                }
                
            })

            ->addColumn('request_type', function ($row) {

                return strtoupper($row->tr_type);
            })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $action = '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#trackRequestModal" data-trtype="' . $row->tr_type . '" data-requestnumber="' . $row->teis_number . '" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
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
            ->addColumn('teis', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                if(!$teis_uploads){
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }else{
                    $uploads_file = [];
                    $uploads_file = '<div class="row mx-auto">';
                    foreach ($teis_uploads as $item) {
                        
                        $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                        <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                        <span>TEIS.pdf</span>
                        </a>
                        </div>';
                        
                    }
                    $uploads_file .= '</div>';
                    return $uploads_file;
                }
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                if(!$ters_uploads){
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }else{
                    $uploads_file = [];
                    $uploads_file = '<div class="row mx-auto">';
                    foreach ($ters_uploads as $item) {

                        $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                        <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                        $item['uploads']['name'] . '">
                        <span>TERS.pdf</span>
                        </a>
                    </div>';

                    }
                    $uploads_file .= '</div>';
                    return $uploads_file;
                }
            })

            ->addColumn('subcon', function($row){
                if(!$row->subcon){
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }else{
                    return $row->subcon;
                }
            })

            ->addColumn('customer_name', function($row){
                if(!$row->customer_name){
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }else{
                    return $row->customer_name;
                }
            })

            ->rawColumns(['view_tools', 'request_status', 'request_type','customer_name','subcon', 'teis', 'ters', 'action'])
            ->toJson();
    }

    public function ps_ongoing_teis_request(Request $request)
    {

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'ongoing')
            ->where('user_id', Auth::user()->id)
            ->get();


        if ($request->path == 'pages/ps_request_for_receiving') {

            $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver')
                ->where('status', 1)
                ->where('progress', 'ongoing')
                ->where('request_status', 'approved')
                ->where('user_id', Auth::user()->id)
                ->whereNotNull('is_deliver')
                ->get();
        }

        return DataTables::of($ps_request_tools)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->teis_number . '" data-transfertype="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
            })

            ->addColumn('request_status', function ($row) {

                if($row->progress === 'completed'){
                    return '<span class="badge bg-success">' . $row->progress . '</span>';
                }elseif($row->progress === 'partial'){
                    return '<span class="badge bg-elegance">' . $row->progress . '</span>';
                }else{
                    return '<span class="badge bg-warning">' . $row->progress . '</span>';
                }
            })

            ->addColumn('request_type', function ($row) {

                return strtoupper($row->tr_type);
            })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $action = '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#trackRequestModal" data-trtype="' . $row->tr_type . '" data-requestnumber="' . $row->teis_number . '" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
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
            ->addColumn('teis', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <span>TEIS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                    $item['uploads']['name'] . '">
                    <span>TERS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->rawColumns(['view_tools', 'request_status', 'request_type', 'teis', 'ters', 'action'])
            ->toJson();
    }


    public function completed_teis_request()
    {
        $request_tools = TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'completed')
            ->where('pe', Auth::user()->id);

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'completed')
            ->where('user_id', Auth::user()->id);

        $unioned_tables = $request_tools->union($ps_request_tools)->get();

        return DataTables::of($unioned_tables)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->teis_number . '" data-transfertype="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
            })

            ->addColumn('request_status', function ($row) {

                if($row->progress === 'completed'){
                    return '<span class="badge bg-success">' . $row->progress . '</span>';
                }elseif($row->progress === 'partial'){
                    return '<span class="badge bg-elegance">' . $row->progress . '</span>';
                }else{
                    return '<span class="badge bg-warning">' . $row->progress . '</span>';
                }
            })

            ->addColumn('request_type', function ($row) {

                return strtoupper($row->tr_type);
            })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $action = '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#trackRequestModal" data-trtype="' . $row->tr_type . '" data-requestnumber="' . $row->teis_number . '" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
            </div>
            ';
                return $action;
            })
            ->addColumn('teis', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <span>TEIS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                    $item['uploads']['name'] . '">
                    <span>TERS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->rawColumns(['view_tools', 'request_status', 'request_type', 'teis', 'ters', 'action'])
            ->toJson();
    }



    public function ongoing_teis_request_modal(Request $request)
    {

        // $tr_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
        //         ->select('transfer_request_items.*')
        //         ->where('transfer_requests.status', 1)
        //         ->where('transfer_requests.request_status', 'pending')
        //         ->get();

        //         return $tr_id;

        if ($request->type) {
            if ($request->type == 'rfteis') {
                if(Auth::user()->user_type_id == 4){
                    $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                    ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->select('tools_and_equipment.*', 'transfer_request_items.price','transfer_request_items.transfer_state', 'transfer_request_items.is_remove', 'transfer_request_items.remove_by', 'transfer_request_items.tool_id', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number', 'transfer_request_items.item_status')
                    ->where('transfer_request_items.status', 1)
                    ->where('transfer_request_items.teis_number', $request->id)
                    ->get();
                }elseif($request->type == 'rftte_signed_form_proof'){
                    //!hindiyin mo ano desisyon nila kung ipapakita ba ang item na not serve or hindi
                    $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                    ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->select('tools_and_equipment.*', 'transfer_request_items.price', 'transfer_request_items.transfer_state', 'transfer_request_items.is_remove', 'transfer_request_items.remove_by', 'transfer_request_items.tool_id', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number', 'transfer_request_items.item_status')
                    ->where('transfer_request_items.status', 1)
                    ->whereNull('transfer_request_items.is_remove')
                    ->where('transfer_request_items.teis_number', $request->id)
                    ->get();
                }else{
                    $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                    ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->select('tools_and_equipment.*', 'transfer_request_items.price', 'transfer_request_items.transfer_state', 'transfer_request_items.is_remove', 'transfer_request_items.remove_by', 'transfer_request_items.tool_id', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number', 'transfer_request_items.item_status')
                    ->where('transfer_request_items.status', 1)
                    ->whereNull('transfer_request_items.is_remove')
                    ->where('transfer_request_items.teis_number', $request->id)
                    ->get();
                }
             
                // if($tools)

                // if ($request->path == "pages/request_for_receiving") {
                //     $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                //         ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                //         ->select('tools_and_equipment.*', 'transfer_request_items.price', 'transfer_request_items.tool_id', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number')
                //         ->where('transfer_request_items.status', 1)
                //         ->where('transfer_request_items.item_status', 0)
                //         ->where('transfer_request_items.teis_number', $request->id)
                //         ->get();
                // }

            } else {
                $tools = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
                    ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                    ->leftJoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.ps_transfer_request_id')
                    ->select('tools_and_equipment.*','ps_transfer_request_items.price', 'ps_transfer_requests.tr_type', 'ps_transfer_requests.reason_for_transfer', 'teis_no', 'ps_transfer_request_items.id as tri_id', 'ps_transfer_request_items.price', 'warehouses.warehouse_name', 'ps_transfer_request_items.request_number as r_number', 'ps_transfer_request_items.tool_id', 'ps_transfer_request_items.item_status')
                    ->where('ps_transfer_request_items.status', 1)
                    ->where('ps_transfer_request_items.request_number', $request->id)
                    ->get();

                // if ($request->path == "pages/request_for_receiving") {
                //     $tools = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
                //         ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                //         ->select('tools_and_equipment.*', 'ps_transfer_request_items.price', 'ps_transfer_request_items.id as tri_id', 'ps_transfer_request_items.price', 'warehouses.warehouse_name', 'ps_transfer_request_items.request_number as r_number', 'ps_transfer_request_items.tool_id', 'ps_transfer_request_items.item_status')
                //         ->where('ps_transfer_request_items.status', 1)
                //         ->where('ps_transfer_request_items.item_status', 0)
                //         ->where('ps_transfer_request_items.request_number', $request->id)
                //         ->get();
                // }

                if ($request->path == "pages/sts_request_completed" || $request->path == "pages/site_to_site_approved") {
                    $tools = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
                        ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                        ->select('tools_and_equipment.*', 'ps_transfer_request_items.price', 'ps_transfer_requests.tr_type', 'ps_transfer_requests.reason_for_transfer', 'teis_no', 'ps_transfer_request_items.id as tri_id', 'ps_transfer_request_items.price', 'warehouses.warehouse_name', 'ps_transfer_request_items.request_number as r_number', 'ps_transfer_request_items.tool_id', 'ps_transfer_request_items.item_status')
                        ->where('ps_transfer_request_items.status', 1)
                        ->where('ps_transfer_request_items.request_number', $request->id)
                        ->get();
                }
            }
        } else {
            $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                ->leftJoin('warehouses', 'tools_and_equipment.location', 'warehouses.id')
                ->select('tools_and_equipment.*', 'transfer_request_items.price', 'transfer_request_items.tool_id','transfer_request_items.transfer_state', 'warehouses.warehouse_name', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number as r_number', 'transfer_request_items.item_status')
                ->where('transfer_request_items.status', 1)
                ->whereNull('transfer_request_items.is_remove')
                ->where('transfer_request_items.teis_number', $request->id)
                ->get();
        }

        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get();

        // return $tools;
        $count = 1; 

        return DataTables::of($tools)
            ->addColumn('checkbox', function ($row) use ($request) {
                return '<input type="checkbox" class="chkItem" />';
            })

            ->addColumn('item_no', function() use (&$count){
                return $count++;
            })

            ->addColumn('picture', function ($row) use ($request) {
                if ($request->type == 'rttte') {
                    $picture = ToolPictures::leftjoin('uploads', 'uploads.id', 'upload_id')
                        ->select('uploads.name', 'uploads.original_name')
                        ->where('tool_pictures.status', 1)
                        ->where('pstr_id', $row->r_number)
                        ->where('tool_id', $row->tool_id)
                        ->first();

                    $uploads_file =
                        '<div class="row mx-auto">
                        <div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                            <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/tool_pictures') . '/' . $picture->name . '">
                            <span>' . $picture->original_name . '</span>
                            </a>
                        </div>
                    </div>';

                    return $uploads_file;
                }
            })

            ->addColumn('action', function ($row) use ($request) {
                $user_type = Auth::user()->user_type_id;
                //?walang bilang to pwede o burahin
                // if ($request->type == 'rfteis') {
                //     $tri = TransferRequest::where('transfer_requests.status', 1)
                //         ->where('transfer_requests.teis_number', $row->r_number)
                //         ->first();

                //     $isPending = $tri->request_status == 'pending' ? 'disabled' : '';
                // } else if ($request->type == 'rttte') {
                //     $ps_tr = PsTransferRequests::where('ps_transfer_requests.status', 1)
                //         ->where('ps_transfer_requests.request_number', $row->r_number)
                //         ->first();

                //     $isPending = $ps_tr->request_status == 'pending' ? 'disabled' : '';
                // } else {
                //     $isPending = 'disabled';
                // }

                if ($request->type == 'rfteis') {
                    ///para lang sa not_serve_item sa warehouse
                    if($request->path == 'pages/not_serve_items'){
                        if($row->transfer_state == 1){
                            return '<div class="text-center"><span class="badge bg-earth text-center">Redeliver</span></div>';
                        }elseif($row->transfer_state == 2){
                            return '<div class="text-center"><span class="badge bg-elegance">For creation of TERS</span></div>';
                        }else{
                            if($row->item_status == 2 && Auth::user()->user_type_id == 2){
                                return '<div class="d-flex justify-content-center align-items-center gap-2"><button data-triid="'.$row->tri_id.'" data-number="'.$row->r_number.'" data-triggerby="yes" class="availableforRedeliver btn btn-sm btn-success">YES</button><button data-triid="'.$row->tri_id.'" data-number="'.$row->r_number.'" data-triggerby="no" class="notAvailableforRedeliver btn btn-sm btn-danger">NO</button></div>';
                            }else{
                                return '';
                            }
                        }
                        
                    }
                    ///para sa lahat
                    if($row->item_status == 1){
                        $action = '<div class="text-center"><span class="badge bg-success text-center">Served</span></div>';
                    }elseif($row->item_status == 2){
                        $action = '<div class="text-center"><span class="badge bg-danger">Not Served</span></div>';
                    }else{
                        if ($user_type == 4 && $request->path == 'pages/request_for_receiving') {
                            $action = '<div class="d-flex gap-2 justify-content-center align-items-center">
                        <button data-trtype="rfteis" data-triid="' . $row->tri_id . '" data-number="' . $row->r_number . '" type="button" class="receivedBtn btn btn-sm btn-alt-success" data-bs-toggle="tooltip" aria-label="Receive Tool" data-bs-original-title="Receive Tool"><i class="fa fa-circle-check"></i></button>
                        <button data-trtype="rfteis" data-triid="' . $row->tri_id . '" data-number="' . $row->r_number . '" type="button" class="notReceivedBtn btn btn-sm btn-alt-danger" data-bs-toggle="tooltip" aria-label="Not Serve" data-bs-original-title="Not Serve"><i class="fa fa-circle-xmark"></i></button>
                        </div>';
                        } else {
                            $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                        }
        
                        if ($request->path == 'pages/site_to_site_transfer') {
                            $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                        }
                    }
                } else {
                    // $tri_id = PsTransferRequestItems::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.request_number')
                    //     ->select('ps_transfer_request_items.*')
                    //     ->where('ps_transfer_requests.status', 1)
                    //     ->where('ps_transfer_request_items.status', 1)
                    //     ->where('ps_transfer_requests.request_number', $row->r_number)
                    //     ->get();

                    // $id = collect($tri_id)->pluck('tool_id')->toArray();

                    // $isApproved = in_array($row->tool_id, $id) == 'pending' ? 'disabled' : '';
                    if($row->item_status == 1){
                        $action = '<div class="text-center"><span class="badge bg-success text-center">Served</span></div>';
                    }elseif($row->item_status == 2){
                        $action = '<div class="text-center"><span class="badge bg-danger">Not Served</span></div>';
                    }else{
                        if ($user_type == 4 && $request->path == 'pages/request_for_receiving') {
                            $action = '<div class="d-flex gap-2 justify-content-center align-items-center">
                        <button data-triid="' . $row->tri_id . '" data-number="' . $row->r_number . '" data-trtype="' . $row->tr_type . '" type="button" class="receivedBtn btn btn-sm btn-alt-success" data-bs-toggle="tooltip" aria-label="Receive Tool" data-bs-original-title="Receive Tool"><i class="fa fa-circle-check"></i></button>
                        <button data-triid="' . $row->tri_id . '" data-number="' . $row->r_number . '" data-trtype="' . $row->tr_type . '" type="button" class="notReceivedBtn btn btn-sm btn-alt-danger" data-bs-toggle="tooltip" aria-label="Not Serve" data-bs-original-title="Not Serve"><i class="fa fa-circle-xmark"></i></button>
                        </div>';
                        } else {
                            $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                        }
        
                        if ($request->path == 'pages/site_to_site_transfer') {
                            $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                        }
                    }
                }  
                /// sa approver na pm and om
                if($user_type == 3 || $user_type == 5){
                    $action = '<button data-trtype="rfteis" data-triid="' . $row->tri_id . '" data-number="' . $row->r_number . '" type="button" class="removeToolRequestBtn btn btn-sm btn-alt-danger d-block mx-auto" data-bs-toggle="tooltip" aria-label="Remove this tool?" data-bs-original-title="Remove this tool?"><i class="fa fa-xmark"></i></button>';
                }elseif($user_type == 4 && $row->is_remove){
                    $name = User::where('status', 1)->where('id', $row->remove_by)->value('fullname');
                    $action = '<span class="d-block text-center text-danger" style="font-size: 14px;">Removed by '.$name.'</span>';
                }

                return $action;
            })

            ->addColumn('tools_delivery_status', function ($row) {
                $status = $row->item_status;
                if ($status == 1) {
                    $status = '<div class="text-center"><span class="badge bg-success text-center">Served</span></div>';
                } else if ($status == 2) {
                    $status ='<div class="text-center"><span class="badge bg-danger">Not Served</span></div>';
                } else {
                    $status = '<div class="text-center"><span class="badge bg-warning">Waiting</span></div>';
                }
                return $status;
            })

            ->addColumn('tools_status', function ($row) {
                $status = $row->tools_status;
                if ($status == 'good') {
                    $status = '<span class="">Good</span>';
                } else if ($status == 'repair') {
                    $status = '<span class="">Defective</span>';
                } else {
                    $status = '<span class="badge bg-danger">' . $status . '</span>';
                }
                return $status;
            })

            ->addColumn('warehouse_name', function ($row) {
                if ($row->current_site_id) {
                    // $location = ProjectSites::where('status', 1)->where('id', $row->current_site_id)->first();
                    return ProjectSites::where('status', 1)->where('id', $row->current_site_id)->value('project_location');
                } else {
                    return $row->warehouse_name;
                }
            })

            ->addColumn('add_price', function ($row) {

                // $is_have_value = $row->price ? 'disabled' : '';
    
                $add_price = '<input class="form-control price" value="' . $row->price . '" data-id="' . $row->tri_id . '" style="width: 110px;" type="number" name="price" min="1">';
                return $add_price;
            })

            ->rawColumns(['tools_status', 'action', 'checkbox', 'picture', 'add_price', 'tools_delivery_status'])
            ->toJson();
    }




    public function fetch_teis_request(Request $request)
    {

        if($request->path == 'pages/rftte_signed_form_proof'){
            $request_tools = TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('status', 1)
                ->where('progress', 'completed')
                ->whereNull('is_proof_upload');


            $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('status', 1)
                ->where('progress', 'completed')
                ->whereNull('is_proof_upload');

        }else if($request->path == 'pages/not_serve_items'){
            $request_tools = TransferRequest::join('transfer_request_items', 'transfer_request_items.transfer_request_id', 'transfer_requests.id')
                ->select('transfer_requests.teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('transfer_request_items.item_status', 2)
                ->where('transfer_request_items.status', 1)
                ->where('progress', 'partial')
                ->where('transfer_requests.status', 1);


            $ps_request_tools = PsTransferRequests::join('ps_transfer_request_items', 'ps_transfer_request_items.ps_transfer_request_id', 'ps_transfer_requests.id')
                ->select('ps_transfer_requests.request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('ps_transfer_request_items.item_status', 2)
                ->where('ps_transfer_request_items.status', 1)
                ->where('progress', 'completed')
                ->where('ps_transfer_requests.status', 1);
        }else{
            $request_tools = TransferRequest::select('teis_number', 'pe', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'for_pricing as current_pe')
            ->where('status', 1)
            ->where('progress', 'ongoing')
            ->where('request_status', 'approved')
            ->whereNull('is_deliver');

            $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'user_id as pe', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'current_pe')
                ->where('status', 1)
                ->where('progress', 'ongoing')
                ->where('request_status', 'approved')
                ///dahil inalis ko yung inputing of price sa acc
                // ->whereNotNull('acc')
                ->whereNull('is_deliver');

         }
            
            $transfer_requests = $request_tools->union($ps_request_tools)->get();

            // return $transfer_requests;

        // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->where('daf_status', 1)->get();

        return DataTables::of($transfer_requests)

            ->addColumn('view_tools', function ($row) {

                return '<button data-type="' . $row->tr_type . '" data-id="' . $row->teis_number . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';
            })
            ->addColumn('action', function ($row) use ($request) {
                $user_type = Auth::user()->user_type_id;

                if($request->path == 'pages/not_serve_items'){


                    $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->teis_number)->where('tr_type', 'rfteis')->get()->toArray();
                    $uploads_file = [];
                    $uploads_file ='<div class="row mx-auto">';
                    foreach($ters_uploads as $item) {
                        
                        $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                            <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="'.asset('uploads/ters_form') . '/' .$item['uploads']['name'].'">
                            <span>TERS.pdf</span>
                            </a>
                        </div>';
                        
                    }
                    $uploads_file .= '</div>';

                    $have_ters = $ters_uploads ? 'disabled' : '';

                    $transfer_state = TransferRequestItems::where('status', 1)->whereNull('is_remove')->where('teis_number', $row->teis_number)->pluck('transfer_state')->toArray();

                    $have_two = in_array(2, $transfer_state);
                    //!baliktarin para gumana
                    if($have_two){
                      return ' <button '.$have_ters.' data-num="' . $row->teis_number . '" data-type="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-success js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Upload TERS" data-trigger="hover" data-bs-original-title="Upload TERS"><span class="d-flex align-items-center"><i class="fa fa-upload me-1"></i>TERS</span></button>
                            ';  
                    }elseif($ters_uploads){
                        return $uploads_file;
                    }else{
                        return '';
                    }
                    
                }

                if ($row->tr_type == 'rfteis') {

                    $tool_ids = TransferRequestItems::where('status', 1)->whereNull('is_remove')->where('teis_number', $row->teis_number)->pluck('tool_id')->toArray();
                    $ids = json_encode($tool_ids);

                    $teis_uploads = TeisUploads::where('status', 1)
                        ->where('tr_type', 'rfteis')->get();

                    $teis_numbers = collect($teis_uploads)->pluck('teis_number')->toArray();

                    $have_teis = in_array($row->teis_number, $teis_numbers) ? 'disabled' : '';
                    $have_teis2 = in_array($row->teis_number, $teis_numbers) ? '' : 'disabled';

                    $action = '
                            <div class="d-flex gap-2 align-items-center justify-content-center">
                                <button ' . $have_teis . ' data-pe="'.$row->pe.'" data-num="' . $row->teis_number . '" data-type="' . $row->tr_type . '" data-toolid="'.$ids.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-success js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Upload TEIS" data-bs-original-title="Upload TEIS"><span class="d-flex align-items-center"><i class="fa fa-upload me-1"></i>TEIS</span></button>
                                <button ' . $have_teis2 . ' data-num="' . $row->teis_number . '" data-type="' . $row->tr_type . '" type="button" class="deliverBtn btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Deliver" data-bs-original-title="Deliver"><i class="fa fa-truck"></i></button>
                            </div>
                            ';
                } else {
                    // kunin lahat ng tool id na nakapaloob dito sa request na ito. para sa pe_logs
                    $ps_tool_ids = PsTransferRequestItems::where('status', 1)->whereNull('is_remove')->where('request_number', $row->teis_number)->pluck('tool_id')->toArray();
                    $ids = json_encode($ps_tool_ids);

                    $prev_request_number = PsTransferRequestItems::where('status', 1)->whereNull('is_remove')->where('request_number', $row->teis_number)->value('prev_request_num');

                    $teis_uploads = TeisUploads::where('status', 1)
                        ->where('tr_type', 'rttte')->get();

                    $teis_numbers = collect($teis_uploads)->pluck('teis_number')->toArray();

                    $have_teis = in_array($row->teis_number, $teis_numbers) ? 'disabled' : '';


                    $ters_uploads = TersUploads::where('status', 1)
                        ->where('tr_type', 'rttte')->get();

                    $ters_numbers = collect($ters_uploads)->pluck('pullout_number')->toArray();

                    $have_ters = in_array($row->teis_number, $ters_numbers) ? 'disabled' : '';
                    $have_ters2 = in_array($row->teis_number, $ters_numbers) ? '' : 'disabled';

                    $action = '<div class="d-flex gap-2">
                    <button ' . $have_ters . ' data-prevreqnum="'.$prev_request_number.'" data-prevpe="'.$row->current_pe.'" data-num="' . $row->teis_number . '" data-type="' . $row->tr_type . '" data-toolid="'.$ids.'" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Upload TERS" data-bs-original-title="Upload TERS"><i class="fa fa-upload me-1"></i>TERS</button>
                    <button ' . $have_teis . ' data-pe="'.$row->pe.'" data-type="' . $row->tr_type . '" data-num="' . $row->teis_number . '" data-toolid="'.$ids.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Upload TEIS" data-bs-original-title="Upload TEIS"><i class="fa fa-upload me-1"></i>TEIS</button>
                    <button ' . $have_ters2 . ' data-num="' . $row->teis_number . '" data-type="' . $row->tr_type . '" type="button" class="proceedBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Deliver" data-bs-original-title="Deliver"><i class="fa fa-truck"></i></button>
                    </div>';
                    }
                    /// for proof of receiving in warehouse
                    if($request->path == 'pages/rftte_signed_form_proof'){
                        $action = '
                                    <button data-num="' . $row->teis_number . '" data-type="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#uploadReceivingProof" type="button" class="uploadReceivingProofBtn btn btn-sm btn-success js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Upload Proof of Received" data-bs-original-title="Upload Proof of Received"><span class="d-flex align-items-center"><i class="fa fa-upload me-1"></i>Proof</span></button>
                                ';
                    }

                
                // <button data-num="'.$row->teis_number.'" data-type="'.$row->tr_type.'" type="button" class="approvedBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Approve" data-bs-original-title="Approve"><i class="fa fa-check"></i></button>
    
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
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <span>TEIS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                    $item['uploads']['name'] . '">
                    <span>TERS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })

            ->addColumn('received_proof', function ($row) {
                $received_proof_uploads = ReceivingProof::with('uploads')->where('status', 1)->where('request_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($received_proof_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/receiving_proofs') . '/' .
                    $item['uploads']['name'] . '">
                    <span>Proof</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })


            ->addColumn('progress', function ($row) {
                if($row->progress === 'completed'){
                    return '<span class="badge bg-success">' . $row->progress . '</span>';
                }elseif($row->progress === 'partial'){
                    return '<span class="badge bg-elegance">' . $row->request_status . '</span>';
                }else{
                    return '<span class="badge bg-warning">' . $row->request_status . '</span>';
                }
            })


            ->addColumn('tr_type', function ($row) {
                return Str::upper($row->tr_type);
            })

            ->rawColumns(['view_tools', 'action', 'teis', 'ters', 'received_proof', 'progress'])
            ->toJson();
    }

    public function fetch_teis_request_completed()
    {

        $request_tools = TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'completed')
            ->whereNotNull('is_proof_upload');


        $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'completed')
            ///inalis na kasi ang acc sa process
            // ->whereNotNull('acc')
            ->whereNotNull('is_proof_upload');

        $unioned_tables = $request_tools->union($ps_request_tools)->get();


        // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->where('daf_status', 1)->get();

        return DataTables::of($unioned_tables)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->teis_number . '" data-type="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';
            })
            ->addColumn('teis', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <span>TEIS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                    $item['uploads']['name'] . '">
                    <span>TERS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })

            ->addColumn('received_proof', function ($row) {
                $received_proof_uploads = ReceivingProof::with('uploads')->where('status', 1)->where('request_number', $row->teis_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($received_proof_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/receiving_proofs') . '/' .
                    $item['uploads']['name'] . '">
                    <span>Proof</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })

            ->rawColumns(['view_tools', 'teis', 'ters', 'received_proof'])
            ->toJson();
    }


    public function fetch_rfteis_approver(Request $request)
    {

        // $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
        // ->leftJoin('transfer_request_items', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
        // ->leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
        // ->select('tools_and_equipment.*', 'transfer_requests.*')
        // ->where('transfer_requests.status', 1)
        // ->where('transfer_request_items.status', 1)
        // ->where('tools_and_equipment.status', 1)
        // ->where('request_approvers.status', 1)
        // ->where('approver_id', Auth::user()->id)
        // ->where('id', 0)
        // ->get();


        $approvers = RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('approver_status', 0)
            ->where('request_type', 1)
            ->get();

        // return $approvers;


        // if (!$approvers) {
        //     return datatables()->of(collect())->make(true);
        // }

        // $tool_approvers = [];

        // foreach ($approvers as $approver) {
        //     if ($approver->sequence == 0) {
        //         // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
        //         $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
        //             ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
        //             ->where('transfer_requests.status', 1)
        //             ->where('request_approvers.status', 1)
        //             ->where('request_approvers.approver_id', Auth::user()->id)
        //             ->where('approver_status', 0)
        //             ->where('request_type', 1)
        //             ->get();

        //         // return $tool_approvers;

        //     } elseif ($approver->sequence == 1) {
        //         $prev_approver = RequestApprover::where('status', 1)
        //             ->where('request_id', $approver->request_id)
        //             ->where('sequence', 0)
        //             ->where('request_type', 1)
        //             ->orderBy('approver_status', 'desc')
        //             ->first();

        //         if ($prev_approver->approver_status == 1) {
        //             $tool_approvers = RequestApprover::leftjoin(
        //                 'transfer_requests',
        //                 'transfer_requests.id',
        //                 'request_approvers.request_id',
        //             )
        //                 ->select(
        //                     'transfer_requests.*',
        //                     'request_approvers.id as approver_id',
        //                     'request_approvers.request_id',
        //                     'request_approvers.series',
        //                     'request_approvers.date_approved'
        //                 )
        //                 ->where('transfer_requests.status', 1)
        //                 ->where('request_approvers.status', 1)
        //                 ->where('approver_id', Auth::user()->id)
        //                 ->where('approver_status', 0)
        //                 ->where('request_type', 1)
        //                 ->get();

        //         } else {
        //             $tool_approvers = [];
        //         }
        //     } else {

        //         $prev_sequence = $approver->sequence - 1;

        //         $prev_approver = RequestApprover::where('status', 1)
        //             ->where('request_id', $approver->request_id)
        //             ->where('sequence', $prev_sequence)
        //             ->where('request_type', 1)
        //             ->first();


        //         if ($prev_approver->approver_status == 1) {
        //             $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
        //                 ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
        //                 ->where('transfer_requests.status', 1)
        //                 ->where('request_approvers.status', 1)
        //                 ->where('approver_id', Auth::user()->id)
        //                 ->where('approver_status', 0)
        //                 ->where('request_type', 1)
        //                 ->get();
        //         } else {
        //             $tool_approvers = [];
        //         }
        //     }

        // }
        $tool_approvers = collect();

        foreach ($approvers as $approver) {
            $current_approvers = collect();

            if ($approver->sequence == 0) {
                $current_approvers = RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                    ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                    ->where('transfer_requests.status', 1)
                    ->where('request_approvers.status', 1)
                    ->where('request_approvers.approver_id', Auth::user()->id)
                    ->where('approver_status', 0)
                    ->where('request_type', 1)
                    ->get();
            } elseif ($approver->sequence == 1) {
                // $prev_approver = RequestApprover::where('status', 1)
                //     ->where('request_id', $approver->request_id)
                //     ->where('sequence', 0)
                //     ->where('request_type', 1)
                //     ->orderBy('approver_status', 'desc')
                //     ->first();

                // if ($prev_approver && $prev_approver->approver_status == 1) {
                    $current_approvers = RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                        ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                        ->where('transfer_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('approver_id', Auth::user()->id)
                        ->where('approver_status', 0)
                        ->where('request_type', 1)
                        ->where('request_approvers.id', $approver->id)
                        // ->where('for_pricing', '2')
                        ->get();
                // }
                // return $current_approvers;
            } else {
                $prev_sequence = $approver->sequence - 1;

                $prev_approver = RequestApprover::where('status', 1)
                    ->where('request_id', $approver->request_id)
                    ->where('sequence', $prev_sequence)
                    ->where('request_type', 1)
                    ->first();

                    // return $prev_approver;

                if ($prev_approver && $prev_approver->approver_status == 1) {
                    $current_approvers = RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                        ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                        ->where('transfer_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('approver_id', Auth::user()->id)
                        ->where('approver_status', 0)
                        ->where('request_type', 1)
                        ->where('request_approvers.id', $approver->id)
                        ->get();
                }
            }

            // Merge the current approvers to the tool_approvers array
            $tool_approvers = $tool_approvers->merge($current_approvers)->unique('request_id');
        }

        // return $tool_approvers;

        if ($request->path == 'pages/rfteis_approved') {
            $tool_approvers = RequestApprover::leftjoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                ->leftjoin('users', 'users.id', 'request_approvers.approved_by')
                ->select('transfer_requests.*', 'users.fullname', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                ->where('transfer_requests.status', 1)
                ->where('request_approvers.status', 1)
                ->where('request_approvers.can_be_view_by', Auth::user()->id)
                ->where('approver_status', 1)
                ->where('request_type', 1)
                ->get();
        }

        // $tool_approvers = (object) $tool_approvers;

        // return $tool_approvers;
        return DataTables::of($tool_approvers)

            ->addColumn('view_tools', function ($row) {

                return '<button data-pe="'.$row->pe.'" data-trid="'.$row->id.'" data-id="' . $row->teis_number . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-info fs-6 d-block">View</button>';
            })


            ->addColumn('approver_name', function ($row) {

                if (!$row->fullname) {
                    return Auth::user()->fullname;
                } else {
                    return $row->fullname;
                }

            })

            // ->addColumn('request_status', function($row){

            //     return $request_status = '<span class="badge bg-warning">'.$row->request_status.'</span>';
            // })

            ->addColumn('action', function ($row) use ($request) {
                $user_type = Auth::user()->user_type_id;

                $tools = TransferRequestItems::where('status', 1)->where('transfer_request_id', $row->id)->pluck('tool_id')->toArray();

                $items = json_encode($tools);

                $action = '<div class="d-flex gap-1"><button type="button" data-requestorid="' . $row->pe . '" data-toolid="' . $items . '" data-requestid="' . $row->request_id . '"  data-series="' . $row->series . '" data-id="' . $row->approver_id . '" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check"></i></button>
            </div>
            ';

                if ($request->path == 'pages/rfteis_approved') {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                }
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
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->id)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <img class="border border-1 border-primary" src="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '" width="30">
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->rawColumns(['view_tools', 'uploads', 'action'])
            ->toJson();
    }

    public function approve_tools(Request $request)
    {
        //* for email
        $requestor_name = User::where('status', 1)->where('id', $request->requestorId)->first('fullname');


        $mail_Items = [];

        $tobeApproveTools = RequestApprover::where('status', 1)
            ->where('request_id', $request->requestId)
            ->where('request_type', 1)
            ->orderBy('sequence', 'desc')
            ->first();


        $tools = RequestApprover::find($request->id);


        $tools->approver_status = 1;
        $tools->approved_by = Auth::user()->id;
        $tools->date_approved = Carbon::now();

        $tools->update();


        /// for logs

        if($tools->sequence == 1){
           $sequence = 'First approver ';
        }elseif($tools->sequence == 2){
            $sequence = 'Second approver ';
        }elseif($tools->sequence == 3){
            $sequence = 'Third approver ';
        }
        elseif($tools->sequence == 4){
            $sequence = 'Fourth approver ';
        }elseif($tools->sequence == 5){
            $sequence = 'Fifth approver ';
        }else{
            $sequence = '';
        }

        RfteisLogs::create([
            'page' => 'rfteis',
            'request_number' => $request->requestNumber,
            'title' => 'Approve Request',
            'message' => $sequence . Auth::user()->fullname .' '. 'approved the request.',
            'approver_name' => Auth::user()->fullname,
            'action' => 3,
        ]);



        if ($tools->sequence == 0) {
            $shotgun_approver = RequestApprover::where('status', 1)
                ->where('request_id', $request->requestId)
                ->where('request_type', 1)
                ->where('approver_status', 0)
                ->orderBy('sequence', 'asc')
                ->first();

            $shotgun_approver->approver_status = 1;
            $shotgun_approver->can_be_view_by = Auth::user()->id;
            $shotgun_approver->date_approved = Carbon::now();
            $shotgun_approver->update();

            $tools->can_be_view_by = $shotgun_approver->approver_id;
            $tools->update();

            //* palatandaan lang na i didisplay na sa accounting user 
            TransferRequest::where('status', 1)->where('id', $request->requestId)->update([
                'for_pricing' => 1
            ]);

            //pag in approved ng acc gawing done yung for_pricing para makita ni cnc

        } else {
            $tools->can_be_view_by = Auth::user()->id;
            $tools->update();
        }




        //ayusin mo to kapag om gumagana pero pag pm hindi
        // if ($tools->sequence == 0) {
        //     $tobeApproveToolsTeis = RequestApprover::where('status', 1)
        //         ->where('request_id', $request->requestId)
        //         ->where('series', $request->series)
        //         ->where('request_type', 1)
        //         ->where('sequence', 0)
        //         ->first();

        //     $tobeApproveToolsTeis->approver_status = 1;
        //     $tobeApproveToolsTeis->update();
        // }

        //*for email
        $nextSec = $tools->sequence + 1;

        $approver = RequestApprover::leftjoin('users', 'users.id', 'request_approvers.approver_id')
            ->select('request_approvers.*', 'users.fullname', 'users.email')
            ->where('request_approvers.status', 1)
            ->where('request_type', 1)
            ->where('request_approvers.request_id', $tools->request_id)
            ->where('request_approvers.sequence', $nextSec)
            ->first();

        // return $approvers;

        $items = ToolsAndEquipment::where('status', 1)->whereIn('id', $request->toolId)->get();

        foreach ($items as $tool) {
            array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
        }


        //! $mail_data = ['requestor_name' => $requestor_name['fullname'], 'date_requested' => Carbon::today()->format('m/d/Y'), 'approver' => $approver->fullname, 'items' => json_encode($mail_Items)];

        // Mail::to($approver->email)->send(new ApproverEmail($mail_data));



        if ($tools->sequence == $tobeApproveTools->sequence) {
            $transfer_request = TransferRequest::find($request->requestId);
            $transfer_request->request_status = "approved";

            $transfer_request->update();


            $user = User::where('status', 1)->where('id', $transfer_request->pe)->first();


            $tools_approved = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                ->select('tools_and_equipment.*')
                ->where('tools_and_equipment.status', 1)
                ->where('transfer_request_items.item_status', 0)
                ->where('transfer_request_id', $transfer_request->id)
                ->get();

            foreach ($tools_approved as $tool) {
                array_push($mail_Items, ['item_code' => $tool->item_code, 'item_description' => $tool->item_description, 'brand' => $tool->brand]);
            }


            $mail_data = ['fullname' => $user->fullname, 'items' => json_encode($mail_Items)];

            // Mail::to($user->email)->send(new EmailRequestor($mail_data));


        }
    }


    public function scanned_teis(Request $request)
    {

        $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
            ->select('tools_and_equipment.*', 'transfer_request_items.tool_id', 'transfer_request_items.id as tri_id', 'transfer_request_items.teis_number')
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

                $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
                    ->select('transfer_request_items.*')
                    // ->where('transfer_requests.progress', 'ongoing')
                    ->where('transfer_requests.status', 1)
                    ->where('transfer_request_items.item_status', 1)
                    ->get();

                $toolIds = collect($tool_id)->pluck('tool_id')->toArray();

                $isApproved = in_array($row->id, $toolIds) ? 'disabled' : '';

                return $action = '
              <button type="button" id="ReceivedToolsBtn" data-id="' . $row->tri_id . '" data-teis="' . $row->teis_number . '" class="receiveBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Received" data-bs-original-title="Received" ' . $isApproved . '>
                <i class="fa fa-clipboard-check"></i>
              </button>';

            })
            ->rawColumns(['tools_status', 'action'])
            ->toJson();
    }

    public function scanned_teis_received(Request $request)
    {

        if ($request->type == 'rttte') {
            if ($request->multi) {

                $pstriIds = json_decode($request->triIdArray);

                foreach ($pstriIds as $pstri_id) {

                    $scannedTools = PsTransferRequestItems::find($pstri_id);


                    $scannedTools->item_status = 1;

                    $scannedTools->update();

                    $tr = PsTransferRequests::where('status', 1)->where('id', $scannedTools->ps_transfer_request_id)->first();
                    $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();


                    $tools = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->first();

                    $tools->wh_ps = 'ps';
                    $tools->current_pe = $scannedTools->user_id;
                    $tools->current_site_id = $project_site->id;
                    $tools->transfer_state = 0;

                    $tools->update();

                }
            } else {
                $scannedTools = PsTransferRequestItems::find($request->id);

                if ($request->has('photo')) {
                    $toolPicture = $request->photo;
                
                    // Decode base64 image
                    $image = explode(',', $toolPicture)[1]; // Remove "data:image/jpeg;base64,"
                    $image = base64_decode($image);
                
                    // Generate a unique file name
                    $pic_name = mt_rand(111111, 999999) . date('YmdHms') . '.jpg';
                
                    // Save the file to the desired directory
                    $uploadPath = public_path('uploads/tool_picture_receiving_uploads/');
                    $filePath = $uploadPath . $pic_name;
                
                    // Ensure directory exists
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                
                    file_put_contents($filePath, $image);
                
                    $uploads = Uploads::create([
                        'name' => $pic_name,
                        'original_name' => $pic_name,
                        'extension' => 'jpg',
                    ]);
                
                    ToolPictureReceivingUploads::create([
                        'request_item_id' => $scannedTools->id, 
                        'tool_id' => $scannedTools->tool_id,
                        'upload_id' => $uploads->id,
                        'user_id' => Auth::id(),
                        'tr_type' => 'rttte',
                    ]);
                }




                $scannedTools->item_status = 1;

                // $scannedTools->clear = 1;

                $scannedTools->update();

                $tr = PsTransferRequests::where('status', 1)->where('id', $scannedTools->ps_transfer_request_id)->first();
                $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();


                $tools = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->first();

                $tools->wh_ps = 'ps';
                $tools->current_pe = $scannedTools->user_id;
                $tools->current_site_id = $project_site->id;
                // $tools->transfer_state = 0;
                $tools->prev_request_num = $scannedTools->request_number;

                $tools->update();  
                
                ToolsAndEquipmentLogs::create([
                    'tool_id' => $scannedTools->tool_id,
                    'pe' => Auth::id(),
                    'tr_type' => $request->type,
                    'remarks' => 'galing kay PE',
                ]);


                /// for logs
                RttteLogs::create([
                    'page' => 'request_for_receiving',
                    'request_number' => $scannedTools->request_number,
                    'title' => 'Received Tool',
                    'message' => Auth::user()->fullname .' '. 'received ' . $tools->item_description,
                    'action' => 7,
                    'approver_name' => Auth::user()->fullname,
                ]);


                /// for logs
                RttteLogs::create([
                    'page' => 'request_for_receiving',
                    'request_number' => $scannedTools->request_number,
                    'title' => 'Tool Picture (Receiving Proof)',
                    'message' => Auth::user()->fullname .' '. 'uploaded a picture of '.$tools->item_description .'.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/tool_picture_receiving_uploads') . '/' .
                    $pic_name . '">
                        <span>View</span>
                        </a>',
                    'action' => 8,
                    'approver_name' => Auth::user()->fullname,
                ]);
            }

            $tri = PsTransferRequestItems::where('status', 1)
                ->where('request_number', $scannedTools->request_number)
                ->get();

            $item_status = collect($tri)->pluck('item_status')->toArray();

            $allStatus = in_array(0, $item_status) || in_array(2, $item_status);
            $is_have_not_served = in_array(2, $item_status);

            if($is_have_not_served){
                $tool_requests = PsTransferRequests::find($tri[0]->ps_transfer_request_id);

                $tool_requests->progress = 'partial';
                $tool_requests->update();
            }elseif(!$allStatus){
                $tool_requests = PsTransferRequests::find($tri[0]->ps_transfer_request_id);

                $tool_requests->progress = 'completed';
                $tool_requests->update();
            }
        } else {

            if ($request->multi) {

                $triIds = json_decode($request->triIdArray);


                foreach ($triIds as $tri_id) {

                    $scannedTools = TransferRequestItems::find($tri_id);

                    $scannedTools->item_status = 1;

                    $scannedTools->update();

                    $tr = TransferRequest::where('status', 1)->where('id', $scannedTools->transfer_request_id)->first();
                    $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();


                    $tools = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->first();

                    $tools->wh_ps = 'ps';
                    $tools->current_pe = $scannedTools->pe;
                    $tools->current_site_id = $project_site->id;
                    $tools->transfer_state = 0;

                    $tools->update();

                }

            } else {
                $scannedTools = TransferRequestItems::find($request->id);

                // rfteis tool upload receiving

                if ($request->has('photo')) {
                    $toolPicture = $request->photo;
                
                    // Decode base64 image
                    $image = explode(',', $toolPicture)[1]; // Remove "data:image/jpeg;base64,"
                    $image = base64_decode($image);
                
                    // Generate a unique file name
                    $pic_name = mt_rand(111111, 999999) . date('YmdHms') . '.jpg';
                
                    // Save the file to the desired directory
                    $uploadPath = public_path('uploads/tool_picture_receiving_uploads/');
                    $filePath = $uploadPath . $pic_name;
                
                    // Ensure directory exists
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                
                    file_put_contents($filePath, $image);
                
                    $uploads = Uploads::create([
                        'name' => $pic_name,
                        'original_name' => $pic_name,
                        'extension' => 'jpg',
                    ]);
                
                    ToolPictureReceivingUploads::create([
                        'request_item_id' => $scannedTools->id, 
                        'tool_id' => $scannedTools->tool_id,
                        'upload_id' => $uploads->id,
                        'user_id' => Auth::id(),
                        'tr_type' => 'rfteis',
                    ]);
                }
                


                // return $scannedTools;

                $scannedTools->item_status = 1;
                ///patalandaan na goods na ang row na to
                $scannedTools->clear = 1;

                $scannedTools->update();

                $tr = TransferRequest::where('status', 1)->where('id', $scannedTools->transfer_request_id)->first();
                $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();


                $tools = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->first();

                $tools->wh_ps = 'ps';
                $tools->current_pe = $scannedTools->pe;
                $tools->current_site_id = $project_site->id;
                $tools->transfer_state = 0;

                $tools->update();


                ToolsAndEquipmentLogs::create([
                    'tool_id' => $scannedTools->tool_id,
                    'pe' => Auth::id(),
                    'tr_type' => $request->type,
                    'remarks' => 'galing sa warehouse',
                ]);


                /// for logs
                RfteisLogs::create([
                    'page' => 'request_for_receiving',
                    'request_number' => $scannedTools->teis_number,
                    'title' => 'Received Tool',
                    'message' => Auth::user()->fullname .' '. 'received ' . $tools->item_description,
                    'action' => 6,
                    'approver_name' => Auth::user()->fullname,
                ]);


                /// for logs
                RfteisLogs::create([
                    'page' => 'request_for_receiving',
                    'request_number' => $scannedTools->teis_number,
                    'title' => 'Tool Picture (Receiving Proof)',
                    'message' => Auth::user()->fullname .' '. 'uploaded a picture of '.$tools->item_description .'.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/tool_picture_receiving_uploads') . '/' .
                    $pic_name . '">
                        <span>View</span>
                        </a>',
                    'action' => 10,
                    'approver_name' => Auth::user()->fullname,
                ]);

            }

            $tri = TransferRequestItems::where('status', 1)
                ->whereNull('is_remove')
                ->where('teis_number', $scannedTools->teis_number)
                ->get();

            $clear = collect($tri)->pluck('clear')->toArray();

            $is_all_not_clear = in_array(null, $clear);
            ///kapag lahat ng item_status is 1 na ibigsabhin lahat ay served items
            // $allStatus = in_array(0, $item_status) || in_array(2, $item_status);
            // ///kapag may 2 ibigsabihin may not serve na item and partial ang magiging status nya
            // $is_have_not_served = in_array(2, $item_status);

            if($is_all_not_clear){
                $tool_requests = TransferRequest::find($tri[0]->transfer_request_id);

                $tool_requests->progress = 'partial';
                $tool_requests->update();
            }else{
                $tool_requests = TransferRequest::find($tri[0]->transfer_request_id);

                $tool_requests->progress = 'completed';
                $tool_requests->update();
            }

           

        }




    }

    public function teis_not_received(Request $request){
        if ($request->type == 'rttte') {
            if ($request->multi) {

                $pstriIds = json_decode($request->triIdArray);

                foreach ($pstriIds as $pstri_id) {

                    $scannedTools = PsTransferRequestItems::find($pstri_id);


                    $scannedTools->item_status = 2;

                    $scannedTools->update();

                }
            } else {
                $scannedTools = PsTransferRequestItems::find($request->id);

                $scannedTools->item_status = 2;

                $scannedTools->update();

            }

            $tri = PsTransferRequestItems::where('status', 1)
                ->where('request_number', $scannedTools->request_number)
                ->get();

            $item_status = collect($tri)->pluck('item_status')->toArray();



            $allStatus = in_array(0, $item_status) || in_array(2, $item_status);
            $is_have_not_served = in_array(2, $item_status);

            if($is_have_not_served){
                $tool_requests = PsTransferRequests::find($tri[0]->ps_transfer_request_id);

                $tool_requests->progress = 'partial';
                $tool_requests->update();
            }elseif(!$allStatus){
                $tool_requests = PsTransferRequests::find($tri[0]->ps_transfer_request_id);

                $tool_requests->progress = 'completed';
                $tool_requests->update();
            }

        } else {

            if ($request->multi) {

                $triIds = json_decode($request->triIdArray);


                foreach ($triIds as $tri_id) {

                    $scannedTools = TransferRequestItems::find($tri_id);

                    $scannedTools->item_status = 2;

                    $scannedTools->update();

                }

            } else {
                $scannedTools = TransferRequestItems::find($request->id);

                $scannedTools->item_status = 2;

                $scannedTools->update();


                /// for logs

                $tool_name = ToolsAndEquipment::where('status', 1)->where('id', $scannedTools->tool_id)->value('item_description');

                RfteisLogs::create([
                    'page' => 'request_for_receiving',
                    'request_number' => $scannedTools->teis_number,
                    'title' => 'Not Served',
                    'message' => Auth::user()->fullname .' '. 'not received the ' . $tool_name,
                    'action' => 7,
                    'approver_name' => Auth::user()->fullname,
                ]);

            }

            $tri = TransferRequestItems::where('status', 1)
                ->whereNull('is_remove')
                ->where('teis_number', $scannedTools->teis_number)
                ->get();

            $item_status = collect($tri)->pluck('item_status')->toArray();


            $allStatus = in_array(0, $item_status) || in_array(2, $item_status);
            $is_have_not_served = in_array(2, $item_status);

            if($is_have_not_served){
                $tool_requests = TransferRequest::find($tri[0]->transfer_request_id);

                $tool_requests->progress = 'partial';
                $tool_requests->update();
            }elseif(!$allStatus){
                $tool_requests = TransferRequest::find($tri[0]->transfer_request_id);

                $tool_requests->progress = 'completed';
                $tool_requests->update();
            }

        }
    }


    public function fetch_daf_approver()
    {

        $series = 1;

        $approver = RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('series', $series)
            ->where('request_type', 4)
            ->first();



        if ($approver->sequence == 1) {
            // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
            $tool_approvers = RequestApprover::leftjoin('dafs', 'dafs.id', 'request_approvers.request_id')
                ->leftjoin('transfer_requests', 'transfer_requests.teis_number', 'dafs.daf_number')
                ->select('dafs.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'transfer_requests.subcon', 'transfer_requests.customer_name', 'transfer_requests.project_name', 'transfer_requests.project_code', 'transfer_requests.project_address')
                ->where('dafs.status', 1)
                ->where('transfer_requests.status', 1)
                ->where('request_approvers.status', 1)
                ->where('request_approvers.approver_id', Auth::user()->id)
                ->where('series', $series)
                ->where('approver_status', 0)
                ->where('request_type', 4)
                ->get();

        } else {

            $prev_sequence = $approver->sequence - 1;

            $prev_approver = RequestApprover::where('status', 1)
                ->where('request_id', $approver->request_id)
                ->where('sequence', $prev_sequence)
                ->where('series', $series)
                ->where('request_type', 4)
                ->first();


            if ($prev_approver->approver_status == 1) {
                $tool_approvers = RequestApprover::leftjoin('dafs', 'dafs.id', 'request_approvers.request_id')
                    ->leftjoin('transfer_requests', 'transfer_requests.teis_number', 'dafs.daf_number')
                    ->select('dafs.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'transfer_requests.subcon', 'transfer_requests.customer_name', 'transfer_requests.project_name', 'transfer_requests.project_code', 'transfer_requests.project_address')
                    ->where('dafs.status', 1)
                    ->where('transfer_requests.status', 1)
                    ->where('request_approvers.status', 1)
                    ->where('request_approvers.approver_id', Auth::user()->id)
                    ->where('series', $series)
                    ->where('approver_status', 0)
                    ->where('request_type', 4)
                    ->get();
            } else {
                $tool_approvers = [];
            }
        }


        return DataTables::of($tool_approvers)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->daf_number . '" data-bs-toggle="modal" data-bs-target="#psOngoingTeisRequestModal" class="teisNumber btn text-info fs-6 d-block">View</button>';
            })

            // ->addColumn('request_status', function($row){

            //     return $request_status = '<span class="badge bg-warning">'.$row->request_status.'</span>';
            // })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $price = [];

                $daf_tools = DafItems::where('status', 1)->where('daf_id', $row->id)->get();
                foreach ($daf_tools as $tools) {

                    array_push($price, $tools->price);
                }

                if (Auth::user()->dept_id !== 1) {
                    $price = [''];
                }

                $has_null = in_array(null, $price, true);

                $has_price = $has_null ? 'disabled' : '';

                $action = '<div class="d-flex gap-1"><button type="button" ' . $has_price . ' data-requestid="' . $row->request_id . '"  data-series="' . $row->series . '" data-id="' . $row->approver_id . '" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check"></i></button>
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
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->id)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <img class="border border-1 border-primary" src="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '" width="30">
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->rawColumns(['view_tools', 'uploads', 'action'])
            ->toJson();
    }



    public function daf_approve_tools(Request $request)
    {

        $mail_Items = [];

        $tobeApproveTools = RequestApprover::where('status', 1)
            ->where('request_id', $request->requestId)
            ->where('series', $request->series)
            ->orderBy('sequence', 'desc')
            ->first();


        $tools = RequestApprover::find($request->id);


        $tools->approver_status = 1;
        $tools->date_approved = Carbon::now();

        $tools->update();

        if ($tools->sequence == $tobeApproveTools->sequence) {
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

            // Mail::to($user->email)->send(new EmailRequestor($mail_data));


        }
    }



    public function daf_table_modal(Request $request)
    {

        $tools = DafItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'daf_items.tool_id')
            ->select('tools_and_equipment.*', 'daf_items.id as pstri_id', 'daf_items.price')
            ->where('daf_items.status', 1)
            ->where('daf_items.daf_number', $request->id)
            ->get();


        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get(); lagay ka barcode to receive btn



        return DataTables::of($tools)

            ->addColumn('action', function ($row) {
                if ($user_type = 6) {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                } else {
                    $action = '
                <button data-bs-toggle="modal" data-bs-target="#" type="button" class="receiveBtn btn btn-sm btn-alt-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-file-circle-check"></i></button>
                ';

                }
                return $action;
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

            ->addColumn('add_price', function ($row) {

                $is_have_value = $row->price ? 'disabled' : '';
                $is_accounting = Auth::user()->dept_id != '1' ? 'disabled' : '';

                $add_price = '<input class="form-control price" 
                value="' . $row->price . '" data-id="' . $row->pstri_id . '" ' . $is_have_value . ' ' . $is_accounting . ' style="width: 100px;" type="number" name="price" min="1">';
                return $add_price;
            })

            ->rawColumns(['tools_status', 'action', 'add_price'])
            ->toJson();
    }


    public function add_price_acc_daf(Request $request)
    {

        $price_datas = json_decode($request->priceDatas);

        foreach ($price_datas as $data) {
            $daf_itams = DafItems::where('status', 1)->where('id', $data->id)->first();

            $daf_itams->price = $data->price;

            $daf_itams->update();
        }

    }


    public function fetch_site_tools()
    {
        if (Auth::user()->user_type_id == 4) {
            $tool_approvers = PsTransferRequests::leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                ->select('ps_transfer_requests.user_id', 'ps_transfer_requests.id', 'users.fullname', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('ps_transfer_requests.status', 1)
                ->where('users.status', 1)
                ->where('request_status', 'pending')
                ->where('current_pe', Auth::user()->id);
        } else {

            $approvers = RequestApprover::where('status', 1)
                ->where('approver_id', Auth::user()->id)
                ->where('approver_status', 0)
                ->where('request_type', 2)
                ->get();

                // return $approvers;

            $tool_approvers = collect();

            foreach ($approvers as $approver) {
                $ps_request_tools = collect();

                if ($approver->sequence == 1) {
                    $ps_request_tools = PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
                        ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                        ->select('ps_transfer_requests.user_id', 'ps_transfer_requests.id', 'users.fullname', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
                        ->where('ps_transfer_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('request_approvers.approver_id', Auth::user()->id)
                        ->where('progress', 'ongoing')
                        ->where('approver_status', 0)
                        ->where('request_type', 2)
                        ->where('sequence', 1)
                        ->where('request_approvers.id', $approver->id)
                        ->get();
                } else {
                    $prev_sequence = $approver->sequence - 1;

                    $prev_approver = RequestApprover::where('status', 1)
                        ->where('request_id', $approver->request_id)
                        ->where('sequence', $prev_sequence)
                        ->where('request_type', 2)
                        ->first();

                        // return $prev_approver->approver_status;


                    if ($prev_approver->approver_status == 1) {
                        $ps_request_tools = PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
                            ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                            ->select('ps_transfer_requests.user_id', 'ps_transfer_requests.id','users.fullname', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
                            ->where('ps_transfer_requests.status', 1)
                            ->where('request_approvers.status', 1)
                            ->where('request_approvers.approver_id', Auth::user()->id)
                            ->where('progress', 'ongoing')
                            ->where('approver_status', 0)
                            ->where('request_type', 2)
                            ->where('sequence', $approver->sequence)
                            ->where('request_approvers.id', $approver->id)
                            ->get();
                    } else {
                        $ps_request_tools = [];
                    }
                }

                // Merge the current approvers to the tool_approvers array
                $tool_approvers = $tool_approvers->merge($ps_request_tools)->unique('request_id');
            }

        }


        return DataTables::of($tool_approvers)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-pstrid="' . $row->id . '" data-pe="' . $row->user_id . '" data-id="' . $row->request_number . '" data-transfertype="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
            })

            ->addColumn('request_status', function ($row) {

                if($row->progress === 'completed'){
                    return '<span class="badge bg-success">' . $row->progress . '</span>';
                }elseif($row->progress === 'partial'){
                    return '<span class="badge bg-elegance">' . $row->progress . '</span>';
                }else{
                    return '<span class="badge bg-warning">' . $row->progress . '</span>';
                }
            })

            ->addColumn('request_type', function ($row) {

                return strtoupper($row->tr_type);
            })

            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';

                if ($user_type !== 4) {
                    $action = '<div class="d-flex gap-1"><button data-requestid="' . $row->request_id . '" data-id="' . $row->request_approver_id . '" data-series="' . $row->series . '" type="button" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-check"></i></button>
                </div>
                ';
                }else{
                    $action = '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#trackRequestModal" data-trtype="' . $row->tr_type . '" data-requestnumber="' . $row->request_number . '" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                    </div>
                    ';
                }

                return $action;
            })
            ->addColumn('uploads', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->id)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                    <img class="border border-1 border-primary" src="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '" width="30">
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->rawColumns(['view_tools', 'request_status', 'request_type', 'uploads', 'action'])
            ->toJson();
    }


    public function ps_approve_tools(Request $request)
    {

        $mail_Items = [];

        $tobeApproveTools = RequestApprover::where('status', 1)
            ->where('request_id', $request->requestId)
            ->where('request_type', 2)
            // ->where('series', $request->series)
            ->orderBy('sequence', 'desc')
            ->first();


        $tools = RequestApprover::find($request->id);

        $tools->approver_status = 1;
        $tools->approved_by = Auth::user()->id;
        $tools->date_approved = Carbon::now();

        $tools->update();

        if ($tools->sequence == $tobeApproveTools->sequence) {
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

            // Mail::to($user->email)->send(new EmailRequestor($mail_data));


        }

        if($tools->sequence == 1){
            $sequence = 'First approver ';
         }elseif($tools->sequence == 2){
             $sequence = 'Second approver ';
         }elseif($tools->sequence == 3){
             $sequence = 'Third approver ';
         }
         elseif($tools->sequence == 4){
             $sequence = 'Fourth approver ';
         }elseif($tools->sequence == 5){
             $sequence = 'Fifth approver ';
         }else{
             $sequence = '';
         }

        /// for logs
        RttteLogs::create([
            'approver_name' => Auth::user()->fullname,
            'page' => 'site_to_site_transfer',
            'request_number' => $request->number,
            'title' => 'Approve Request',
            'message' => $sequence . Auth::user()->fullname .' '. 'approved the request.',
            'action' => 3,
        ]);

    }

    public function ps_approve_rttte(Request $request)
    {
        $ps_tools = PsTransferRequests::where('status', 1)->where('request_number', $request->requestNum)->first();

        $ps_tools->acc = Carbon::now();

        $ps_tools->update();

    }

    public function tools_deliver(Request $request)
    {
        if ($request->type == 'rfteis') {
            $teis_tools = TransferRequest::where('status', 1)->where('teis_number', $request->requestNum)->first();

            $teis_tools->is_deliver = Carbon::now();

            $teis_tools->update();

            /// for logs
            RfteisLogs::create([
                'page' => 'rftte',
                'request_number' => $request->requestNum,
                'title' => 'Deliver',
                'message' => 'Request tools is out for delivery!',
                'action' => 5,
                'approver_name' => Auth::user()->fullname,
            ]);

        } else if ($request->type == 'pullout') {
            $pullout_tools = PulloutRequest::where('status', 1)->where('pullout_number', $request->requestNum)->first();

            $pullout_tools->is_deliver = Carbon::now();

            $pullout_tools->update();
        } else {
            $tool_request = PsTransferRequests::where('status', 1)->where('request_number', $request->requestNum)->first();

            $tool_request->is_deliver = Carbon::now();

            $tool_request->update();

            /// for logs
            RttteLogs::create([
                'page' => 'rftte',
                'request_number' => $request->requestNum,
                'title' => 'Deliver',
                'message' => 'Request tools is out for delivery!',
                'action' => 6,
                'approver_name' => Auth::user()->fullname,
            ]);
        }
    }

    public function track_request(Request $request)
    {
        if ($request->trType == 'rfteis') {

            $tool_requests = RfteisLogs::where('status', 1)->where('request_number', $request->requestNumber)->get();

            // return $tool_requests;

            $html = '';
            if(count($tool_requests) > 0){
               foreach ($tool_requests as $tool_request) {

                if($tool_request->action == 1){
                    $icon = 'fa-file-pen bg-primary';
                }elseif($tool_request->action == 2){
                    $icon = 'fa-xmark bg-pulse';
                }elseif($tool_request->action == 3){
                    $icon = 'fa-check bg-earth';
                }elseif($tool_request->action == 4){
                    $icon = 'fa-upload bg-elegance';
                }elseif($tool_request->action == 5){
                    $icon = 'fa-truck-fast bg-info';
                }elseif($tool_request->action == 6){
                    $icon = 'fa-file-circle-check bg-corporate';
                }elseif($tool_request->action == 7){
                    $icon = 'fa-file-circle-xmark bg-danger';
                }elseif($tool_request->action == 8){
                    $icon = 'fa-road-circle-check bg-corporate';
                }elseif($tool_request->action == 9){
                    $icon = 'fa-road-circle-xmark bg-danger';
                }elseif($tool_request->action == 10){
                    $icon = 'fa-upload bg-primary';
                }elseif($tool_request->action == 11){
                    $icon = 'fa-upload bg-elegance';
                }else{
                    $icon = 'fa-file bg-primary';
                }


                $createdAt = $tool_request->created_at; 
                $timeAgo = Carbon::parse($createdAt)->diffForHumans();

                $html .='
                    <li class="timeline-event">
                        <div class="timeline-event-time">'.$timeAgo.'</div>
                        <i class="timeline-event-icon fa '.$icon.'"></i>
                        <div class="timeline-event-block">
                        <p class="fw-semibold">'.$tool_request->title.'</p>
                        <p>'.$tool_request->message.'</p>
                        </div>
                    </li>
                ';
                } 
            }
            

            return $html;
            
        } elseif ($request->trType == 'pullout') {
    
        } else {
 
        }



        // if ($request->trType == 'rfteis') {
        //     $request_progress = TransferRequest::where('status', 1)->where('teis_number', $request->requestNumber)->where('progress', 'completed')->count();
        //     if ($request_progress) {
        //         return '<li class="active text-center"></li>
        //                 <li class="active text-center"></li>
        //                 <li class="active text-center"></li>
        //                 <li class="active text-center"></li>';
        //     } else {
        //         $requests = TransferRequest::where('status', 1)->where('teis_number', $request->requestNumber)->whereNotNull('is_deliver')->count();
        //         if ($requests) {
        //             return '<li class="active text-center"></li>
        //                     <li class="active text-center"></li>
        //                     <li class="active text-center"></li>
        //                     <li class="text-center"></li>';
        //         } else {
        //             //! short version
        //             // $teis_uploads = TeisUploads::where('status', 1)
        //             // ->where('tr_type', 'rfteis')
        //             // ->pluck('teis_number')
        //             // ->toArray();

        //             $teis_uploads = TeisUploads::where('status', 1)
        //                 ->where('tr_type', 'rfteis')->get();

        //             $teis_numbers = collect($teis_uploads)->pluck('teis_number')->toArray();
        //             $have_teis = in_array($request->requestNumber, $teis_numbers);

        //             if ($have_teis) {
        //                 return '<li class="active text-center"></li>
        //                         <li class="active text-center"></li>
        //                         <li class=" text-center"></li>
        //                         <li class=" text-center"></li>';
        //             } else {
        //                 $requests = TransferRequest::where('status', 1)->where('teis_number', $request->requestNumber)->where('request_status', 'approved')->count();
        //                 if ($requests) {
        //                     return '<li class="active text-center"></li>
        //                             <li class="text-center"></li>
        //                             <li class="text-center"></li>
        //                             <li class="text-center"></li>';
        //                 }else{
        //                     return '<li class="text-center"></li>
        //                             <li class="text-center"></li>
        //                             <li class="text-center"></li>
        //                             <li class="text-center"></li>';
        //                 }
        //             }

        //         }
        //     }
        // } elseif ($request->trType == 'pullout') {
        //     $pullout_progress = PulloutRequest::where('status', 1)->where('pullout_number', $request->requestNumber)->where('progress', 'completed')->count();
        //     if ($pullout_progress) {
        //         return '<li class="active text-center"></li>
        //                 <li class="active text-center"></li>
        //                 <li class="active text-center"></li>
        //                 <li class="active text-center"></li>';
        //     } else {
        //         $requests = PulloutRequest::where('status', 1)->where('pullout_number', $request->requestNumber)->whereNotNull('is_deliver')->count();
        //         if ($requests) {
        //             return '<li class="active text-center"></li>
        //                     <li class="active text-center"></li>
        //                     <li class="active text-center"></li>
        //                     <li class="text-center"></li>';
        //         } else {
        //             $requests = PulloutRequest::where('status', 1)->where('pullout_number', $request->requestNumber)->whereNotNull('approved_sched_date')->count();
        //             if ($requests) {
        //                 return '<li class="active text-center"></li>
        //                         <li class="active text-center"></li>
        //                         <li class=" text-center"></li>
        //                         <li class=" text-center"></li>';
        //             } else {
        //                 $requests = PulloutRequest::where('status', 1)->where('pullout_number', $request->requestNumber)->where('request_status', 'approved')->count();
        //                 if ($requests) {
        //                     return '<li class="active text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>';
        //                 }else{
        //                     return '<li class="text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>';
        //                 }
                        
        //             }

        //         }
        //     }
        // } else {
        //     // rttte
        //     $requests = PsTransferRequests::where('status', 1)->where('request_number', $request->requestNumber)->where('progress', 'completed')->count();
        //     if ($requests) {
        //         return '<li class="active text-center"></li>
        //                 <li class="active text-center"></li>
        //                 <li class="active text-center"></li>
        //                 <li class="active text-center"></li>';
        //     } else {
        //         $requests = PsTransferRequests::where('status', 1)->where('request_number', $request->requestNumber)->whereNotNull('is_deliver')->count();
        //         if ($requests) {
        //             return '<li class="active text-center"></li>
        //                     <li class="active text-center"></li>
        //                     <li class="active text-center"></li>
        //                     <li class="text-center"></li>';
        //         } else {

        //             $teis_uploads = TeisUploads::where('status', 1)
        //                 ->where('tr_type', 'rttte')->get();

        //             $teis_numbers = collect($teis_uploads)->pluck('teis_number')->toArray();
        //             $have_teis = in_array($request->requestNumber, $teis_numbers);


        //             $ters_uploads = TersUploads::where('status', 1)
        //                 ->where('tr_type', 'rttte')->get();

        //             $ters_numbers = collect($ters_uploads)->pluck('pullout_number')->toArray();
        //             $have_ters = in_array($request->requestNumber, $ters_numbers);
        //             // $have_ters2 = in_array($row->teis_number, $ters_numbers) ? '' : 'disabled';

        //             // $action =  '<div class="d-flex gap-2">
        //             // <button '.$have_teis.' data-type="'.$row->tr_type.'" data-num="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Upload TEIS" data-bs-original-title="Upload TEIS"><i class="fa fa-upload me-1"></i>TEIS</button>
        //             // <button '.$have_ters.' data-num="'.$row->teis_number.'" data-type="'.$row->tr_type.'" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled d-flex align-items-center" data-bs-toggle="tooltip" aria-label="Upload TERS" data-bs-original-title="Upload TERS"><i class="fa fa-upload me-1"></i>TERS</button>
        //             // <button '.$have_ters2.' data-num="'.$row->teis_number.'" data-type="'.$row->tr_type.'" type="button" class="proceedBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Done" data-bs-original-title="Done"><i class="fa fa-check"></i></button>
        //             // </div>';


        //             if ($have_teis && $have_ters) {
        //                 return '<li class="active text-center"></li>
        //                         <li class="active text-center"></li>
        //                         <li class=" text-center"></li>
        //                         <li class=" text-center"></li>';
        //             } else {
        //                 $requests = PsTransferRequests::where('status', 1)->where('request_number', $request->requestNumber)->where('request_status', 'approved')->count();
        //                 if ($requests) {
        //                     return '<li class="active text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>';
        //                 }else{
        //                     return '<li class=" text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>
        //                             <li class=" text-center"></li>';
        //                 }
        //             }


        //         }
        //     }
        // }
    }

    public function completed_sts_request()
    {
        $ps_tools = PsTransferRequests::where('status', 1)
            ->where('progress', 'completed')
            ->get();


        return DataTables::of($ps_tools)

            ->addColumn('view_tools', function ($row) {

                return '<button data-id="' . $row->request_number . '" data-type="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';
            })
            ->addColumn('teis', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->request_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                <span>TEIS.pdf</span>
                </a>
            </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->request_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                $item['uploads']['name'] . '">
                <span>TERS.pdf</span>
                </a>
            </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })

            ->rawColumns(['view_tools', 'teis', 'ters'])
            ->toJson();
    }

    public function sts_request_approved()
    {
        $tool_approvers = RequestApprover::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'request_approvers.request_id')
            ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
            ->select('ps_transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved', 'users.fullname')
            ->where('ps_transfer_requests.status', 1)
            ->where('request_approvers.status', 1)
            ->where('request_approvers.approved_by', Auth::user()->id)
            ->where('approver_status', 1)
            ->where('request_type', 2)
            ->get();


        return DataTables::of($tool_approvers)

            ->addColumn('view_tools', function ($row) {

                return '<button data-pe="'.$row->user_id.'" data-pstrid="'.$row->id.'" data-id="' . $row->request_number . '" data-type="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-info fs-6 d-block">View</button>';
            })

            ->addColumn('request_status', function ($row) {

                if($row->progress === 'completed'){
                    return '<span class="badge bg-success">' . $row->progress . '</span>';
                }elseif($row->progress === 'partial'){
                    return '<span class="badge bg-elegance">' . $row->progress . '</span>';
                }else{
                    return '<span class="badge bg-warning">' . $row->progress . '</span>';
                }
            })

            ->addColumn('teis', function ($row) {
                $teis_uploads = TeisUploads::with('uploads')->where('status', 1)->where('teis_number', $row->request_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($teis_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $item['uploads']['name'] . '">
                <span>TEIS.pdf</span>
                </a>
            </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->request_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' .
                $item['uploads']['name'] . '">
                <span>TERS.pdf</span>
                </a>
            </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })

            ->rawColumns(['view_tools', 'teis', 'ters', 'request_status'])
            ->toJson();
    }


    public function fetch_teis_request_acc()
    {

        $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->where('for_pricing', 1)->get();

        return DataTables::of($request_tools)

            ->addColumn('view_tools', function ($row) {

                return '<button data-id="' . $row->teis_number . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';
            })
            ->addColumn('action', function ($row) {
                $user_type = Auth::user()->user_type_id;

                $price = [];

                $tools = TransferRequestItems::where('status', 1)->where('transfer_request_id', $row->id)->get();
                foreach ($tools as $tool) {

                    array_push($price, $tool->price);
                }

                // if (Auth::user()->dept_id !== 1) {
                //     $price = [''];
                // }
    
                $has_null = in_array(null, $price, true);

                $has_price = $has_null ? 'disabled' : '';


                if ($user_type == 7) {
                    $action = '<button data-requestnum="' . $row->teis_number . '" ' . $has_price . ' type="button" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-check"></i></button>';
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

    public function rfteis_acc_proceed(Request $request)
    {
        TransferRequest::where('status', 1)->where('teis_number', $request->requestNum)->update([
            'for_pricing' => '2'
        ]);
    }


    /// Projects Assignment--------------------------------------------------------------------------
    public function project_tagging(Request $request)
    {
        $users = User::leftJoin('companies', 'companies.id', 'users.comp_id')
        ->leftJoin('positions', 'positions.id', 'users.pos_id')
        ->select('users.emp_id', 'users.fullname', 'positions.position', 'companies.code', 'users.id')
        ->where('users.status', 1)
        ->where('positions.status', 1)
        ->where('companies.status', 1)
        ->orderBy('user_type_id', 'asc')
        ->whereIn('users.user_type_id', [3, 4])
        ->get();

        $project_sites = ProjectSites::where('status', 1)
        ->select('id', 'project_name')
        ->where('area', Auth::user()->area)->get();


        return view('/pages/project_assignment', compact('users', 'project_sites'));
    }

    public function fetch_assigned_personnel(Request $request)
    {
            $projects = AssignedProjects::leftJoin('users', 'assigned_projects.emp_id', 'users.emp_id')
            ->leftJoin('companies', 'companies.id', 'users.comp_id')
            ->leftJoin('positions', 'positions.id', 'users.pos_id')
            ->select('assigned_projects.id as ap_id', 'users.emp_id', 'users.fullname', 'positions.position', 'companies.code', 'users.id')
            ->where('users.status', 1)
            ->where('positions.status', 1)
            ->where('companies.status', 1)
            ->where('assigned_projects.status', 1)
            ->where('assigned_projects.project_id', $request->projectsiteId)
            ->orderBy('user_type_id', 'asc')
            ->get();


        return DataTables::of($projects)

            ->addColumn('action', function ($row) {

                return '<button type="button" data-id="'. $row->ap_id .'" class="deletePersonnel d-flex align-items-center btn-sm btn btn-sm btn-danger"><i class="fa fa-xmark"></i></button>';

                // return '<button type="button" data-id="'. $row->sa_id .'" data-fn="'. $row->fullname .'" data-comp="'. $row->code .'" data-pos="'. $row->position .'" data-triggerby="edit" class="editApprover btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#approverSetupModal"><i class="fa fa-pen"></i></button>
                // <button type="button" data-id="'. $row->ap_id .'" class="deleteApprover btn btn-sm btn-danger"><i class="fa fa-xmark"></i></button>';

            })

            ->rawColumns(['action'])
            ->toJson();

    }


    public function assign_personnel(Request $request)
    {
        
        $personnels = json_decode($request->arrPersonnel);


        if ($request->hiddenTriggerBy == 'edit') {
            // $updateApprover = SetupApprover::where('status', 1)
            //     ->where('id', $request->hiddenId)
            //     ->first();

            // $updateApprover->user_id = $approvers;
            // $updateApprover->update();
        } else {
            // $fetch_approver_count = SetupApprover::where('status', 1)
            //     ->where('company_id', $request->selectedComp)
            //     ->where('request_type', $request->selectedRT)
            //     ->orderBy('sequence', 'desc')
            //     ->count();

            // $sequence = 1;

            // if ($fetch_approver_count) {
            //     $sequence = $fetch_approver_count + 1;
            // }


            foreach ($personnels as $personnel) {
                $data = User::join('positions', 'positions.id', 'users.pos_id')
                ->select('positions.code', 'users.id')
                ->where('users.status', 1)
                ->where('positions.status', 1)
                ->where('emp_id', $personnel)
                ->first();

                AssignedProjects::create([
                    'project_id' => $request->selectedProjectSite,
                    'emp_id' => $personnel,
                    'user_id' => $data->id,
                    'assigned_by' => Auth::id(),
                    'pos' => $data->code

                ]);
            }
        }




    }

    public function delete_personnel(Request $request)
    {
        AssignedProjects::find($request->personnelId)->update(
            ['status' => 0]
        );

    }


    public function remove_tool(Request $request)
    {

        $tool = TransferRequestItems::find($request->triId);

        $tool->is_remove = 1;
        $tool->remove_by = Auth::id();

        $tool->update();

        $tool_name = ToolsAndEquipment::where('status', 1)->where('id', $tool->tool_id)->value('item_description');

        /// for logs
        RfteisLogs::create([
            'approver_name' => Auth::user()->fullname,
            'page' => 'rfteis',
            'request_number' => $request->number,
            'title' => 'Remove Tool',
            'message' => Auth::user()->fullname .' '. 'removed ' . $tool_name,
            'action' => 2,
        ]);

    }


    public function redelivery_status(Request $request)
    {
        
        $tool = TransferRequestItems::find($request->triId);

        if($request->trigger == 'yes'){
            $tool->transfer_state = 1;
            $tool->item_status = 0;
        }else{
            $tool->transfer_state = 2;

            PeLogs::where('status', 1)->where('request_number', $tool->teis_number)->where('tool_id', $tool->tool_id)->where('pe', $tool->pe)->first()->update([
                'status' => 0
            ]);

        }
        $tool->update();


        
        // / for logs
        $tool_name = ToolsAndEquipment::where('status', 1)->where('id', $tool->tool_id)->value('item_description');
        
        if($request->trigger == 'yes'){
            $title = 'Redeliver';
            $message = $tool_name . ' is possible for Redeliver. Tagged by ' . Auth::user()->fullname;
            $action = 8;
        }else{
            $title = 'Redeliver Unavailable';
            $message = $tool_name . ' is not possible for Redeliver, Creating TERS. Tagged by ' . Auth::user()->fullname;
            $action = 9;
        }

        RfteisLogs::create([
            'approver_name' => Auth::user()->fullname,
            'page' => 'not_serve_items',
            'request_number' => $request->requestNumber,
            'title' => $title,
            'message' => $message,
            'action' => $action,
        ]);

    }


    // para sa report log ng pe
    public function report_pe_logs(Request $request)
    {
        $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
            ->select('tools_and_equipment.po_number', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks')
            ->where('tools_and_equipment.status', 1)
            ->where('pe_logs.status', 1)
            ->where('pe_logs.pe', Auth::user()->id)
            ->get();


        return DataTables::of($pe_logs)


            ->addColumn('teis', function ($row) {
                if($row->teis_upload_id){
                    $teis_uploads = Uploads::where('status', 1)->where('id', $row->teis_upload_id)->first()->toArray();
    
                    return '<div class="row mx-auto"><div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                        <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $teis_uploads['name'] . '">
                        <span>TEIS.pdf</span>
                        </a>
                    </div></div>';
                }else{
                    return '';
                }

    
            })

            ->addColumn('ters', function ($row) {
                
                if($row->ters_upload_id){
                    $ters_uploads = Uploads::where('status', 1)->where('id', $row->ters_upload_id)->first();
                    return '<div class="row mx-auto"><div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' . $ters_uploads['name'] . '">
                    <span>TEIS.pdf</span>
                    </a>
                </div></div>';
                }else{
                    return '';
                }

                
            })

            ->addColumn('action', function(){

                return'<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
            })

            ->rawColumns(['teis', 'ters', 'action'])
            ->toJson();

    }

}
