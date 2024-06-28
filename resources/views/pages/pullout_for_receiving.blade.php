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
    </style>
@endsection

@section('content-title', 'List of Pullout Request for Receiving')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-scroll">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Items</th>
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

    <!-- Fileupload JS -->
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>


    <script>
        $(document).ready(function() {


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

                const modalTable = $("#modalTable").DataTable({
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    "aoColumnDefs": [{
                        "bSortable": false,
                        "aTargets": [0]
                    }],
                    ajax: {
                        type: 'get',
                        url: '{{ route('ongoing_pullout_request_modal') }}',
                        data: {
                            id,
                            type,
                            path,
                            _token: '{{ csrf_token() }}'
                        }

                    },
                    columns: [{
                            data: null,
                            render: DataTable.render.select(),
                            className: 'selectTools'
                        },
                        {
                            data: 'po_number'
                        },
                        {
                            data: 'asset_code'
                        },
                        {
                            data: 'serial_number'
                        },
                        {
                            data: 'item_code'
                        },
                        {
                            data: 'item_description'
                        },
                        {
                            data: 'brand'
                        },
                        {
                            data: 'warehouse_name'
                        },
                        {
                            data: 'tools_status'
                        },
                        {
                            data: 'new_tools_status'
                        },
                        {
                            data: 'action'
                        }
                    ],
                    select: {
                        style: 'multi+shift',
                        selector: 'td'
                    },
                    scrollX: true,
                    drawCallback: function() {
                        $(".receivedBtn").tooltip();
                    }
                });

                modalTable.select.selector('td:first-child');

                $(".test").click()
                $(".test").click()
                $(".test").click()

                let data;


                $(document).on("change", ".selectTools", function() {

                    data = modalTable.rows({
                        selected: true
                    }).data();

                })


                $(document).on("click", "#receiveBtnModal", function() {
                    const multi = "multi";

                    data = $("#modalTable").DataTable().rows({
                        selected: true
                    }).data();

                    if (data.length == 0) {
                        showToast("error", "Select Item first!");
                        return;
                    }

                    const allData = [];
                    const prevCount = parseInt($("#pulloutForReceivingCount").text());


                    for (var i = 0; i < data.length; i++) {

                        const tool_eval = $('.whEval').eq([i]).val()
                        const pri_id = data[i].pri_id
                        const user_eval = data[i].tool_status_eval

                        const datas = {
                            tool_eval,
                            pri_id,
                            user_eval
                        }
                        allData.push(datas)
                    }


                    const arrayToString = JSON.stringify(allData);

                    const modalTable = $("#modalTable").DataTable()

                    $.ajax({
                        url: '{{ route('received_pullout_tools') }}',
                        method: 'post',
                        data: {
                            id,
                            multi,
                            dataArray: arrayToString,
                            _token: "{{ csrf_token() }}"
                        },
                        success() {
                            modalTable.ajax.reload();
                            showToast("success", "Received Successful");
                            if(prevCount == 1){
                                    $(".countContainer").addClass("d-none")
                                }else{
                                    $("#pulloutForReceivingCount").text(prevCount - 1);
                                }

                            if (modalTable.data().count() == 1) {
                                $("#receiveBtnModal").prop('disabled', 'true')
                                setTimeout(function() {
                                    $("#ongoingPulloutRequestModal").modal('hide')
                                }, 1000);
                            }
                        }
                    })
                })

            })

        })
    </script>
@endsection
