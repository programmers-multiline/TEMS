@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }
    </style>
@endsection

@section('content-title', 'Completed Pull-Out Request')

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
                            <th>Subcon</th>
                            <th>Date Requested</th>
                            <th>Date Approved</th>
                            <th>Pickup Date</th>
                            <th>Contact Number</th>
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

    @include('pages.modals.ongoing_pullout_request_modal')
    @include('pages.modals.track_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>

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
                    url: '{{ route('fetch_completed_pullout') }}'
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
                        data: 'subcon'
                    },
                    {
                        data: 'date_requested'
                    },
                    {
                        data: 'date_approved'
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
                scrollX: true,
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
                const path = $("#path").val()

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
                                // {
                                //     data: 'action'
                                // }
                            ],
                            drawCallback: function() {

                            },
                            // scrollX: true,
                        });

                    }
                })
                ///old viewing of tools
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
                //     columns: [
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
                //             data: 'action'
                //         }
                //     ],
                // });
            })

            $(document).on("click", "#signed_pullout_form_btn", function (e) {
                e.preventDefault();

                const fileInput = $("#signed_pullout_form")[0].files[0];
                const reqnum = $("#attachreqnum").val();
                const reqtype = $("#attachreqtype").val();


                if (!fileInput) {
                    showToast("error", "Please select a file first!");
                    return;
                }

                const formData = new FormData();
                formData.append("signed_pullout_form", fileInput);
                formData.append("reqnum", reqnum);
                formData.append("reqtype", reqtype);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: '{{ route('upload_signed_pullout_form') }}',
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend() {
                        $("#signed_pullout_form_btn").prop("disabled", true);
                        $("#signed_pullout_form_btn").val('uploading..');
                    },
                    success(response) {
                        showToast("success", "Pullout Form uploaded successfully");
                        $("#ongoingPulloutRequestModal").modal('hide');
                        // $("#table").DataTable().ajax.reload();
                        $("#ongoingPulloutRequestModal").one("hidden.bs.modal", function () {
                            $('.pulloutNumber[data-id="' + reqnum + '"]').trigger("click");
                        });

                    },
                    error(xhr, status, error) {
                        console.error("Upload failed:", error);
                        showToast("error", "Failed to upload form");
                    }
                });
            });


        })
    </script>
@endsection
