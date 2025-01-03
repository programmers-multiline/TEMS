<?php

namespace App\Http\Controllers;

use App\Models\PeLogs;
use App\Models\Uploads;
use App\Models\RttteLogs;
use App\Models\RfteisLogs;
use App\Models\TeisUploads;
use App\Models\TersUploads;
use Illuminate\Support\Str;
use App\Models\ToolPictures;
use Illuminate\Http\Request;
use App\Models\PulloutRequest;
use App\Models\ReceivingProof;
use App\Models\TransferRequest;
use App\Models\ToolsAndEquipment;
use Illuminate\Http\UploadedFile;
use App\Models\PsTransferRequests;
use App\Models\PulloutRequestItems;
use App\Models\TransferRequestItems;
use Illuminate\Support\Facades\Auth;

class FileUploadController extends Controller
{
    public function upload_process(Request $request)
    {
        //  dd($request->all());
        if ($request->hasFile('teis_upload')) {
            $teis_form = $request->teis_upload;
            foreach ($teis_form as $teis) {
                $teis_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $teis->getClientOriginalExtension();
                $uploads = Uploads::create([
                    'name' => $teis_name,
                    'original_name' => $teis->getClientOriginalName(),
                    'extension' => $teis->getClientOriginalExtension(),
                ]);
                $teis->move('uploads/teis_form/', $teis_name);

                if ($request->trType == 'rfteis') {
                    $remarks = "From Warehouse";
                } else {
                    //! mali ito dapat sa previous owner hindi sa nanghihiram ngayon
                    $remarks = "From Project Site";
                }

                $tool_ids = explode(',', $request->toolId);


                foreach ($tool_ids as $tool_id) {
                    PeLogs::create([
                        'request_number' => $request->teisNum,
                        'tool_id' => $tool_id,
                        'teis_upload_id' => $uploads->id,
                        'pe' => $request->pe,
                        'tr_type' => $request->trType,
                        'remarks' => $remarks
                    ]);

                }



                // $uploads = Uploads::where('status', 1)->orderBy('id', 'desc')->first();

                TeisUploads::create([
                    'teis' => $request->inputedTeisNum,
                    'teis_number' => $request->teisNum,
                    'upload_id' => $uploads->id,
                    'tr_type' => $request->trType,
                ]);


                /// for logs
                if ($request->trType == 'rfteis') {
                    RfteisLogs::create([
                        'page' => 'rftte',
                        'request_number' => $request->teisNum,
                        'title' => 'Upload TEIS',
                        'message' => Auth::user()->fullname . ' ' . 'upload TEIS.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/teis_form') . '/' .
                            $teis_name . '">
                            <span>View</span>
                            </a>',
                        'action' => 4,
                        'approver_name' => Auth::user()->fullname,
                    ]);
                } else {
                    RttteLogs::create([
                        'page' => 'rftte',
                        'request_number' => $request->teisNum,
                        'title' => 'Upload TEIS',
                        'message' => Auth::user()->fullname . ' ' . 'upload TEIS.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/teis_form') . '/' .
                            $teis_name . '">
                            <span>View</span>
                            </a>',
                        'action' => 5,
                        'approver_name' => Auth::user()->fullname,
                    ]);
                }

            }
        }

        // // We don't know the name of the file input, so we need to grab
        // // all the files from the request and grab the first file.
        // /** @var UploadedFile[] $files */
        // $files = $request->allFiles();

        // if (empty($files)) {
        //     abort(422, 'No files were uploaded.');
        // }

        // if (count($files) > 1) {
        //     abort(422, 'Only 1 file can be uploaded at a time.');
        // }

        // // Now that we know there's only one key, we can grab it to get
        // // the file from the request.
        // $requestKey = array_key_first($files);

        // // If we are allowing multiple files to be uploaded, the field in the
        // // request will be an array with a single file rather than just a
        // // single file (e.g. - `csv[]` rather than `csv`). So we need to
        // // grab the first file from the array. Otherwise, we can assume
        // // the uploaded file is for a single file input and we can
        // // grab it directly from the request.
        // $file = is_array($request->input($requestKey))
        //     ? $request->file($requestKey)[0]
        //     : $request->file($requestKey);

        // // Store the file in a temporary location and return the location
        // // for FilePond to use.
        // return $file->store(
        //     path: 'tmp/'.now()->timestamp.'-'.Str::random(20)
        // );
    }

    // RTTTE - TERS
    public function ps_upload_process_ters(Request $request)
    {
        if ($request->hasFile('ters_upload')) {

            $ters_form = $request->ters_upload;

            foreach ($ters_form as $ters) {
                $ters_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $ters->getClientOriginalExtension();
                $uploads = Uploads::create([
                    'name' => $ters_name,
                    'original_name' => $ters->getClientOriginalName(),
                    'extension' => $ters->getClientOriginalExtension(),
                ]);
                $ters->move('uploads/ters_form/', $ters_name);

                // $uploads = Uploads::where('status', 1)->orderBy('id', 'desc')->first();

                $tool_ids = explode(',', $request->psToolId);

                //! pwede idagdag pa ang pe_id or yung prev_tr_type sa parameter dito ispin mo ulit kung alin dyan sa dalawa para maging unique lang at di mapunta sa iba ang uploaded
                foreach ($tool_ids as $tool_id) {
                    PeLogs::where('status', 1)
                        ->where('request_number', $request->prevReqNum)
                        ->where('tool_id', $tool_id)
                        ->where('pe', $request->prevPe)
                        ->update([
                            'ters_upload_id' => $uploads->id,
                            'remarks' => "Project site"
                        ]);
                }
                ;

                TersUploads::create([
                    'pullout_number' => $request->tersNum, //lagyan ng palatandaan
                    'upload_id' => $uploads->id,
                    'tr_type' => $request->trType,
                    'prev_req_num' => $request->prevReqNum,
                ]);


                /// for logs
                RttteLogs::create([
                    'page' => 'rftte',
                    'request_number' => $request->tersNum,
                    'title' => 'Upload TERS',
                    'message' => Auth::user()->fullname . ' ' . 'upload TERS.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/ters_form') . '/' .
                        $ters_name . '">
                        <span>View</span>
                        </a>',
                    'action' => 4,
                    'approver_name' => Auth::user()->fullname,
                ]);
            }
        }

    }



    public function upload_process_ters(Request $request)
    {
        //  dd($request->all());
        $prev_req_data = json_decode($request->prevreqdata, true);

        $not_serve_tools = TransferRequestItems::where('status', 1)->where('teis_number', $request->tersNum)->where('transfer_state', 2)->pluck('tool_id')->toArray();

        $pullout_tools = PulloutRequestItems::where('status', 1)->where('pullout_number', $request->tersNum)->where('item_status', 1)->pluck('tool_id')->toArray();
        if ($request->hasFile('ters_upload')) {

            $ters_form = $request->ters_upload;

            $image_id = mt_rand(111111, 999999) . date('YmdHms');
            foreach ($ters_form as $ters) {
                $ters_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $ters->getClientOriginalExtension();
                $uploads = Uploads::create([
                    'name' => $ters_name,
                    'original_name' => $ters->getClientOriginalName(),
                    'extension' => $ters->getClientOriginalExtension(),
                ]);
                $ters->move('uploads/ters_form/', $ters_name);

                // $uploads = Uploads::where('status', 1)->orderBy('id', 'desc')->first();

                TersUploads::create([
                    'pullout_number' => $request->tersNum,
                    'upload_id' => $uploads->id,
                    'tr_type' => $request->trType,
                ]);

                if ($request->path == 'pages/not_serve_items') {
                    ///patalandaan para malaman kung lahat na ng items sa tools na hindi na served at di na possible sa redelivery
                    TransferRequestItems::where('status', 1)->where('teis_number', $request->tersNum)->where('transfer_state', 2)->update([
                        'clear' => 1
                    ]);

                    $tr_tools = TransferRequestItems::where('status', 1)->where('teis_number', $request->tersNum)->whereNull('is_remove')->pluck('clear');
                    ///tignan kung ang bawat row is 1 lahat
                    $is_all_clear = collect($tr_tools)->every(function ($value) {
                        return $value === 1;
                    });

                    if ($is_all_clear) {
                        TransferRequest::where('status', 1)->where('teis_number', $request->tersNum)->update([
                            'progress' => 'completed'
                        ]);
                    }
                }



                /// for logs

                if ($request->path == 'pages/pullout_for_receiving') {

                    foreach ($prev_req_data as $tool_id => $request_number) {
                        PeLogs::where('status', 1)
                            ->where('request_number', $request_number)
                            ->where('tool_id', $tool_id)
                            ->update([
                                'ters_upload_id' => $uploads->id,
                                'remarks' => "Pullout tool",
                            ]);
                    }
                } else {
                    foreach ($not_serve_tools as $tool_id) {
                        PeLogs::where('status', 1)->where('request_number', $request->tersNum)->where('tool_id', $tool_id)->update([
                            'ters_upload_id' => $uploads->id,
                            'remarks' => "Not serve tool",
                        ]);
                    }

                    RfteisLogs::create([
                        'page' => 'not_serve_items',
                        'request_number' => $request->tersNum,
                        'title' => 'Upload TERS',
                        'message' => Auth::user()->fullname . ' ' . 'upload TERS due to "Not served" tool redelivery unavailable.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/ters_form') . '/' .
                            $ters_name . '">
                            <span>View</span>
                            </a>',
                        'action' => 11,
                        'approver_name' => Auth::user()->fullname,
                    ]);
                }


                ///para lang kasi ito sa pullout
                if ($request->path != 'pages/not_serve_items') {
                    PulloutRequest::where('status', 1)->where('pullout_number', $request->tersNum)->update([
                        'progress' => 'completed',
                    ]);
                }

            }
        }

    }


    // Upload Picture of Tools

    public function upload_tools_pic(Request $request)
    {
        //  dd($request->all());
        if ($request->hasFile('picture_upload')) {


            $toolPicture = $request->picture_upload;

            foreach ($toolPicture as $pic) {
                $pic_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $pic->getClientOriginalExtension();
                $uploads = Uploads::create([
                    'name' => $pic_name,
                    'original_name' => $pic->getClientOriginalName(),
                    'extension' => $pic->getClientOriginalExtension(),
                ]);
                $pic->move('uploads/tool_pictures/', $pic_name);

                // $uploads = Uploads::where('status', 1)->orderBy('id', 'desc')->first();

                ToolPictures::create([
                    'pstr_id' => $request->reqNum,
                    'tool_id' => $request->toolId,
                    'upload_id' => $uploads->id,
                    'tr_type' => 'rttte',
                ]);


                /// for logs
                $tools_desc = ToolsAndEquipment::where('status', 1)->where('id', $request->toolId)->value('item_description');

                RttteLogs::create([
                    'page' => 'site_to_site_transfer',
                    'request_number' => $request->reqNum,
                    'title' => 'Tool Picture Uploaded (Owner)',
                    'message' => Auth::user()->fullname . ' ' . 'uploaded a picture of ' . $tools_desc . '.' . '<a target="_blank" class="img-link img-thumb" href="' . asset('uploads/tool_pictures') . '/' .
                        $pic_name . '">
                        <span>View</span>
                        </a>',
                    'action' => 2,
                    'approver_name' => Auth::user()->fullname,
                ]);

            }
        }

    }



    public function upload_proof_of_receiving(Request $request)
    {
        //  dd($request->all());
        if ($request->hasFile('proof_upload')) {


            $por = $request->proof_upload;

            foreach ($por as $proof) {
                $proof_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $proof->getClientOriginalExtension();
                $uploads = Uploads::create([
                    'name' => $proof_name,
                    'original_name' => $proof->getClientOriginalName(),
                    'extension' => $proof->getClientOriginalExtension(),
                ]);
                $proof->move('uploads/receiving_proofs/', $proof_name);

                ReceivingProof::create([
                    'request_number' => $request->reqNum,
                    'upload_id' => $uploads->id,
                    'tr_type' => $request->trType,
                ]);

                if ($request->trType == 'rfteis') {
                    TransferRequest::where('status', 1)->where('teis_number', $request->reqNum)->update([
                        'is_proof_upload' => now()
                    ]);
                } else {
                    PsTransferRequests::where('status', 1)->where('request_number', $request->reqNum)->update([
                        'is_proof_upload' => now()
                    ]);
                }

            }
        }

    }





}
