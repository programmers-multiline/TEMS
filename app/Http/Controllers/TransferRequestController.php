<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferRequest;
use Yajra\DataTables\DataTables;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;

class TransferRequestController extends Controller
{
    public function ongoing_teis_request(){

        $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
        
        return DataTables::of($request_tools)
        
        ->addColumn('view_tools', function($row){
            
            return $view_tools = '<button data-id="'.$row->teis_number.'" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">'.$row->teis_number.'</button>';;
        })
        ->addColumn('action', function($row){
            $user_type = Auth::user()->user_type_id;

            $action =  '<button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>';

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
        ->rawColumns(['view_tools','action'])
        ->toJson();
    }



    public function ongoing_teis_request_modal(Request $request){

        $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
                                     ->select('tools_and_equipment.*','transfer_request_items.tool_id')
                                     ->where('transfer_request_items.status', 1)
                                     ->where('transfer_request_items.teis_number', $request->id)
                                     ->get();

        // $data = TransferRequestItems::with('tools')->where('teis_number', $request->id)->get();
        
        return DataTables::of($tools)
        
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
        ->rawColumns(['tools_status'])
        ->toJson();
    }
}
