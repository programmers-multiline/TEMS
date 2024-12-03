@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }
    </style>
@endsection

@section('content-title', 'Item Logs')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover">
                    <thead>
                        <tr>
                            <th class="w-100">Request#</th>
                            <th class="w-100">PO Number</th>
                            <th class="w-100">Asset Code</th>
                            <th class="w-100">Item Code</th>
                            <th class="w-100">Item Desc</th>
                            <th class="w-100">TEIS</th>
                            <th class="w-100">TERS</th>
                            <th class="w-100">Remarks</th>
                            <th class="w-100">Action</th>
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
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>


    <script>
        $(document).ready(function() {

            const path = $("#path").val();

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                destroy: true,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('report_pe_logs') }}', /// gawin ito bukas
                },
                columns: [
                    {
                        data: 'request_number'
                    },
                    {
                        data: 'po_number'
                    },
                    {
                        data: 'asset_code'
                    },
                    {
                        data: 'item_code'
                    },
                    {
                        data: 'item_description'
                    },
                    {
                        data: 'teis'
                    },
                    {
                        data: 'ters'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'action'
                    },
                ],
                drawCallback: function() {
                    $(".uploadTeisBtn").tooltip();
                    $(".uploadTersBtn").tooltip();
                }
            });

            $(document).on('click', '.teisNumber', function() {

                const id = $(this).data("id");
                const type = $(this).data("type");

                $.ajax({
                    url: '{{ route('view_transfer_request') }}',
                    method: 'get',
                    data: {
                        id,
                        type,
                        path,
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
                                        data: 'tools_delivery_status'
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
                                scrollX: true,
                                ajax: {
                                    type: 'get',
                                    url: '{{ route('ps_ongoing_teis_request_modal') }}',
                                    data: {
                                        id,
                                        type,
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

                    }
                })

            })


        })
    </script>
@endsection
