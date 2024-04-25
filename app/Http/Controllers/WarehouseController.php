<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TransferRequest;
use App\Models\ToolsAndEquipment;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function add_tools(Request $request){


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


    public function fetch_tools(Request $request) {

        $action = '';
        $status = '';

        
        $tools = ToolsAndEquipment::where('status', 1)->where('wh_ps', 'wh')->get();
        
        return DataTables::of($tools)
        
        
        // ->addColumn('box', function($row){
        //     return $box = '
        //       <div class="form-check">
        //         <input class="form-check-input" style="margin-left: -10px" type="checkbox">
        //         <label class="form-check-label" for="row_1"></label>
        //         </div>
        //       ';
        // })
        
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


    public function edit_tools(Request $request){

        
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


    public function delete_tools(Request $request){

        $deleteTools = ToolsAndEquipment::find($request->id);

        $deleteTools->status = 0;
        $deleteTools->update();

    }


    public function request_tools(Request $request){

        $prev_tn = TransferRequest::where('status', 1)->orderBy('teis_number', 'desc')->first();
        

        $new_teis_number = '';
        if(!$prev_tn){
            $new_teis_number = 1;
        }else{
            $new_teis_number = $prev_tn->teis_number + 1;
        }

        TransferRequest::create([
            'teis_number' => $new_teis_number,
            'pe' => Auth::user()->id,
            'subcon' => $request->subcon,
            'customer_name' => $request->customerName,
            'project_name' => $request->projectName,
            'project_code' => $request->projectCode,
            'project_address' => $request->projectAddress,
            'date_requested' => Carbon::now(),
            'status' => 1,
        ]);

        $last_id = TransferRequest::orderBy('id', 'desc')->first();


        $array_id = json_decode($request->idArray);

        $array_count = count($array_id);

        for ($i=0; $i < $array_count; $i++) { 
            TransferRequestItems::create([
                'tool_id' => $array_id[$i],
                'teis_number' => $new_teis_number,
                'transfer_request_id' => $last_id->id,
                'pe' => Auth::user()->id,
                'status' => 1,
            ]);
        }

    }



 
}
