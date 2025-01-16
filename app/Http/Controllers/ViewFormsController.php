<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ToolPictures;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\RequestApprover;
use App\Models\TransferRequest;
use App\Models\PsTransferRequests;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;
use App\Models\PsTransferRequestItems;

class ViewFormsController extends Controller
{
    // public function view_transfer_request(Request $request){

    //     if($request->type == 'rfteis'){
    //         $request_tools = TransferRequest::select('id', 'pe', 'teis_number as request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
    //             ->where('status', 1)
    //             ->where('progress', 'ongoing')
    //             ->where('teis_number', $request->id)
    //             ->first();
    //     }else{
    //         $request_tools = PsTransferRequests::select('id','user_id', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
    //             ->where('status', 1)
    //             ->where('progress', 'ongoing')
    //             ->where('request_number', $request->id)
    //             ->first();
    //     }

    //     if ($request->path == 'pages/request_for_receiving') {

    //         if($request->type == 'rfteis'){
    //             $request_tools = TransferRequest::select('id','pe', 'teis_number as request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver')
    //             ->where('status', 1)
    //             ->where('progress', 'ongoing')
    //             ->where('request_status', 'approved')
    //             ->where('teis_number', $request->id)
    //             ->whereNotNull('is_deliver')
    //             ->first();
    //         }else{
    //             $request_tools = PsTransferRequests::select('id','user_id','request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver')
    //             ->where('status', 1)
    //             ->where('progress', 'ongoing')
    //             ->where('request_status', 'approved')
    //             ->where('request_number', $request->id)
    //             ->whereNotNull('is_deliver')
    //             ->first(); 
    //         }


    //     }

    //     if($request->type == 'rfteis'){
    //         $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
    //         ->select('request_approvers.*', 'users.fullname')
    //         ->where('request_approvers.status', 1)
    //         ->where('users.status', 1)
    //         ->where('request_id', $request_tools->id)
    //         ->where('request_type', 1)
    //         ->orderBy('sequence', 'asc')
    //         ->get();
    //     }else{
    //         $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
    //         ->select('request_approvers.*', 'users.fullname')
    //         ->where('request_approvers.status', 1)
    //         ->where('users.status', 1)
    //         ->where('request_id', $request_tools->id)
    //         ->where('request_type', 2)
    //         ->orderBy('sequence', 'asc')
    //         ->get();
    //     }


    //     if($request->type == 'rfteis'){
    //         $requestor = User::where('status', 1)->where('id', $request_tools->pe)->value('fullname');
    //     }else{
    //          $requestor = User::where('status', 1)->where('id', $request_tools->user_id)->value('fullname');
    //     }


    //     if($request->type == 'rfteis'){
    //         //PM
    //         $firstApprover = $approvers[0]->approver_status;
    //         if($firstApprover){
    //             $name_first = $approvers[0]->fullname;
    //             $date_approved_first = $approvers[0]->date_approved;
    //         }else{
    //             $name_first = '<span class="text-warning">Pending</span>';
    //             $date_approved_first = '--';
    //         }
    //         // OM
    //         $secondApprover = $approvers[1]->approver_status;
    //         if($secondApprover){
    //             $name_second = $approvers[1]->fullname;
    //             $date_approved_second = $approvers[1]->date_approved;
    //         }else{
    //             $name_second = '<span class="text-warning">Pending</span>';
    //             $date_approved_second = '--';
    //         }
    //         // CNC
    //         $thirdApprover = $approvers[2]->approver_status;
    //         if($thirdApprover){
    //             $name_third = $approvers[2]->fullname;
    //             $date_approved_third = $approvers[2]->date_approved;
    //         }else{
    //             $name_third = '<span class="text-warning">Pending</span>';
    //             $date_approved_third = '--';
    //         }
    //         // Warehouse Manager
    //         $fourthApprover = $approvers[3]->approver_status;
    //         if($fourthApprover){
    //             $name_fourth = $approvers[3]->fullname;
    //             $date_approved_fourth = $approvers[3]->date_approved;
    //         }else{
    //             $name_fourth = '<span class="text-warning">Pending</span>';
    //             $date_approved_fourth = '--';
    //         }

    //         $html = '
    //             <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                             <h6 style="">prepared by</h6>
    //                             <div style="text-align: center; padding-top: 10px;">
    //                                 <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                     '.$requestor.'</p>
    //                                 <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PROJECT ENGINEER</p>
    //                             </div>
    //                         </div>
    //                         <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                             <div class="d-flex justify-content-between">
    //                                 <h6 style="">Noted by</h6>
    //                                 <div class="d-flex">
    //                                     <h6 style="">'.$date_approved_first.'</h6>
    //                                     /
    //                                     <h6 style="">'.$date_approved_second.'</h6>
    //                                 </div>
    //                             </div>
    //                             <div style="text-align: center; padding-top: 10px;">
    //                                 <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                     '.$name_first.' / '.$name_second.'</p>
    //                                 <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PID TEAM LEADER/OPERATIONS
    //                                     MANAGER</p>

    //                             </div>
    //                         </div>
    //                         <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                             <div class="d-flex justify-content-between">
    //                                 <h6 style="">approved by</h6>
    //                                 <div class="d-flex">
    //                                     <h6 style="">'.$date_approved_third.'</h6>
    //                                 </div>
    //                             </div>
    //                             <div style="text-align: center; padding-top: 10px;">
    //                                 <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                     '.$name_third.'</p>
    //                                 <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">CNC MANAGER/FINANCE MANAGER
    //                                 </p>
    //                             </div>
    //                         </div>
    //                         <div style="padding-inline: 3px; width: 100%">
    //                             <div class="d-flex justify-content-between">
    //                                 <h6 style="">verified by</h6>
    //                                 <div class="d-flex">
    //                                     <h6 style="">'.$date_approved_fourth.'</h6>
    //                                 </div>
    //                             </div>
    //                             <div style="text-align: center; padding-top: 10px;">
    //                                 <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                     '.$name_fourth.'</p>
    //                                 <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">WAREHOUSE MANAGER</p>
    //                             </div>
    //                         </div>
    //         ';
    //     }else{
    //          //PM ng nanghihiram
    //          $firstApprover = $approvers[0]->approver_status;
    //          if($firstApprover){
    //              $name_first = $approvers[0]->fullname;
    //              $date_approved_first = $approvers[0]->date_approved;
    //          }else{
    //              $name_first = '<span class="text-warning">Pending</span>';
    //              $date_approved_first = '--';
    //          }
    //          // OM ng nanghihiram
    //          $secondApprover = $approvers[1]->approver_status;
    //          if($secondApprover){
    //              $name_second = $approvers[1]->fullname;
    //              $date_approved_second = $approvers[1]->date_approved;
    //          }else{
    //              $name_second = '<span class="text-warning">Pending</span>';
    //              $date_approved_second = '--';
    //          }
    //          // pe na hinihiraman
    //          $thirdApprover = $approvers[2]->approver_status;
    //          if($thirdApprover){
    //              $name_third = $approvers[2]->fullname;
    //              $date_approved_third = $approvers[2]->date_approved;
    //          }else{
    //              $name_third = '<span class="text-warning">Pending</span>';
    //              $date_approved_third = '--';
    //          }
    //          // PM ng hinihiraman
    //          $fourthApprover = $approvers[3]->approver_status;
    //          if($fourthApprover){
    //              $name_fourth = $approvers[3]->fullname;
    //              $date_approved_fourth = $approvers[3]->date_approved;
    //          }else{
    //              $name_fourth = '<span class="text-warning">Pending</span>';
    //              $date_approved_fourth = '--';
    //          }
    //          // OM ng hinihiraman
    //          $fifthApprover = $approvers[4]->approver_status;
    //          if($fifthApprover){
    //              $name_fifth = $approvers[4]->fullname;
    //              $date_approved_fifth = $approvers[4]->date_approved;
    //          }else{
    //              $name_fifth = '<span class="text-warning">Pending</span>';
    //              $date_approved_fifth = '--';
    //          }

    //          $html = '

    //                 <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                         <h6 style="">prepared by</h6>
    //                         <div style="text-align: center; padding-top: 10px;">
    //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                 a</p>
    //                             <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PROJECT ENGINEER</p>
    //                         </div>
    //                     </div>
    //                     <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                         <div class="d-flex justify-content-between">
    //                             <h6 style="visibility: hidden;">.</h6>
    //                             <div class="d-flex">
    //                                 <h6 style="">'.$date_approved_second.'</h6>
    //                             </div>
    //                         </div>
    //                         <div style="text-align: center; padding-top: 10px;">
    //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                 '.$name_second.'</p>
    //                             <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PPROJECT MANAGER</p>

    //                         </div>
    //                     </div>
    //                     <div style="padding-inline: 3px; width: 100%">
    //                         <div class="d-flex justify-content-between">
    //                             <h6 style="">approved by</h6>
    //                             <div class="d-flex">
    //                                 <h6 style="">'.$date_approved_third.'</h6>
    //                             </div>
    //                         </div>
    //                         <div style="text-align: center; padding-top: 10px;">
    //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                 '.$name_third.'</p>
    //                             <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">OPERATIONS MANAGER</p>
    //                         </div>
    //                     </div>
    //                 </div>
    //                 <div class="borders">
    //                     <h3 style="margin-block: 7px; margin-left: 5px; font-size: 11px;">By signing on the space provided below, i hereby accept the transfer of possession and responsibility for the tools and equipment listed above</h3>
    //                 </div>
    //                 <div class="borders" id="printFooter" style="display: flex;">
    //                     <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                         <h6 style="">prepared by</h6>
    //                         <div style="text-align: center; padding-top: 10px;">
    //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                 a</p>
    //                             <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PROJECT ENGINEER</p>
    //                         </div>
    //                     </div>
    //                     <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                         <h6 style="visibility: hidden;">.</h6>
    //                         <div style="text-align: center; padding-top: 10px;">
    //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                 a</p>
    //                             <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PPROJECT MANAGER</p>

    //                         </div>
    //                     </div>
    //                     <div style="padding-inline: 3px; width: 100%">
    //                         <h6 style="">approved by</h6>
    //                         <div style="text-align: center; padding-top: 10px;">
    //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                 a</p>
    //                             <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">OPERATIONS MANAGER</p>
    //                         </div>
    //                     </div>
    //                 </div>



    //              <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                              <h6 style="">prepared by</h6>
    //                              <div style="text-align: center; padding-top: 10px;">
    //                                  <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                      '.$requestor.'</p>
    //                                  <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PROJECT ENGINEER</p>
    //                              </div>
    //                          </div>
    //                          <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                              <div class="d-flex justify-content-between">
    //                                  <h6 style="">Noted by</h6>
    //                                  <div class="d-flex">
    //                                      <h6 style="">'.$date_approved_first.'</h6>
    //                                      /
    //                                      <h6 style="">'.$date_approved_second.'</h6>
    //                                  </div>
    //                              </div>
    //                              <div style="text-align: center; padding-top: 10px;">
    //                                  <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                      '.$name_first.' / '.$name_second.'</p>
    //                                  <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PID TEAM LEADER/OPERATIONS
    //                                      MANAGER</p>

    //                              </div>
    //                          </div>
    //                          <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
    //                              <div class="d-flex justify-content-between">
    //                                  <h6 style="">approved by</h6>
    //                                  <div class="d-flex">
    //                                      <h6 style="">'.$date_approved_third.'</h6>
    //                                  </div>
    //                              </div>
    //                              <div style="text-align: center; padding-top: 10px;">
    //                                  <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                      '.$name_third.'</p>
    //                                  <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">CNC MANAGER/FINANCE MANAGER
    //                                  </p>
    //                              </div>
    //                          </div>
    //                          <div style="padding-inline: 3px; width: 100%">
    //                              <div class="d-flex justify-content-between">
    //                                  <h6 style="">verified by</h6>
    //                                  <div class="d-flex">
    //                                      <h6 style="">'.$date_approved_fourth.'</h6>
    //                                  </div>
    //                              </div>
    //                              <div style="text-align: center; padding-top: 10px;">
    //                                  <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
    //                                      '.$name_fourth.'</p>
    //                                  <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">WAREHOUSE MANAGER</p>
    //                              </div>
    //                          </div>
    //          ';
    //     }




    //     // return $request_tools;
    //     if($request->type == 'rfteis'){
    //         return view('pages.view_rfteis', compact('request_tools', 'html'))->render();
    //     }else{
    //         return view('pages.view_rttte', compact('request_tools', 'html'))->render();
    //     }

    // }


    public function view_transfer_request(Request $request)
    {

        if ($request->type == 'rfteis') {
            $request_tools = TransferRequest::select('id', 'pe', 'teis_number as request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'wh_location')
                ->where('status', 1)
                // ->where('progress', 'ongoing')
                ->where('teis_number', $request->id)
                ->first();
        } else {
            $request_tools = PsTransferRequests::select('id', 'user_id', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
                ->where('status', 1)
                // ->where('progress', 'ongoing')
                ->where('request_number', $request->id)
                ->first();

            $tools_owner = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
                ->leftJoin('project_sites', 'project_sites.id', 'tools_and_equipment.current_site_id')
                ->leftJoin('users', 'users.id', 'tools_and_equipment.current_pe')
                ->select('users.fullname', 'project_sites.project_location', 'project_sites.project_name')
                ->where('tools_and_equipment.status', 1)
                ->where('ps_transfer_request_items.status', 1)
                ->where('project_sites.status', 1)
                ->where('users.status', 1)
                ->where('ps_transfer_request_id', $request_tools->id)
                ->first();

        }

        if ($request->path == 'pages/request_for_receiving') {

            if ($request->type == 'rfteis') {
                $request_tools = TransferRequest::select('id', 'pe', 'teis_number as request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver', 'wh_location')
                    ->where('status', 1)
                    ->where('progress', 'ongoing')
                    ->where('request_status', 'approved')
                    ->where('teis_number', $request->id)
                    ->whereNotNull('is_deliver')
                    ->first();
            } else {
                $request_tools = PsTransferRequests::select('id', 'user_id', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'is_deliver')
                    ->where('status', 1)
                    ->where('progress', 'ongoing')
                    ->where('request_status', 'approved')
                    ->where('request_number', $request->id)
                    ->whereNotNull('is_deliver')
                    ->first();
            }


        }

        if ($request->type == 'rfteis') {
            $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
                ->leftJoin('positions', 'positions.id', 'users.pos_id')
                ->select('request_approvers.*', 'users.fullname', 'positions.position')
                ->where('request_approvers.status', 1)
                ->where('users.status', 1)
                ->where('request_id', $request_tools->id)
                ->where('request_type', 1)
                ->orderBy('sequence', 'asc')
                ->get();
        } else {
            $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
                ->leftJoin('positions', 'positions.id', 'users.pos_id')
                ->select('request_approvers.*', 'users.fullname', 'positions.position')
                ->where('request_approvers.status', 1)
                ->where('users.status', 1)
                ->where('request_id', $request_tools->id)
                ->where('request_type', 2)
                ->orderBy('sequence', 'asc')
                ->get();
        }

        // return $approvers;


        if ($request->type == 'rfteis') {
            $requestor = User::where('status', 1)->where('id', $request_tools->pe)->value('fullname');
        } else {
            $requestor = User::where('status', 1)->where('id', $request_tools->user_id)->value('fullname');
        }


        if ($request->type == 'rfteis') {
            //Warehouse Manager
            $firstApprover = $approvers[0]->approver_status;
            if ($firstApprover) {
                $name_first = $approvers[0]->fullname;
                $date_approved_first = $approvers[0]->date_approved;
            } else {
                $name_first = '<span class="text-warning">Pending</span>';
                $date_approved_first = '--';
            }
            // PM
            $secondApprover = $approvers[1]->approver_status;
            if ($secondApprover) {
                $name_second = $approvers[1]->fullname;
                $date_approved_second = $approvers[1]->date_approved;
            } else {
                $name_second = '<span class="text-warning">Pending</span>';
                $date_approved_second = '--';
            }
            // OM
            $thirdApprover = $approvers[2]->approver_status;
            if ($thirdApprover) {
                $name_third = $approvers[2]->fullname;
                $date_approved_third = $approvers[2]->date_approved;
            } else {
                $name_third = '<span class="text-warning">Pending</span>';
                $date_approved_third = '--';
            }
            // CNC
            $fourthApprover = $approvers[3]->approver_status;
            if ($fourthApprover) {
                $name_fourth = $approvers[3]->fullname;
                $date_approved_fourth = $approvers[3]->date_approved;
            } else {
                $name_fourth = '<span class="text-warning">Pending</span>';
                $date_approved_fourth = '--';
            }

            $html = '
                <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                                <h6 style="">prepared by</h6>
                                <div style="text-align: center; padding-top: 10px;">
                                    <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                        ' . $requestor . '</p>
                                    <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PROJECT ENGINEER</p>
                                </div>
                            </div>
                            <div style="border-right: 1px solid black; padding-inline: 3px; width: 120%">
                                <div class="d-flex justify-content-between">
                                    <h6 style="">Noted by</h6>
                                    <div class="d-flex">
                                        <h6 style="">' . $date_approved_second . '</h6>
                                        /
                                        <h6 style="">' . $date_approved_third . '</h6>
                                    </div>
                                </div>
                                <div style="text-align: center; padding-top: 10px;">
                                    <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                        ' .  $name_second . ' / ' . $name_third . '</p>
                                    <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PID TEAM LEADER/OPERATIONS
                                        MANAGER</p>

                                </div>
                            </div>
                            <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                                <div class="d-flex justify-content-between">
                                    <h6 style="">approved by</h6>
                                    <div class="d-flex">
                                        <h6 style="">' . $date_approved_fourth . '</h6>
                                    </div>
                                </div>
                                <div style="text-align: center; padding-top: 10px;">
                                    <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                        ' . $name_fourth . '</p>
                                    <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">CNC MANAGER
                                    </p>
                                </div>
                            </div>
                            <div style="padding-inline: 3px; width: 100%">
                                <div class="d-flex justify-content-between">
                                    <h6 style="">verified by</h6>
                                    <div class="d-flex">
                                        <h6 style="">' . $date_approved_first . '</h6>
                                    </div>
                                </div>
                                <div style="text-align: center; padding-top: 10px;">
                                    <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                        ' . $name_first . '</p>
                                    <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">WAREHOUSE MANAGER</p>
                                </div>
                            </div>
            ';


            // $html = '
            //     <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
            //         <h6 style="">prepared by</h6>
            //         <div style="text-align: center; padding-top: 10px;">
            //             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
            //                 ' . $requestor . '</p>
            //             <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PROJECT ENGINEER</p>
            //         </div>
            //     </div>

            //     <div style="border-right: 1px solid black; padding-inline: 3px; width: 120%; position: relative;">
            //         ' . ($is_approved2 && $is_approved3 ? '<div style="opacity: .40; position: absolute; top: 60%; right: -20px; background-color: #28a745; color: white; padding: 5px 10px; font-size: 10px; font-weight: bold; transform: rotate(40deg); transform-origin: top right; width: 100px; text-align: center;">APPROVED</div>' : '') . '
            //         <div class="d-flex justify-content-between">
            //             <h6>Noted by</h6>
            //             <div class="d-flex">
            //                 <h6>' . $date_approved_second . '</h6> /
            //                 <h6>' . $date_approved_third . '</h6>
            //             </div>
            //         </div>
            //         <div style="text-align: center; padding-top: 10px;">
            //             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">' . $name_second . ' / ' . $name_third . '</p>
            //             <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PID TEAM LEADER/OPERATIONS MANAGER</p>
            //         </div>
            //     </div>

            //     <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%; position: relative;">
            //         ' . ($is_approved4 ? '<div style="position: absolute; top: 0; right: 0; background-color: #28a745; color: white; padding: 5px 10px; font-size: 10px; font-weight: bold; transform: rotate(45deg); transform-origin: top right; width: 100px; text-align: center;">APPROVED</div>' : '') . '
            //         <div class="d-flex justify-content-between">
            //             <h6>Approved by</h6>
            //             <div class="d-flex">
            //                 <h6>' . $date_approved_fourth . '</h6>
            //             </div>
            //         </div>
            //         <div style="text-align: center; padding-top: 10px;">
            //             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">' . $name_fourth . '</p>
            //             <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">CNC MANAGER</p>
            //         </div>
            //     </div>

            //     <div style="padding-inline: 3px; width: 100%; position: relative;">
            //         ' . ($is_approved1 ? '<div style="position: absolute; top: 0; right: 0; background-color: #28a745; color: white; padding: 5px 10px; font-size: 10px; font-weight: bold; transform: rotate(45deg); transform-origin: top right; width: 100px; text-align: center;">APPROVED</div>' : '') . '
            //         <div class="d-flex justify-content-between">
            //             <h6>Verified by</h6>
            //             <div class="d-flex">
            //                 <h6>' . $date_approved_first . '</h6>
            //             </div>
            //         </div>
            //         <div style="text-align: center; padding-top: 10px;">
            //             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">' . $name_first . '</p>
            //             <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">WAREHOUSE MANAGER</p>
            //         </div>
            //     </div>
            // ';
        } else {
            //PM ng nanghihiram
            $firstApprover = $approvers[0]->approver_status;
            if ($firstApprover) {
                $name_first = $approvers[0]->fullname;
                $date_approved_first = $approvers[0]->date_approved;
            } else {
                $name_first = '<span class="text-warning popoverInPending" style="cursor: pointer;" data-bs-toggle="popover" data-bs-animation="true" data-bs-placement="top" title="'.$approvers[0]->fullname.'">Pending</span>';
                $date_approved_first = '--';
            }
            // OM ng nanghihiram
            $secondApprover = $approvers[1]->approver_status;
            if ($secondApprover) {
                $name_second = $approvers[1]->fullname;
                $date_approved_second = $approvers[1]->date_approved;
            } else {
                $name_second = '<span class="text-warning popoverInPending" style="cursor: pointer;" data-bs-toggle="popover" data-bs-animation="true" data-bs-placement="top" title="'.$approvers[1]->fullname.'">Pending</span>';
                $date_approved_second = '--';
            }
            // pe na hinihiraman
            $thirdApprover = $approvers[2]->approver_status;
            if ($thirdApprover) {
                $name_third = $approvers[2]->fullname;
                $date_approved_third = $approvers[2]->date_approved;
            } else {
                $name_third = '<span class="text-warning popoverInPending" style="cursor: pointer;" data-bs-toggle="popover" data-bs-animation="true" data-bs-placement="top" title="'.$approvers[2]->fullname.'">Pending</span>';
                $date_approved_third = '--';
            }
            // PM ng hinihiraman
            $fourthApprover = $approvers[3]->approver_status;
            if ($fourthApprover) {
                $name_fourth = $approvers[3]->fullname;
                $date_approved_fourth = $approvers[3]->date_approved;
            } else {
                $name_fourth = '<span class="text-warning popoverInPending" style="cursor: pointer;" data-bs-toggle="popover" data-bs-animation="true" data-bs-placement="top" title="'.$approvers[3]->fullname.'">Pending</span>';
                $date_approved_fourth = '--';
            }
            // OM ng hinihiraman
            $fifthApprover = $approvers[4]->approver_status;
            if ($fifthApprover) {
                $name_fifth = $approvers[4]->fullname;
                $date_approved_fifth = $approvers[4]->date_approved;
            } else {
                $name_fifth = '<span class="text-warning popoverInPending" style="cursor: pointer;" data-bs-toggle="popover" data-bs-animation="true" data-bs-placement="top" title="'.$approvers[4]->fullname.'">Pending</span>';
                $date_approved_fifth = '--';
            }

            $html = '

                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">prepared by</h6>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $requestor . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PROJECT ENGINEER</p>
                            </div>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="visibility: hidden;">.</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_first . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $name_first . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PPROJECT MANAGER</p>

                            </div>
                        </div>
                        <div style="padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="">approved by</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_second . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $name_second . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">OPERATIONS MANAGER</p>
                            </div>
                        </div>
                    </div>
                    <div class="borders">
                        <h3 style="margin-block: 7px; margin-left: 5px; font-size: 11px;">By signing on the space provided below, i hereby accept the transfer of possession and responsibility for the tools and equipment listed above</h3>
                    </div>
                    <div class="borders" id="printFooter" style="display: flex;">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="">prepared by</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_third . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $name_third . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PROJECT ENGINEER</p>
                            </div>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="visibility: hidden;">.</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_fourth . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $name_fourth . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PPROJECT MANAGER</p>

                            </div>
                        </div>
                        <div style="padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="">approved by</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_fifth . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $name_fifth . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">OPERATIONS MANAGER</p>
                            </div>
                        </div>
                    </div>
             ';
        }
        $path = '';
        if($request->path){
            $path =  $request->path;
        }

        // return $request_tools;
        if ($request->type == 'rfteis') {
            return view('pages.view_rfteis', compact('request_tools', 'html', 'requestor', 'approvers', 'path'))->render();
        } else {
            return view('pages.view_rttte', compact('request_tools', 'html', 'requestor', 'tools_owner', 'approvers'))->render();
        }

    }
    public function rfteis_approvers_view(Request $request)
    {

        $request_tools = TransferRequest::select('id', 'pe', 'teis_number as request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type', 'disapproved_by', 'wh_location')
            ->where('status', 1)
            // ->where('progress', 'ongoing')
            ->where('teis_number', $request->id)
            ->first();

            // return $request_tools;

        /// kunin ang approvers ng specific na request 
        $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
            ->leftJoin('positions', 'positions.id', 'users.pos_id')
            ->select('request_approvers.*', 'users.fullname', 'positions.position')
            ->where('request_approvers.status', 1)
            ->where('users.status', 1)
            ->where('request_id', $request->trid)
            ->where('request_type', 1)
            ->orderBy('sequence', 'asc')
            ->get();

        /// kunin yung info object ng naka login na user 
        $loggedInApprover = $approvers->firstWhere(function ($approver) {
            return $approver->approver_id == Auth::id();
        });


        ///kunin kung sino ang nag request
        $requestor = User::where('status', 1)->where('id', $request->pe)->value('fullname');

        ///para lang gumitna ang approve btn haha
        $mx_auto = '';
        if ($approvers[0]->approver_id == Auth::id() || $approvers[3]->approver_id == Auth::id()) {
            $mx_auto = 'mx-auto';
        }

        $tools = TransferRequestItems::where('status', 1)->whereNull('is_remove')->where('transfer_request_id', $request->trid)->pluck('tool_id')->toArray();
        $items = json_encode($tools);

        // para to sa pe (para sa disapproved rfteis)
        $atr = '';
        if(Auth::user()->user_type_id !== 4){
            $atr = 'data-approverid="' . $loggedInApprover->id . '"';
        }

        $action = '<button type="button" data-requestumber="'.$request_tools->request_number.'" data-requestorid="' . $request->pe . '" data-toolid="' . $items . '" data-requestid="' . $request->trid . '"  '.$atr.'  class="approveBtn ' . $mx_auto . ' btn btn-sm btn-primary d-block js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check me-1"></i>Approve</button>';
        
        if(Auth::user()->user_type_id == 6 && $request->path == 'pages/rfteis'){
            $action = '
            <div class="d-flex gap-2 justify-content-center">
            <button type="button" data-requestumber="'.$request_tools->request_number.'" data-requestorid="' . $request->pe . '" data-toolid="' . $items . '" data-requestid="' . $request->trid . '"  data-approverid="' . $loggedInApprover->id . '" class="approveBtn btn btn-sm btn-primary d-block js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check me-1"></i>Approve</button>
            <button type="button" data-requestumber="'.$request_tools->request_number.'" data-requestorid="' . $request->pe . '" data-toolid="' . $items . '" data-requestid="' . $request->trid . '"  data-approverid="' . $loggedInApprover->id . '" class="disapproveBtn btn btn-sm btn-danger d-block js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Disapprove" data-bs-original-title="Disapprove"><i class="fa fa-xmark me-1"></i>Disapprove</button>
            </div>
            ';
        }

        if($request->path == 'pages/rfteis_disapproved'){
            // $disapproved_by = User::where('status', 1)->where('id', $request_tools->disapproved_by)->value('fullname')

            $action = '<span class="mx-auto fw-bold text-pulse" style="font-size: 14px;">Disapproved</span>';
        }

        //WAREHOUSE MANAGER    
        $firstApprover = $approvers[0]->approver_status;
        if ($firstApprover) {
            $name_first = $approvers[0]->fullname;
            $date_approved_first = $approvers[0]->date_approved;
        } else {
            $name_first = $action;
            $date_approved_first = '--';
        }
        // PM
        $secondApprover = $approvers[1]->approver_status;
        if ($secondApprover) {
            $name_second = $approvers[1]->fullname;
            $date_approved_second = $approvers[1]->date_approved;
        } else if ($approvers[0]->approver_status == 1 && $request->path == 'pages/rfteis') {
            $name_second = $action;
            $date_approved_second = '--';
        } else {
            $name_second = '<span class="text-warning">Pending</span>';
            $date_approved_second = '--';
        }
        // OM
        $thirdApprover = $approvers[2]->approver_status;
        if ($thirdApprover) {
            $name_third = $approvers[2]->fullname;
            $date_approved_third = $approvers[2]->date_approved;
        } else if ($approvers[1]->approver_status == 1 && $request->path == 'pages/rfteis') {
            $name_third = $action;
            $date_approved_third = '--';
        } else {
            $name_third = '<span class="text-warning">Pending</span>';
            $date_approved_third = '--';
        }
        // CNC
        $fourthApprover = $approvers[3]->approver_status;
        if ($fourthApprover) {
            $name_fourth = $approvers[3]->fullname;
            $date_approved_fourth = $approvers[3]->date_approved;
        } else if ($approvers[2]->approver_status == 1 && $request->path == 'pages/rfteis') {
            $name_fourth = $action;
            $date_approved_fourth = '--';
        } else {
            $name_fourth = '<span class="text-warning">Pending</span>';
            $date_approved_fourth = '--';
        }

        $html = '
            <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">prepared by</h6>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $requestor . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PROJECT ENGINEER</p>
                            </div>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="">Noted by</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_second . '</h6>
                                    /
                                    <h6 style="">' . $date_approved_third . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p><div class="d-flex mx-auto justify-content-center align-items-center" style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">' . $name_second . ' / ' . $name_third . '</div></p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">PID TEAM LEADER/OPERATIONS
                                    MANAGER</p>

                            </div>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="">approved by</h6>
                                <div class="d-flex">
                                    <h6 style="">' . $date_approved_fourth . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase; margin-inline: auto">
                                    ' . $name_fourth . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">CNC MANAGER
                                </p>
                            </div>
                        </div>
                        <div style="padding-inline: 3px; width: 100%">
                            <div class="d-flex justify-content-between">
                                <h6 style="">verified by</h6>
                                <div class="d-flex">
                                    <h6 style="">' .  $date_approved_first . '</h6>
                                </div>
                            </div>
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                    ' . $name_first . '</p>
                                <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">WAREHOUSE MANAGER</p>
                            </div>
                        </div>
        ';

        $path = '';
        return view('pages.view_rfteis', compact('request_tools', 'html', 'requestor', 'approvers', 'path'))->render();

    }


    public function rttte_approvers_view(Request $request)
    {

        $request_tools = PsTransferRequests::select('id', 'user_id', 'request_number', 'daf_status', 'request_status', 'subcon', 'customer_name', 'project_name', 'project_code', 'project_address', 'date_requested', 'tr_type')
            ->where('status', 1)
            // ->where('progress', 'ongoing')
            ->where('request_number', $request->id)
            ->first();

        //para sa picture ng tool, disable kapag di pa nakakaupload ng pic sa lahat ng items
        $picture = ToolPictures::leftjoin('uploads', 'uploads.id', 'upload_id')
            ->select('tool_pictures.tool_id')
            ->where('tool_pictures.status', 1)
            ->where('pstr_id', $request->id)
            ->orderBy('tool_pictures.created_at', 'desc')
            ->get()
            ->unique('tool_id');


        $tools_owner = PsTransferRequestItems::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ps_transfer_request_items.tool_id')
            ->leftJoin('project_sites', 'project_sites.id', 'tools_and_equipment.current_site_id')
            ->leftJoin('users', 'users.id', 'tools_and_equipment.current_pe')
            ->select('users.fullname', 'project_sites.project_location', 'project_sites.project_name')
            ->where('tools_and_equipment.status', 1)
            ->where('ps_transfer_request_items.status', 1)
            ->where('project_sites.status', 1)
            ->where('users.status', 1)
            ->where('ps_transfer_request_id', $request_tools->id)
            ->first();

        /// kunin ang approvers ng specific na request 
        $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
            ->leftJoin('positions', 'positions.id', 'users.pos_id')
            ->select('request_approvers.*', 'users.fullname', 'positions.position')
            ->where('request_approvers.status', 1)
            ->where('users.status', 1)
            ->where('request_id', $request->pstrid)
            ->where('request_type', 2)
            ->orderBy('sequence', 'asc')
            ->get();

            // return $approvers;

        /// kunin yung info object ng naka login na user 
        $loggedInApprover = $approvers->where(function ($approver) {
            return $approver->approver_id == Auth::id();
        })->sortBy('id')->values();

        if($loggedInApprover->count() > 1){
            if($loggedInApprover[0]->approver_status == 1){
                $ra_id = $loggedInApprover[1]->id;
            }else{
                $ra_id = $loggedInApprover[0]->id;
            }
        }else{
            $ra_id = $loggedInApprover[0]->id;
        }


        ///kunin kung sino ang nag request
        $requestor = User::where('status', 1)->where('id', $request->pe)->value('fullname');

        ///para lang gumitna ang approve btn haha

        $tools = PsTransferRequestItems::where('status', 1)->whereNull('is_remove')->where('ps_transfer_request_id', $request->pstrid)->pluck('tool_id')->toArray();
        $items = json_encode($tools);

        // return count($tools);
        // return $picture->count();

        ///check if all the tools have uploaded picture of tool
        if (Auth::id() == $request_tools->user_id) {
            $picAllUploaded = "";

        } else if(count($tools) != $picture->count()){
            $picAllUploaded = "disabled";
        }else {
            $picAllUploaded = "";
            /// para lang ma enable yung button kapag lahat na nalagyan
            $isAllUploaded = true;
        }

        $action = '<button type="button" data-requestnumber="'.$request_tools->request_number.'" data-requestorid="' . $request->pe . '" data-toolid="' . $items . '" data-requestid="' . $request->pstrid . '"  data-approverid="' . $ra_id . '" class="approveBtn mx-auto btn btn-sm btn-primary d-block js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check"></i></button>';

        $user_type = Auth::user()->user_type_id;

        //PM ng nanghihiram
        $firstApprover = $approvers[0]->approver_status;
        if ($firstApprover) {
            $name_first = $approvers[0]->fullname;
            $date_approved_first = $approvers[0]->date_approved;
        } else {
            $name_first = $action;
            if ($user_type == 4) {
                $name_first = '<span class="text-warning">Pending</span>';
            }
            $date_approved_first = '--';
        }
        // OM ng nanghihiram
        $secondApprover = $approvers[1]->approver_status;
        if ($secondApprover) {
            $name_second = $approvers[1]->fullname;
            $date_approved_second = $approvers[1]->date_approved;
        } else if ($approvers[0]->approver_status == 1 && $request->path == 'pages/site_to_site_transfer' && $user_type != 4) {
            $name_second = $action;
            $date_approved_second = '--';
        } else {
            $name_second = '<span class="text-warning">Pending</span>';
            $date_approved_second = '--';
        }
        // pe na hinihiraman
        $thirdApprover = $approvers[2]->approver_status;
        if ($thirdApprover) {
            $name_third = $approvers[2]->fullname;
            $date_approved_third = $approvers[2]->date_approved;
        } else if ($approvers[1]->approver_status == 1 && $request->path == 'pages/site_to_site_transfer') {
            $name_third = '<button id="peProceedBtn" data-requestnumber="'.$request_tools->request_number.'" type="button" ' . $picAllUploaded . ' data-requestorid="' . $request->pe . '" data-toolid="' . $items . '" data-requestid="' . $request->pstrid . '"  data-approverid="' . $ra_id . '" class="approveBtn mx-auto btn btn-sm btn-primary d-block js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approved" data-bs-original-title="Approved"><i class="fa fa-check"></i></button>';
            $date_approved_third = '--';
        } else {
            $name_third = '<span class="text-warning">Pending</span>';
            $date_approved_third = '--';
        }
        // PM ng hinihiraman
        $fourthApprover = $approvers[3]->approver_status;
        if ($fourthApprover) {
            $name_fourth = $approvers[3]->fullname;
            $date_approved_fourth = $approvers[3]->date_approved;
        } else if ($approvers[2]->approver_status == 1 && $request->path == 'pages/site_to_site_transfer' && $approvers[3]->approver_id == Auth::id()) {
            $name_fourth = $action;
            $date_approved_fourth = '--';
        } else {
            $name_fourth = '<span class="text-warning">Pending</span>';
            $date_approved_fourth = '--';
        }
        // OM ng hinihiraman
        $fifthApprover = $approvers[4]->approver_status;
        if ($fifthApprover) {
            $name_fifth = $approvers[4]->fullname;
            $date_approved_fifth = $approvers[4]->date_approved;
        } else if ($approvers[3]->approver_status == 1 && $request->path == 'pages/site_to_site_transfer' && $approvers[4]->approver_id == Auth::id()) {
            $name_fifth = $action;
            $date_approved_fifth = '--';
        } else {
            $name_fifth = '<span class="text-warning">Pending</span>';
            $date_approved_fifth = '--';
        }

        $html = '

              <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                      <h6 style="">prepared by</h6>
                      <div style="text-align: center; padding-top: 10px;">
                          <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                              ' . $requestor . '</p>
                          <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PROJECT ENGINEER</p>
                      </div>
                  </div>
                  <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                      <div class="d-flex justify-content-between">
                          <h6 style="visibility: hidden;">.</h6>
                          <div class="d-flex">
                              <h6 style="">' . $date_approved_first . '</h6>
                          </div>
                      </div>
                      <div style="text-align: center; padding-top: 10px;">
                          <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                              ' . $name_first . '</p>
                          <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PPROJECT MANAGER</p>

                      </div>
                  </div>
                  <div style="padding-inline: 3px; width: 100%">
                      <div class="d-flex justify-content-between">
                          <h6 style="">approved by</h6>
                          <div class="d-flex">
                              <h6 style="">' . $date_approved_second . '</h6>
                          </div>
                      </div>
                      <div style="text-align: center; padding-top: 10px;">
                          <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                              ' . $name_second . '</p>
                          <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">OPERATIONS MANAGER</p>
                      </div>
                  </div>
              </div>
              <div class="borders">
                  <h3 style="margin-block: 7px; margin-left: 5px; font-size: 11px;">By signing on the space provided below, i hereby accept the transfer of possession and responsibility for the tools and equipment listed above</h3>
              </div>
              <div class="borders" id="printFooter" style="display: flex;">
                  <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                      <div class="d-flex justify-content-between">
                          <h6 style="">prepared by</h6>
                          <div class="d-flex">
                              <h6 style="">' . $date_approved_third . '</h6>
                          </div>
                      </div>
                      <div style="text-align: center; padding-top: 10px;">
                          <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                              ' . $name_third . '</p>
                          <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PROJECT ENGINEER</p>
                      </div>
                  </div>
                  <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                      <div class="d-flex justify-content-between">
                          <h6 style="visibility: hidden;">.</h6>
                          <div class="d-flex">
                              <h6 style="">' . $date_approved_fourth . '</h6>
                          </div>
                      </div>
                      <div style="text-align: center; padding-top: 10px;">
                          <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                              ' . $name_fourth . '</p>
                          <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">PPROJECT MANAGER</p>

                      </div>
                  </div>
                  <div style="padding-inline: 3px; width: 100%">
                      <div class="d-flex justify-content-between">
                          <h6 style="">approved by</h6>
                          <div class="d-flex">
                              <h6 style="">' . $date_approved_fifth . '</h6>
                          </div>
                      </div>
                      <div style="text-align: center; padding-top: 10px;">
                          <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                              ' . $name_fifth . '</p>
                          <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">OPERATIONS MANAGER</p>
                      </div>
                  </div>
              </div>
       ';


        return view('pages.view_rttte', compact('request_tools', 'html', 'tools_owner', 'requestor', 'approvers'))->render();

    }



    public function view_pullout_request(Request $request)
    {

        $pullout_tools = PulloutRequest::where('status', 1)
            // ->where('progress', 'ongoing')
            ->where('pullout_number', $request->id)
            ->first();

        $approvers = RequestApprover::leftJoin('users', 'users.id', 'request_approvers.approver_id')
            ->leftJoin('positions', 'positions.id', 'users.pos_id')
            ->select('request_approvers.*', 'users.fullname', 'positions.position')
            ->where('request_approvers.status', 1)
            ->where('users.status', 1)
            ->where('request_id', $pullout_tools->id)
            ->where('request_type', 3)
            ->orderBy('sequence', 'asc')
            ->get();

            $loggedInApprover = $approvers->firstWhere(function ($approver) {
                return $approver->approver_id == Auth::id();
            });


        $requestor = User::where('status', 1)->where('id', $pullout_tools->user_id)->value('fullname');
        
        $atr = '';
        if(Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5){
            $atr = 'data-id="'.$loggedInApprover->id.'"';
        }


        $action = '<button type="button" data-requestid="'.$pullout_tools->id.'" '. $atr .' class="pulloutApproveBtn btn btn-sm btn-primary d-block mx-auto js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve" data-bs-original-title="Approve"><i class="fa fa-check"></i></button>';


        //PM
        $firstApprover = $approvers[0]->approver_status;
        if ($firstApprover) {
            $name_first = $approvers[0]->fullname;
            $date_approved_first = $approvers[0]->date_approved;
        }else if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5 && $request->path == 'pages/pullout_ongoing') {
            $name_first = $action;
            $date_approved_first = '--';
        } else {
            $name_first = '<span class="text-warning">Pending</span>';
            $date_approved_first = '--';
        }
        // OM
        $secondApprover = $approvers[1]->approver_status;
        if ($secondApprover) {
            $name_second = $approvers[1]->fullname;
            $date_approved_second = $approvers[1]->date_approved;
        }else if ($approvers[0]->approver_status == 1 && $request->path == 'pages/pullout_ongoing' && (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 5)) {
            $name_second = $action;
            $date_approved_second = '--';
        } else {
            $name_second = '<span class="text-warning">Pending</span>';
            $date_approved_second = '--';
        }
        // // CNC
        // $thirdApprover = $approvers[2]->approver_status;
        // if ($thirdApprover) {
        //     $name_third = $approvers[2]->fullname;
        //     $date_approved_third = $approvers[2]->date_approved;
        // } else {
        //     $name_third = '<span class="text-warning">Pending</span>';
        //     $date_approved_third = '--';
        // }
        // // Warehouse Manager
        // $fourthApprover = $approvers[3]->approver_status;
        // if ($fourthApprover) {
        //     $name_fourth = $approvers[3]->fullname;
        //     $date_approved_fourth = $approvers[3]->date_approved;
        // } else {
        //     $name_fourth = '<span class="text-warning">Pending</span>';
        //     $date_approved_fourth = '--';
        // }

        // <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
        //                         <div class="d-flex justify-content-between">
        //                             <h6 style="">approved by</h6>
        //                             <div class="d-flex">
        //                                 <h6 style="">' . $date_approved_third . '</h6>
        //                             </div>
        //                         </div>
        //                         <div style="text-align: center; padding-top: 10px;">
        //                             <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
        //                                 ' . $name_third . '</p>
        //                             <p style="margin-block: 0px; font-weight: 500; font-size: 10px;">CNC MANAGER/FINANCE MANAGER
        //                             </p>
        //                         </div>
        //                     </div>

        $html = '
                <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <div class="d-flex justify-content-between">
                            <h6 style="">Project manager</h6>
                            <div class="d-flex">
                                <h6 style="">' . $date_approved_first . '</h6>
                            </div>
                        </div>
                    <div style="text-align: center; padding-top: 10px;">
                        <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                            ' . $name_first . '</p>
                    </div>
                </div>
                <div style="padding-inline: 3px; width: 100%">
                    <div class="d-flex justify-content-between">
                        <h6 style="">pid manager</h6>
                        <div class="d-flex">
                            <h6 style="">' . $date_approved_second . '</h6>
                        </div>
                    </div>
                    <div style="text-align: center; padding-top: 10px;">
                        <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                            ' . $name_second . '</p>
                    </div>
                </div>
            ';
        // para malaman kung ilalabas ba ang action or hindi
        $path = $request->path;
        return view('pages.view_pullout', compact('pullout_tools', 'requestor', 'approvers', 'html', 'path'))->render();

    }
}
