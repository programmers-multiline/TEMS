<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Daf;
use App\Models\DafItems;
use App\Models\PmGroupings;
use App\Models\TeisUploads;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Models\PsTransferRequests;
use App\Models\PulloutRequestItems;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use App\Models\PsTransferRequestItems;

class ProjectSiteController extends Controller
{

    public function view_project_site(){

        $pg = PmGroupings::leftjoin('assigned_projects', 'assigned_projects.pm_group_id', 'pm_groupings.id')
        ->leftjoin('project_sites', 'project_sites.id','assigned_projects.project_id')
        ->select('project_sites.customer_name','project_sites.project_name','project_sites.project_code','project_sites.project_address')
        ->where('assigned_projects.status', 1)
        ->where('pm_groupings.status', 1)
        ->where('project_sites.status', 1)
        ->where('pm_groupings.pe_code', Auth::user()->emp_id)
        ->orwhere('pm_groupings.pm_code', Auth::user()->emp_id)
        ->get();

        return view('/pages/project_site', compact('pg'));
    }


    public function fetch_tools_ps(Request $request) {

        $action = '';
        $status = '';

        $id = [];

        $tool_id = TransferRequestItems::leftjoin('transfer_requests', 'transfer_requests.id', 'transfer_request_items.transfer_request_id')
        ->select('transfer_request_items.tool_id')
        ->where('transfer_requests.status', 1)
        ->where('transfer_request_items.item_status', 1)
        ->where('transfer_request_items.pe', Auth::user()->id)
        ->get();

        foreach($tool_id as $tool){
            $id[] = $tool->tool_id;
        }


        if($request->warehouseId){
            $tools = ToolsAndEquipment::leftJoin('warehouses','warehouses.id','tools_and_equipment.location')
            ->leftjoin('project_sites','project_sites.id','tools_and_equipment.current_site_id')
            ->select('tools_and_equipment.*', 'warehouses.warehouse_name', 'project_sites.project_location')
            ->where('tools_and_equipment.status', 1)
            ->where('tools_and_equipment.wh_ps', 'ps')
            ->where('location', $request->warehouseId)
            ->whereNotIn('tools_and_equipment.id', $id)
            ->get();
        }else{
            $tools = ToolsAndEquipment::leftJoin('warehouses','warehouses.id','tools_and_equipment.location')
            ->leftjoin('project_sites','project_sites.id','tools_and_equipment.current_site_id')
            ->select('tools_and_equipment.*', 'warehouses.warehouse_name','project_sites.project_location')
            ->where('tools_and_equipment.status', 1)
            ->where('tools_and_equipment.wh_ps', 'ps')
            ->whereNotIn('tools_and_equipment.id', $id)
            ->get();
        }
        
        
        
        return DataTables::of($tools)
            
            ->setRowClass(function ($row) {
                $tool_id = PulloutRequestItems::leftjoin('pullout_requests', 'pullout_requests.id', 'pullout_request_items.pullout_request_id')
                ->select('pullout_request_items.*')
                ->where('pullout_requests.progress', 'ongoing')
                ->where('pullout_requests.status', 1)->get();

                $psTransferRequest = PsTransferRequestItems::leftjoin('ps_transfer_requests', 'ps_transfer_requests.id', 'ps_transfer_request_items.ps_transfer_request_id')
                ->select('ps_transfer_request_items.*')
                ->where('ps_transfer_requests.progress', 'ongoing')
                ->where('ps_transfer_requests.status', 1)
                ->where('ps_transfer_request_items.status', 1)
                ->get();

                $tools = ToolsAndEquipment::where('status', 1)->where('transfer_state', 0)->get();

                $toolIds = [];
        
                $PulloutToolIds = collect($tool_id)->pluck('tool_id')->toArray();
                $PsToolIds = collect($psTransferRequest)->pluck('tool_id')->toArray();
                $tools_id = collect($tools)->pluck('id')->toArray();

                $toolIds = array_merge($PulloutToolIds, $PsToolIds, $tools_id);

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

        ->addColumn('transfer_state', function($row){
            $state = '';
            if($row->transfer_state == 1){
                $state = '<span class="btn btn-sm btn-alt-success" style="font-size: 12px;">Available to transfer</span>';
            }else if($row->transfer_state == 2){
                $state = '<span class="btn btn-sm btn-alt-primary" style="font-size: 12px;">Pullout Ongoing</span>';
            }else{
                $state =  '<span class="btn btn-sm btn-alt-danger" style="font-size: 12px;">Currently Using</span>';
            }
            return $state;
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
            }else if($user_type == 3 || $user_type == 4 || $user_type == 5){
                $action =  '<button data-bs-toggle="modal" data-bs-target="#modalRequestWarehouse" type="button" id="requestWhBtn" class="btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-file-pen"></i></button>';
            }
            return $action;
        })
        ->rawColumns(['transfer_state','tools_status','action'])
        ->toJson();
    }


    public function ps_request_tools(Request $request){

        $prev_rn = PsTransferRequests::where('status', 1)->orderBy('request_number', 'desc')->first();

        

        $new_request_number = '';
        if(!$prev_rn){
            $new_request_number = 1;
        }else{
            $new_request_number = $prev_rn->request_number + 1;
        }

        PsTransferRequests::create([
            'request_number'  => $new_request_number,
            'user_id'         => Auth::user()->id,
            'project_name'    => $request->projectName,
            'project_code'    => $request->projectCode,
            'project_address' => $request->projectAddress,
            'date_requested'  => Carbon::now(),
            'current_pe'      => $request->currentPe,
            'current_site_id' => $request->currentSiteId,
        ]);

        Daf::create([
            'daf_number'     => $new_request_number,
            'user_id'        => Auth::user()->id,
            'date_requested' => Carbon::now(),
            'tr_type'        => 'rttte',
        ]);

        $last_id = PsTransferRequests::where('status', 1)->orderBy('id', 'desc')->first();


        $array_id = json_decode($request->idArray);

        $array_count = count($array_id);

        for ($i=0; $i < $array_count; $i++) { 
            PsTransferRequestItems::create([
                'tool_id'                => $array_id[$i],
                'request_number'         => $new_request_number,
                'ps_transfer_request_id' => $last_id->id,
                'user_id'                => Auth::user()->id,
                'status'                 => 1,
            ]);
        }

        for ($i=0; $i < $array_count; $i++) { 
            DafItems::create([
                'tool_id'    => $array_id[$i],
                'daf_number' => $new_request_number,
                'daf_id'     => $last_id->id,
                'user_id'    => Auth::user()->id,
            ]);
        }

    }


        public function fetch_teis_request_ps(){

            $request_tools = PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->whereNull('acc')->get();
            // if(Auth::user()->user_type_id == 7){
            // }
            // else{
            //     $request_tools = PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->where('request_status', 'approved')->get();
            // }

            
            return DataTables::of($request_tools)
            
            ->addColumn('view_tools', function($row){
                
                return $view_tools = '<button data-id="'.$row->request_number.'" data-bs-toggle="modal" data-bs-target="#psOngoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';;
            })
            ->addColumn('action', function($row){
                $user_type = Auth::user()->user_type_id;

                $price = [];
                
                $ps_tools = PsTransferRequestItems::where('status', 1)->where('ps_transfer_request_id', $row->id)->get();
                foreach ($ps_tools as $tools) {
        
                    array_push($price, $tools->price);
                }

                if( Auth::user()->dept_id !== 1){
                    $price = [''];
                }
        
                $has_null = in_array(null, $price, true);
        
                $has_price = $has_null ? 'disabled' : '';
                

                if($user_type == 2){
                    $action =  '<button data-requestnum="'.$row->request_number.'" data-bs-toggle="modal" data-bs-target="#createTeis" type="button" class="uploadTeisBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-upload me-1"></i>TEIS</button>';
                }else if($user_type == 7){
                    $action =  '<button data-requestnum="'.$row->request_number.'" '.$has_price.' type="button" class="approveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-check"></i></button>';
                }else{
                    $action == '';
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
                $teis_uploads = TeisUploads::with('uploads')->where('teis_number', $row->id)->get()->toArray();
            })
            ->rawColumns(['view_tools','action','uploads'])
            ->toJson();
        }


        public function ps_ongoing_teis_request_modal(Request $request){

            $tools = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
                                         ->leftjoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                                         ->select('tools_and_equipment.*','ps_transfer_request_items.id as pstri_id', 'ps_transfer_request_items.price', 'warehouses.warehouse_name')
                                         ->where('ps_transfer_request_items.status', 1)
                                         ->where('ps_transfer_request_items.request_number', $request->id)
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

            ->addColumn('add_price', function($row){

                $is_have_value = $row->price ? 'disabled' : '';

                $add_price = '<input value="'.$row->price.'" data-id="'.$row->pstri_id.'" style="width: 100px;" type="number" class="price" name="price" min="1">';
                return $add_price;
            })

            ->rawColumns(['tools_status', 'action', 'add_price'])
            ->toJson();
        }

        
        public function add_price_acc(Request $request){
            
            $price_datas = json_decode($request->priceDatas);

            foreach ($price_datas as $data) {
                $pstri = PsTransferRequestItems::where('status', 1)->where('id', $data->id)->first();

                $pstri->price = $data->price;

                $pstri->update();
            }
            
        }
}
