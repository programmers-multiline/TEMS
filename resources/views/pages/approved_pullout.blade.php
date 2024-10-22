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

@section('content-title', 'Approved Pull-Out Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Pullout#</th>
                            <th>Approver</th>
                            <th>Customer Name</th>
                            <th>Project Name</th>
                            <th>Project Code</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Subcon</th>
                            <th>Date Approved</th>
                            <th>Pickup Date</th>
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
    <!-- END Page Content -->

    @include('pages.modals.ongoing_pullout_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        $(document).ready(function() {
            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_approved_pullout') }}'
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'pullout_number'
                    },
                    {
                        data: 'approver_name'
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
                        data: 'date_approved'
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
            });

            $(document).on('click', '.pulloutNumber', function() {

                const id = $(this).data("id");
                const path = $("#path").val();


                const modalTable = $("#modalTable").DataTable({
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
                    columns: [
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
                    scrollX: true,
                });
            })    
        })
    </script>
@endsection
