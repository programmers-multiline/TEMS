@extends('layouts.backend')

@section('css')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css"> --}}
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/magnific-popup/magnific-popup.css') }}">
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

@section('content-title', 'For Receiving TEIS Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="loader-container" id="loader" style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1056;">
            <dotlottie-player src="{{asset('js/loader.json')}}" background="transparent" speed="1" style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal" loop autoplay>Loading</dotlottie-player>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Request#</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>TEIS</th>
                            {{-- <th>TERS</th> --}}
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


    @include('pages.modals.ongoing_teis_request_modal')
    @include('pages.modals.track_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="{{ asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/webcam-easy/dist/webcam-easy.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <script type="module">
        Codebase.helpersOnLoad(['jq-magnific-popup']);
    </script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        $(document).ready(function() {

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


                navigator.mediaDevices.getUserMedia({ video: true })
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





            const path = $("#path").val();

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('ongoing_teis_request') }}',
                    data: {
                        path,
                        _token: '{{ csrf_token() }}'
                    }
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'teis_number'
                    },
                    {
                        data: 'subcon'
                    },
                    {
                        data: 'customer_name'
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
                        data: 'request_status'
                    },
                    {
                        data: 'request_type'
                    },
                    {
                        data: 'teis'
                    },
                    // {
                    //     data: 'ters'
                    // },
                    {
                        data: 'action'
                    },
                ],
                drawCallback: function() {
                    $(".trackBtn").tooltip();

                    $(".trackBtn").click(function() {
                        const requestNumber = $(this).data('requestnumber');
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

                            }
                        })
                    })
                }
            });

            let type;

            $(document).on('click', '.teisNumber', function() {

                const id = $(this).data("id");
                type = $(this).data("transfertype");
                const path = $("#path").val();

                $("#receiveBtnModal").attr("data-type", type);

                $.ajax({
                    url: '{{ route('view_transfer_request') }}',
                    method: 'get',
                    data: {
                        id,
                        type,
                        _token: '{{ csrf_token() }}',
                    },
                    success(result) {
                        $("#requestFormLayout").html(result)

                        if (type == 'rfteis') {
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
                                    url: '{{ route('ongoing_teis_request_modal') }}',
                                    data: {
                                        id,
                                        type,
                                        path,
                                        _token: '{{ csrf_token() }}'
                                    }

                                },
                                columns: [{
                                        data: 'qty'
                                    },
                                    {
                                        data: 'unit'
                                    },
                                    {
                                        data: 'item_description'
                                    },
                                    {
                                        data: 'item_code'
                                    },
                                    {
                                        data: 'action'
                                    },
                                ],
                                // scrollX: true,
                                initComplete: function() {
                                    const data = modalTable.rows().data();

                                    for (var i = 0; i < data.length; i++) {

                                        $("#itemListDaf").append(
                                            `<p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                                ${data[i].qty} ${data[i].unit ? data[i].unit : ''} - ${data[i].asset_code} ${data[i].item_description} 
                                                (${data[i].price ? data[i].price : '<span class="text-danger">No Price</span>'})
                                            </p>`
                                        );

                                        // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                                    }

                                    // console.log(data)
                                },
                                drawCallback: function() {
                                    $(".receivedBtn").tooltip();
                                    $(".notReceivedBtn").tooltip();
                                }
                            });
                        } else {
                            const modalTable = $("#modalTable").DataTable({
                                paging: false,
                                order: false,
                                searching: false,
                                info: false,
                                sort: false,
                                processing: true,
                                serverSide: false,
                                destroy: true,
                                // scrollX: true,
                                ajax: {
                                    type: 'get',
                                    url: '{{ route('ongoing_teis_request_modal') }}',
                                    data: {
                                        id,
                                        type,
                                        path,
                                        _token: '{{ csrf_token() }}'
                                    }

                                },
                                columns: [{
                                        data: 'picture'
                                    },
                                    {
                                        data: 'item_no'
                                    },
                                    {
                                        data: 'teis_no'
                                    },
                                    {
                                        data: 'item_code'
                                    },
                                    {
                                        data: 'item_description'
                                    },
                                    {
                                        data: 'serial_number'
                                    },
                                    {
                                        data: 'qty'
                                    },
                                    {
                                        data: 'unit'
                                    },
                                    {
                                        data: 'tools_status'
                                    },
                                    {
                                        data: 'reason_for_transfer'
                                    },
                                    {
                                        data: 'action'
                                    }
                                ],
                                initComplete: function() {
                                    const data = modalTable.rows().data();

                                    for (var i = 0; i < data.length; i++) {

                                        $("#itemListDaf").append(
                                            `<p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                                ${data[i].qty} ${data[i].unit ? data[i].unit : ''} - ${data[i].asset_code} ${data[i].item_description} 
                                                (${data[i].price ? data[i].price : '<span class="text-danger">No Price</span>'})
                                            </p>`
                                        );

                                        // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                                    }

                                    // console.log(data)
                                },
                                drawCallback: function() {
                                    $('table thead th.pictureHeader').show();
                                }
                            });
                        }

                        $("#modalTable").DataTable().select.selector('td:first-child');
                    }
                })

                /// old viewing of tools
                // const modalTable = $("#modalTable").DataTable({
                //     processing: true,
                //     serverSide: false,
                //     destroy: true,
                //     columnDefs: [{
                //         orderable: false,
                //         // render: DataTable.render.select(),
                //         targets: 0
                //     }],
                //     ajax: {
                //         type: 'get',
                //         url: '{{ route('ongoing_teis_request_modal') }}',
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
                //             data: 'picture'
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
                //             data: 'price'
                //         },
                //         {
                //             data: 'tools_status'
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

                //         // if(type == 'rttte'){
                //         //     $('table thead th.pictureHeader').show();
                //         // }else{
                //         //     $('table thead th.pictureHeader').hide();
                //         // }
                //     }
                // });


                // if (type == 'rttte') {
                //     modalTable.column(0).visible(true);
                //     //!!! modalTable.column(0).searchable(true);
                // } else if(path == 'pages/request_for_receiving'){
                //     modalTable.column(1).visible(false);
                // }else {
                //     modalTable.column(0).visible(false);
                //     modalTable.column(0).searchable(false);
                // }


                $(".test").click()
                $(".test").click()
                $(".test").click()

                let data;


                $(document).on("change", ".selectTools", function() {

                    data = modalTable.rows({
                        selected: true
                    }).data();

                    // if(data.length > 0){
                    //     $("#receiveBtnModal").prop()
                    // }else{

                    // }


                })

                $(document).on("click", "#receiveBtnModal", function() {

                    data = $("#modalTable").DataTable().rows({
                        selected: true
                    }).data();

                    if (data.length == 0) {
                        showToast("error", "Select Item first!");
                        return;
                    }
                    const multi = "multi";
                    const type = $(this).data("type");
                    const selectedItemId = [];

                    for (var i = 0; i < data.length; i++) {
                        selectedItemId.push(data[i].tri_id)
                    }

                    const arrayToString = JSON.stringify(selectedItemId);

                    const modalTable = $("#modalTable").DataTable()
                    const table = $("#table").DataTable()

                    $.ajax({
                        url: '{{ route('scanned_teis_received') }}',
                        method: 'post',
                        data: {
                            id,
                            multi,
                            type,
                            triIdArray: arrayToString,
                            _token: "{{ csrf_token() }}"
                        },
                        success() {
                            showToast("success", "Received Successful");
                            modalTable.ajax.reload(function() {
                                if (!modalTable.rows().count()) {
                                    setTimeout(() => $("#ongoingTeisRequestModal")
                                        .modal('hide'), 1000);
                                }
                            });
                            table.ajax.reload();
                        }
                    })
                })

            })




            $(document).on('click', '.receivedBtn', function() {
                const id = $(this).data('triid');
                const teis_num = $(this).data('number');
                const type = $(this).data('trtype');

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ms-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Receive?",
                    text: "Are you sure you want to Receive this tool?",
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
                                url: '{{ route('scanned_teis_received') }}',
                                method: 'post',
                                data: {
                                    id,
                                    teis_num,
                                    type,
                                    photo: pictureDataURL, // Pass photo as base64
                                    _token: '{{ csrf_token() }}',
                                },
                                beforeSend(){
                                    $("#loader").show()
                                },
                                success(result) {
                                    $("#loader").hide()
                                    showToast("success", "Tool Received");
                                    $("#modalTable").DataTable().ajax.reload();
                                    $("#table").DataTable().ajax.reload();
                                },
                                error(err) {
                                    showToast("error",
                                        "An error occurred. Please try again.");
                                }
                            });
                        });
                    }
                });
            });





            // $(document).on('click', '.receivedBtn', function() {
            //     const id = $(this).data('triid');
            //     const teis_num = $(this).data('number');
            //     const type = $(this).data('trtype');

            //     const confirm = Swal.mixin({
            //         customClass: {
            //             confirmButton: "btn btn-success ms-2",
            //             cancelButton: "btn btn-danger"
            //         },
            //         buttonsStyling: false
            //     });

            //     confirm.fire({
            //         title: "Receive?",
            //         text: "Are you sure you want to Received this tool?",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: "Yes!",
            //         cancelButtonText: "Back",
            //         reverseButtons: true
            //     }).then((result) => {
            //         if (result.isConfirmed) {



            //             $.ajax({
            //                 url: '{{ route('scanned_teis_received') }}',
            //                 method: 'post',
            //                 data: {
            //                     id,
            //                     teis_num,
            //                     type,
            //                     _token: '{{ csrf_token() }}',
            //                 },
            //                 success(result) {
            //                     showToast("success",
            //                         "Tool Received");
            //                     $("#modalTable").DataTable().ajax.reload();

            //                 }
            //             })

            //         } else if (
            //             /* Read more about handling dismissals below */
            //             result.dismiss === Swal.DismissReason.cancel
            //         ) {

            //         }
            //     });


            // })


            $(document).on('click', '.notReceivedBtn', function() {
                const id = $(this).data('triid');
                const teis_num = $(this).data('number');
                const type = $(this).data('trtype');

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
                            url: '{{ route('teis_not_received') }}',
                            method: 'post',
                            data: {
                                id,
                                teis_num,
                                type,
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
    </script>
@endsection
