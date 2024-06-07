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

@section('content-title', 'My Tools and Equipment')

@section('content')
    <!-- Page Content -->
    <div class="content">
        @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4)
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fa fa-filter fs-2 me-2 text-secondary"></i>
                    <select class="form-select" id="projectCode" name="example-select">
                        <option disabled selected="">Project Site</option>
                        <option value="pn-1">Project Site 1</option>
                        <option value="pn-2">Project Site 2</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="changeStateBtn" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#changeTransferStateModal" disabled><i class="fa fa-arrows-rotate me-1"></i>Change Transfer
                        State</button>
                    <button type="button" id="pulloutRequestBtn" class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#pulloutRequestModal" disabled><i
                            class="fa fa-truck-arrow-right me-1"></i>Pull-Out</button>
                </div>
            </div>
        @endif
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th style="padding-right: 10px;"></th>
                            <th style="text-align: left; font-size: 14px;">Teis#</th>
                            <th style="text-align: left; font-size: 14px;">PO Number</th>
                            <th style="text-align: left; font-size: 14px;">Asset Code</th>
                            <th style="text-align: left; font-size: 14px;">Serial#</th>
                            <th style="text-align: left; font-size: 14px;">Item Code</th>
                            <th style="text-align: left; font-size: 14px;">Item Desc</th>
                            <th style="text-align: left; font-size: 14px;">Brand</th>
                            <th style="text-align: left; font-size: 14px;">Location</th>
                            <th style="text-align: left; font-size: 14px;">Status</th>
                            <th style="text-align: left; font-size: 14px;"> Transfer State</th>
                            <th style="text-align: left; font-size: 14px;">Action</th>
                            {{-- <th style="width: 15%;">Access</th>
                    <th class="d-none d-sm-table-cell text-center" style="width: 15%;">Profile</th> --}}
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END Page Content -->


    @include('pages.modals.pullout_form_modal')
    @include('pages.modals.change_state_modal')

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

            // if($("#changeStateBtn").is(':disabled') || $("#changeTransferStateModal").is(':disabled')){
            //     alert()
            // }

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                searchable: true,
                pagination: true,
                "aoColumnDefs": [{
                        "bSortable": false,
                        "aTargets": [0]
                    },
                    // { "targets": [0], "visible": false, "searchable": false }
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_my_te') }}',
                },
                columns: [{
                        data: null,
                        render: DataTable.render.select()
                    },
                    {
                        data: 'teis_number'
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
                        data: 'transfer_state'
                    },
                    {
                        data: 'action'
                    },
                ],
                scrollX: true,
                select: true,
                select: {
                    style: 'multi+shift',
                    selector: 'td'
                },
            });



            $("#changeStateBtn").on('click', function() {


                $(".closeModalRfteis").click(function() {
                    $("#tbodyModal").empty()
                })


                const data = table.rows({
                    selected: true
                }).data();

                console.log(data)

                for (var i = 0; i < data.length; i++) {

                    $("#tbodyModal").append(
                        `<tr>
                            <td>${data[i].po_number}</td>
                            <td>${data[i].asset_code}</td>
                            <td>${data[i].serial_number}</td>
                            <td>${data[i].item_code}></td>
                            <td>${data[i].item_description}</td>
                            <td>${data[i].brand}</td>
                            <td>${data[i].warehouse_name}</td>
                            <td>${data[i].tools_status}</td>
                            <td style="width: 180px;">
                                <select data-id="${data[i].id}" class="form-select selectState" name="example-select">
                                    <option selected disabled>Select State</option>
                                    <option value="0">Currently Use</option>
                                    <option value="1">Available to Transfer</option>
                                </select>
                            </td>
                        </tr>`
                    );

                }
                $("#modalTable").DataTable();

            })


            $("#contact").keypress(function(e) {
                if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
            });


            $('#projectCode').change(function() {

                const table = $("#table").DataTable({
                    processing: true,
                    serverSide: false,
                    searchable: true,
                    pagination: true,
                    destroy: true,
                    "aoColumnDefs": [{
                            "bSortable": false,
                            "aTargets": [0]
                        },
                        // { "targets": [1], "visible": false, "searchable": false }
                    ],
                    ajax: {
                        type: 'get',
                        url: '{{ route('fetch_my_te') }}',
                        data: {
                            id: $(this).val(),
                        }
                    },
                    columns: [{
                            data: null,
                            render: DataTable.render.select()
                        },
                        {
                            data: 'teis_number'
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
                            data: 'location'
                        },
                        {
                            data: 'tools_status'
                        },
                        {
                            data: 'action'
                        },
                    ],
                    select: true
                });

            });

            table.select.selector('td:first-child');

            $("#poNumber").keypress(function(e) {
                if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
            });


            $("#tableContainer").click(function() {
                const dataCount = table.rows({
                    selected: true
                }).count();

                if (dataCount > 0) {
                    $("#pulloutRequestBtn").prop('disabled', false);
                    $("#changeStateBtn").prop('disabled', false);
                } else {
                    $("#pulloutRequestBtn").prop('disabled', true);
                    $("#changeStateBtn").prop('disabled', true);

                }
            })



            $("#pulloutRequestBtn").click(function() {
                const data = table.rows({
                    selected: true
                }).data();

                console.log(data);


                for (var i = 0; i < data.length; i++) {

                    $("#tbodyPulloutModal").append(
                        `<tr><td>${data[i].teis_number}</td><td>${data[i].item_code} <input type="hidden" value="${data[i].id}"></td><td class="d-none d-sm-table-cell w-50">${data[i].item_description}</td><td><select class="form-select toolsStatus"><option disabled selected="">Select Status</option><option value="good">Good</option><option value="repair">Need Repair</option><option value="dispose">Disposal</option></select></td></tr>`
                    );

                }

            })

          



            $("#requestPulloutModalBtn").click(function() {

                const finalData = {}

                const inputData = $("#pulloutFrom").serializeArray();

                const id = $("#tbodyPulloutModal input[type=hidden]").map((i, id) => id.value);
                const toolsStatus = $(".toolsStatus").map((i, toolsStatus) => toolsStatus.value);



                const selectedItemId = [];

                for (var i = 0; i < id.length; i++) {
                    selectedItemId.push({
                        id: id[i],
                        tools_status: toolsStatus[i]
                    })
                }

                // const arrayToString = JSON.stringify(selectedItemId);


                inputData.forEach(({
                    name,
                    value
                }) => {

                    finalData[`${name}`] = value

                })

                finalData.tableData = selectedItemId


                $.ajax({
                    url: '{{ route('pullout_request') }}',
                    method: 'post',
                    data: finalData,
                    success() {
                        table.ajax.reload();
                        $("#pulloutRequestModal").modal("hide")
                        showToast("success", "Request Pullout Successfully");

                    }
                })

                // #tbodyModal > tr:nth-child(1) > td:nth-child(1) > input[type=text]
            })


            $("#addStateBtn").click(function() {

                const allData = [];

                $('.selectState').each(function(i, obj) {

                const id = $(this).data('id')

                    const state = obj.value

                    const data = {
                        id,
                        state
                    }

                    allData.push(data)


                });


                const stringData = JSON.stringify(allData)

                $.ajax({
                    url: '{{ route('add_state') }}',
                    method: 'post',
                    data: {
                        stateDatas: stringData,
                        _token: "{{ csrf_token() }}"
                    },
                    success() {
                        $("#changeTransferStateModal").modal('hide')
                        table.ajax.reload();
                        table.rows('.selected').deselect();
                        $("#pulloutRequestBtn").prop('disabled', true);
                        $("#changeStateBtn").prop('disabled', true);
                    }
                })

            })


        })
    </script>
@endsection
