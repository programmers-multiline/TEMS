@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">
    <link rel="stylesheet" href="{{ asset('js/plugins/magnific-popup/magnific-popup.css') }}">

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }
    </style>
@endsection

@section('content-title', 'For Receiving TEIS Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>TEIS</th>
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

    @include('pages.modals.ongoing_teis_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>
    <script src="{{ asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

    <script type="module">
        Codebase.helpersOnLoad(['jq-magnific-popup']);
    </script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        $(document).ready(function() {

            const path = $("#path").val();

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
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
                    {
                        data: 'ters'
                    },
                    {
                        data: 'action'
                    },
                ],
                drawCallback: function() {
                    $(".trackBtn").tooltip();
                }
            });

            let type;

            $(document).on('click', '.teisNumber', function() {

                const id = $(this).data("id");
                type = $(this).data("transfertype");
                const path = $("#path").val();

                if (path == "pages/request_for_receiving") {
                    const modalTable = $("#modalTable").DataTable({
                        processing: true,
                        serverSide: false,
                        destroy: true,
                        "aoColumnDefs": [{
                                "bSortable": false,
                                "aTargets": [0]
                            },
                            {
                                "targets": [1],
                                "visible": false,
                                "searchable": false
                            }
                        ],
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
                                data: null,
                                render: DataTable.render.select()
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
                } else {
                    const modalTable = $("#modalTable").DataTable({
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
                                data: 'action'
                            }
                        ],
                        scrollX: true,
                        drawCallback: function() {
                            $(".receivedBtn").tooltip();
                        }
                    });
                }

            })

            $(document).on('click', '.receivedBtn', function() {
                const id = $(this).data('triid');
                const teis_num = $(this).data('number');

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ms-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Recieved?",
                    text: "Are you sure you want to Received this tool?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Back",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('scanned_teis_received') }}',
                            method: 'post',
                            data: {
                                id,
                                teis_num,
                                type,
                                _token: '{{ csrf_token() }}',
                            },
                            success(result) {
                                showToast("success",
                                    "Tool Received");
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