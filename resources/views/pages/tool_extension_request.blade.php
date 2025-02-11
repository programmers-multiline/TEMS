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

@section('content-title', 'Tool Extension Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="loader-container" id="loader"
            style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1033;">
            <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
                style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1"
                playMode="normal" loop autoplay>Loading</dotlottie-player>
        </div>
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fa fa-filter fs-2 me-2 text-secondary"></i>
                    <select class="form-select" id="selectStatus" name="example-select">
                        <option value="all">All Status</option>
                        <option selected value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>  
                <div class="d-flex align-items-center">
                    <button type="button" id="approveExtensionBtn" class="btn btn-success"><i
                        class="fa fa-check me-1"></i>Approve</button>
                </div>  
            </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable w-100 fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th style="padding-right: 10px;"></th>
                            <th style="text-align: left;">Asset Code</th>
                            <th style="text-align: left;">Serial#</th>
                            <th style="text-align: left;">Item Code</th>
                            <th style="text-align: left;">Item Desc</th>
                            <th style="text-align: left;">Brand</th>
                            <th style="text-align: left;">Location</th>
                            <th style="text-align: left;">Status</th>
                            <th style="text-align: left;">Orginal End Date</th>
                            <th style="text-align: left;">Extension Date</th>
                            <th style="text-align: left;">Reason</th>
                            {{-- <th style="text-align: left;">Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END Page Content -->

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <script src="{{ asset('js/plugins/masked-inputs/jquery.maskedinput.min.js')}}"></script>
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
                    url: '{{ route('fetch_request_for_extension') }}',
                },
                columns: [{
                        data: null,
                        render: DataTable.render.select()
                    },
                    // {
                    //     data: 'po_number'
                    // },
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
                        data: 'orig_end_date'
                    },
                    {
                        data: 'extension_date'
                    },
                    {
                        data: 'reason'
                    },
                    // {
                    //     data: 'action'
                    // },
                ],
                // scrollX: true,
                select: true,
                select: {
                    style: 'multi+shift',
                    selector: 'td'
                },
            });

            $("#selectStatus").change(function() {
                const status = $(this).val();
                table.ajax.url('{{ route('fetch_request_for_extension') }}?selectedStatus=' + status).load();
            })



            $("#selectStatus").on('click', function() {

                const data = table.rows({
                    selected: true
                }).data();

                // console.log(data)

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
                                    <option value="" selected disabled>Select State</option>
                                    <option value="0">Currently Use</option>
                                    <option value="1">Available to Transfer</option>
                                </select>
                            </td>
                        </tr>`
                    );

                }
                $("#modalTable").DataTable();

            })


            table.on('select', function(e, dt, type, indexes) {
                if (type === 'row') {
                    var rows = table.rows(indexes).nodes().to$();
                    $.each(rows, function() {
                        if ($(this).hasClass('bg-gray')) {
                            table.row($(this)).deselect();
                            showToast("error","Cannot select, This Request is already approved!");
                            return
                        }
                    })
                }
            });




            $("#approveExtensionBtn").click(function() {

                const tools = [];

                const datas = table.rows({
                    selected: true
                }).data();

                for (let i = 0; i < datas.length; i++) {
                    
                    const id = datas[i].id
                    const pe = datas[i].pe
                    const exDate = datas[i].extension_date
                    
                    const data = {
                        id,
                        pe,
                        exDate
                    }

                    if(datas[i].approver_status == 1){
                        showToast('error', 'Some selected tools extension request is/are already approved.')
                        return
                    }

                    tools.push(data)
                }


                if(datas.length === 0){
                    showToast('warning', 'Please select tools first')
                    return
                }
                

                $.ajax({
                    url: '{{ route('approve_extension_tool') }}',
                    method: 'post',
                    data: {
                        tools,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend() {
                        $("#loader").show()
                    },
                    success() {
                        $("#loader").hide()
                        $("#table").DataTable().ajax.reload();
                        showToast("success", "Tool Extension Approved");
                    },
                    complete() {
                    }
                })

            })


            $('#projectName').change(function() {
                const selectedPname = $(this).find(':selected')
                const custName = selectedPname.data('custname');
                const pCode = selectedPname.data('pcode');
                const pAddress = selectedPname.data('paddress');

                $("#projectCode").val(pCode)
                $("#client").val(custName)
                $("#projectAddress").val(pAddress)

            });


            $(document).on('click','.requestForExtensionBtn', function(){
                pe = $(this).data('pe');
                toolId = $(this).data('toolid');
                endDate = $(this).data('enddate');


                $("#rfePe").val(pe)
                $("#rfeToolId").val(toolId)
                $("#rfeDate").val(endDate)
            });


            $(document).on('click','#extensionDateBtn', function(){
                const exDate = $("#extensionDate").val();
                const reason = $("#reasonInputed").val();
                const pe = $("#rfePe").val();
                const toolId = $("#rfeToolId").val();
                const origEndDate = $("#rfeDate").val();


                if (!exDate || !reason) {
                    showToast("info", "Please provide both the extension date and reason.");
                    return;
                }

                if (!pe || !toolId) {
                    showToast("error", "cannot retrieve other data!");
                    return;
                }


                $.ajax({
                    url: '{{ route('request_for_extension') }}',
                    method: 'post',
                    data: {
                        origEndDate,
                        exDate,
                        reason,
                        pe,
                        toolId,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend() {
                        $("#loader").show();
                    },
                    success(result) {
                        $("#loader").hide();

                        $("#table").DataTable().ajax.reload();
                        showToast("success", "Request Extension Successfully");

                    }
                })


            })


        })
    </script>
@endsection
