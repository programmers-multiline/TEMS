@php
    /// para sa RFTEIS count - for approval
    if (
        (Auth::user()->user_type_id == 6 && Auth::user()->comp_id == 1) ||
        (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5)
    ) {

        $approvers = App\Models\RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('approver_status', 0)
            ->where('request_type', 1)
            ->get();

        if ($approvers->isEmpty()) {
            $tool_approvers = 0;
        }else{
            $tool_approvers = collect();

            foreach ($approvers as $approver) {
                $current_approvers = collect();

                if ($approver->sequence == 0) {
                    $current_approvers = App\Models\RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                        ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                        ->where('transfer_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('request_approvers.approver_id', Auth::user()->id)
                        ->where('approver_status', 0)
                        ->where('request_type', 1)
                        ->whereNot('transfer_requests.request_status', 'disapproved')
                        ->get();
                } elseif ($approver->sequence == 1) {
                        $current_approvers = App\Models\RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                            ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                            ->where('transfer_requests.status', 1)
                            ->whereNot('transfer_requests.request_status', 'disapproved')
                            ->where('request_approvers.status', 1)
                            ->where('approver_id', Auth::user()->id)
                            ->where('approver_status', 0)
                            ->where('request_type', 1)
                            ->where('request_approvers.id', $approver->id)
                            ->whereNot('transfer_requests.request_status', 'disapproved')
                            // ->where('for_pricing', '2')
                            ->get();
                    // }
                    // return $current_approvers;
                } elseif($approver->sequence == 4){

                    $prev_sequence = $approver->sequence - 1;

                    $prev_approver = App\Models\RequestApprover::where('status', 1)
                        ->where('request_id', $approver->request_id)
                        ->where('sequence', $prev_sequence)
                        ->where('request_type', 1)
                        ->first();

                    if ($prev_approver && $prev_approver->approver_status == 1) {
                        $current_approvers = App\Models\RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                        ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                        ->where('transfer_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('approver_id', Auth::user()->id)
                        ->where('approver_status', 0)
                        ->where('request_type', 1)
                        ->where('request_approvers.id', $approver->id)
                        ->where('for_pricing', '2')
                        ->whereNot('transfer_requests.request_status', 'disapproved')
                        ->get();
                    }
                } else {
                    $prev_sequence = $approver->sequence - 1;

                    $prev_approver = App\Models\RequestApprover::where('status', 1)
                        ->where('request_id', $approver->request_id)
                        ->where('sequence', $prev_sequence)
                        ->where('request_type', 1)
                        ->first();

                        // return $prev_approver;

                    if ($prev_approver && $prev_approver->approver_status == 1) {
                        $current_approvers = App\Models\RequestApprover::leftJoin('transfer_requests', 'transfer_requests.id', 'request_approvers.request_id')
                            ->select('transfer_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.date_approved')
                            ->where('transfer_requests.status', 1)
                            ->where('request_approvers.status', 1)
                            ->where('approver_id', Auth::user()->id)
                            ->where('approver_status', 0)
                            ->where('request_type', 1)
                            ->where('request_approvers.id', $approver->id)
                            ->whereNot('transfer_requests.request_status', 'disapproved')
                            ->get();
                    }
                }

                // Merge the current approvers to the tool_approvers array
                $tool_approvers = $tool_approvers->merge($current_approvers)->unique('request_id');
            }

            $tool_approvers = $tool_approvers->count();
        }

    } else {
        $tool_approvers = 0;
    }

    if (
        Auth::user()->user_type_id == 6 &&
        Auth::user()->comp_id == 2 &&
        (Auth::user()->pos_id == 5 || Auth::user()->pos_id == 6)
    ) {
        $series = 1;

        $approver = App\Models\RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('series', $series)
            ->where('request_type', 4)
            ->first();

        if ($approver->sequence == 1) {
            // $request_tools = TransferRequest::where('status', 1)->where('progress', 'ongoing')->get();
            $tools_approver_dafs = App\Models\RequestApprover::leftjoin(
                'dafs',
                'dafs.id',
                'request_approvers.request_id',
            )
                ->leftjoin('transfer_requests', 'transfer_requests.teis_number', 'dafs.daf_number')
                ->select(
                    'dafs.*',
                    'request_approvers.id as approver_id',
                    'request_approvers.request_id',
                    'request_approvers.series',
                    'transfer_requests.subcon',
                    'transfer_requests.customer_name',
                    'transfer_requests.project_name',
                    'transfer_requests.project_code',
                    'transfer_requests.project_address',
                )
                ->where('dafs.status', 1)
                ->where('transfer_requests.status', 1)
                ->where('request_approvers.status', 1)
                ->where('request_approvers.approver_id', Auth::user()->id)
                ->where('series', $series)
                ->where('approver_status', 0)
                ->where('request_type', 4)
                ->count();
        } else {
            $prev_sequence = $approver->sequence - 1;

            $prev_approver = App\Models\RequestApprover::where('status', 1)
                ->where('request_id', $approver->request_id)
                ->where('sequence', $prev_sequence)
                ->where('series', $series)
                ->where('request_type', 4)
                ->first();

            if ($prev_approver->approver_status == 1) {
                $tools_approver_dafs = App\Models\RequestApprover::leftjoin(
                    'dafs',
                    'dafs.id',
                    'request_approvers.request_id',
                )
                    ->leftjoin('transfer_requests', 'transfer_requests.teis_number', 'dafs.daf_number')
                    ->select(
                        'dafs.*',
                        'request_approvers.id as approver_id',
                        'request_approvers.request_id',
                        'request_approvers.series',
                        'transfer_requests.subcon',
                        'transfer_requests.customer_name',
                        'transfer_requests.project_name',
                        'transfer_requests.project_code',
                        'transfer_requests.project_address',
                    )
                    ->where('dafs.status', 1)
                    ->where('transfer_requests.status', 1)
                    ->where('request_approvers.status', 1)
                    ->where('request_approvers.approver_id', Auth::user()->id)
                    ->where('series', $series)
                    ->where('approver_status', 0)
                    ->where('request_type', 4)
                    ->count();
            } else {
                $tools_approver_dafs = [];
            }
        }
    }

    if (Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5) {

        $approvers = App\Models\RequestApprover::where('status', 1)
                ->where('approver_id', Auth::user()->id)
                ->where('approver_status', 0)
                ->where('request_type', 2)
                ->get();

        if ($approvers->isEmpty()) {
            $ps_request_tools_count = 0;
        }else{

            $tool_approvers_ps = collect();

            foreach ($approvers as $approver) {
                $ps_request_tools = collect();

                if ($approver->sequence == 1) {
                    $ps_request_tools = App\Models\PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
                        ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                        ->select('ps_transfer_requests.user_id', 'ps_transfer_requests.id', 'users.fullname', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
                        ->where('ps_transfer_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('request_approvers.approver_id', Auth::user()->id)
                        ->where('progress', 'ongoing')
                        ->where('approver_status', 0)
                        ->where('request_type', 2)
                        ->where('sequence', 1)
                        ->where('request_approvers.id', $approver->id)
                        ->get();
                } else {
                    $prev_sequence = $approver->sequence - 1;

                    $prev_approver = App\Models\RequestApprover::where('status', 1)
                        ->where('request_id', $approver->request_id)
                        ->where('sequence', $prev_sequence)
                        ->where('request_type', 2)
                        ->first();

                        // return $prev_approver->approver_status;


                    if ($prev_approver->approver_status == 1) {
                        $ps_request_tools = App\Models\PsTransferRequests::leftjoin('request_approvers', 'request_approvers.request_id', 'ps_transfer_requests.id')
                            ->leftjoin('users', 'users.id', 'ps_transfer_requests.user_id')
                            ->select('ps_transfer_requests.user_id', 'ps_transfer_requests.id','users.fullname', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'request_approvers.id as request_approver_id', 'request_approvers.request_id', 'request_approvers.series')
                            ->where('ps_transfer_requests.status', 1)
                            ->where('request_approvers.status', 1)
                            ->where('request_approvers.approver_id', Auth::user()->id)
                            ->where('progress', 'ongoing')
                            ->where('approver_status', 0)
                            ->where('request_type', 2)
                            ->where('sequence', $approver->sequence)
                            ->where('request_approvers.id', $approver->id)
                            ->get();
                    } else {
                        $ps_request_tools = [];
                    }
                }

                // Merge the current approvers to the tool_approvers array
                $tool_approvers_ps = $tool_approvers_ps->merge($ps_request_tools)->unique('request_id');
            }

            $ps_request_tools_count = $tool_approvers_ps->count();
        }
        // tignan sa upload kung andun ang number niya, pag andun wag isama sa bilang
        // if(request()->path() == 'pages/rftte'){
        //     $request_tools = TransferRequest::select('teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address', 'date_requested', 'tr_type')
        //     ->where('status', 1)
        //     ->where('progress', 'ongoing')
        //     ->where('request_status', 'approved');

        //     $ps_request_tools = PsTransferRequests::select('request_number as teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'tr_type')
        //     ->where('status', 1)
        //     ->where('progress', 'ongoing')
        //     ->where('request_status', 'approved')
        //     ->whereNotNull('acc');

        //     $unioned_tables = $request_tools->union($ps_request_tools)->get();
        // }
    }

    // Pullout_ongoing
    if(Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5) {
        $approvers =  App\Models\RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('approver_status', 0)
            ->where('request_type', 3)
            ->get();

        if ($approvers->isEmpty()) {
            $pullout_tools = 0;
        }else{

            $tool_approvers_pullout = collect();

            foreach ($approvers as $approver) {
                $current_approvers = collect();

                if ($approver->sequence == 1) {
                    $current_approvers =  App\Models\RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
                        ->select('pullout_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series')
                        ->where('pullout_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('request_approvers.approver_id', Auth::user()->id)
                        // ->where('series', $series)
                        ->where('approver_status', 0)
                        ->where('request_type', 3)
                        ->get();
                } else {
                    $prev_sequence = $approver->sequence - 1;

                    $prev_approver =  App\Models\RequestApprover::where('status', 1)
                        ->where('request_id', $approver->request_id)
                        ->where('sequence', $prev_sequence)
                        ->where('request_type', 3)
                        ->first();

                    if ($prev_approver && $prev_approver->approver_status == 1) {
                        $current_approvers =  App\Models\RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
                            ->select('pullout_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series')
                            ->where('pullout_requests.status', 1)
                            ->where('request_approvers.status', 1)
                            ->where('request_approvers.approver_id', Auth::user()->id)
                            // ->where('series', $series)
                            ->where('request_approvers.id', $approver->id)
                            ->where('approver_status', 0)
                            ->where('request_type', 3)
                            ->get();
                    }
                }

                // Merge the current approvers to the tool_approvers array
                $tool_approvers_pullout = $tool_approvers_pullout->merge($current_approvers)->unique('request_id');

                $pullout_tools = $tool_approvers_pullout->count();
            }  
        }
    }else {
        $pullout_tools = 0;
    }

    //RFTTE - warehouse
    // $request_tools = App\Models\TransferRequest::leftjoin('teis_uploads','teis_uploads.teis_number','transfer_requests.teis_number')
    // ->select('transfer_requests.teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address', 'date_requested', 'transfer_requests.tr_type')
    // ->where('transfer_requests.status', 1)
    // // ->where('teis_uploads.status', 1)
    // ->where('progress', 'ongoing')
    // ->where('request_status', 'approved')
    // ->whereNull('is_deliver');

    // $ps_request_tools_wh = App\Models\PsTransferRequests::leftjoin('teis_uploads','teis_uploads.teis_number','ps_transfer_requests.request_number')
    // ->leftjoin('ters_uploads','ters_uploads.pullout_number','ps_transfer_requests.request_number')
    // ->select('request_number as teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address','date_requested', 'ps_transfer_requests.tr_type')
    // ->where('ps_transfer_requests.status', 1)
    // ->where('progress', 'ongoing')
    // ->where('request_status', 'approved')
    // ->whereNotNull('acc')
    // ->whereNull('is_deliver');


    $request_tools = App\Models\TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'ongoing')
            ->where('request_status', 'approved')
            ->whereNull('is_deliver');

    $ps_request_tools_wh = App\Models\PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
        ->where('status', 1)
        ->where('progress', 'ongoing')
        ->where('request_status', 'approved')
        ///dahil inalis ko yung inputing of price sa acc
        ->where('for_pricing', 2)
        ->whereNull('is_deliver');

    $unioned_tables = $request_tools->union($ps_request_tools_wh)->count();


    //My TEIS Request - for receiving
    $request_tools_for_receiving = App\Models\TransferRequest::join('transfer_request_items', 'transfer_request_items.transfer_request_id', 'transfer_requests.id')
        ->select('transfer_requests.teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver', 'progress')
        ->where('transfer_requests.status', 1)
        ->where('transfer_request_items.status', 1)
        ->whereNull('transfer_request_items.is_remove')
        ->where(function($query) {
            $query->where('progress', 'ongoing')
                ->orWhere('progress', 'partial');
        })
        ->where('transfer_requests.pe', Auth::user()->id)
        ->whereNotNull('is_deliver')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('transfer_request_items')
                ->whereColumn('transfer_request_items.transfer_request_id', 'transfer_requests.id')
                ->whereNull('transfer_request_items.is_remove')
                ->where('transfer_request_items.item_status', 0);
        });
    
    // $request_tools_for_receiving =  App\Models\TransferRequest::select('teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver', 'progress')
    //     ->where('status', 1)
    //     ->where(function($query) {
    //         $query->where('progress', 'ongoing')
    //             ->orWhere('progress', 'partial');
    //     })
    //     ->where('pe', Auth::user()->id)
    //     ->whereNotNull('is_deliver');

    $ps_request_tools_for_receiving =  App\Models\PsTransferRequests::select('request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver', 'progress')
        ->where('status', 1)
        ->where('progress', 'ongoing')
        ->where('request_status', 'approved')
        ->where('user_id', Auth::user()->id)
        ->whereNotNull('is_deliver');

    $unioned_tables_for_receiving = $request_tools_for_receiving->union($ps_request_tools_for_receiving)->count();

    // pullout-out schedule - warehouse
    if(Auth::user()->user_type_id == 2){
        $pullout_for_schedule = App\Models\PulloutRequest::leftjoin('users', 'users.id', 'pullout_requests.user_id')
            ->select('pullout_requests.*', 'users.fullname')
            ->where('pullout_requests.status', 1)
            ->where('users.status', 1)
            ->where('progress', 'ongoing')
            ->where('request_status', 'approved')
            ->whereNull('approved_sched_date')
            ->count();
    }else{
        $pullout_for_schedule = 0;
    }

    // pullout for receiving warehouse
    if(Auth::user()->user_type_id == 2){
        $pullout_for_receiving = App\Models\PulloutRequest::leftJoin('users', 'users.id', 'pullout_requests.user_id')
            ->select('pullout_requests.*', 'users.fullname')
            ->where('pullout_requests.status', 1)
            ->where('users.status', 1)
            ->where('request_status', 'approved')
            ->where('progress', 'ongoing')
            ->whereNotNull('is_deliver')
            ->count();
    }else{
        $pullout_for_receiving = 0;
    }

    /// rftte_signed_form_proof
    if(Auth::user()->user_type_id == 2){
        $request_tools = App\Models\TransferRequest::select('progress', 'teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'completed')
            ->whereNull('is_proof_upload');


        $ps_request_tools = App\Models\PsTransferRequests::select('progress', 'request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            ->where('progress', 'completed')
            ->whereNull('is_proof_upload');

        $transfer_requests = $request_tools->union($ps_request_tools)->get();

        $rftte_signed_form_proof_count = $transfer_requests->count();
    }else{
        $rftte_signed_form_proof_count = 0;
    }

    /// Not Serve Count
    if(Auth::user()->user_type_id == 2){
        $request_tools = App\Models\TransferRequest::join('transfer_request_items', 'transfer_request_items.transfer_request_id', 'transfer_requests.id')
                ->select('progress','transfer_requests.teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('transfer_request_items.status', 1)
                ->where('progress', 'partial')
                ->where('transfer_requests.status', 1);


        $ps_request_tools = App\Models\PsTransferRequests::join('ps_transfer_request_items', 'ps_transfer_request_items.ps_transfer_request_id', 'ps_transfer_requests.id')
            ->select('progress', 'ps_transfer_requests.request_number as teis_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('ps_transfer_request_items.item_status', 2)
            ->where('ps_transfer_request_items.status', 1)
            ->where('progress', 'completed')
            ->where('ps_transfer_requests.status', 1);

        $transfer_requests = $request_tools->union($ps_request_tools)->get();

        $not_serve_count = $transfer_requests->count();
    }else{
        $not_serve_count = 0;
    }
    ///RFTEIS ACC
    $rfteis_acc_count = App\Models\TransferRequest::where('status', 1)->where('progress', 'ongoing')->where('for_pricing', 1)->where('request_status', '!=' ,'disapproved')->count();

    ///RTTTE ACC
    $rttte_acc_count = App\Models\PsTransferRequests::where('status', 1)->where('progress', 'ongoing')->where('for_pricing', 1)->count();
@endphp

<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <meta name="description" content="Tools and Equipment Monitoring System">
    <meta name="author" content="Pao">
    <meta name="robots" content="index, follow">
  
    <!-- Open Graph Meta -->
    <meta property="og:title" content="Tools and Equipment Monitoring System">
    <meta property="og:site_name" content="TEMS">
    <meta property="og:description" content="Tools and Equipment Monitoring System">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
  
    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/logo.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/FMLC_LOGO.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/media/favicons/apple-touch-icon-180x180.png') }}">

    <title>Tools And Equipment Monitoring System</title>


    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">

    @yield('css')


    {{-- <link rel="stylesheet" id="css-main" href="{{asset('js/codebase.min.css')}}"> --}}


    <!-- Modules -->


    @vite(['resources/sass/main.scss', 'resources/js/codebase/app.js'])

    <!-- Alternatively, you can also include a specific color theme after the main stylesheet to alter the default color theme of the template -->
    {{-- @vite(['resources/sass/main.scss', 'resources/sass/codebase/themes/corporate.scss', 'resources/js/codebase/app.js']) --}}
    <style>
        table th, 
        table td { 
            font-size: .75rem; 
        }
        table.dataTable {
            width: 100% !important;
        }
        .table thead th {
            font-size: .85em; 
        }
    </style>
</head>

<body>
    <!-- Page Container -->
    <!--
    Available classes for #page-container:

    SIDEBAR & SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Classic Header style if no class is added
      'page-header-modern'                        Modern Header style
      'page-header-dark'                          Dark themed Header (works only with classic Header style)
      'page-header-glass'                         Light themed Header with transparency by default
                                                  (absolute position, perfect for light images underneath - solid light background on scroll if the Header is also set as fixed)
      'page-header-glass page-header-dark'        Dark themed Header with transparency by default
                                                  (absolute position, perfect for dark images underneath - solid dark background on scroll if the Header is also set as fixed)

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)

    DARK MODE

      'sidebar-dark page-header-dark dark-mode'   Enable dark mode (light sidebar/header is not supported with dark mode)
  -->
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">

        <!-- Sidebar -->
        <!--
      Helper classes

      Adding .smini-hide to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
      Adding .smini-show to an element will make it visible (opacity: 1) when the sidebar is in mini mode
        If you would like to disable the transition, just add the .no-transition along with one of the previous 2 classes

      Adding .smini-hidden to an element will hide it when the sidebar is in mini mode
      Adding .smini-visible to an element will show it only when the sidebar is in mini mode
      Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
    -->
        <nav id="sidebar">
            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Side Header -->
                <div class="content-header justify-content-lg-center">
                    <!-- Logo -->
                    <div>
                        <span class="smini-visible fw-bold tracking-wide fs-lg">
                            c<span class="text-primary">b</span>
                        </span>
                        <a class="link-fx fw-bold tracking-wide mx-auto" href="/dashboard">
                            <span class="smini-hidden">
                                <img src="{{ asset('media/logo.png') }}" width="170" alt="">
                            </span>
                        </a>
                    </div>
                    <!-- END Logo -->

                    <!-- Options -->
                    <div>
                        <!-- Close Sidebar, Visible only on mobile screens -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout"
                            data-action="sidebar_close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                        <!-- END Close Sidebar -->
                    </div>
                    <!-- END Options -->
                </div>
                <!-- END Side Header -->

                <!-- Sidebar Scrolling -->
                <div class="js-sidebar-scroll">
                    <!-- Side User -->

                    <!-- END Side User -->

                    <!-- Side Navigation -->
                    <div class="content-side content-side-full">
                        <ul class="nav-main">
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('dashboard') ? ' active' : '' }}"
                                    href="/dashboard">
                                    <i class="nav-main-link-icon fa fa-house-user"></i>
                                    <span class="nav-main-link-name">Dashboard</span>
                                </a>
                            </li>

                            @if (Auth::user()->user_type_id == 1)
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/users_management') ? ' active' : '' }}"
                                        href="/pages/users_management">
                                        <i class="nav-main-link-icon fa fa-user-group"></i>
                                        <span class="nav-main-link-name">Users Management</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('approvers_setup') ? ' active' : '' }}"
                                        href="/approvers_setup">
                                        <i class="nav-main-link-icon fa fa-users-gear"></i>
                                        <span class="nav-main-link-name">Setup Approver</span>
                                    </a>
                                </li>
                                
                            @endif

                            @if (Auth::user()->user_type_id == 2)
                                <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/rftte', 'pages/rftte_completed') ? ' active' : '' }}"
                                        data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                        <i class="nav-main-link-icon fa fa-box-open"></i>
                                        <span class="nav-main-link-name">RFTTE</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/rftte') ? ' active' : '' }}"
                                                href="/pages/rftte">
                                                <span class="nav-main-link-name">
                                                    Ongoing
                                                </span>
                                            </a>
                                            <span class="countContainer nav-main-link text-light {{ $unioned_tables == 0 ? 'd-none' : '' }}"><span
                                                id="rftteCount" class="bg-info"
                                                style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $unioned_tables }}</span>
                                            </span>
                                        </li>
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/rftte_signed_form_proof') ? ' active' : '' }}"
                                                href="/pages/rftte_signed_form_proof">
                                                <span class="nav-main-link-name">
                                                    Proof of Receiving
                                                </span>
                                            </a>
                                            <span class="countContainer nav-main-link text-light {{ $rftte_signed_form_proof_count == 0 ? 'd-none' : '' }}"><span
                                                id="rftteSignedFormProofCount" class="bg-info"
                                                style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $rftte_signed_form_proof_count }}</span>
                                            </span>
                                        </li>
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/not_serve_items') ? ' active' : '' }}"
                                                href="/pages/not_serve_items">
                                                <span class="nav-main-link-name">
                                                    Not Serve Items
                                                </span>
                                            </a>
                                            <span class="countContainer nav-main-link text-light {{$not_serve_count == 0 ? 'd-none' : '' }} "><span
                                                id="notServeCount" class="bg-info"
                                                style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $not_serve_count }}</span>
                                            </span>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('pages/rftte_completed') ? ' active' : '' }}"
                                                href="/pages/rftte_completed">
                                                <span class="nav-main-link-name">Completed</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/pullout_warehouse', 'pages/pullout_completed_warehouse') ? ' active' : '' }}"
                                        data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                        <i class="nav-main-link-icon fa fa-building-circle-arrow-right"></i>
                                        <span class="nav-main-link-name">Pull-Out Request</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/pullout_warehouse') ? ' active' : '' }}"
                                                href="/pages/pullout_warehouse">
                                                <span class="nav-main-link-name">
                                                    Schedule
                                                </span>
                                            </a>
                                            <span class="countContainer nav-main-link text-light {{$pullout_for_schedule == 0 ? 'd-none' : '' }}"><span
                                                id="pulloutForSchedCount" class="bg-info"
                                                style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $pullout_for_schedule }}</span>
                                            </span>
                                        </li>
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/pullout_for_receiving') ? ' active' : '' }}"
                                                href="/pages/pullout_for_receiving">
                                                <span class="nav-main-link-name">For Receiving</span>
                                            </a>
                                            <span class="countContainer nav-main-link text-light {{$pullout_for_receiving == 0 ? 'd-none' : '' }}"><span
                                                id="pulloutForReceivingCount" class="bg-info"
                                                style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $pullout_for_receiving }}</span>
                                            </span>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('pages/pullout_completed') ? ' active' : '' }}"
                                                href="/pages/pullout_completed">
                                                <span class="nav-main-link-name">Completed</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-main-heading">Generator</li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/qrcode_generator') ? ' active' : '' }}"
                                        href="/pages/qrcode_generator">
                                        <i class="nav-main-link-icon fa fa-qrcode"></i>
                                        <span class="nav-main-link-name">Qr Code Generator</span>
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 5)
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('view_my_te') ? ' active' : '' }}"
                                        href="/view_my_te">
                                        <i class="nav-main-link-icon fa fa-screwdriver-wrench"></i>
                                        <span class="nav-main-link-name">
                                            @if (Auth::user()->user_type_id == 4)
                                                My
                                            @endif
                                            Tools and Equipment
                                        </span>
                                    </a>
                                </li>
                                @if (Auth::user()->user_type_id == 5)
                                    <li class="nav-main-item">
                                        <a class="nav-main-link{{ request()->is('pages/tool_extension_request') ? ' active' : '' }}"
                                            href="/pages/tool_extension_request">
                                            <i class="nav-main-link-icon fa fa-toolbox"></i>
                                            <span class="nav-main-link-name">
                                                Tool Extension Request 
                                            </span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/pullout_ongoing', 'pages/pullout_completed') ? ' active' : '' }}"
                                        data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                        <i class="nav-main-link-icon fa fa-arrows-turn-right"></i>
                                        <span class="nav-main-link-name">
                                            @if (Auth::user()->user_type_id == 4)
                                                My
                                            @endif
                                            Request for Pull-Out
                                        </span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/pullout_ongoing') ? ' active' : '' }}"
                                                href="/pages/pullout_ongoing">
                                                <span class="nav-main-link-name">
                                                    @if (Auth::user()->user_type_id == 4)
                                                        Ongoing
                                                    @endif
                                                    @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5)
                                                        For Approval
                                                    @endif
                                                </span>
                                            </a>
                                            <span class="countContainer nav-main-link text-light {{ $pullout_tools == 0 ? 'd-none' : '' }}"><span
                                                id="pulloutCount" class="bg-info"
                                                style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $pullout_tools }}</span>
                                            </span>
                                        </li>
                                        @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5)
                                            <li class="nav-main-item">
                                                <a class="nav-main-link{{ request()->is('pages/approved_pullout') ? ' active' : '' }}"
                                                    href="/pages/approved_pullout">
                                                    <span class="nav-main-link-name">Approved Pullout</span>
                                                </a>
                                            </li>
                                        @endif
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('pages/pullout_completed') ? ' active' : '' }}"
                                                href="/pages/pullout_completed">
                                                <span class="nav-main-link-name">Completed</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @if (Auth::user()->user_type_id == 4)
                                    <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                        <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/request_ongoing', 'pages/request_completed') ? ' active' : '' }}"
                                            data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                            <i class="nav-main-link-icon fa fa-file-pen"></i>
                                            <span class="nav-main-link-name">
                                                @if (Auth::user()->user_type_id == 4)
                                                    My
                                                @endif
                                                Request for TEIS
                                            </span>
                                        </a>
                                        <ul class="nav-main-submenu">
                                            <li class="nav-main-item">
                                                <a class="nav-main-link{{ request()->is('pages/request_ongoing') ? ' active' : '' }}"
                                                    href="/pages/request_ongoing">
                                                    <span class="nav-main-link-name">Ongoing</span>
                                                </a>
                                            </li>
                                            <li class="nav-main-item d-flex align-items-center justify-content-between">
                                                <a class="nav-main-link{{ request()->is('pages/request_for_receiving') ? ' active' : '' }}"
                                                    href="/pages/request_for_receiving">
                                                    <span class="nav-main-link-name">For Receiving</span>
                                                </a>
                                                <span class="countContainer nav-main-link text-light {{$unioned_tables_for_receiving == 0 ? 'd-none' : '' }}"><span
                                                    id="forReceivingCount" class="bg-info"
                                                    style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $unioned_tables_for_receiving }}</span>
                                                </span>
                                            </li>
                                            <li class="nav-main-item">
                                                <a class="nav-main-link{{ request()->is('pages/rfteis_disapproved') ? ' active' : '' }}"
                                                    href="/pages/rfteis_disapproved">
                                                    <span class="nav-main-link-name">Disapproved RFTEIS</span>
                                                </a>
                                            </li>
                                            <li class="nav-main-item">
                                                <a class="nav-main-link{{ request()->is('pages/request_completed') ? ' active' : '' }}"
                                                    href="/pages/request_completed">
                                                    <span class="nav-main-link-name">Completed</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                {{-- <li class="nav-main-item d-flex align-items-center justify-content-between">
                                    <a class="nav-main-link{{ request()->is('pages/site_to_site_transfer') ? ' active' : '' }}"
                                        href="/pages/site_to_site_transfer">
                                        <i class="nav-main-link-icon fa fa-building-circle-arrow-right"></i>
                                        <span class="nav-main-link-name">Site to Site Transfer</span>
                                    </a>
                                    <span @php
                                    if(Auth::user()->user_type_id == 4){$ps_request_tools = 0;} @endphp
                                        class="countContainer nav-main-link text-light {{ $ps_request_tools == 0 ? 'd-none' : '' }}"><span
                                            id="siteToSiteCount" class="bg-info"
                                            style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $ps_request_tools }}</span>
                                    </span>
                                </li> --}}

                                <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/site_to_site_transfer', 'pages/sts_request_completed', 'pages/ps_request_for_receiving') ? ' active' : '' }}"
                                        data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                        <i class="nav-main-link-icon fa fa-building-circle-arrow-right"></i>
                                        <span class="nav-main-link-name">
                                            Site to Site Transfer
                                        </span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/site_to_site_transfer') ? ' active' : '' }}"
                                                href="/pages/site_to_site_transfer">
                                                <span class="nav-main-link-name">
                                                    @if (Auth::user()->user_type_id == 4)
                                                        Ongoing
                                                    @else
                                                    For Approval 
                                                    @endif
                                                </span>
                                            </a>
                                            <span @php
                                            if(Auth::user()->user_type_id == 4){$ps_request_tools_count = 0;} @endphp
                                                class="countContainer nav-main-link text-light {{ $ps_request_tools_count == 0 ? 'd-none' : '' }}"><span
                                                    id="siteToSiteCount" class="bg-info"
                                                    style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $ps_request_tools_count }}</span>
                                            </span>
                                        </li>
                                        {{-- @if (Auth::user()->user_type_id == 4)
                                            <li class="nav-main-item d-flex align-items-center justify-content-between">
                                                <a class="nav-main-link{{ request()->is('pages/ps_request_for_receiving') ? ' active' : '' }}"
                                                    href="/pages/ps_request_for_receiving">
                                                    <span class="nav-main-link-name">For Receiving</span>
                                                </a>
                                                <span class="countContainer nav-main-link text-light {{$unioned_tables_for_receiving == 0 ? 'd-none' : '' }}"><span
                                                    id="forReceivingCount" class="bg-info"
                                                    style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $unioned_tables_for_receiving }}</span>
                                                </span>
                                            </li> 
                                        @endif --}}
                                        
                                        @if (Auth::user()->user_type_id == 4)
                                            <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('pages/sts_request_completed') ? ' active' : '' }}"
                                                href="/pages/sts_request_completed">
                                                <span class="nav-main-link-name">
                                                        Completed
                                                    </span>
                                                </a>
                                            </li>
                                            @else
                                            <li class="nav-main-item">
                                                <a class="nav-main-link{{ request()->is('pages/site_to_site_approved') ? ' active' : '' }}"
                                                    href="/pages/site_to_site_approved">
                                                    <span class="nav-main-link-name">
                                                        Approved
                                                    </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>


                                @if (Auth::user()->user_type_id !== 4)
                                    <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                        <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/rfteis', 'pages/rfteis_approved') ? ' active' : '' }}"
                                            data-toggle="submenu" aria-haspopup="true" aria-expanded="true"
                                            href="#">
                                            <i class="nav-main-link-icon fa fa-box-open"></i>
                                            <span class="nav-main-link-name">
                                                RFTEIS
                                            </span>
                                        </a>
                                        <ul class="nav-main-submenu">
                                            <li
                                                class="nav-main-item d-flex align-items-center justify-content-between">
                                                <a class="nav-main-link{{ request()->is('pages/rfteis') ? ' active' : '' }}"
                                                    href="/pages/rfteis">
                                                    <span class="nav-main-link-name">For Approval</span>
                                                </a>
                                                <span
                                                    class="countContainer nav-main-link text-light {{ $tool_approvers == 0 ? 'd-none' : '' }}"><span
                                                        id="rfteisCount" class="bg-info"
                                                        style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $tool_approvers }}</span>
                                                </span>
                                            </li>
                                            <li class="nav-main-item">
                                                <a class="nav-main-link{{ request()->is('pages/rfteis_approved') ? ' active' : '' }}"
                                                    href="/pages/rfteis_approved">
                                                    <span class="nav-main-link-name">Approved</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                @if (Auth::user()->user_type_id == 5)
                                    <li class="nav-main-item">
                                        <a class="nav-main-link{{ request()->is('project_tagging') ? ' active' : '' }}"
                                            href="/project_tagging">
                                            <i class="nav-main-link-icon fa fa-user-tag"></i>
                                            <span class="nav-main-link-name">
                                                Project Assignment
                                            </span>
                                        </a>
                                    </li>
                                @endif
                            @endif

                            @if (Auth::user()->user_type_id == 4)
                                <li class="nav-main-heading">Receive Tools</li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/qrcode_scanner') ? ' active' : '' }}"
                                        href="/pages/qrcode_scanner">
                                        <i class="nav-main-link-icon fa fa-qrcode"></i>
                                        <span class="nav-main-link-name">Qr Code Scannner</span>
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->user_type_id == 6 && Auth::user()->comp_id == 1)
                                <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/rfteis', 'pages/rfteis_approved') ? ' active' : '' }}"
                                        data-toggle="submenu" aria-haspopup="true" aria-expanded="true"
                                        href="#">
                                        <i class="nav-main-link-icon fa fa-box-open"></i>
                                        <span class="nav-main-link-name">
                                            RFTEIS
                                        </span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item d-flex align-items-center justify-content-between">
                                            <a class="nav-main-link{{ request()->is('pages/rfteis') ? ' active' : '' }}"
                                                href="/pages/rfteis">
                                                <span class="nav-main-link-name">For Approval</span>
                                            </a>
                                            <span
                                                class="countContainer nav-main-link text-light {{ $tool_approvers == 0 ? 'd-none' : '' }}"><span
                                                    id="rfteisCount" class="bg-info"
                                                    style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $tool_approvers }}</span>
                                            </span>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('pages/rfteis_approved') ? ' active' : '' }}"
                                                href="/pages/rfteis_approved">
                                                <span class="nav-main-link-name">Approved</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('pages/rfteis_disapproved') ? ' active' : '' }}"
                                                href="/pages/rfteis_disapproved">
                                                <span class="nav-main-link-name">Disapproved</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif

                            @if (Auth::user()->user_type_id == 6 && (Auth::user()->pos_id == 2 || Auth::user()->pos_id == 6))
                                <li class="nav-main-item d-flex align-items-center justify-content-between">
                                    <a class="nav-main-link{{ request()->is('pages/daf') ? ' active' : '' }}"
                                        href="/pages/daf">
                                        <i class="nav-main-link-icon fa fa-box-open"></i>
                                        <span class="nav-main-link-name">DAF</span>
                                    </a>
                                    <span
                                        class="countContainer nav-main-link text-light {{ $tools_approver_dafs == 0 ? 'd-none' : '' }}"><span
                                            id="dafCount" class="bg-info"
                                            style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $tools_approver_dafs }}</span>
                                    </span>
                                </li>
                            @endif

                            @if (Auth::user()->user_type_id == 7)
                                <li class="nav-main-item d-flex align-items-center justify-content-between">
                                    <a class="nav-main-link{{ request()->is('pages/rfteis_acc') ? ' active' : '' }}"
                                        href="/pages/rfteis_acc">
                                        <i class="nav-main-link-icon fa fa-box"></i>
                                        <span class="nav-main-link-name">RFTEIS</span>
                                    </a>
                                    <span
                                        class="countContainer nav-main-link text-light {{ $rfteis_acc_count == 0 ? 'd-none' : '' }}"><span
                                            id="rfteisAccCount" class="bg-info"
                                            style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $rfteis_acc_count }}</span>
                                    </span>
                                </li>
                                <li class="nav-main-item d-flex align-items-center justify-content-between">
                                    <a class="nav-main-link{{ request()->is('pages/rttte_acc') ? ' active' : '' }}"
                                        href="/pages/rttte_acc">
                                        <i class="nav-main-link-icon fa fa-box-open"></i>
                                        <span class="nav-main-link-name">RTTTE</span>
                                    </a>
                                    <span
                                        class="countContainer nav-main-link text-light {{ $rttte_acc_count == 0 ? 'd-none' : '' }}"><span
                                            id="rttteAccCount" class="bg-info"
                                            style="width: 20px; line-height: 20px; border-radius: 50%;text-align: center; font-size: .65rem;">{{ $rttte_acc_count }}</span>
                                    </span>
                                </li>

                                <li class="nav-main-item d-flex align-items-center justify-content-between">
                                    <a class="nav-main-link{{ request()->is('pages/acc_approved_request') ? ' active' : '' }}"
                                        href="/pages/acc_approved_request">
                                        <i class="nav-main-link-icon fa fa-file-circle-check"></i>
                                        <span class="nav-main-link-name">Approved Request</span>
                                    </a>
                                </li>
                                {{-- <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/daf') ? ' active' : '' }}"
                                        href="/pages/daf">
                                        <i class="nav-main-link-icon fa fa-box-archive"></i>
                                        <span class="nav-main-link-name">DAF</span>
                                    </a>
                                </li> --}}
                            @endif


                            @if (Auth::user()->user_type_id == 8)
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('/pages/list_of_requests') ? ' active' : '' }}"
                                        href="/pages/list_of_requests">
                                        <i class="nav-main-link-icon fa fa-clipboard-list"></i>
                                        <span class="nav-main-link-name">
                                            List of Request
                                        </span>
                                    </a>
                                </li>
                            @endif


                            <li class="nav-main-heading">Tools and Equipment from</li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('view_warehouse') ? ' active' : '' }}"
                                    href="/view_warehouse">
                                    <i class="nav-main-link-icon fa fa-warehouse"></i>
                                    <span class="nav-main-link-name">Warehouses</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('view_project_site') ? ' active' : '' }}"
                                    href="/view_project_site">
                                    <i class="nav-main-link-icon fa fa-building"></i>
                                    <span class="nav-main-link-name">Project Sites</span>
                                </a>
                            </li>

                            @if (Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 5 || Auth::user()->user_type_id == 7)
                                <li class="nav-main-heading">Reports</li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/report_pe_logs') ? ' active' : '' }}"
                                        href="/pages/report_pe_logs">
                                        <i class="nav-main-link-icon fa fa-book-bookmark"></i>
                                        <span class="nav-main-link-name">Item Logs</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/report_te_logs') ? ' active' : '' }}"
                                        href="/pages/report_te_logs">
                                        <i class="nav-main-link-icon fa fa-address-book"></i>
                                        <span class="nav-main-link-name">Tools & Equipment Logs</span>
                                    </a>
                                </li>
                            @endif
                            {{-- @if (Auth::user()->user_type_id == 7 || Auth::user()->user_type_id == 5)
                            @if (Auth::user()->user_type_id == 7)
                                <li class="nav-main-heading">Reports</li>
                            @endif
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('pages/report_te_logs') ? ' active' : '' }}"
                                        href="/pages/report_te_logs">
                                        <i class="nav-main-link-icon fa fa-address-book"></i>
                                        <span class="nav-main-link-name">Tools & Equipment Logs</span>
                                    </a>
                                </li>
                            @endif --}}
                        </ul>
                    </div>
                    <!-- END Side Navigation -->
                </div>
                <!-- END Sidebar Scrolling -->
            </div>
            <!-- Sidebar Content -->
        </nav>
        <!-- END Sidebar -->

        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header">
                <!-- Left Section -->
                <div class="space-x-1">
                    <!-- Toggle Sidebar -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout"
                        data-action="sidebar_toggle">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                    <!-- END Toggle Sidebar -->

                    <!-- Open Search Section -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout"
                        data-action="header_search_on" data-bs-toggle="modal" data-bs-target="#search">
                        <i class="fa fa-fw fa-search"></i>
                    </button>
                    <!-- END Open Search Section -->
                </div>
                <!-- END Left Section -->

                <!-- Right Section -->
                <div class="space-x-1">
                    <!-- User Dropdown -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user d-sm-none"></i>
                            <span class="d-none d-sm-inline-block fw-semibold">{{ Auth::user()->fullname }}</span>
                            <i class="fa fa-angle-down opacity-50 ms-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0"
                            aria-labelledby="page-header-user-dropdown">
                            <div class="px-2 py-3 bg-body-light rounded-top">
                                <h5 class="h6 text-center mb-0">
                                    {{ Auth::user()->fullname }}
                                </h5>
                            </div>
                            <div class="p-2">
                                {{-- <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                                    href="javascript:void(0)">
                                    <span>Profile</span>
                                    <i class="fa fa-fw fa-user opacity-25"></i>
                                </a> --}}
                                {{-- <div class="dropdown-divider"></div> --}}
                                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                                    href="#" onclick="logoutUser()">
                                    <span>Sign Out</span>
                                    <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- END User Dropdown -->

                    <!-- Notifications -->
                    <div class="dropdown d-inline-block">
                        {{-- <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-notifications"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell animated swing loop"></i>
                            <span class="text-xl text-primary">&bull;</span>
                        </button> --}}
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications">
                            <div class="px-2 py-3 bg-body-light rounded-top">
                                <h5 class="h6 text-center mb-0">
                                    Notifications
                                </h5>
                            </div>
                            <ul class="nav-items my-2 fs-sm">
                                <li>
                                    <a class="text-dark d-flex py-2" href="javascript:void(0)">
                                        <div class="flex-shrink-0 me-2 ms-3">
                                            <i class="fa fa-fw fa-check text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 pe-2">
                                            <p class="fw-medium mb-1">You’ve upgraded to a VIP account successfully!
                                            </p>
                                            <div class="text-muted">15 min ago</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="text-dark d-flex py-2" href="javascript:void(0)">
                                        <div class="flex-shrink-0 me-2 ms-3">
                                            <i class="fa fa-fw fa-exclamation-triangle text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1 pe-2">
                                            <p class="fw-medium mb-1">Please check your payment info since we can’t
                                                validate them!</p>
                                            <div class="text-muted">50 min ago</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="text-dark d-flex py-2" href="javascript:void(0)">
                                        <div class="flex-shrink-0 me-2 ms-3">
                                            <i class="fa fa-fw fa-times text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1 pe-2">
                                            <p class="fw-medium mb-1">Web server stopped responding and it was
                                                automatically restarted!</p>
                                            <div class="text-muted">4 hours ago</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="text-dark d-flex py-2" href="javascript:void(0)">
                                        <div class="flex-shrink-0 me-2 ms-3">
                                            <i class="fa fa-fw fa-exclamation-triangle text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1 pe-2">
                                            <p class="fw-medium mb-1">Please consider upgrading your plan. You are
                                                running out of space.</p>
                                            <div class="text-muted">16 hours ago</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="text-dark d-flex py-2" href="javascript:void(0)">
                                        <div class="flex-shrink-0 me-2 ms-3">
                                            <i class="fa fa-fw fa-plus text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 pe-2">
                                            <p class="fw-medium mb-1">New purchases! +$250</p>
                                            <div class="text-muted">1 day ago</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <div class="p-2 bg-body-light rounded-bottom">
                                <a class="dropdown-item text-center mb-0" href="javascript:void(0)">
                                    <i class="fa fa-fw fa-flag opacity-50 me-1"></i> View All
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- END Notifications -->

                </div>
                <!-- END Right Section -->
            </div>
            <!-- END Header Content -->

            <!-- Header Search -->
            {{-- <div id="page-header-search" class="overlay-header">
                <div class="content-header">
                    <form class="w-50" action="/dashboard" method="POST">
                        @csrf
                        <div class="input-group">
                            <!-- Close Search Section -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-secondary" data-toggle="layout"
                                data-action="header_search_off">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                            <!-- END Close Search Section -->
                            <input type="text" class="form-control" placeholder="Search or hit ESC.."
                                id="page-header-search-input" name="page-header-search-input">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div> --}}
            <!-- END Header Search -->

            <!-- Header Loader -->
            <div id="page-header-loader" class="overlay-header bg-primary">
                <div class="content-header">
                    <div class="w-100 text-center">
                        <i class="far fa-sun fa-spin text-white"></i>
                    </div>
                </div>
            </div>
            <!-- END Header Loader -->
        </header>
        <!-- END Header -->


        <!-- Main Container -->
        <main id="main-container">
            <div class="content">
                <h2 style="margin-bottom: -10px;">@yield('content-title')</h2>
            </div>
            @yield('content')
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">

                    </div>
                    <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                        {{-- <a class="fw-semibold" href="/" target="_blank">Codebase</a> &copy; <span data-toggle="year-copy"></span> --}}
                    </div>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->


    {{-- search modal --}}

    <div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="modal-popin"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <form class="w-100" action="/dashboard" method="POST">
                            @csrf
                            <div class="input-group">
                                <!-- END Close Search Section -->
                                <input type="text" class="form-control" placeholder="Search.." id="searchTools"
                                    name="page-header-search-input">
                                {{-- <button type="submit" class="btn btn-secondary">
                                        <i class="fa fa-fw fa-search"></i>
                                    </button> --}}
                            </div>
                        </form>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm w-100">
                        <div class="block">
                            <!-- Classic -->
                            <div class="" id="search-classic">
                                <div id="searchResult" class="row">

                                </div>
                            </div>
                            <!-- END Classic -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/numberstowords/numberstowords.min.js') }}"></script>
    {{-- <script src="{{asset('js/plugins/datatables-buttons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-jszip/jszip.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-pdfmake/pdfmake.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-pdfmake/vfs_fonts.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons/buttons.print.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons/buttons.html5.min.js')}}"></script> --}}
    {{-- <script src="{{asset('js/codebase.app.min.js')}}"></script> --}}

    <script>

            // setTimeout(function() {
            //     document.querySelector('.fl-container').classList.remove('fl-show');
            // }, 2000);

            function showToast(icon, title) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
    
                Toast.fire({
                    icon: icon,
                    title: title,
                    width: '27em'
                });
            }

            function pesoFormat(amount){
                let formattedNumber = new Intl.NumberFormat(
                    'en-PH', {
                        style: 'currency',
                        currency: 'PHP'
                    }).format(amount);

                    return formattedNumber
            }
    
    
            function showDialogConfirm(title = "Approve?", text = "Are you sure you want to approved this Tools?",
                confirmTitle = "Approved!", confirmTxt = "Items Approved Successfully.") {
                const dialogConfirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });
                dialogConfirm.fire({
                    title: title,
                    text: text,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Back",
                    reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        dialogConfirm.fire({
                            title: confirmTitle,
                            text: confirmTxt,
                            icon: "success"
                        });
                    } else if (
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        // dialogConfirm.fire({
                        //     title: "Cancelled",
                        //     text: "Your imaginary file is safe :)",
                        //     icon: "error"
                        // });
                    }
                });
            }
    
            $("#searchTools").keyup(function() {
                const searchVal = $(this).val();
    
                $.ajax({
                    url: '{{ route('search') }}',
                    method: 'post',
                    data: {
                        searchVal,
                        _token: "{{ csrf_token() }}"
                    },
                    success(response) {
                        $("#searchResult").empty()
    
                        $("#searchResult").append(response);
    
                    }
                })
            })

            const path = window.location.pathname.substring(1);

            $(".closeModalBtn").click(function(){
                if(path == "pages/request_for_receiving"){
                    // location.reload();
                    $("#requestFormLayout").empty();
                }else{
                    $("#requestFormLayout").empty();
                }
            })



            function logoutUser() {
                $.ajax({
                    url: '{{ route("logout") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        window.history.pushState(null, "", window.location.href);
                        window.onpopstate = function() {
                            window.history.pushState(null, "", window.location.href);
                        };
                        window.location.href = '{{ route("login") }}';
                    }
                });
            }

    </script>

    @yield('js')
</body>

</html>
