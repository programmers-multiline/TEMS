<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\TransferRequest;
use App\Models\PulloutRequestItems;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;

class MyToolsAndEquipmentController extends Controller
{
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
        if($request->id){

            $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
            ->leftJoin('transfer_requests','transfer_requests.id','transfer_request_items.transfer_request_id')
            ->select('tools_and_equipment.*', 'transfer_request_items.teis_number')
            ->where('transfer_requests.pe', Auth::user()->id)
            ->where('transfer_request_items.status', 1)
            ->where('transfer_requests.progress', 'completed')
            ->where('transfer_requests.project_code', $request->id)
            ->get();
        }else{
            $tools = TransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'transfer_request_items.tool_id')
            ->leftJoin('transfer_requests','transfer_requests.id','transfer_request_items.transfer_request_id')
            ->select('transfer_request_items.teis_number','tools_and_equipment.*')
            ->where('transfer_requests.pe', Auth::user()->id)
            ->where('transfer_request_items.status', 1)
            ->where('transfer_requests.progress', 'completed')
            ->get();
        }

        // return $tools[0]->teis_number;

        
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
            if($user_type == 3){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-pencil-alt"></i>
              </button>
              <button type="button" id="deleteToolsBtn" data-id="'.$row->id.'" class="btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                <i class="fa fa-times"></i>
              </button>';
            }else if($user_type == 4){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#modalEditTools" type="button" id="editBtn" data-id="'.$row->id.'" data-po="'.$row->po_number.'" data-asset="'.$row->asset_code.'" data-serial="'.$row->serial_number.'" data-itemcode="'.$row->item_code.'" data-itemdesc="'.$row->item_description.'" data-brand="'.$row->brand.'" data-location="'.$row->location.'" data-status="'.$row->tools_status.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-pencil-alt"></i></button>';
            }
            return $action;
        })


        ->rawColumns(['tools_status','action'])
        ->toJson();
    }


    public function pullout_request(Request $request){

        $prev_pn = PulloutRequest::where('status', 1)->orderBy('pullout_number', 'desc')->first();
        

        $new_pullout_number = '';
        if(!$prev_pn){
            $new_pullout_number = 1;
        }else{
            $new_pullout_number = $prev_pn->pullout_number + 1;
        }

        PulloutRequest::create([
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



        $last_id = PulloutRequest::orderBy('id', 'desc')->first();


        $array_table_data = $request->tableData;

        // return $array_table_data;

        $array_count = count($array_table_data);

        foreach ($array_table_data as $table_data) {
            PulloutRequestItems::create([
                'pullout_request_id' => $last_id->id,
                'tool_id' => $table_data['id'],
                'pullout_number' => $new_pullout_number,
                'user_id' => Auth::user()->id,
                'tools_status' => $table_data['tools_status'],
            ]);
        }

        // for ($i=0; $i < $array_count; $i++) { 
            
        // }
    }

}
