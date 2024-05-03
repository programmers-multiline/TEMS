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
            <button type="button" id="pulloutRequestBtn" class="btn btn-danger" data-bs-toggle="modal"
                data-bs-target="#pulloutRequestModal" disabled><i class="fa fa-truck-arrow-right me-1"></i>Pull-Out</button>
        </div>
        @endif
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead >
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
                    { "bSortable": false, "aTargets": [ 0 ] },
                    // { "targets": [0], "visible": false, "searchable": false }
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_my_te') }}',
                },
                columns: [
                    {
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
                select: true,
                select: {
                    style: 'multi+shift',
                    selector: 'td'
                },
            });


                
                
            
            $('#projectCode').change(function(){

                const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                searchable: true,
                pagination: true,
                destroy:true,
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [ 0 ] },
                    // { "targets": [1], "visible": false, "searchable": false }
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_my_te') }}',
                    data: {
                        id: $(this).val(),
                    }
                },
                columns: [
                    {
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
   

            $("#tableContainer").click(function(){
                const dataCount = table.rows({ selected: true }).count();

                if(dataCount > 0){
                    $("#pulloutRequestBtn").prop('disabled', false);
                }else{
                    $("#pulloutRequestBtn").prop('disabled', true);
                    
                }
            })

            

            $("#pulloutRequestBtn").click(function(){
                const data = table.rows({ selected: true }).data();

                console.log(data);


                for(var i = 0; i < data.length; i++ ){
                    
                $("#tbodyPulloutModal").append(`<tr><td>${data[i].teis_number}</td><td>${data[i].item_code} <input type="hidden" value="${data[i].id}"></td><td class="d-none d-sm-table-cell w-50">${data[i].item_description}</td><td><select class="form-select toolsStatus"><option disabled selected="">Select Status</option><option value="good">Good</option><option value="repair">Need Repair</option><option value="dispose">Disposal</option></select></td></tr>`);
                   
                }

            })

            $(".closeModalRfteis").click(function(){
                $("#tbodyPulloutModal").empty()
            })



            $("#requestPulloutModalBtn").click(function(){

                const finalData = {}

                const inputData = $("#pulloutFrom").serializeArray();

                const id = $("#tbodyPulloutModal input[type=hidden]").map((i, id) => id.value);
                const toolsStatus = $(".toolsStatus").map((i, toolsStatus) => toolsStatus.value);

                

                const selectedItemId = [];

                for(var i = 0; i < id.length; i++ ){
                    selectedItemId.push({id: id[i], tools_status: toolsStatus[i]})
                }

                // const arrayToString = JSON.stringify(selectedItemId);


                inputData.forEach(({name,value})=>{
                    
                    finalData[`${name}`] = value

                })

                finalData.tableData = selectedItemId


                $.ajax({
                    url: '{{route("pullout_request")}}',
                    method: 'post',
                    data: finalData,
                    success(){
                        // table.ajax.reload();
                    }
                })

                // #tbodyModal > tr:nth-child(1) > td:nth-child(1) > input[type=text]
            })
            
        })
    </script>
@endsection
