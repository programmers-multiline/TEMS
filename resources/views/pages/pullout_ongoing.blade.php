@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">
    {{-- <link rel="stylesheet" href="{{ asset('css/track_request.css') }}"> --}}

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        .camera-container {
            position: relative;
            width: 90vw;
            height: auto;
            overflow: hidden;
            margin-top: 2%;
        }

        video,
        canvas,
        #cameraModal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            position: absolute;
            bottom: 5%;
            left: 50%;
            transform: translateX(-50%);
            gap: 15px;
        }

        .controls button {
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 10px;
        }

        .controls button:hover:not(:disabled) {
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
        }

        @media only screen and (max-width: 420px) {
            .controls {
                /* position: relative; */
                /* margin-top: 100%;  */
                bottom: 24%;
                /* left: unset;
                transform: unset;
                align-items: center;
                justify-content: center; */
            }

            .camera-container {
                margin-top: 20%;
            }
        }

        #cameraModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 11050;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .camera-container {
            display: inline-block;
            position: relative;
            background: white;
            /* padding: 20px; */
            border-radius: 8px;
        }

        .controls {
            margin-top: 10px;
        }
    </style>
@endsection

@section('content-title', 'Ongoing Pull-Out Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="loader-container" id="loader"
            style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1056;">
            <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
                style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1"
                playMode="normal" loop autoplay>Loading</dotlottie-player>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Pullout#</th>
                            <th>Customer Name</th>
                            <th>Project Name</th>
                            <th>Project Code</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Subcon</th>
                            <th>Pickup Date</th>
                            <th>Assign Sched</th>
                            <th>Contact Number</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Camera Modal -->
    <div id="cameraModal" style="display: none;">
        <div class="camera-container">
            <video id="webcam" autoplay playsinline></video>
            <canvas id="canvas" style="display: none;"></canvas>
            <img id="photo" src="" alt="Captured Image" style="display: none;" />
        </div>
        <div class="controls">
            <button id="start-camera">Start Camera</button>
            <button id="capture" style="display: none;">Capture</button>
            <button id="retake" style="display: none;">Retake</button>
            <button id="upload" style="display: none;">Upload</button>
            <button id="cancel" style="display: none;">Cancel</button>
        </div>
    </div>


    <!-- END Page Content -->

    @include('pages.modals.ongoing_pullout_request_modal')
    @include('pages.modals.track_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/webcam-easy/dist/webcam-easy.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        const webcamElement = document.getElementById('webcam');
        const canvasElement = document.getElementById('canvas');
        const photoElement = document.getElementById('photo');
        let pictureDataURL = null; // Store the captured photo
        const webcam = new Webcam(webcamElement, 'environment', canvasElement);

        function showCameraModal() {

            pictureDataURL = null;
            photoElement.style.display = 'none'; // Hide previous photo
            webcamElement.style.display = 'block'; // Show camera
            document.getElementById('cameraModal').style.display = 'block';
            document.getElementById('start-camera').style.display = 'inline-block';
            document.getElementById('capture').style.display = 'none';
            document.getElementById('retake').style.display = 'none';
            document.getElementById('upload').style.display = 'none';
            document.getElementById('cancel').style.display = 'inline-block';
            $('#cameraModal').fadeIn();
        }

        function hideCameraModal() {
            document.getElementById('cameraModal').style.display = 'none';
            webcam.stop();
        }

        // Start the camera
        document.getElementById('start-camera').addEventListener('click', () => {

            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    } // Use the back camera
                })
                .then((stream) => {
                    const video = document.querySelector('video');
                    video.srcObject = stream;
                    video.play();
                });

            webcam.start()
                .then(() => {
                    document.getElementById('start-camera').style.display = 'none';
                    document.getElementById('capture').style.display = 'inline-block';
                })
                .catch(err => console.error(err));
        });

        // Capture photo
        document.getElementById('capture').addEventListener('click', () => {
            pictureDataURL = webcam.snap();
            photoElement.src = pictureDataURL;
            photoElement.style.display = 'block';
            webcamElement.style.display = 'none';
            document.getElementById('capture').style.display = 'none';
            document.getElementById('retake').style.display = 'inline-block';
            document.getElementById('upload').style.display = 'inline-block';
        });

        // Retake photo
        document.getElementById('retake').addEventListener('click', () => {
            photoElement.style.display = 'none';
            webcamElement.style.display = 'block';
            webcam.start();
            document.getElementById('capture').style.display = 'inline-block';
            document.getElementById('retake').style.display = 'none';
            document.getElementById('upload').style.display = 'none';
        });

        // Cancel camera modal
        document.getElementById('cancel').addEventListener('click', () => {
            hideCameraModal();
        });




        $(document).ready(function() {
            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_ongoing_pullout') }}'
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'pullout_number'
                    },
                    {
                        data: 'client'
                    },
                    {
                        data: 'project_name'
                    },
                    {
                        data: 'project_code'
                    },
                    {
                        data: 'project_address'
                    },
                    {
                        data: 'date_requested'
                    },
                    {
                        data: 'subcon'
                    },
                    {
                        data: 'pickup_date'
                    },
                    {
                        data: 'approved_sched_date'
                    },
                    {
                        data: 'contact_number'
                    },
                    {
                        data: 'reason'
                    },
                    {
                        data: 'action'
                    },
                ],
                drawCallback: function() {
                    $(".trackBtn").tooltip();


                    $(".trackBtn").click(function() {
                        const requestNumber = $(this).data('requestnum');
                        const trType = $(this).data('trtype');

                        $.ajax({
                            url: "{{ route('track_request') }}",
                            method: "post",
                            data: {
                                requestNumber,
                                trType,
                                _token: "{{ csrf_token() }}"
                            },
                            success(result) {

                                $("#requestProgress").html(result)
                                $(".trackRequestNumber").text('#' + requestNumber)

                                // $("#requestProgress li").each(function(index) {
                                //     if (index < result) {
                                //         $(this).addClass("active");
                                //     }
                                // });

                            }
                        })
                    })
                }
            });

            $(document).on('click', '.pulloutNumber', function() {

                const userId = {{ Auth::user()->user_type_id }}
                const id = $(this).data("id");
                const path = $("#path").val();

                $.ajax({
                    url: '{{ route('view_pullout_request') }}',
                    method: 'get',
                    data: {
                        id,
                        path,
                        _token: '{{ csrf_token() }}',
                    },
                    success(result) {
                        $("#requestFormLayout").html(result)


                        const modalTable = $("#modalTable").DataTable({
                            paging: false,
                            order: false,
                            searching: false,
                            info: false,
                            sort: false,
                            processing: true,
                            serverSide: false,
                            destroy: true,
                            "aoColumnDefs": [
                                {
                                    "targets": [-1],
                                    "visible": userId == 4 && path == 'pages/pullout_ongoing',
                                    "searchable": userId == 4 && path == 'pages/pullout_ongoing'
                                }
                            ],
                            ajax: {
                                type: 'get',
                                url: '{{ route('ongoing_pullout_request_modal') }}',
                                data: {
                                    id,
                                    path,
                                    _token: '{{ csrf_token() }}'
                                }

                            },
                            columns: [{
                                    data: 'item_no'
                                },
                                {
                                    data: 'asset_code'
                                },
                                {
                                    data: 'item_code'
                                },
                                {
                                    data: 'teis_no_dr_ar'
                                },
                                {
                                    data: 'item_description'
                                },
                                {
                                    data: 'new_tools_status'
                                },
                                {
                                    data: 'new_tools_status_defective'
                                },
                                {
                                    data: 'reason'
                                },
                                {
                                    data: 'capture_tool'
                                }
                            ],
                            drawCallback: function() {

                            },
                            // scrollX: true,
                        });

                        /// old viewing of tools
                        // const modalTable = $("#modalTable").DataTable({
                        //     processing: true,
                        //     serverSide: false,
                        //     destroy: true,
                        //     ajax: {
                        //         type: 'get',
                        //         url: '{{ route('ongoing_pullout_request_modal') }}',
                        //         data: {
                        //             id,
                        //             path,
                        //             _token: '{{ csrf_token() }}'
                        //         }

                        //     },
                        //     columns: [{
                        //             data: 'po_number'
                        //         },
                        //         {
                        //             data: 'asset_code'
                        //         },
                        //         {
                        //             data: 'serial_number'
                        //         },
                        //         {
                        //             data: 'item_code'
                        //         },
                        //         {
                        //             data: 'item_description'
                        //         },
                        //         {
                        //             data: 'brand'
                        //         },
                        //         {
                        //             data: 'warehouse_name'
                        //         },
                        //         {
                        //             data: 'tools_status'
                        //         },
                        //         {
                        //             data: 'new_tools_status'
                        //         },
                        //         {
                        //             data: 'action'
                        //         }
                        //     ],
                        //     drawCallback: function() {

                        //     },
                        //     scrollX: true,
                        // });

                    }
                })

            })

            /// old view of tools
            // const modalTable = $("#modalTable").DataTable({
            //     processing: true,
            //     serverSide: false,
            //     destroy: true,
            //     ajax: {
            //         type: 'get',
            //         url: '{{ route('ongoing_pullout_request_modal') }}',
            //         data: {
            //             id,
            //             path,
            //             _token: '{{ csrf_token() }}'
            //         }

            //     },
            //     columns: [{
            //             data: 'po_number'
            //         },
            //         {
            //             data: 'asset_code'
            //         },
            //         {
            //             data: 'serial_number'
            //         },
            //         {
            //             data: 'item_code'
            //         },
            //         {
            //             data: 'item_description'
            //         },
            //         {
            //             data: 'brand'
            //         },
            //         {
            //             data: 'warehouse_name'
            //         },
            //         {
            //             data: 'tools_status'
            //         },
            //         {
            //             data: 'new_tools_status'
            //         },
            //         {
            //             data: 'action'
            //         }
            //     ],
            //     scrollX: true,
            // });

            $(document).on('click', '.pulloutApproveBtn', function() {
                const id = $(this).data('id');
                const requestId = $(this).data('requestid');

                const prevCount = parseInt($("#pulloutCount").text());

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success me-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Approve?",
                    text: "Are you sure you want to approved this tools?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Back",
                    reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('tobe_approve_tools') }}',
                            method: 'post',
                            data: {
                                id,
                                requestId,
                                _token: '{{ csrf_token() }}'
                            },
                            success() {
                                $("#table").DataTable().ajax.reload()
                                $("#ongoingPulloutRequestModal").modal('hide')
                                confirm.fire({
                                    title: "Approved!",
                                    text: "Items Approved Successfully.",
                                    icon: "success"
                                });

                                if (prevCount == 1) {
                                    $(".countContainer").addClass("d-none")
                                } else {
                                    $("#rfteisCount").text(prevCount - 1);
                                }
                            }
                        })

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {

                    }
                });


            })


            $(document).on('click', '.deliverBtn', function() {

                
                /// para malaman kung may mga wala pang picture sa ipupullout
                const isClear = $(this).data('proceed_pullout');
                
                if(!isClear){
                    showToast('warning', 'Please take a photo of all tools to be pulled out.')
                    return
                }

                const requestNum = $(this).data('num');
                const type = $(this).data('type');

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success me-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Pullout?",
                    text: "are you sure you want to pull out this tools?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Close",
                    reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('tools_deliver') }}',
                            method: 'post',
                            data: {
                                requestNum,
                                type,
                                _token: '{{ csrf_token() }}'
                            },
                            success() {
                                table.ajax.reload();
                                confirm.fire({
                                    title: "Pullout success!",
                                    text: "The item has been successfully pull out.",
                                    icon: "success"
                                });
                            }
                        })

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {

                    }
                });

            })



            $(document).on('click', '.pulloutCaptureBtn', function() {
                const id = $(this).data('pri_id');
                const pullout_num = $(this).data('number');
                const type = $(this).data('trtype');

                // Show camera modal
                showCameraModal();

                // Handle upload click
                $(document).off('click', '#upload').on('click', '#upload', function() {

                    hideCameraModal();

                    if (!pictureDataURL) {
                        alert("No photo captured!");
                        return;
                    }

                    // Execute AJAX after photo is captured
                    $.ajax({
                        url: '{{ route('upload_photo_for_pullout') }}',
                        method: 'post',
                        data: {
                            id,
                            pullout_num,
                            type,
                            photo: pictureDataURL, // Pass photo as base64
                            _token: '{{ csrf_token() }}',
                        },
                        beforeSend() {
                            $("#loader").show()
                        },
                        success(result) {
                            $("#loader").hide()
                            showToast("success", "Photo saved");
                            $("#modalTable").DataTable().ajax.reload();
                            $("#table").DataTable().ajax.reload();
                        },
                        error(err) {
                            showToast("error",
                                "An error occurred. Please try again.");
                        }
                    });
                });
            });


            $(document).on('click', '.cancelBtn', function(){
                const trType = $(this).data('trtype');
                const requestNumber = $(this).data('requestnumber');
                const toolId = $(this).data('toolid');

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success me-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Cancel?",
                    text: "Are you sure you want to cancel this request?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Back",
                    reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('cancel_request') }}',
                            method: 'post',
                            data: {
                                toolId,
                                requestNumber,
                                trType,
                                _token: '{{ csrf_token() }}',
                            },
                            success(result) {
                                showToast("success","Request Cancel");
                                $("#table").DataTable().ajax.reload();

                            }
                        })

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {

                    }
                });
            })

        })
    </script>
@endsection
