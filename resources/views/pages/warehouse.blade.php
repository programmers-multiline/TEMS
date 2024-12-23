@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order,
        {
        display: none;
        }

        #table>thead>tr>th.dt-orderable-asc.dt-orderable-desc.dt-type-numeric>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-asc.dt-orderable-desc.dt-type-numeric.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {}

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none !important;
            border: 1px solid red !important;
        }

        #accordion_tac {
            /* transition: all 1s ease-out; */
        }
    </style>
@endsection

@section('content-title', 'Warehouse Tools')

@section('content')
    <div class="loader-container" id="loader"
        style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1033;">
        <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
            style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal"
            loop autoplay>Loading</dotlottie-player>
    </div>
    <!-- Page Content -->
    <div class="content">
        <input type="hidden" id="userId" value="{{ Auth::user()->user_type_id }}">
        @if (Auth::user()->user_type_id == 2 || Auth::user()->user_type_id == 1)
            <button type="button" class="btn btn-primary mb-3 d-block ms-auto" data-bs-toggle="modal"
                data-bs-target="#modal-tools"><i class="fa fa-plus me-1"></i>Add Tools</button>
        @endif
        @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4)
            <div class="d-flex mb-3 justify-content-between align-items-center flex-column flex-lg-row">
                <div class="d-flex align-items-center">
                    <i class="fa fa-filter fs-2 me-2 text-secondary"></i>
                    <select class="form-select" id="selectWarehouse" name="example-select">
                        <option value="" disabled selected>Select Warehouse</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="requesToolstBtn" class="btn btn-primary d-block ms-auto col-12 col-lg-2"><i
                        class="fa fa-pen-to-square me-1"></i>Request Tools</button>
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
                            <th>ID</th>
                            <th>PO Number</th>
                            <th class="test">Asset Code</th>
                            <th>Serial#</th>
                            <th>Item Code</th>
                            <th>Item Desc</th>
                            <th>Brand</th>
                            <th>Location</th>
                            <th>Status</th>
                            {{-- @if (!Auth::user()->user_type_id == 6) --}}
                            <th>Action</th>
                            {{-- @endif --}}
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










    {{-- modal add tools --}}

    <div class="modal fade" id="modal-tools" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">ADD TOOLS</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <form id="addToolsForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="poNumber">PO number</span></label>
                                    <input type="text" class="form-control" id="poNumber" name="poNumber"
                                        placeholder="Enter PO" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="assetCode">Asset Code</span></label>
                                    <input type="text" class="form-control" id="assetCode" name="assetCode"
                                        placeholder="Enter Asset code">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="serialNumber"> Serial Number</span></label>
                                    <input type="text" class="form-control" id="serialNumber" name="serialNumber"
                                        placeholder="Enter Serial Number">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="itemCode">Item Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="itemCode" name="itemCode"
                                        placeholder="Enter Item code">
                                </div>
                            </div>
                            <div class="col2 mb-3">
                                <label class="form-label" for="itemDescription">Item Description <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="itemDescription" name="itemDescription"
                                    placeholder="Enter Item Description">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="brand">Brand <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="brand" name="brand"
                                    placeholder="Enter Brand">
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="location">Location <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="location" name="location" size="1">
                                        <option value="" disabled selected>Select Location</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="status">Tools Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" size="1">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="good">Good</option>
                                        <option value="repair">Need Repair</option>
                                        <option value="dispose">Disposal</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="btnAddTools" type="button" class="btn btn-alt-primary">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- modal edit tools --}}

    <div class="modal fade" id="modalEditTools" tabindex="-1" role="dialog" aria-labelledby="modalEditTools"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">EDIT TOOLS</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <form id="editToolsForm">
                            @csrf
                            <input type="hidden" id="hiddenId" name="hiddenId">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="editPo">PO number</span></label>
                                    <input type="text" class="form-control" id="editPo" name="editPo"
                                        placeholder="Enter PO" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="editAssetCode">Asset Code</span></label>
                                    <input type="text" class="form-control" id="editAssetCode" name="editAssetCode"
                                        placeholder="Enter Asset code">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="editSerialNumber"> Serial Number</span></label>
                                    <input type="text" class="form-control" id="editSerialNumber"
                                        name="editSerialNumber" placeholder="Enter Serial Number">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="editItemCode">Item Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editItemCode" name="editItemCode"
                                        placeholder="Enter Item code">
                                </div>
                            </div>
                            <div class="col2 mb-3">
                                <label class="form-label" for="editItemDescription">Item Description <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editItemDescription"
                                    name="editItemDescription" placeholder="Enter Item Description">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="editBrand">Brand <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editBrand" name="editBrand"
                                    placeholder="Enter Brand">
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="editLocation">Location <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="editLocation" name="editLocation" size="1">
                                        <option value="" disabled selected>Select Location</option>
                                        <option value="1">Warehouse 1</option>
                                        <option value="2">Warehouse 2</option>
                                        <option value="3">Warehouse 3</option>
                                        <option value="4">Warehouse 4</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="editStatus">Tools Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="editStatus" name="editStatus" size="1">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="good">Good</option>
                                        <option value="repair">Need Repair</option>
                                        <option value="dispose">Disposal</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeEditToolsModal" class="btn btn-alt-secondary"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="updateBtnModal" type="button" class="btn btn-alt-primary">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.modals.request_tool_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}



    <script>
        $(document).ready(function() {
            const userId = $("#userId").val()

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
                    {
                        "targets": [1],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "targets": [0],
                        "visible": userId == 4,
                        "searchable": userId == 4
                    }
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_tools') }}'
                },
                columns: [{
                        data: null,
                        render: DataTable.render.select(),
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
                scrollX: true,
                select: {
                    style: 'multi+shift',
                    selector: 'td'
                },
            });

            $(".test").click()
            $(".test").click()
            $(".test").click()

            var searchVal = new URLSearchParams(window.location.search).get('searchVal');

            if (@json($search) == "search") {

                table.search(searchVal).draw();

            }

            table.on('select', function(e, dt, type, indexes) {
                if (type === 'row') {
                    var rows = table.rows(indexes).nodes().to$();
                    $.each(rows, function() {
                        if ($(this).hasClass('bg-gray')) {
                            table.row($(this)).deselect();
                            showToast("error",
                                "Cannot select, This tools is currently on process!");
                        }
                    })
                }
            });

            table.select.selector('td:first-child');

            // .replace(/_/g, " ")


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


            $(document).on('click', '#editBtn', function() {
                const id = $(this).data('id');
                const po = $(this).data('po');
                const asset = $(this).data('asset');
                const serial = $(this).data('serial');
                const itemCode = $(this).data('itemcode');
                const itemDesc = $(this).data('itemdesc');
                const brand = $(this).data('brand');
                const location = $(this).data('location');
                const status = $(this).data('status');

                $("#editPo").val(po);
                $("#editAssetCode").val(asset);
                $("#editSerialNumber").val(serial);
                $("#editItemCode").val(itemCode)
                $("#editItemDescription").val(itemDesc)
                $("#editBrand").val(brand)
                $("#editLocation").val(location)
                $("#editStatus").val(status)
                $("#hiddenId").val(id)

            })

            $("#updateBtnModal").click(function() {

                const hiddenToolsId = $("#hiddenId").val()
                const updatePo = $("#editPo").val();
                const updateAsset = $("#editAssetCode").val();
                const updateSerial = $("#editSerialNumber").val();
                const updateItemCode = $("#editItemCode").val()
                const updateItemDesc = $("#editItemDescription").val()
                const updateBrand = $("#editBrand").val()
                const updateLocation = $("#editLocation").val()
                const updateStatus = $("#editStatus").val()

                $.ajax({
                    url: '{{ route('edit_tools') }}',
                    method: 'post',
                    data: {
                        hiddenToolsId,
                        updatePo,
                        updateAsset,
                        updateSerial,
                        updateItemCode,
                        updateItemDesc,
                        updateBrand,
                        updateLocation,
                        updateStatus,
                        _token: "{{ csrf_token() }}"
                    },
                    success(e) {
                        table.ajax.reload();
                        $('#closeEditToolsModal').click();
                        // console.log(e)
                        // alert()
                    }
                })
            })

            $(document).on('click', '#deleteToolsBtn', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: '{{ route('delete_tools') }}',
                    method: 'post',
                    data: {
                        id,
                        _token: "{{ csrf_token() }}"
                    },
                    success() {
                        table.ajax.reload();
                    }
                })

            })

            // $("#tableContainer").click(function() {
            //     const dataCount = table.rows({
            //         selected: true
            //     }).count();

            //     if (dataCount < 1) {
            //         showToast('warning', 'Select tool first!')
            //     }
            // })



            $("#requesToolstBtn").click(function() {
                const data = table.rows({
                    selected: true
                }).data();

                const locId = [];

                for (let i = 0; i < data.length; i++) {
                    locId.push(data[i].location)
                }
                
                const alllocId = locId.every( data => data === locId[0])

                if(!alllocId){
                    showToast('warning', 'Requesting of tools must be per Warehouse')
                    return
                }



                console.log(data)

                if (data.count() < 1) {
                    showToast('warning', 'Select tool first!')
                    return
                } else {
                    $("#requestToolsModal").modal('show')
                }

                $("#warehouseFrom").val(data[0].warehouse_name)


                // const arrItem = []


                for (var i = 0; i < data.length; i++) {
                    // arrItem.push({icode: data[i].item_code, idesc: data[i].item_description})

                    $("#tbodyModal").append(
                        `<tr><td>${data[i].warehouse_name}</td><td>${data[i].asset_code}</td><td>${data[i].item_code} <input type="hidden" value="${data[i].id}"></td><td class="d-sm-table-cell">${data[i].item_description}</td> <td class="d-sm-table-cell"><button type="button" class="deleteToolRequestBtnModal btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete"><i class="fa fa-times"></i></td></tr>`
                    );
                    // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                }

            })

            $(".closeModalRfteis").click(function() {
                $("#tbodyModal").empty()
            })

            //old
            // $('#inputCheck').change(function() {
            //     if ($(this).is(':checked')) {
            //         $("#requestToolsModalBtn").prop('disabled', false);
            //         // $("#accordion_tac").collapse('show');
            //     } else {
            //         $("#requestToolsModalBtn").prop('disabled', true);
            //         // $("#accordion_tac").collapse('hide');
            //     }
            // });

            $("#backAgreement").click(function() {
                $('#inputCheck').prop('checked', false);
                $("#requestToolsModalBtn").prop('disabled', true);
            })

            $("#agree").click(function() {
                $('#inputCheck').prop('checked', true);
                $("#requestToolsModalBtn").prop('disabled', false);
            })





            $("#requestToolsModalBtn").click(function() {
                const pe = $("#pe").val();
                const subcon = $("#subcon").val();
                const date = $("#date").val();
                const customerName = $("#customerName").val();
                const projectName = $("#projectName").val();
                const projectCode = $("#projectCode").val();
                const projectAddress = $("#projectAddress").val();
                const durationDate = $("#durationDate").val();
                const whLocation = $("#warehouseFrom").val();
                if (!projectCode) {
                    showToast('info', 'Select Project Code first!')
                    return
                }

                if (!durationDate) {
                    showToast('info', 'Please fill up all fields!')
                    return
                }

                const id = $("#tbodyModal input[type=hidden]").map((i, id) => id.defaultValue);

                if (id.length == 0) {
                    showToast("error", "No Selected Item!");
                    return
                }

                const selectedItemId = [];

                for (var i = 0; i < id.length; i++) {
                    selectedItemId.push(id[i])
                }

                const arrayToString = JSON.stringify(selectedItemId);

                $.ajax({
                    url: '{{ route('request_tools') }}',
                    method: 'post',
                    data: {
                        pe,
                        subcon,
                        date,
                        customerName,
                        projectName,
                        projectCode,
                        projectAddress,
                        durationDate,
                        whLocation,
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend() {
                        $("#requestToolsModal").modal('hide')
                        $("#loader").show()
                    },
                    success(result) {
                        $("#loader").hide()
                        if(result == 1){
                            Swal.fire({
                                title: "Cannot request!",
                                text: "No assigned Project Manager to the selected project site, please contact your OM.",
                                icon: "error"
                            });
                        return
                        }else if(result == 2){
                            Swal.fire({
                                title: "Cannot request!",
                                text: "walang naka assigned na cnc and whm sa setup approver.",
                                icon: "error"
                            });
                            return
                        }

                        table.ajax.reload();
                        showToast("success", "Request Successfully");
                        // $("#requesToolstBtn").prop('disabled', true);
                        $("#subcon").val('');
                        $("#customerName").val('');
                        $("#projectName").val('');
                        $("#projectCode").val('');
                        $("#projectAddress").val('');
                        $('#inputCheck').prop('checked', false);
                        $("#tbodyModal").empty()
                    }
                })

                // #tbodyModal > tr:nth-child(1) > td:nth-child(1) > input[type=text]
            })


            $("#selectWarehouse").change(function() {
                const warehouseId = $(this).val();
                table.ajax.url('{{ route('fetch_tools') }}?warehouseId=' + warehouseId).load();
            })

            $('#projectCode').change(function() {
                const selectedPcode = $(this).find(':selected')
                const custName = selectedPcode.data('custname');
                const pName = selectedPcode.data('pname');
                const pAddress = selectedPcode.data('paddress');

                $("#projectName").val(pName)
                $("#customerName").val(custName)
                $("#projectAddress").val(pAddress)

            });

            //delete selected tools in modal
            $(document).on('click', '.deleteToolRequestBtnModal', function() {
                $(this).closest('tr').remove();
            })


        })
    </script>
@endsection
