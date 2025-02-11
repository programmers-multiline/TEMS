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

@section('content-title', 'My Tools and Equipment')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="loader-container" id="loader"
            style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1033;">
            <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
                style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1"
                playMode="normal" loop autoplay>Loading</dotlottie-player>
        </div>
        @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 5)
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fa fa-filter fs-2 me-2 text-secondary"></i>
                    <select class="form-select" id="selectProjectCode" name="example-select">
                        <option value="" disabled selected>Project Site</option>
                        @foreach ($pg as $project_detail)
                            <option value="{{ $project_detail->id }}">
                                {{ Str::title($project_detail->project_name) }}</option>
                        @endforeach
                    </select>
                </div>
                @if (Auth::user()->user_type_id == 4)
                    <div class="d-flex gap-2">
                    {{-- <button type="button" id="changeStateBtn" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#changeTransferStateModal" disabled><i class="fa fa-arrows-rotate me-1"></i>Change
                        Transfer
                        State</button> --}}
                        <button type="button" id="pulloutRequestBtn" class="btn btn-danger" disabled><i
                            class="fa fa-truck-arrow-right me-1"></i>Pull-Out</button>
                    </div>
                @endif
                
            </div>
        @endif
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable w-100 fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th style="padding-right: 10px;"></th>
                            <th style="text-align: left;">PO Number</th>
                            <th style="text-align: left;">Asset Code</th>
                            <th style="text-align: left;">Serial#</th>
                            <th style="text-align: left;">Item Code</th>
                            <th style="text-align: left;">Item Desc</th>
                            <th style="text-align: left;">Brand</th>
                            <th style="text-align: left;">Location</th>
                            <th style="text-align: left;">Status</th>
                            <th style="text-align: left;">Usage End Date</th>
                            <th style="text-align: left;">Action</th>
                            {{-- <th style="text-align: left; font-size: 14px;"> Transfer State</th> --}}
                            {{-- <th style="text-align: left; font-size: 14px;">Action</th> --}}
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


    <div class="modal fade" id="requestforExtensionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Request for Extension</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <h3></h3>
                        <input type="hidden" value="{{ date('Y-m-d') }}" id="currentDate">
                        <input type="hidden" id="rfePe">
                        <input type="hidden" id="rfeToolId">
                        <input type="hidden" id="rfeDate">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="extensionDate">Extension Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="extensionDate" name="extensionDate"
                                min="{{ date('Y-m-d') }}" placeholder="Enter Extension Date">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="reasonInputed">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reasonInputed" name="reason" rows="3" placeholder="Enter your reason.."></textarea>
                        </div>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="extensionDateBtn" type="button" class="btn btn-alt-primary">
                            Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <script src="{{ asset('js/plugins/masked-inputs/jquery.maskedinput.min.js')}}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}





    <script>
        $(document).ready(function() {

            // if($("#changeStateBtn").is(':disabled') || $("#changeTransferStateModal").is(':disabled')){
            //     alert()
            // }

            $("#selectProjectCode").select2({
                placeholder: "Select Project site",
            });

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
                        data: 'usage_end_date'
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


            $("#contact").keypress(function(e) {
                if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
            });


            $('#selectProjectCode').change(function() {
                const projectId = $(this).val();
                table.ajax.url('{{ route('fetch_my_te') }}?projectId=' + projectId).load();

            });

            table.select.selector('td:first-child');

            $("#poNumber").keypress(function(e) {
                if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
            });


            // $("#tableContainer").click(function() {

            //     const data = table.rows({
            //         selected: true
            //     }).data();

            //     $("#pulloutRequestBtn").attr("data-current_site", data[0].current_site_id);

            //     const dataCount = table.rows({
            //         selected: true
            //     }).count();

            //     if (dataCount > 0) {
            //         $("#pulloutRequestBtn").prop('disabled', false);
            //         $("#changeStateBtn").prop('disabled', false);
            //     } else {
            //         $("#pulloutRequestBtn").prop('disabled', true);
            //         $("#changeStateBtn").prop('disabled', true);

            //     }
            // })

            table.on('select', function(e, dt, type, indexes) {
                    if (type === 'row') {
                        var rows = table.rows(indexes).nodes().to$();
                        $.each(rows, function() {
                            if ($(this).hasClass('bg-gray')) {
                                table.row($(this)).deselect();
                                showToast("error","Cannot select, This tools is currently on process!");
                                return
                            }
                        })
                    }
                });


            $('#table').on('select.dt deselect.dt', function(e, dt, type, indexes) {
                const selectedRows = table.rows({
                    selected: true
                }).nodes();
                const count = selectedRows.length;

                if (count > 0) {
                    $("#pulloutRequestBtn").prop('disabled', false);
                    $("#changeStateBtn").prop('disabled', false);
                } else {
                    $("#pulloutRequestBtn").prop('disabled', true);
                    $("#changeStateBtn").prop('disabled', true);
                }


                const data = table.rows({
                    selected: true
                }).data();

                console.log(data)

                if (data.length > 0) {
                        $("#pulloutRequestBtn").attr("data-current_site", data[0].current_site_id);
                } else {
                    $("#pulloutRequestBtn").removeAttr("data-current_site");
                }

            });



            $("#pulloutRequestBtn").click(function() {

                const sitesId = [];

                const datas = table.rows({
                    selected: true
                }).data();

                for (let i = 0; i < datas.length; i++) {
                    sitesId.push(datas[i].current_site_id)
                }
                

                const allsiteId = sitesId.every( data => data === sitesId[0])

                if(!allsiteId){
                    showToast('warning', 'Pullout of tools must be per project site')
                    return
                }
                
                
                $("#pulloutRequestModal").modal('show')
                const currentSiteId = $(this).data('current_site')

                $.ajax({
                    url: '{{ route('fetch_current_site') }}',
                    method: 'post',
                    data: {
                        currentSiteId,
                        _token: '{{ csrf_token() }}'
                    },
                    success(currenSite) {
                        $("#client").val(currenSite.customer_name)
                        $("#projectAddress").val(currenSite.project_address)
                        $("#projectName").val(currenSite.project_name)
                        $("#projectCode").val(currenSite.project_code)

                        $("#pulloutRequestBtn").prop('disabled', true);
                        $("#changeStateBtn").prop('disabled', true);
                    },
                    complete() {
                        $('#client, #projectAddress, #projectName, #projectCode').on('keydown',
                            function() {
                                return false;
                            });
                    }
                })

                $("#tbodyPulloutModal").empty()

                const data = table.rows({
                    selected: true
                }).data();

                console.log(data);

                for (var i = 0; i < data.length; i++) {

                    $("#tbodyPulloutModal").append(
                        `<tr><td>${data[i].asset_code}</td><td>${data[i].item_code} <input type="hidden" class="ids" value="${data[i].id}"> <input type="hidden" class="prevReqNum" value="${data[i].prev_request_num}"></td><td class="d-sm-table-cell w-50">${data[i].item_description}</td><td><select class="form-select toolsStatus"><option value="" disabled selected>Select Status</option><option value="good">Good</option><option value="defective">Defective</option></select></td></tr>`
                    );
                    //<option value="dispose">Disposal</option>
                }

            })





            $("#requestPulloutModalBtn").click(function() {

                const finalData = {}

                const inputData = $("#pulloutFrom").serializeArray();

                /// kapag walang laman ang contact #, date to pickup and reason
                if(!inputData[3].value || !inputData[4].value || !inputData[8].value){
                    showToast('warning', 'Fill out all fields')
                    return
                }
                
                if($("#contact").val().length > 11 || $("#contact").val().length < 11){
                    showToast("warning", "It seems that your contact number is not correct")
                    return
                }



                /// kunin ang id ng tools and yung status na nilagay ng user
                const id = $("#tbodyPulloutModal .ids").map((i, id) => id.value);
                const prevReqNum = $("#tbodyPulloutModal .prevReqNum").map((i, reqNum) => reqNum.value);
                const toolsStatus = $(".toolsStatus").map((i, toolsStatus) => toolsStatus.value);


                /// destructures ng data and gawing property sa final data
                inputData.forEach(({name,value}) => {
                    
                    finalData[`${name}`] = value
                    
                })

                /// kunin ang lahat ng ids and tools status na nilagay ng requestor and i attach sa final data
                const selectedItemId = [];

                for (var i = 0; i < id.length; i++) {
                    selectedItemId.push({
                        id: id[i],
                        prev_req_num: prevReqNum[i],
                        tools_status: toolsStatus[i]
                    })
                }

                /// check if all select status have value
                const hasEmptyStatus = selectedItemId.some(data => !data.tools_status);
                if(hasEmptyStatus){
                    showToast("info", "Please select tools status first.")
                    return
                }

                finalData.tableData = selectedItemId


                $.ajax({
                    url: '{{ route('pullout_request') }}',
                    method: 'post',
                    data: finalData,
                    beforeSend() {
                        $("#loader").show();
                        $("#pulloutRequestModal").modal("hide")
                    },
                    success(result) {
                        $("#loader").hide();
                        if(result == 1){
                            Swal.fire({
                                title: "Cannot request!",
                                text: "No assigned Project Manager to the selected project site, please contact your OM.",
                                icon: "error"
                            });
                        return
                        }

                        table.ajax.reload();
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


                const hasEmptyState = allData.some(data => !data.state);

                if(hasEmptyState){
                    showToast("info", "Please select state first.")
                    return
                }

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
                        $("#requestforExtensionModal").modal('hide')
                    },
                    success(result) {
                        $("#loader").hide();

                        $("#table").DataTable().ajax.reload();
                        $("#requestforExtensionModal").modal('hide')
                        showToast("success", "Request Extension Successfully");

                    }
                })


            })


        })
    </script>
@endsection
