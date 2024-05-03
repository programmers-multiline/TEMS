<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PullOutController extends Controller
{
    public function fetch_ongoing_pullout(){
        $action = '';
        $status = '';

    
    $pullout_ongoing = ToolsAndEquipment::where('status', 1)->where('wh_ps', 'wh')->get();
    
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
    ->addColumn('action', function($row){
        $user_type = Auth::user()->user_type_id;
        if($user_type == 1){
            $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
            <i class="fa fa-pencil-alt"></i>
          </button>
          <button type="button" id="deleteToolsBtn" data-id="'.$row->id.'" class="btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
            <i class="fa fa-times"></i>
          </button>';
        }else if($user_type == 2){
            $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
            <i class="fa fa-pencil-alt"></i></button>';
        }else if($user_type == 3 || $user_type == 4){
            $action =  '<button data-bs-toggle="modal" data-bs-target="#modalRequestWarehouse" type="button" id="requestWhBtn" class="btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
            <i class="fa fa-file-pen"></i></button>';
        }
        return $action;
    })
    ->rawColumns(['tools_status','action'])
    ->toJson();
    }
}

