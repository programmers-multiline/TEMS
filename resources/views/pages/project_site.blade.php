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

@section('content-title', 'Project Site')

@section('content')
    <!-- Page Content -->
    <div class="content">
        @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4)
            <button type="button" id="requesToolstBtn" class="btn btn-primary mb-3 d-block ms-auto" data-bs-toggle="modal"
                data-bs-target="#rttteModal" disabled><i class="fa fa-pen-to-square me-1"></i>Request Tools</button>
        @endif
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th style="padding-right: 10px;"></th>
                            <th>ID</th>
                            <th>PO Number</th>
                            <th>Asset Code</th>
                            <th>Serial#</th>
                            <th>Item Code</th>
                            <th>Item Desc</th>
                            <th>Brand</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
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


    @include('pages.modals.rttte_form_modal')

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
                searchable: true,
                pagination: true,
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
                    url: '{{ route('fetch_tools_ps') }}'
                },
                columns: [{
                        data: null,
                        render: DataTable.render.select()
                    },
                    {
                        data: 'id'
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
                    },
                ],
                select: true,
                select: {
                    style: 'multi+shift',
                    selector: 'td'
                },
            });


            table.on('select', function(e, dt, type, indexes) {
                if (type === 'row') {
                    var rows = table.rows(indexes).nodes().to$();
                    $.each(rows, function() {
                        if ($(this).hasClass('bg-gray')){
                            table.row($(this)).deselect();
                            showToast("error", "Cannot select, This tools is currently on transfer process!");
                        } 
                    })

                }
            });


            $("#poNumber").keypress(function(e) {
                if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
            });

            $("#btnAddTools").click(function() {

                // $("#poNumber").val("");
                // $("#assetCode").val("");
                // $("#serialNumber").val("");
                // $("#itemCode").val("");
                // $("#itemDescription").val("");
                // $("#brand").val("");



                var input = $("#addToolsForm").serializeArray();
                $.ajax({
                    url: '{{ route('add_tools') }}',
                    method: 'post',
                    data: input,
                    success() {
                        $("#modal-tools").modal('hide')
                        table.ajax.reload();
                        $("#addToolsForm")[0].reset();
                        // $('#closeModal').click();

                    }
                })
            })

            // table.select.selector('td:first-child');

            $("#tableContainer").click(function() {
                const dataCount = table.rows({
                    selected: true
                }).count();

                if (dataCount > 0) {
                    $("#requesToolstBtn").prop('disabled', false);
                } else {
                    $("#requesToolstBtn").prop('disabled', true);

                }
            })


            $("#requesToolstBtn").click(function() {
                const data = table.rows({
                    selected: true
                }).data();

                // const arrItem = []


                for (var i = 0; i < data.length; i++) {
                    // arrItem.push({icode: data[i].item_code, idesc: data[i].item_description})

                    $("#tbodyModal").append(
                        `<tr><td>${data[i].item_code} <input type="hidden" value="${data[i].id}"></td><td class="d-none d-sm-table-cell">${data[i].item_description}</td></tr>`
                        );
                    // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                }

            })

            $(".closeModalRfteis").click(function() {
                $("#tbodyModal").empty()
            })




            $("#psRequestToolsModalBtn").click(function() {
                const projectName = $("#projectName").val();
                const projectCode = $("#projectCode").val();
                const projectAddress = $("#projectAddress").val();

                const id = $("#tbodyModal input[type=hidden]").map((i, id) => id.defaultValue);

                const selectedItemId = [];

                for (var i = 0; i < id.length; i++) {
                    selectedItemId.push(id[i])
                }

                const arrayToString = JSON.stringify(selectedItemId);

                $.ajax({
                    url: '{{ route('ps_request_tools') }}',
                    method: 'post',
                    data: {
                        projectName,
                        projectCode,
                        projectAddress,
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}"
                    },
                    success() {
                        // table.ajax.reload();
                    }
                })

                // #tbodyModal > tr:nth-child(1) > td:nth-child(1) > input[type=text]
            })

        })
    </script>
@endsection
