<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PeLogs;
use App\Models\Uploads;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\ToolsAndEquipmentLogs;

class ReportsController extends Controller
{
    // para sa report log ng pe
    public function report_pe_logs(Request $request)
    {
        $pe_logs = PeLogs::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'pe_logs.tool_id')
            ->select('tools_and_equipment.po_number', 'tools_and_equipment.asset_code', 'tools_and_equipment.item_code', 'tools_and_equipment.item_description', 'pe_logs.teis_upload_id', 'pe_logs.ters_upload_id', 'pe_logs.remarks', 'pe_logs.request_number')
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
 
             ->addColumn('action', function(){
 
                 return'<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
             })
 
             ->rawColumns([ 'action'])
             ->toJson();
 
     }
}