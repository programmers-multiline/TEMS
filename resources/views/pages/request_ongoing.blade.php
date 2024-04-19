@extends('layouts.backend')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">

<style>
    #table > thead > tr > th.text-center.dt-orderable-none.dt-ordering-asc > span.dt-column-order{
        display: none;
    }

    #table > thead > tr > th.dt-orderable-none.dt-select.dt-ordering-asc > span.dt-column-order{
        display: none;
    }

</style>
@endsection

@section('content-title', 'Ongoing TEIS Request')

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
                            <th style="padding-right: 10px;"></th>
                            <th>TEIS#</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
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
                                        <option disabled selected>Select Location</option>
                                        <option value="warehouse1">Warehouse 1</option>
                                        <option value="warehouse2">Warehouse 2</option>
                                        <option value="warehouse3">Warehouse 3</option>
                                        <option value="warehouse4">Warehouse 4</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="status">Tools Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" size="1">
                                        <option disabled selected>Select Status</option>
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
                                        <option disabled selected>Select Location</option>
                                        <option value="warehouse1">Warehouse 1</option>
                                        <option value="warehouse2">Warehouse 2</option>
                                        <option value="warehouse3">Warehouse 3</option>
                                        <option value="warehouse4">Warehouse 4</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="editStatus">Tools Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="editStatus" name="editStatus" size="1">
                                        <option disabled selected>Select Status</option>
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
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [ 0 ] }
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_tools') }}'
                },
                columns: [
                    {
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
                        data: 'action'
                    },
                ],
                select: true
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
                    url: '{{ route("edit_tools") }}',
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
                        alert()
                    }
                })
            })

            $(document).on('click','#deleteToolsBtn', function(){
                const id = $(this).data('id');

                $.ajax({
                    url: '{{route("delete_tools")}}',
                    method: 'post',
                    data: {
                        id,
                        _token: "{{csrf_token()}}"
                    },
                    success(){
                        table.ajax.reload();
                    }
                })

            })

            $("#tableContainer").click(function(){
                const dataCount = table.rows({ selected: true }).count();

                if(dataCount > 0){
                    $("#requesToolstBtn").prop('disabled', false);
                }else{
                    $("#requesToolstBtn").prop('disabled', true);
                    
                }
            })

            
            $("#requesToolstBtn").click(function(){
                const data = table.rows({ selected: true }).data();
           
                // const arrItem = []


                for(var i = 0; i < data.length; i++ ){
                    // arrItem.push({icode: data[i].item_code, idesc: data[i].item_description})
                    
                $("#tbodyModal").append(`<tr><td>${data[i].item_code}</td><td class="d-none d-sm-table-cell">${data[i].item_description}</td></tr>`);
                    // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                }

            })

            $(".closeModalRfteis").click(function(){
                $("#tbodyModal").empty()
            })
            
        })
    </script>
@endsection