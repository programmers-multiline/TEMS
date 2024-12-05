@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/filepond/filepond.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css') }}">


    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        .filepond--credits {
            display: none;
        }

        .camera-container {
            position: relative;
            width: 90vw;
            height: auto;
            overflow: hidden;
            margin-top: 2%;
        }

        video, canvas, #cameraModal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .controls {
            position: absolute;
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .controls button {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
        }

        .controls button:disabled {
            background-color: #555;
            cursor: not-allowed;
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

@section('content-title', 'List of Pullout Request for Receiving')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="loader-container" id="loader" style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1056;">
            <dotlottie-player src="{{asset('js/loader.json')}}" background="transparent" speed="1" style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal" loop autoplay>Loading</dotlottie-player>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-scroll">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Pullout#</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Pickup Date</th>
                            <th>Contact</th>
                            <th>Reason</th>
                            <th>TERS</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END Page Content -->

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


    @include('pages.modals.upload_pullout_modal')
    @include('pages.modals.ongoing_pullout_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>

    <script src="{{ asset('js/plugins/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script
        src="{{ asset('js/plugins/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/webcam-easy/dist/webcam-easy.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <!-- Fileupload JS -->
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>


    <script>
        $(document).ready(function() {

            const webcamElement = $('#webcam')[0];
            const canvasElement = $('#canvas')[0];
            const photoElement = $('#photo')[0];
            let pictureDataURL = null; // Store the captured photo
            const webcam = new Webcam(webcamElement, 'environment', canvasElement);

            function showCameraModal() {
                pictureDataURL = null;
                $('#photo').hide(); // Hide previous photo
                $('#webcam').show(); // Show camera
                $('#cameraModal').fadeIn(); // Show modal
                $('#start-camera').show();
                $('#capture, #retake, #upload').hide();
                $('#cancel').show();
            }

            function hideCameraModal() {
                $('#cameraModal').fadeOut();
                webcam.stop();
            }

            // Start the camera
            $('#start-camera').on('click', function () {
                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: "environment" } } // "ideal" tries back camera but falls back
                })
                .then((stream) => {
                    const video = $('video')[0];
                    video.srcObject = stream;
                    video.play();
                })
                .catch(err => {
                    console.error("Error accessing the camera:", err);
                    alert("Could not access the camera. Please check your device settings.");
                });


                webcam.start()
                    .then(() => {
                        document.getElementById('start-camera').style.display = 'none';
                        document.getElementById('capture').style.display = 'inline-block';
                    })
                    .catch(err => console.error(err));
            });


            // Capture photo
            $('#capture').on('click', function () {
                pictureDataURL = webcam.snap();
                $('#photo').attr('src', pictureDataURL).show(); // Update and display photo
                $('#webcam').hide();
                $('#capture').hide();
                $('#retake, #upload').show();
            });

            // Retake photo
            $('#retake').on('click', function () {
                $('#photo').hide();
                $('#webcam').show();
                webcam.start();
                $('#capture').show();
                $('#retake, #upload').hide();
            });

            // Cancel camera modal
            $('#cancel').on('click', function () {
                hideCameraModal();
            });

            let pulloutNum;

            $(document).on('click', '#addSchedBtn', function() {
                pulloutNum = $(this).data('pulloutnum')
            })


            $("#btnAddSched").click(function() {
                const pickupDate = $("#pickupDate").val();

                $.ajax({
                    url: '{{ route('add_schedule') }}',
                    method: 'post',
                    data: {
                        pickupDate,
                        pulloutNum,
                        _token: '{{ csrf_token() }}',

                    },
                    success() {
                        calendar.refetchEvents();
                        $("#addSched").modal('hide')
                    }
                })
            })

            const path = $("#path").val();

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_pullout_request') }}',
                    data: {
                        path
                    }
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'pullout_number'
                    },
                    {
                        data: 'subcon'
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
                        data: 'pickup_date'
                    },
                    {
                        data: 'contact_number'
                    },
                    {
                        data: 'reason'
                    },
                    {
                        data: 'ters'
                    },
                    {
                        data: 'action'
                    },
                ],
            });

            let type;

            $(document).on('click', '.teisNumber', function() {


                const id = $(this).data("id");
                type = $(this).data("transfertype");
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
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                // {
                                //     data: 'empty_tools_status'
                                // },
                                {
                                    data: 'reason'
                                },
                                {
                                    data: 'checker'
                                },
                                {
                                    data: 'wh_eval'
                                },
                                {
                                    data: 'action'
                                }
                            ],
                            drawCallback: function() {
                                $(".receivedBtn").tooltip();
                                $(".notReceivedBtn").tooltip();
                            },
                        });

                    }
                })


                ///old viewing of tools
                // const modalTable = $("#modalTable").DataTable({
                //     processing: true,
                //     serverSide: false,
                //     destroy: true,
                //     "aoColumnDefs": [{
                //         "bSortable": false,
                //         "aTargets": [0]
                //     }],
                //     ajax: {
                //         type: 'get',
                //         url: '{{ route('ongoing_pullout_request_modal') }}',
                //         data: {
                //             id,
                //             type,
                //             path,
                //             _token: '{{ csrf_token() }}'
                //         }

                //     },
                //     columns: [{
                //             data: null,
                //             render: DataTable.render.select(),
                //             className: 'selectTools'
                //         },
                //         {
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
                //     select: {
                //         style: 'multi+shift',
                //         selector: 'td'
                //     },
                //     scrollX: true,
                //     drawCallback: function() {
                //         $(".receivedBtn").tooltip();
                //     }
                // });

                // modalTable.select.selector('td:first-child');

                $(".test").click()
                $(".test").click()
                $(".test").click()

                let data;


                $(document).on("change", ".selectTools", function() {

                    data = modalTable.rows({
                        selected: true
                    }).data();

                })

                /// old receiving function
                // $(document).on("click", "#receiveBtnModal", function() {
                //     const multi = "multi";

                //     data = $("#modalTable").DataTable().rows({
                //         selected: true
                //     }).data();

                //     if (data.length == 0) {
                //         showToast("error", "Select Item first!");
                //         return;
                //     }

                //      const allData = [];
                //     const whEval = [];
                //     const prevCount = parseInt($("#pulloutForReceivingCount").text());


                //     for (var i = 0; i < data.length; i++) {

                //         const tool_eval = $('.whEval').eq([i]).val()
                //         const pri_id = data[i].pri_id
                //         const user_eval = data[i].tool_status_eval

                //         const datas = {
                //             tool_eval,
                //             pri_id,
                //             user_eval
                //         }

                //         whEval.push(tool_eval)
                //         allData.push(datas)
                //     }

                //     /// check if all select status have value
                //     const hasEmptyStatus = whEval.some(status => status === null || status === "");
                //     if(hasEmptyStatus){
                //         showToast("info", "Please select tools evaluation first.")
                //         return
                //     }


                //     const arrayToString = JSON.stringify(allData);

                //     const modalTable = $("#modalTable").DataTable()

                //     $.ajax({
                //         url: '{{ route('received_pullout_tools') }}',
                //         method: 'post',
                //         data: {
                //             id,
                //             multi,
                //             dataArray: arrayToString,
                //             _token: "{{ csrf_token() }}"
                //         },
                //         success() {
                //             modalTable.ajax.reload();
                //             $("#table").DataTable().ajax.reload();
                //             showToast("success", "Received Successful");
                //             if(prevCount == 1){
                //                     $(".countContainer").addClass("d-none")
                //                 }else{
                //                     $("#pulloutForReceivingCount").text(prevCount - 1);
                //                 }

                //             if (modalTable.data().count() == 1) {
                //                 $("#receiveBtnModal").prop('disabled', 'true')
                //                 setTimeout(function() {
                //                     $("#ongoingPulloutRequestModal").modal('hide')
                //                 }, 1000);
                //             }
                //         }
                //     })
                // })





                $(document).on("click", ".receivedBtn", function() {
                    const priId = $(this).data('pri_id');
                    const number = $(this).data('number');
                    /// Find the input within this row and get its value
                    const checker = $(this).closest('tr').find('.checker').val();
                    const whEval = $(this).closest('tr').find('.whEval').val();

                    if (!checker || !whEval) {
                        showToast("warning", "Please input checker and select status.");
                        return;
                    }

                    const prevCount = parseInt($("#pulloutForReceivingCount").text());

                    const modalTable = $("#modalTable").DataTable()


                    const confirm = Swal.mixin({
                        customClass: {
                            confirmButton: "btn btn-success ms-2",
                            cancelButton: "btn btn-danger"
                        },
                        buttonsStyling: false
                    });

                    confirm.fire({
                        title: "Receive?",
                        text: "Are you sure you want to Received this tool?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes!",
                        cancelButtonText: "Back",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {


                            // Show camera modal
                        showCameraModal();

                        // Handle upload click
                        $(document).off('click', '#upload').on('click','#upload', function() {
                            hideCameraModal();

                            if (!pictureDataURL) {
                                alert("No photo captured!");
                                return;
                            }

                            // Execute AJAX after photo is captured
                            $.ajax({
                                url: '{{ route('received_pullout_tools') }}',
                                method: 'post',
                                data: {
                                    priId,
                                    checker,
                                    whEval,
                                    photo: pictureDataURL,
                                    _token: "{{ csrf_token() }}"
                                },
                                beforeSend(){
                                    $("#loader").show()
                                },
                                success() {
                                    $("#loader").hide()
                                    modalTable.ajax.reload();
                                    $("#table").DataTable().ajax.reload();
                                    showToast("success", "Received Successful");
                                    if (prevCount == 1) {
                                        $(".countContainer").addClass("d-none")
                                    } else {
                                        $("#pulloutForReceivingCount").text(
                                            prevCount - 1);
                                    }
                                    /// sa multiple received dati
                                    // if (modalTable.data().count() == 1) {
                                    //     $("#receiveBtnModal").prop('disabled',
                                    //         'true')
                                    //     setTimeout(function() {
                                    //         $("#ongoingPulloutRequestModal")
                                    //             .modal('hide')
                                    //     }, 1000);
                                    // }
                                },
                                error(err) {
                                    showToast("error",
                                        "An error occurred. Please try again.");
                                }
                            })
                        });


                        } else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                        ) {

                        }
                    });

                })




                $(document).on('click', '.notReceivedBtn', function() {
                    const priId = $(this).data('pri_id');
                    const number = $(this).data('number');

                    const confirm = Swal.mixin({
                        customClass: {
                            confirmButton: "btn btn-success ms-2",
                            cancelButton: "btn btn-danger"
                        },
                        buttonsStyling: false
                    });

                    confirm.fire({
                        title: "Not Serve?",
                        text: "Are you sure this tool is not serve?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes!",
                        cancelButtonText: "Back",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.ajax({
                                url: '{{ route('pullout_not_received') }}',
                                method: 'post',
                                data: {
                                    priId,
                                    number,
                                    _token: '{{ csrf_token() }}',
                                },
                                success(result) {
                                    showToast("success",
                                        "Tool not Received");
                                    $("#modalTable").DataTable().ajax.reload();

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

        })
    </script>
@endsection
