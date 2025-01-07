<?php

namespace App\Http\Controllers;

use App\Models\Daf;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use App\Models\ToolsAndEquipment;
use App\Models\PsTransferRequests;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){

        $total_tools = ToolsAndEquipment::where('status', 1)->count();
        $issued = TransferRequest::where('status', 1)->where('progress', 'completed')->count();
        $pending_rttte = PsTransferRequests::where('status', 1)->where('request_status', 'pending')->count();
        $rttte_approval = PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->count();


        // requestor
        $approved_rttte = PsTransferRequests::where('status', 1)->where('request_status', 'approved')->where('user_id', Auth::user()->id)->count();
        $teis = TransferRequest::where('status', 1)->where('request_status', 'approved')->where('pe', Auth::user()->id)->count();
        $pending_teis = TransferRequest::where('status', 1)->where('request_status', 'pending')->where('pe', Auth::user()->id)->count();
        $total_pullout = PulloutRequest::where('status', 1)->where('user_id', Auth::user()->id)->count();
        $pending_pullout = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->where('user_id', Auth::user()->id)->count();
        $approved_pullout = PulloutRequest::where('status', 1)->where('request_status', 'approved')->where('user_id', Auth::user()->id)->count();
        $request_daf = Daf::where('status', 1)->where('user_id', Auth::user()->id)->count();
        $pending_daf = Daf::where('status', 1)->where('request_status', 'pending')->where('user_id', Auth::user()->id)->count();
        $approved_daf = Daf::where('status', 1)->where('request_status', 'approved')->where('user_id', Auth::user()->id)->count();

        $total_approved = RequestApprover::where('status', 1)->where('approved_by', Auth::user()->id)->count();
        
        
        if(Auth::user()->user_type_id == 7){
            $total_approved_rfteis = TransferRequest::where('status', 1)->where('for_pricing', 2)->count();
            $total_approved_rttte = PsTransferRequests::where('status', 1)->where('for_pricing', 2)->count();
    
            $total_count_acc = $total_approved_rfteis + $total_approved_rttte;
            $total_approved = $total_count_acc;
        }

        if(Auth::user()->user_type_id == 2){
            $pending_teis = TransferRequest::where('status', 1)->where('progress', 'ongoing')->count();
            $pending_pullout = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->count();
        }


        // $pending_daf = Daf::leftjoin('transfer_requests', 'transfer_requests.teis_number', 'dafs.daf_number')
        // ->where('transfer_requests.status', 1)
        // ->where('dafs.status', 1)
        // ->where('transfer_requests.status', 1)
        // ->count();


        return view('/dashboard', compact('total_tools','issued','pending_rttte', 'rttte_approval', 'approved_rttte', 'teis', 'pending_teis', 'total_pullout', 'pending_pullout', 'request_daf', 'approved_pullout', 'pending_daf', 'total_approved'));

    }
}
