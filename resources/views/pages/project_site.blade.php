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

        @media (min-width: 768px) {
            .form-select{
                width: unset !important;
            }
        }
    </style>
@endsection

@section('content-title', 'Project Site')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <input type="hidden" id="userId" value="{{ Auth::user()->user_type_id }}">
        @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4)
            <button type="button" id="requesToolstBtn" class="btn btn-primary mb-3 d-block ms-auto" data-bs-toggle="modal"
                data-bs-target="#rttteModal" disabled><i class="fa fa-pen-to-square me-1"></i>Request Tools</button>
        @endif
        <select class="form-select col-12 col-sm-12 col-md-6 col-lg-4 mb-3" id="selectProjectSite" name="example-select">
            <option value="" disabled selected>Select Project Site</option>
            @foreach ($all_pg as $site)
                <option value="{{ $site->id }}">{{ $site->project_name }}</option>
            @endforeach
        </select>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th style="padding-right: 10px;"></th>
                            <th>ID</th>
                            <th>Current PE</th>
                            <th>Current Site</th>
                            <th>Previous Request Number</th>
                            <th>PO Number</th>
                            <th>Asset Code</th>
                            <th>Serial#</th>
                            <th>Item Code</th>
                            <th>Item Desc</th>
                            <th>Brand</th>
                            <th>Project name</th>
                            <th>Location</th>
                            <th>Status</th>
                            {{-- <th>Transfer State</th> --}}
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
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>

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
                // scrollX: true,
                autoWidht: false,
                "aoColumnDefs": [{
                        "bSortable": false,
                        "aTargets": [0]
                    },
                    {
                        "targets": [1, 2, 3, 4],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "targets": [0],
                        "visible": userId == 2,
                        "searchable": userId == 2
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
                        data: 'current_pe'
                    },
                    {
                        data: 'current_site_id'
                    },
                    {
                        data: 'prev_request_num'
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
                        data: 'project_name'
                    },
                    {
                        data: 'project_location'
                    },
                    {
                        data: 'tools_status'
                    },
                    // {
                    //     data: 'transfer_state'
                    // },
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

            table.select.selector('td:first-child');

            /// search
            var searchVal = new URLSearchParams(window.location.search).get('searchVal');

            if (searchVal) {

                table.search(searchVal).draw();

            }
            ///filter Project Site
            $("#selectProjectSite").change(function() {
                const projectSiteId = $(this).val();
                table.ajax.url('{{ route('fetch_tools_ps') }}?projectSiteId=' + projectSiteId).load();
            })


            table.on('select', function(e, dt, type, indexes) {
                if (type === 'row') {
                    var rows = table.rows(indexes).nodes().to$();
                    $.each(rows, function() {
                        if ($(this).hasClass('bg-gray')) {
                            table.row($(this)).deselect();
                            showToast("error", "Currently on transfer process!");
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

                // console.log(data)
                for (var i = 0; i < data.length; i++) {
                    // arrItem.push({icode: data[i].item_code, idesc: data[i].item_description})

                    /// dati nung nasa request pa ang pagupload ng pic ng tools
                    // <td class="d-sm-table-cell">
                    //             <form id="formRequest-${data[i].id}" method="POST" enctype="multipart/form-data">
                    //                 @csrf
                    //             <input type="file" class="picUpload form-control" name="file" style="width: 88%" data-id="${data[i].id}" multiple data-allow-reorder="true" data-max-file-size="10MB" data-max-files="6" accept="image/*">
                    //             </form>
                    //         </td>

                    $("#tbodyModal").append(
                        `<tr>
                            <td>${data[i].item_code} <input class="toolId" type="hidden" value="${data[i].id}"> <input class="currentSiteId" type="hidden" value="${data[i].current_site_id}"> <input class="currentPe" type="hidden" value="${data[i].current_pe}"> <input class="prevReqNum" type="hidden" value="${data[i].prev_request_num}"> </td>
                            <td class="d-sm-table-cell">${data[i].asset_code}</td>
                            <td class="d-sm-table-cell">${data[i].item_description}</td>
                            </tr>`
                    );
                    // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                }

            })

            $(".closeModalRfteis").click(function() {
                $("#tbodyModal").empty()
            })


            const files = {};

            // Handle file selection for each row
            $(document).on('change', '.picUpload', function() {
                const rowId = $(this).data('id');
                const file = $(this)[0].files[0];

                if (file) {
                    files[rowId] = file;
                } else {
                    delete files[rowId];
                }
            });



            $("#psRequestToolsModalBtn").click(function() {
                const projectName = $("#projectName").val();
                const projectCode = $("#projectCode").val();
                const projectAddress = $("#projectAddress").val();
                const reason = $("#reasonForTransfer").val();

                const currentPe = $("#tbodyModal .currentPe").val();
                const currentSiteId = $("#tbodyModal .currentSiteId").val();
                const prevReqNum = $("#tbodyModal .prevReqNum").val();


                if(!projectName){
                    showToast('warning','Fill up all fields!');
                    return;
                }

                const id = $("#tbodyModal .toolId").map((i, id) => id.defaultValue);


                const selectedItemId = [];

                for (var i = 0; i < id.length; i++) {
                    selectedItemId.push(id[i])
                }

                const arrayToString = JSON.stringify(selectedItemId);

                /// sa hinihiraman dapat ito kaya inalis
                // if (Object.keys(files).length === 0) {
                //     showToast('warning','No picture selected!');
                //     return;
                // }

                // if(Object.keys(files).length !== selectedItemId.length){
                //     showToast('warning','Add picture to all selected tools!');
                //     return;
                // }


                var formData = new FormData();

                // Append each file and its corresponding row_id to FormData
                Object.keys(files).forEach(function(rowId, index) {
                    formData.append('files[]', files[rowId]);
                    formData.append('row_ids[]', rowId);
                });

                formData.append('currentSiteId', currentSiteId);
                formData.append('currentPe', currentPe);
                formData.append('prevReqNum', prevReqNum);
                formData.append('projectName', projectName);
                formData.append('projectCode', projectCode);
                formData.append('projectAddress', projectAddress);
                formData.append('reason', reason);
                formData.append('idArray', arrayToString);
                formData.append('_token', $('input[name=_token]').val());

                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }


                $.ajax({
                    url: '{{ route('ps_request_tools') }}',
                    method: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success(result) {
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
                                text: "ang hinihiraman mong project site ay walang naka assigned na pm",
                                icon: "error"
                            });
                            return
                        }
                        showToast('success', 'Request Successful')
                        $("#rttteModal").modal('hide')
                        table.ajax.reload();
                    }
                })

            })
            //old
            // $('#inputCheck').change(function() {
            //     if ($(this).is(':checked')) {
            //         $("#psRequestToolsModalBtn").prop('disabled', false);
            //         $("#accordion_tac").collapse('show');
            //     } else {
            //         $("#psRequestToolsModalBtn").prop('disabled', true);
            //         $("#accordion_tac").collapse('hide');
            //     }
            // });


            $("#backAgreement").click(function(){
                $('#inputCheck').prop('checked', false);
                $("#psRequestToolsModalBtn").prop('disabled', true);
            })

            $("#agree").click(function(){
                $('#inputCheck').prop('checked', true);
                $("#psRequestToolsModalBtn").prop('disabled', false);
            })

            $('#projectName').change(function() {
                const selectedPcode = $(this).find(':selected')
                const pCode = selectedPcode.data('pcode');
                const pAddress = selectedPcode.data('paddress');

                $("#projectCode").val(pCode)
                $("#projectAddress").val(pAddress)

            });

        })
    </script>
@endsection
