<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Uploads;
use App\Models\PulloutLogs;
use App\Models\TeisUploads;
use App\Models\TersUploads;
use App\Models\ProjectSites;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\RequestApprover;
use Yajra\DataTables\DataTables;
use App\Models\ToolsAndEquipment;
use App\Mail\DeliveryScheduleNotif;
use App\Models\PulloutRequestItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\ToolsAndEquipmentLogs;
use App\Models\ToolPictureReceivingUploads;

class PullOutController extends Controller
{

    public function fetch_ongoing_pullout()
    {

        $approvers = RequestApprover::where('status', 1)
            ->where('approver_id', Auth::user()->id)
            ->where('approver_status', 0)
            ->where('request_type', 3)
            ->get();

        $tool_approvers = collect();

        foreach ($approvers as $approver) {
            $current_approvers = collect();

            if ($approver->sequence == 1) {
                $current_approvers = RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
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

                $prev_approver = RequestApprover::where('status', 1)
                    ->where('request_id', $approver->request_id)
                    ->where('sequence', $prev_sequence)
                    ->where('request_type', 3)
                    ->first();

                if ($prev_approver && $prev_approver->approver_status == 1) {
                    $current_approvers = RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
                        ->select('pullout_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series')
                        ->where('pullout_requests.status', 1)
                        ->where('request_approvers.status', 1)
                        ->where('request_approvers.approver_id', Auth::user()->id)
                        // ->where('series', $series)
                        ->where('approver_status', 0)
                        ->where('request_type', 3)
                        ->get();
                }
            }

            // Merge the current approvers to the tool_approvers array
            $tool_approvers = $tool_approvers->merge($current_approvers)->unique('request_id');
        }


        if (Auth::user()->user_type_id == 4) {

            $tool_approvers = PulloutRequest::where('status', 1)->where('progress', 'ongoing')->get();
        }


        return DataTables::of($tool_approvers)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->pullout_number . '" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
            })

            ->addColumn('subcon', function ($row) {
                if (!$row->subcon) {
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                } else {
                    return $row->subcon;
                }
            })

            ->addColumn('approved_sched_date', function ($row) {
                if (!$row->approved_sched_date) {
                    return '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">--</span>';
                } else {
                    return $row->approved_sched_date;
                }
            })


            ->addColumn('action', function ($row) {

                $tool = PulloutRequest::where('status', 1)
                    ->where('pullout_requests.request_status', 'approved')
                    ->get();

                // $isApproved = $row->request_status == 'approved' ? 'disabled' : '';
    
                $user_type = Auth::user()->user_type_id;

                $have_sched = $row->approved_sched_date ? '' : 'disabled';
                $have_sched2 = $row->is_deliver ? 'd-none' : 'd-block';
                if ($user_type == 4) {
                    $action = '<div class="d-flex gap-2">
                <button data-bs-toggle="modal" data-requestnum="' . $row->pullout_number . '" data-trtype="pullout" data-bs-target="#trackRequestModal" type="button" class="trackBtn btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                <button ' . $have_sched . ' data-num="' . $row->pullout_number . '" data-type="' . $row->tr_type . '" type="button" class="deliverBtn ' . $have_sched2 . ' btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Deliver" data-bs-original-title="Deliver"><i class="fa fa-truck"></i></button>
                </div>';
                } else if ($user_type == 3 || $user_type == 5) {
                //     $action = '<div class="d-flex">
                // <button type="button" data-requestid="' . $row->request_id . '"  data-series="' . $row->series . '" data-id="' . $row->approver_id . '" class="pulloutApproveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve" data-bs-original-title="Approve"><i class="fa fa-check"></i></button>
                // </div>';
                $action = '';
                };
                return $action;
            })

            // ->setRowClass(function ($row) { 
            //     $tool = PulloutRequest::where('status', 1)->get();
            //     $status = collect($tool)->pluck('request_status')->toArray();

            //     return in_array('approved', $status) ? 'bg-gray' : '';

            // })

            ->rawColumns(['view_tools', 'subcon', 'approved_sched_date', 'action'])
            ->toJson();
    }



    public function ongoing_pullout_request_modal(Request $request)
    {
        if ($request->path == "pages/pullout_for_receiving") {
            $tools = PulloutRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'pullout_request_items.tool_id')
                ->leftjoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->leftJoin('pullout_requests', 'pullout_requests.id', 'pullout_request_items.pullout_request_id')
                ->select('tools_and_equipment.*', 'pullout_requests.reason', 'pullout_requests.pullout_number', 'pullout_request_items.tool_id', 'pullout_request_items.teis_no_dr_ar', 'warehouses.warehouse_name', 'pullout_request_items.tools_status as tool_status_eval', 'pullout_request_items.wh_tool_eval', 'pullout_request_items.checker', 'pullout_request_items.id as pri_id', 'pullout_request_items.item_status')
                ->where('pullout_request_items.status', 1)
                ->where('pullout_requests.status', 1)
                ->where('tools_and_equipment.status', 1)
                /// sa data na receiving
                // ->where('pullout_request_items.item_status', 0)
                ->where('pullout_request_items.pullout_number', $request->id)
                ->get();
        } else {
            $tools = PulloutRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'pullout_request_items.tool_id')
                ->leftjoin('warehouses', 'warehouses.id', 'tools_and_equipment.location')
                ->leftJoin('pullout_requests', 'pullout_requests.id', 'pullout_request_items.pullout_request_id')
                ->select('tools_and_equipment.*', 'pullout_requests.reason', 'pullout_requests.pullout_number', 'pullout_request_items.tool_id', 'pullout_request_items.teis_no_dr_ar', 'warehouses.warehouse_name', 'pullout_request_items.tools_status as tool_status_eval', 'pullout_request_items.wh_tool_eval', 'pullout_request_items.checker', 'pullout_request_items.id as pri_id', 'pullout_request_items.item_status')
                ->where('pullout_request_items.status', 1)
                ->where('pullout_requests.status', 1)
                ->where('tools_and_equipment.status', 1)
                ->where('pullout_request_items.status', 1)
                ->where('pullout_request_items.pullout_number', $request->id)
                ->get();
        }


        $count = 1;

        return DataTables::of($tools)

            ->addColumn('item_no', function () use (&$count) {
                return '<span style="display: block; text-align: center;">' . $count++ . '</span>';
            })

            ->addColumn('action', function ($row) use ($request) {
                $action = '
            <button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-alt-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Scan to received" data-bs-original-title="Scan to received"><i class="fa fa-file-circle-check"></i></button>
            ';

                if ($request->path == "pages/pullout_for_receiving") {
                    if ($row->item_status == 1) {
                        $action = '<div class="text-center"><span class="badge bg-success text-center">Served</span></div>';
                    } elseif ($row->item_status == 2) {
                        $action = '<div class="text-center"><span class="badge bg-danger">Not Served</span></div>';
                    } else {
                        if (Auth::user()->user_type_id == 2 && $request->path == 'pages/pullout_for_receiving') {
                            $action = '<div class="d-flex gap-2 justify-content-center align-items-center">
                    <button data-trtype="pullout" data-pri_id="' . $row->pri_id . '" data-number="' . $row->pullout_number . '" type="button" class="receivedBtn btn btn-sm btn-alt-success" data-bs-toggle="tooltip" aria-label="Receive Tool" data-bs-original-title="Receive Tool"><i class="fa fa-circle-check"></i></button>
                    <button data-trtype="pullout" data-pri_id="' . $row->pri_id . '" data-number="' . $row->pullout_number . '" type="button" class="notReceivedBtn btn btn-sm btn-alt-danger" data-bs-toggle="tooltip" aria-label="Not Serve" data-bs-original-title="Not Serve"><i class="fa fa-circle-xmark"></i></button>
                    </div>';
                        } else {
                            $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                        }

                        if ($request->path == 'pages/site_to_site_transfer') {
                            $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                        }
                    }
                }

                if ($request->path == "pages/pullout_completed" || $request->path == "pages/pullout_ongoing" || $request->path == "pages/approved_pullout") {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                }
                return $action;
            })

            ->addColumn('wh_eval', function ($row) use ($request) {
                if ($request->path == "pages/pullout_for_receiving") {

                    if ($row->wh_tool_eval) {
                        return '<div style="text-align:center">' . ucwords($row->wh_tool_eval) . '</div>';
                    } elseif ($row->item_status) {
                        return '';
                    } else {
                        return '  
                    <select class="whEval form-select">
                        <option value="" disabled selected>Select Status</option>
                        <option value="good">Good</option>
                        <option value="defective">Defective</option>
                    </select>
                ';
                    }

                }
            })

            ->addColumn('checker', function ($row) use ($request) {
                if ($request->path == "pages/pullout_for_receiving") {

                    if ($row->checker) {
                        return '<div style="text-align:center">' . $row->checker . '</div>';
                    } elseif ($row->item_status) {
                        return '';
                    } else {
                        return '
                        <input type="text"
                        class="form-control checker" name="checker"
                        placeholder="Enter checker of tools">
                ';
                    }

                };
            })

            ->addColumn('tools_status', function ($row) {
                $status = $row->tools_status;
                if ($status == 'good') {
                    $status = '<span class="badge bg-success">' . $status . '</span>';
                } else if ($status == 'repair') {
                    $status = '<span class="badge bg-warning">' . $status . '</span>';
                } else {
                    $status = '<span class="badge bg-danger">' . $status . '</span>';
                }
                return $status;
            })

            ->addColumn('new_tools_status', function ($row) {
                $status = $row->tool_status_eval;
                if ($status == 'good') {
                    $status = '<i class="fa fa-check d-block text-center"></i>';
                } else {
                    $status = '<div>&nbsp;</div>';
                }
                return $status;
            })

            ->addColumn('new_tools_status_defective', function ($row) {
                $status = $row->tool_status_eval;
                if ($status == 'defective') {
                    $status = '<i class="fa fa-check d-block text-center"></i>';
                } else {
                    $status = '<div>&nbsp;</div>';
                }
                return $status;
            })

            ->addColumn('empty_tools_status', function () {
                return '<div>&nbsp;</div>';
            })



            ->addColumn('warehouse_name', function ($row) {
                if ($row->current_site_id) {
                    // $location = ProjectSites::where('status', 1)->where('id', $row->current_site_id)->first();
                    return ProjectSites::where('status', 1)->where('id', $row->current_site_id)->value('project_location');
                } else {
                    return $row->warehouse_name;
                }
            })

            ->rawColumns(['tools_status', 'action', 'new_tools_status', 'new_tools_status_defective', 'checker', 'wh_eval', 'item_no'])
            ->toJson();
    }


    public function fetch_approved_pullout()
    {

        $pullout_tools = RequestApprover::leftjoin('pullout_requests', 'pullout_requests.id', 'request_approvers.request_id')
            ->leftjoin('users', 'users.id', 'request_approvers.approved_by')
            // ->leftJoin('users', function ($join) {
            //     $join->on('users.id', '=', \DB::raw('COALESCE(request_approvers.approved_by, request_approvers.can_be_view_by)'));
            // })
            ->select('pullout_requests.*', 'request_approvers.id as approver_id', 'request_approvers.request_id', 'request_approvers.series', 'request_approvers.approved_by', 'users.fullname', 'request_approvers.date_approved')
            ->where('pullout_requests.status', 1)
            ->where('request_approvers.status', 1)
            // ->where(function ($query) {
            //     $query->where('request_approvers.approved_by', Auth::user()->id)
            //         ->orWhere('request_approvers.can_be_view_by', Auth::user()->id);
            // })
            ->where('request_approvers.can_be_view_by', Auth::user()->id)
            ->where('request_type', 3)
            ->get();

        // return $pullout_tools;

        return DataTables::of($pullout_tools)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->pullout_number . '" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
            })
            ->addColumn('approver_name', function ($row) {

                if (!$row->fullname) {
                    return Auth::user()->fullname;
                } else {
                    return $row->fullname;
                }

            })
            ->addColumn('action', function ($row) {

                $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';

                return $action;
            })

            ->rawColumns(['view_tools', 'action', 'approver_name'])
            ->toJson();
    }




    public function fetch_pullout_request(Request $request)
    {



        if ($request->path == "pages/pullout_for_receiving") {
            //! lumaa
            // $request_tools = PulloutRequest::leftJoin('users', 'users.id', 'pullout_requests.user_id')
            // ->leftJoin('ters_uploads', 'ters_uploads.pullout_number', '=', 'pullout_requests.pullout_number')
            // ->select('pullout_requests.*', 'users.fullname')
            // ->where('pullout_requests.status', 1)
            // ->where('users.status', 1)
            // ->where(function ($query) {
            //     $query->where('progress', 'ongoing')
            //         ->orWhereNull('ters_uploads.pullout_number');
            // })
            // ->where('request_status', 'approved')
            // ->get();

            $request_tools = PulloutRequest::leftJoin('users', 'users.id', 'pullout_requests.user_id')
                ->leftJoin('ters_uploads', 'ters_uploads.pullout_number', '=', 'pullout_requests.pullout_number')
                ->select('pullout_requests.*', 'users.fullname')
                ->where('pullout_requests.status', 1)
                ->where('users.status', 1)
                ->where('request_status', 'approved')
                ->where(function ($query) {
                    $query->where('progress', 'ongoing')
                        ->orWhereNull('ters_uploads.pullout_number');
                })
                ->distinct()
                ->orderBy('ters_uploads.created_at', 'desc')
                ->whereNotNull('is_deliver')
                ->get();

        } else {
            $request_tools = PulloutRequest::leftjoin('users', 'users.id', 'pullout_requests.user_id')
                ->select('pullout_requests.*', 'users.fullname')
                ->where('pullout_requests.status', 1)
                ->where('users.status', 1)
                ->where('progress', 'ongoing')
                ->where('request_status', 'approved')
                ->whereNull('is_deliver')
                ->get();
        }
        // $request_tools = App\Models\TransferRequest::leftjoin('teis_uploads','teis_uploads.teis_number','transfer_requests.teis_number')
        // ->select('transfer_requests.teis_number','daf_status','request_status','subcon','customer_name','project_name','project_code','project_address', 'date_requested', 'transfer_requests.tr_type')
        // ->where('transfer_requests.status', 1)
        // // ->where('teis_uploads.status', 1)
        // ->where('progress', 'ongoing')
        // ->where('request_status', 'approved')
        // ->whereNull('teis_uploads.teis_number');

        return DataTables::of($request_tools)

            ->setRowClass(function ($row) use ($request) {
                if ($request->path != "pages/pullout_for_receiving") {
                    $ids = PulloutRequest::leftjoin('users', 'users.id', 'pullout_requests.user_id')
                        ->select('pullout_requests.*', 'users.fullname')
                        ->where('pullout_requests.status', 1)
                        ->where('users.status', 1)
                        ->where('progress', 'ongoing')
                        ->where('request_status', 'approved')
                        ->whereNotNull('approved_sched_date')
                        ->pluck('id')
                        ->toArray();

                    return in_array($row->id, $ids) ? 'bg-gray' : '';
                }
            })

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->pullout_number . '" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="teisNumber btn text-primary fs-6 d-block me-auto">View</button>';
            })
            ->addColumn('action', function ($row) use ($request) {

                $pri = PulloutRequestItems::where('status', 1)
                ->where('pullout_request_id', $row->id)
                ->pluck('req_num', 'tool_id')
                ->toArray();

                $priJson = htmlspecialchars(json_encode($pri), ENT_QUOTES, 'UTF-8');
                
                // $user_type = Auth::user()->user_type_id;
                $ters_uploads = TersUploads::where('status', 1)
                    ->where('tr_type', 'pullout')->get();

                $ters_numbers = collect($ters_uploads)->pluck('pullout_number')->toArray();

                $have_ters = in_array($row->pullout_number, $ters_numbers) ? 'disabled' : '';

                $pui = PulloutRequestItems::where('status', 1)->where('pullout_number', $row->pullout_number)->pluck('item_status')->toArray();

                $is_tools_received = in_array(0, $pui) ? 'disabled' : '';

                if ($request->path == "pages/pullout_for_receiving") {
                    $action = '<div class="d-flex align-items-center justify-content-center">
                        <button ' . $have_ters . ' ' . $is_tools_received . ' data-pulloutnum="' . $row->pullout_number . '" data-jsondata="' .$priJson. '" data-type="pullout" data-bs-toggle="modal" data-bs-target="#uploadTers" type="button" class="uploadTersBtn btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-upload"></i></button>
                        ';
                } else {
                    $action = '<div class="d-flex align-items-center gap-2">
                <button id="addSchedBtn" data-pulloutnum="' . $row->pullout_number . '" data-pe="' . $row->fullname . '" data-location="' . $row->project_address . '" data-pickupdate="' . $row->pickup_date . '" data-bs-toggle="modal" data-bs-target="#addSched" type="button" class="btn btn-sm btn-secondary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Add Schedule" data-bs-original-title="Add Schedule"><i class="fa fa-calendar-plus"></i></button>
                ';
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
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->pullout_number)->where('tr_type', $row->tr_type)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' . $item['uploads']['name'] . '">
                    <span>TERS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->rawColumns(['view_tools', 'action', 'ters'])
            ->toJson();
    }

    public function tobe_approve_tools(Request $request)
    {

        $tools = RequestApprover::find($request->id);

        $tools->approver_status = 1;
        $tools->date_approved = Carbon::now();
        $tools->approved_by = Auth::user()->id;
        // sinama dahil nagbago na, parehas na sila dapat mag approved
        $tools->can_be_view_by = $tools->approver_id;
        $tools->update();


        $last_sequence = RequestApprover::where('status', 1)
            ->where('request_id', $request->requestId)
            ->where('request_type', 3)
            ->orderBy('sequence', 'desc')
            ->value('sequence');

        // $tobeApproveTools->approver_status = 1;
        // $tobeApproveTools->can_be_view_by = Auth::user()->id;
        // $tobeApproveTools->date_approved = Carbon::now();
        // $tobeApproveTools->update();

        // $tools->can_be_view_by = $tobeApproveTools->approver_id;
        // $tools->update();

        if ($tools->sequence == $last_sequence) {
            $pullout_request = PulloutRequest::find($request->requestId);
            $pullout_request->request_status = "approved";

            $pullout_request->update();
        }


        /// for logs

        $request_number = PulloutRequest::where('status', 1)->where('id', $request->requestId)->value('pullout_number');

        if ($tools->sequence == 1) {
            $sequence = 'First approver ';
        } elseif ($tools->sequence == 2) {
            $sequence = 'Second approver ';
        } else {
            $sequence = '';
        }

        PulloutLogs::create([
            'page' => 'pullout_ongoing',
            'request_number' => $request_number,
            'title' => 'Approve Request',
            'message' => $sequence . Auth::user()->fullname . ' ' . 'approved the request.',
            'approver_name' => Auth::user()->fullname,
            'action' => 2,
        ]);

    }



    public function fetch_completed_pullout()
    {
        $pullout_tools = PulloutRequest::leftjoin('request_approvers', 'request_approvers.request_id', 'pullout_requests.id')
            ->select('pullout_requests.*', 'request_approvers.date_approved')
            ->where('request_approvers.status', 1)
            ->where('pullout_requests.status', 1)
            ->where('request_approvers.request_type', 3)
            ->where('progress', 'completed')
            ->distinct('request_id')
            ->get();

        // return $pullout_tools;

        return DataTables::of($pullout_tools)

            ->addColumn('view_tools', function ($row) {

                return $view_tools = '<button data-id="' . $row->pullout_number . '" data-bs-toggle="modal" data-bs-target="#ongoingPulloutRequestModal" class="pulloutNumber btn text-primary fs-6 d-block">View</button>';
            })
            ->addColumn('ters', function ($row) {
                $ters_uploads = TersUploads::with('uploads')->where('status', 1)->where('pullout_number', $row->pullout_number)->where('tr_type', $row->tr_type)->where('status', 1)->get()->toArray();
                $uploads_file = [];
                $uploads_file = '<div class="row mx-auto">';
                foreach ($ters_uploads as $item) {

                    $uploads_file .= '<div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                    <a target="_blank" class="img-link img-link-zoom-in img-thumb img-lightbox" href="' . asset('uploads/ters_form') . '/' . $item['uploads']['name'] . '">
                    <span>TERS.pdf</span>
                    </a>
                </div>';

                }
                $uploads_file .= '</div>';
                return $uploads_file;
            })
            ->addColumn('action', function ($row) {

                $user_type = Auth::user()->user_type_id;


                if ($user_type == 4) {
                    $action = '<button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                ';
                } else if ($user_type == 3 || $user_type == 5) {
                    $action = '<div class="d-flex"><button data-bs-toggle="modal" data-bs-target="#" type="button" class="btn btn-sm btn-success d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Track" data-bs-original-title="Track"><i class="fa fa-map-location-dot"></i></button>
                <button type="button" data-requestid="' . $row->request_id . '"  data-series="' . $row->series . '" data-id="' . $row->approver_id . '" class="pulloutApproveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve" data-bs-original-title="Approve"><i class="fa fa-check"></i></button>
                </div>';
                } else {
                    $action = '<span class="mx-auto fw-bold text-secondary" style="font-size: 14px; opacity: 65%">No Action</span>';
                };
                return $action;
            })

            ->rawColumns(['view_tools', 'ters', 'action'])
            ->toJson();
    }

    public function fetch_sched_date()
    {
        $pullout_request = PulloutRequest::leftjoin('users', 'users.id', 'pullout_requests.user_id')
            ->select('pullout_requests.pullout_number', 'pullout_requests.project_address', 'pullout_requests.approved_sched_date', 'pullout_requests.contact_number', 'users.fullname', 'pullout_requests.project_name', 'pullout_requests.client')
            ->where('pullout_requests.status', 1)->whereNotNull('approved_sched_date')->get();
        return $pullout_request;
    }

    public function add_schedule(Request $request)
    {
        $pullout_request = PulloutRequest::where('status', 1)->where('pullout_number', $request->pulloutNum)->first();

        $user_info = User::select('fullname', 'email')->where('status', 1)->where('id', $pullout_request->user_id)->first();

        if ($pullout_request->pickup_date !== $request->pickupDate) {
            $pulloutDate = new DateTime($pullout_request->pickup_date);
            $requestedDate = new DateTime($request->pickupDate);

            

            if ($requestedDate > $pulloutDate) {
                // Delayed
                $change_type =  "delayed";
            } else {
                // Advanced
                $change_type = "advanced";
            }

            $formated_date_original = $pulloutDate->format('F j, Y');
            $formated_date_new = $requestedDate->format('F j, Y');


            $mail_data = ['requestor_name' => $user_info->fullname,  'pullout_number' => $pullout_request->pullout_number, 'original_pickup_date' => $formated_date_original, 'new_pickup_date' => $formated_date_new, 'change_type' => $change_type];
        
            Mail::to($user_info->email)->send(new DeliveryScheduleNotif($mail_data));

        }

        $pullout_request->approved_sched_date = $request->pickupDate;

        $pullout_request->update();
    }

    public function received_pullout_tools(Request $request)
    {
        if ($request->multi) {

            $all_data = json_decode($request->dataArray);

            foreach ($all_data as $data) {

                $received_tools = PulloutRequestItems::find($data->pri_id);

                $received_tools->item_status = 1;
                $received_tools->wh_tool_eval = $data->tool_eval;

                $received_tools->update();

                $tr = PulloutRequest::where('status', 1)->where('id', $received_tools->pullout_request_id)->first();
                $project_site = ProjectSites::where('status', 1)->where('project_code', $tr->project_code)->first();


                $tools = ToolsAndEquipment::where('status', 1)->where('id', $received_tools->tool_id)->first();

                $is_tool_eval_null = $data->tool_eval == null ? $data->user_eval : $data->tool_eval;

                $tools->tools_status = $is_tool_eval_null;
                $tools->wh_ps = 'wh';
                $tools->current_pe = null;
                $tools->current_site_id = null;

                $tools->update();

            }

        } else {
            $received_tools = PulloutRequestItems::find($request->priId);

            if ($request->has('photo')) {
                $toolPicture = $request->photo;

                // Decode base64 image
                $image = explode(',', $toolPicture)[1]; // Remove "data:image/jpeg;base64,"
                $image = base64_decode($image);

                // Generate a unique file name
                $pic_name = mt_rand(111111, 999999) . date('YmdHms') . '.jpg';

                // Save the file to the desired directory
                $uploadPath = public_path('uploads/tool_picture_receiving_uploads/');
                $filePath = $uploadPath . $pic_name;

                // Ensure directory exists
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                file_put_contents($filePath, $image);

                $uploads = Uploads::create([
                    'name' => $pic_name,
                    'original_name' => $pic_name,
                    'extension' => 'jpg',
                ]);

                ToolPictureReceivingUploads::create([
                    'request_item_id' => $received_tools->id,
                    'tool_id' => $received_tools->tool_id,
                    'upload_id' => $uploads->id,
                    'user_id' => Auth::id(),
                    'tr_type' => 'pullout',
                ]);
            }


            $received_tools->item_status = 1;
            $received_tools->wh_tool_eval = $request->whEval;
            $received_tools->checker = ucwords(strtolower($request->checker));
            $received_tools->update();

            $tools = ToolsAndEquipment::where('status', 1)->where('id', $received_tools->tool_id)->first();

            $tools->wh_ps = 'wh';
            $tools->current_pe = null;
            $tools->current_site_id = null;
            $tools->tools_status = $request->whEval;

            $tools->update();

            // for logs
            ToolsAndEquipmentLogs::create([
                'tool_id' => $received_tools->tool_id,
                'pe' => Auth::id(),
                'tr_type' => 'pullout',
                'remarks' => 'Received in warehouse',
            ]);

        }

        // $pri = PulloutRequestItems::where('status', 1)
        // ->where('pullout_number', $received_tools->pullout_number)
        // ->get();

        // $item_status = collect($pri)->pluck('item_status')->toArray();

        // $allStatus = array_unique($item_status);

        // if(count($allStatus) == 1){
        //     $tool_requests = PulloutRequest::find($pri[0]->pullout_request_id);

        //     $tool_requests->progress = 'completed';
        //     $tool_requests->update();
        // }
    }

    public function pullout_not_received(Request $request)
    {
        $received_tools = PulloutRequestItems::find($request->priId);
        $received_tools->item_status = 2;
        $received_tools->update();
    }

    public function fetch_current_site(Request $request)
    {
        return ProjectSites::where('status', 1)
            ->where('id', $request->currentSiteId)
            ->first();
    }


}
