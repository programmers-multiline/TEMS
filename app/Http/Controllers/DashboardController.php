<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\TransferRequest;
use App\Models\PsTransferRequests;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){

        $issued = TransferRequest::where('status', 1)->where('progress', 'completed')->count();
        $pending_rttte = PsTransferRequests::where('status', 1)->where('request_status', 'pending')->count();
        $rttte_approval = PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->count();


        // requestor
        $approved_rttte = PsTransferRequests::where('status', 1)->where('request_status', 'approved')->count();
        $teis = TransferRequest::where('status', 1)->where('request_status', 'approved')->where('pe', Auth::user()->id)->count();
        $total_pullout = PulloutRequest::where('status', 1)->count();
        $pending_pullout = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->count();
        $approved_pullout = PulloutRequest::where('status', 1)->where('request_status', 'approved')->count();
        $request_daf = Daf::where('status', 1)->count();
        $pending_daf = Daf::leftjoin('transfer_requests', 'transfer_requests.teis_number', 'dafs.daf_number')
        ->where('transfer_requests.status', 1)
        ->where('dafs.status', 1)
        ->where('transfer_requests.status', 1)
        ->count();


        return view('/dashboard', compact('issued','pending_rttte', 'rttte_approval', 'approved_rttte', 'teis', 'total_pullout', 'pending_pullout', 'request_daf', 'approved_pullout', 'request_daf', 'request_daf', 'request_daf',));

    }
}
