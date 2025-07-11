@php
if(Auth::user()->user_type_id == 4){
    $auth_pg = App\Models\AssignedProjects::leftjoin('project_sites as ps', 'ps.id', 'assigned_projects.project_id')
        ->select('ps.id', 'ps.project_code', 'ps.project_name')
        ->where('ps.status', 1)
        ->where('assigned_projects.status', 1)
        ->where('assigned_projects.user_id', Auth::id())
        ->where('assigned_projects.pos', 'pe')
        ->get();
}else{
    $auth_pg = App\Models\ProjectSites::select('project_sites.id', 'project_sites.project_code', 'project_sites.project_name')
        ->where('project_sites.status', 1)
        ->get();
}
    
@endphp

@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        .pictureContainer {
            display: block;
            white-space: nowrap;
            width: 110px !important;
            overflow-x: hidden;
            text-overflow: ellipsis;
        }
        .hidden-important {
            display: none !important;
        }
    </style>
@endsection

@section('content-title', 'List of tools')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <select class="col-12 col-md-6 col-lg-4" id="selectProjectSite" name="psite">
            <option value="" disabled selected>Select Project Site</option>
            @foreach ($auth_pg as $site)
                <option value="{{ $site->id }}">{{ $site->project_code . ' - ' . $site->project_name }}</option>
            @endforeach
        </select>

        <select class="col-12 col-md-6 col-lg-4" id="selectProgress" name="progress">
            <option value="" disabled selected>Select Status</option>
            <option value="0">Pending</option>
            <option value="1">Completed</option>
        </select>

        <button id="confirmImport" class="btn btn-sm btn-primary mt-3 mt-lg-0" style="display:none;">
            <i class="fa fa-file-import me-2"></i>Import Tools
        </button>

        <div id="tableContainer" class="block block-rounded mt-3">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Uploader</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Date Uploaded</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- END Page Content -->


        {{-- modal add tools --}}

        <div class="modal fade" id="ExcelImportDetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
                <div class="modal-content">
                    <div class="block block-rounded shadow-none mb-0">
                        <div class="block-header block-header-default">
                            <input type="hidden" id="path" value="{{ request()->path() }}">
                            <h3 class="block-title">TOOLS AND EQUIPMENTS</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option closeModalBtn" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content fs-sm">
                            @if (Auth::user()->user_type_id == 7 || (Auth::user()->user_type_id == 8 && Auth::user()->comp_id == 3 && Auth::user()->dept_id == 5))
                                <button type="button" id="addPriceBtn" class="btn btn-primary mb-3 ms-auto"><i class="fa fa-clipboard-check me-1"></i>Save Cost</button>
                            @endif
                            <table id="modalTable" class="table fs-sm table-bordered table-hover table-vcenter w-100">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Description</th>
                                        <th>Quantity</th>
                                        <th>TEIS# Ref</th>
                                        <th>Asset Code</th>
                                        @if (Auth::user()->user_type_id == 7 || (Auth::user()->user_type_id == 8 && Auth::user()->comp_id == 3 && Auth::user()->dept_id == 5))
                                            <th>Add Cost</th>
                                        @endif
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="block-content block-content-full block-content-sm text-end border-top">
                            <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalBtn"
                                data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
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
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $("#selectProjectSite").select2({
                placeholder: "Select Project site",
            });

            $("#selectProgress").select2({
                placeholder: "Select Status",
            });


            const user_type_id = {{ Auth::user()->user_type_id }}

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                "aoColumnDefs": [{
                    "targets": [1],
                    "visible": user_type_id == 7 || 8,
                    "searchable": user_type_id == 7 || 8
                }],
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_upload_tools') }}'
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'fullname'
                    },
                    {
                        data: 'project_code'
                    },
                    {
                        data: 'project_name'
                    },
                    {
                        data: 'date_upload'
                    },
                    {
                        data: 'request_status'
                    }
                ],
                drawCallback: function() {
                    $(".trackBtn").tooltip();
                }
            });


            $("#selectProjectSite").change(function() {
                const projectSiteId = $(this).val();
                const status = $('#selectProgress').val();
                table.ajax.url('{{ route('fetch_upload_tools') }}?status=' + status + '&projectSiteId=' + projectSiteId).load();
            })

            $("#selectProgress").change(function() {
                const projectSiteId = $('#selectProjectSite').val();
                const status = $(this).val();
                table.ajax.url('{{ route('fetch_upload_tools') }}?status=' + status + '&projectSiteId=' + projectSiteId).load();
            })


            $(document).on('click', '.uploadToolDetails', function() {

                const id = $(this).data("id");

                if($(this).data("status") === 1){
                    $("#addPriceBtn").addClass("hidden-important");
                }else{
                    $("#addPriceBtn").removeClass("hidden-important");
                }

                // attached to the save price button
                $("#addPriceBtn").attr("data-id", id);

                const modalTable = $("#modalTable").DataTable({
                    scrollX: true,
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    pagination: false,
                    "pageLength": 1000,
                    "aoColumnDefs": [{
                    "targets": [-1, -2],
                    "visible": user_type_id == 7 || 8,
                    "searchable": user_type_id == 7 || 8
                    }],
                    ajax: {
                        type: 'post',
                        url: '{{ route('import_tools_details') }}',
                        data: {
                            id,
                            _token: '{{ csrf_token() }}'
                        }
                    },
                    columns: [
                        {
                            data: 'item_code'
                        },
                        {
                            data: 'item_description'
                        },
                        {
                            data: 'qty'
                        },
                        {
                            data: 'teis_ref'
                        },
                        {
                            data: 'asset_code'
                        },
                        {
                            data: 'add_price'
                        },
                        {
                            data: 'request_status'
                        }
                    ],
                });
            })


            $("#addPriceBtn").click(function() {

            const id = $(this).data("id");

            const allData = [];
            const prices = [];

            $('.price').each(function(i, obj) {


                const id = $(this).data('id')
                const price = obj.value

                prices.push(price)

                const data = {
                    id,
                    price
                }

                // if (price) {
                    allData.push(data)
                // }

            });

            

            const hasEmptyPrice = prices.some(price => price.trim() === "");

            if (hasEmptyPrice) {
                showToast('error', 'Please input cost in every tool!');
                return;
            }

            const stringData = JSON.stringify(allData)

            $.ajax({
                url: '{{ route('import_tool_add_price') }}',
                method: 'post',
                data: {
                    id,
                    priceDatas: stringData,
                    _token: "{{ csrf_token() }}"
                },
                success(response) {
                    console.log(response)
                    if(response === 0){
                        showToast('error', 'Please input cost in every Tools!')
                        return
                    }else{
                        showToast("success", "Price Added!");
                    }
                    $("#addPriceBtn").addClass("hidden-important");
                    $("#table").DataTable().ajax.reload();
                    $("#modalTable").DataTable().ajax.reload();
                }
            })

        })

        })

        // const user_type_id = {{ Auth::user()->user_type_id }}

        // let previewTable = $('#table').DataTable({
        //     paging: true,
        //     searching: true,
        //     info: true,
        //     ordering: true
        // });

        // // Preview Import
        // $("#importForm").submit(function(e) {
        //     e.preventDefault();
        //     let formData = new FormData(this);
        //     formData.append('_token', '{{ csrf_token() }}');


        //     $.ajax({
        //         url: '{{ route('import_preview') }}',
        //         type: "POST",
        //         data: formData,
        //         contentType: false,
        //         processData: false,
        //         success: function(response) {
        //             console.log(response);
        //             previewTable.clear().draw();

        //             response.data.forEach(row => {
        //                 previewTable.row.add([
        //                     row[0],
        //                     row[1],
        //                     row[2],
        //                     row[3]
        //                 ]).draw();
        //             });

        //             $("#upload_id").val(response.upload_id);

        //             $("#confirmImport").show();
        //         },
        //         error: function(xhr) {
        //             alert("Error: " + xhr.responseText);
        //         }
        //     });
        // });

        // // Confirm Import
        // $("#confirmImport").click(function() {
        //     let allData = previewTable.rows().data().toArray();
        //     let psite = $("#selectProjectSite").val();
        //     let upload_id = $("#upload_id").val();

        //     $.ajax({
        //         url: '{{ route('import_excel') }}',
        //         type: "POST",
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             data: allData,
        //             psite,
        //             upload_id,
        //         },
        //         success: function(response) {
        //             alert(response.success);
        //             previewTable.clear().draw();
        //             $("#confirmImport").hide();
        //         },
        //         error: function(xhr) {
        //             alert("Error: " + xhr.responseText);
        //         }
        //     });
        // });
    </script>
@endsection
