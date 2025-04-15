<?php

namespace App\Http\Controllers;

use App\Models\ActionLogs;
use App\Models\PulloutRequest;
use Carbon\Carbon;
use App\Models\PeLogs;
use App\Models\Uploads;
use App\Models\TeisUploads;
use App\Models\TersUploads;
use Illuminate\Http\Request;
use App\Models\TransferRequest;
use App\Models\AssignedProjects;
use Yajra\DataTables\DataTables;
use App\Models\PsTransferRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\ToolsAndEquipmentLogs;

class ReportsController extends Controller
{
    // para sa report log ng pe
    public function report_pe_logs(Request $request)
    {
        // return $request;
        
        /// under ng PM
        $projectIds = AssignedProjects::where('status', 1)
            ->where('user_id', Auth::id())
            ->where('pos', 'pm')
            ->pluck('project_id');

        // Get PE user IDs associated with those project IDs
        $peUserIds = AssignedProjects::where('status', 1)
            ->whereIn('project_id', $projectIds)
            ->where('pos', 'pe')
            ->pluck('user_id');

        /// kay sir benjo yung mga else
        /// user_type_id == 4 PE
        /// user_type_id == 3 PM
        /// user_type_id == 5 OM
        if($request->toolId){
            if(Auth::user()->user_type_id == 4){
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
               ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number', 'pe_logs.tr_type', 'users.fullname')
               ->where('tools_and_equipment.status', 1)
               ->where('tools_and_equipment.id', $request->toolId)
               ->where('pe_logs.status', 1)
               ->where('pe_logs.pe', Auth::user()->id)
               ->get();
           }elseif(Auth::user()->user_type_id == 3){
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number', 'pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.id', $request->toolId)
                ->where('pe_logs.status', 1)
                ->whereIn('pe_logs.pe', $peUserIds)
                ->get();
            }elseif(Auth::user()->user_type_id == 5){
               $PEs = AssignedProjects::where('status', 1)->where('assigned_by', Auth::id())->pluck('user_id')->toArray();
   
               $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
               ->leftjoin('users', 'users.id', 'pe_logs.pe')
               ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
               ->where('tools_and_equipment.status', 1)
               ->where('tools_and_equipment.id', $request->toolId)
               ->where('pe_logs.status', 1)
               ->whereIn('pe_logs.pe', $PEs)
               ->get();
           }else{
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('tools_and_equipment.id', $request->toolId)
                ->where('pe_logs.status', 1)
                ->whereNull('ters_upload_id')
                ->get();
           }
        }elseif($request->PeId){
            if(Auth::user()->user_type_id == 3){
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number', 'pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('pe_logs.status', 1)
                ->where('pe_logs.pe', $request->PeId)
                ->get();
            }elseif(Auth::user()->user_type_id == 5){
               $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
               ->leftjoin('users', 'users.id', 'pe_logs.pe')
               ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
               ->where('tools_and_equipment.status', 1)
               ->where('pe_logs.status', 1)
               ->where('pe_logs.pe', $request->PeId)
               ->get();
           }else{
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('pe_logs.pe', $request->PeId)
                ->where('pe_logs.status', 1)
                ->whereNull('ters_upload_id')
                ->get();
           }
        }elseif($request->projectSiteId){

                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->leftjoin('transfer_requests', 'pe_logs.request_number', 'transfer_requests.teis_number')
                ->leftjoin('project_sites', 'project_sites.project_code', 'transfer_requests.project_code')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('pe_logs.status', 1)
                ->where('tools_and_equipment.status', 1)
                ->where('transfer_requests.status', 1)
                ->where('project_sites.status', 1)
                ->where('project_sites.progress', 'ongoing')
                ->where('users.status', 1)
                ->where('project_sites.id', $request->projectSiteId)
                ->whereNull('ters_upload_id')
                ->get();
        }else{
            if(Auth::user()->user_type_id == 4){
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
               ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number', 'pe_logs.tr_type', 'users.fullname')
               ->where('tools_and_equipment.status', 1)
               ->where('pe_logs.status', 1)
               ->where('pe_logs.pe', Auth::user()->id)
               ->get();
           }elseif(Auth::user()->user_type_id == 3){
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number', 'pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('pe_logs.status', 1)
                ->whereIn('pe_logs.pe', $peUserIds)
                ->get();
            }elseif(Auth::user()->user_type_id == 5){
               $PEs = AssignedProjects::where('status', 1)->where('assigned_by', Auth::id())->pluck('user_id')->toArray();
   
               $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
               ->leftjoin('users', 'users.id', 'pe_logs.pe')
               ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
               ->where('tools_and_equipment.status', 1)
               ->where('pe_logs.status', 1)
               ->whereIn('pe_logs.pe', $PEs)
               ->get();
           }else{
                $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
                ->leftjoin('users', 'users.id', 'pe_logs.pe')
                ->select('tools_and_equipment.po_number', 'tools_and_equipment.id', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number','pe_logs.tr_type', 'users.fullname')
                ->where('tools_and_equipment.status', 1)
                ->where('pe_logs.status', 1)
                ->whereNull('ters_upload_id')
                ->get();
           }
        }
        
       
        // return $pe_logs;

        return DataTables::of($pe_logs)

        ->addColumn('request_number', function($row){

            return'<button data-id="' . $row->request_number . '" data-transfertype="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary d-block" style="font-size: .80rem;">'.$row->request_number.'</button>';
        })

            ->addColumn('teis', function ($row) {
                if($row->teis_upload_id){
                    $teis_uploads = Uploads::where('status', 1)->where('id', $row->teis_upload_id)->first()->toArray();
                    $teis_num = TeisUploads::where('status', 1)->where('upload_id', $row->teis_upload_id)->value('teis');
    
                    return '<div class="row mx-auto"><div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn pictureContainer">
                        <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $teis_uploads['name'] . '">
                        <span>'.$teis_num.'.pdf</span>
                        </a>
                    </div></div>';
                }else{
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }

    
            })

            ->addColumn('ters', function ($row) {


                if ($row->ters_upload_id) {
                    $upload_ids = explode(',', $row->ters_upload_id); // Convert string to array
                    $ters_uploads = Uploads::where('status', 1)->whereIn('id', $upload_ids)->get();
                
                    if (count($ters_uploads) === 1) {
                        // If only one file, display it directly
                        $upload = $ters_uploads->first();
                        $teis_num = TersUploads::where('status', 1)->where('upload_id', $upload->id)->value('teis');
                
                        return '<a target="_blank" class="text-primary" href="' . asset('uploads/ters_form/' . $upload->name) . '">' . $teis_num . '.pdf</a>';
                    } else {
                        // If multiple files, show dropdown
                        $dropdown_id = 'dropdownTers' . $row->id; // Unique dropdown ID
                
                        $output = '<div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="' . $dropdown_id . '" data-bs-toggle="dropdown" aria-expanded="false">
                                View Files
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="' . $dropdown_id . '">';
                
                        foreach ($ters_uploads as $upload) {
                            $teis_num = TersUploads::where('status', 1)->where('upload_id', $upload->id)->value('teis');
                
                            $output .= '<li><a class="dropdown-item" target="_blank" href="' . asset('uploads/ters_form/' . $upload->name) . '">' . $teis_num . '.pdf</a></li>';
                        }
                
                        $output .= '</ul></div>';
                
                        return $output;
                    }
                } else {
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                }
                

                /// old viewing for multiple file
                // if ($row->ters_upload_id) {
                //     $upload_ids = explode(',', $row->ters_upload_id); // Convert string to array
                //     $ters_uploads = Uploads::where('status', 1)->whereIn('id', $upload_ids)->get();
                
                //     $dropdown_id = 'dropdownTers' . $row->id; // Unique dropdown ID
                
                //     $output = '<div class="dropdown">
                //         <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="' . $dropdown_id . '" data-bs-toggle="dropdown" aria-expanded="false">
                //             View Files
                //         </button>
                //         <ul class="dropdown-menu" aria-labelledby="' . $dropdown_id . '">';
                
                //     foreach ($ters_uploads as $upload) {
                //         $teis_num = TersUploads::where('status', 1)->where('upload_id', $upload->id)->value('teis');
                
                //         $output .= '<li><a class="dropdown-item" target="_blank" href="' . asset('uploads/ters_form/' . $upload->name) . '">' . $teis_num . '.pdf</a></li>';
                //     }
                
                //     $output .= '</ul></div>';
                
                //     return $output;
                // } else {
                //     return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                // }
                
                
                /// old view for only one file
                // if($row->ters_upload_id){
                //     $ters_uploads = Uploads::where('status', 1)->where('id', $row->ters_upload_id)->first();
                //     $teis_num = TersUploads::where('status', 1)->where('upload_id', $row->ters_upload_id)->value('teis');
                //     return '<div class="row mx-auto"><div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn pictureContainer">
                //     <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' . $ters_uploads['name'] . '">
                //     <span>'.$teis_num.'.pdf</span>
                //     </a>
                // </div></div>';
                // }else{
                //     return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                // }

                
            })

            ->addColumn('action', function(){

                return'<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
            })

            ->rawColumns(['request_number', 'teis', 'ters', 'action'])
            ->toJson();

    }



     // para sa report log ng per PE
     public function report_te_logs(Request $request)
     {
         $te_logs = ToolsAndEquipmentLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'tools_and_equipment_logs.tool_id')
             ->leftjoin('users', 'users.id', 'tools_and_equipment_logs.pe')
             ->select('tools_and_equipment.po_number', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'users.fullname', 'tools_and_equipment_logs.tr_type', 'tools_and_equipment_logs.created_at')
             ->where('tools_and_equipment.status', 1)
             ->where('tools_and_equipment_logs.status', 1)
             ->where('users.status', 1)
             ->where('tools_and_equipment_logs.tool_id', $request->toolId)
             ->get();
 
 
         return DataTables::of($te_logs)
                // ayusin ito bukas
            ->addColumn('date_received', function($row){
    
                $carbonDate = Carbon::parse($row->created_at); 
                $readableDate = $carbonDate->toDayDateTimeString();

                return $readableDate;
            })


            ->addColumn('attachment', function ($row) {
                if($row->tr_type == 'rttte' || $row->tr_type == 'rfteis'){
                    if($row->teis_upload_id){
                    $teis_uploads = Uploads::where('status', 1)->where('id', $row->teis_upload_id)->first()->toArray();
                    $teis_num = TeisUploads::where('status', 1)->where('upload_id', $row->teis_upload_id)->value('teis');
    
                    return '<div class="row mx-auto"><div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn pictureContainer">
                        <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/teis_form') . '/' . $teis_uploads['name'] . '">
                        <span>'.$teis_num.'.pdf</span>
                        </a>
                    </div></div>';
                    }else{
                        return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                    }
                }else{
                    if($row->ters_upload_id){
                        $ters_uploads = Uploads::where('status', 1)->where('id', $row->ters_upload_id)->first();
                        $teis_num = TersUploads::where('status', 1)->where('upload_id', $row->ters_upload_id)->value('teis');
                        return '<div class="row mx-auto"><div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn pictureContainer">
                        <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' . $ters_uploads['name'] . '">
                        <span>'.$teis_num.'.pdf</span>
                        </a>
                    </div></div>';
                    }else{
                        return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                    }
                }    

            })
             ->addColumn('action', function(){
 
                 return'<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
             })
 
             ->rawColumns([ 'action', 'attachment'])
             ->toJson();
 
     }



     public function request_list(Request $request)
    {

        $request_tools = TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'progress')
            ->where('status', 1)
            ->where('request_status', 'approved');

        $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type','progress')
            ->where('status', 1)
            ->where('request_status', 'approved');

        $pullout_request = PulloutRequest::select('pullout_number as teis_number', 'contact_number', 'request_status', 'subcon', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'progress')
            ->where('status', 1)
            ->where('request_status', 'approved');

        // filter
        if ($request->request_type == 'rfteis') {
            $query = $request_tools;
        } elseif ($request->request_type == 'rttte') {
            $query = $ps_request_tools;
        } elseif ($request->request_type == 'pullout') {
            $query = $pullout_request;
        } else {
            // no filter
            $query = $request_tools->union($ps_request_tools)->union($pullout_request);
        }
        

        $unioned_tables = $query->get();

        return DataTables::of($unioned_tables)

            ->addColumn('view_tools', function ($row) {

                return '<button data-id="' . $row->teis_number . '" data-transfertype="' . $row->tr_type . '" data-bs-toggle="modal" data-bs-target="#ongoingTeisRequestModal" class="teisNumber btn text-primary fs-6 d-block">View</button>';
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

            ->rawColumns(['view_tools', 'request_status', 'request_type','customer_name','subcon'])
            ->toJson();
    }

    public function fetch_logs(Request $request)
    {

        $logs = ActionLogs::leftJoin('users as u', 'u.id', 'action_logs.user_id')
        ->leftJoin('companies as c', 'c.id', 'u.comp_id')
        ->leftJoin('positions as p', 'p.id' , 'u.pos_id')
        ->select('u.fullname', 'u.emp_id', 'u.username', 'u.fullname', 'p.position', 'c.code', 'action_logs.created_at', 'action_logs.action')
        ->where('u.status', 1)
        ->where('p.status', 1)
        ->where('c.status', 1)
        ->get();

        // $ps_request_tools = PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type','progress')
        //     ->where('status', 1)
        //     ->where('request_status', 'approved');

        // $pullout_request = PulloutRequest::select('pullout_number as teis_number', 'contact_number', 'request_status', 'subcon', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'progress')
        //     ->where('status', 1)
        //     ->where('request_status', 'approved');

        // // filter
        // if ($request->request_type == 'rfteis') {
        //     $query = $request_tools;
        // } elseif ($request->request_type == 'rttte') {
        //     $query = $ps_request_tools;
        // } elseif ($request->request_type == 'pullout') {
        //     $query = $pullout_request;
        // } else {
        //     // no filter
        //     $query = $request_tools->union($ps_request_tools)->union($pullout_request);
        // }
        

        // $unioned_tables = $query->get();

        return DataTables::of($logs)
        
        ->addColumn('date', function($row){
    
            $carbonDate = Carbon::parse($row->created_at); 
            $readableDate = $carbonDate->format('M j, Y g:i A');

            return $readableDate;
        })

        ->toJson();
    }
}
