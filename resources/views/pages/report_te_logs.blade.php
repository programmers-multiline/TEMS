@php
    $tools = App\Models\ToolsAndEquipment::where('status', 1)
        ->where('tools_status', 'good')
        ->select('id', 'asset_code', 'item_description')
        ->get();
@endphp

@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }
    </style>
@endsection

@section('content-title', 'Tools and Equipment Logs')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <select class="js-select2 col-12 col-sm-12 col-md-6 col-lg-4" id="selectTools">
            <option disabled selected>Select Tools</option>
            @foreach ($tools as $tool)
                <option value="{{ $tool->id }}">{{ $tool->asset_code . '-' . $tool->item_description }}</option>
            @endforeach
        </select>
        <div id="tableContainer" class="block block-rounded mt-3">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table w-100 js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            {{-- <th>Action</th>
                            <th>PO Number</th>
                            <th>Asset Code</th>
                            <th>Item Code</th>
                            <th>Item Desc</th>
                            <th>Brand</th>
                            <th>Remarks</th> --}}
                            <th>Project Engineer</th>
                            <th>Date Received</th>
                            <th>Asset Code</th>
                            <th>Item Code</th>
                            <th>Item Desc</th>
                            <th>Remarks</th>
                            {{-- <th>Attachment</th> --}}
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
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>


    <script>
        $(document).ready(function() {

            $("#selectTools").select2({
                placeholder: "Select Tools",
            });

            const path = $("#path").val();

            $(document).on('change', '#selectTools', function() {

                const toolId = $(this).val();

                const table = $("#table").DataTable({
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    scrollX: true,
                    ajax: {
                        type: 'get',
                        url: '{{ route('report_te_logs') }}',
                        data: {
                            toolId,
                            _token : '{{ csrf_token() }}'
                        }
                    },
                    columns: [{
                            data: 'fullname'
                        },
                        {
                            data: 'date_received'
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
                            data: 'tr_type'
                        },
                        // {
                        //     data: 'attachment'
                        // }
                    ],
                    drawCallback: function() {
                        $(".uploadTeisBtn").tooltip();
                        $(".uploadTersBtn").tooltip();
                    }
                });
            })






        })
    </script>
@endsection
