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
        .pictureContainer{
        display: block;
        white-space: nowrap; 
        width: 110px !important; 
        overflow-x: hidden;
        text-overflow: ellipsis;
    }
    </style>
@endsection

@section('content-title', 'Completed TEIS Request')

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

    @include('pages.modals.ongoing_teis_request_modal')
    @include('pages.modals.track_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>
    <script src="{{ asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>


    <script>
        $(document).ready(function() {

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('completed_teis_request') }}'
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

            $(document).on('click', '.teisNumber', function() {

                const id = $(this).data("id");
                const type = $(this).data("transfertype");

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
                                        _token: '{{ csrf_token() }}'
                                    }

                                },
                                columns: [{
                                        data: 'qty'
                                    },
                                    {
                                        data: 'asset_code'
                                    },
                                    {
                                        data: 'item_description'
                                    },
                                    {
                                        data: 'item_code'
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
                                    url: '{{ route('ps_ongoing_teis_request_modal') }}',
                                    data: {
                                        id,
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
                                        data: 'asset_code'
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
                                    },
                                ],
                                drawCallback: function() {
                                    $('table thead th.pictureHeader').show();
                                }
                            });
                        }

                    }
                })

                ///old viewing of tools
                // const modalTable = $("#modalTable").DataTable({
                //     processing: true,
                //     serverSide: false,
                //     destroy: true,
                //     ajax: {
                //         type: 'get',
                //         url: '{{ route('ongoing_teis_request_modal') }}',
                //         data: {
                //             id,
                //             type,
                //             _token: '{{ csrf_token() }}'
                //         }

                //     },
                //     columns: [
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
                //     modalTable.column(0).searchable(true);
                // } else {
                //     modalTable.column(0).visible(false);
                //     modalTable.column(0).searchable(false);
                // }

            })

        })
    </script>
@endsection
