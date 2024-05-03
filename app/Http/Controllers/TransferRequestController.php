<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferRequest;
use Yajra\DataTables\DataTables;
use App\Models\TransferRequestItems; 
use App\Models\TeisUploads; 
use Illuminate\Support\Facades\Auth;

class TransferRequestController extends Controller
{
    public function ongoing_teis_request(){

        $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
        
        return DataTables::of($request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
        })
        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action =  '<div class="d-flex gap-1"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
            <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-alt-danger d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-barcode"></i></button>
            </div>
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



    public function ongoing_teis_request_modal(Request $request){

        $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                                     ->select('tools_and_equipment.*','transfer_request_items.tool_id')
                                     ->where('transfer_request_items.status', 1)
                                     ->where('transfer_request_items.teis_number', $request->id)
                                     ->get();

        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get(); lagay ka barcode to receive btn

        
        
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
}
