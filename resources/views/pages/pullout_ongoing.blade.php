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
    </style>
@endsection

@section('content-title', 'Ongoing Pull-Out Request')

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
    <!-- END Page Content -->

    @include('pages.modals.ongoing_pullout_request_modal')
    @include('pages.modals.track_request_modal')

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
                                {
                                    data: 'reason'
                                },
                                // {
                                //     data: 'action'
                                // }
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
                        confirmButton: "btn btn-success ms-2",
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
                    reverseButtons: true
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
                            success() {srw
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
                const requestNum = $(this).data('num');
                const type = $(this).data('type');

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ms-2",
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
                    reverseButtons: true
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



        })
    </script>
@endsection
