@php
    // $auth_pg = App\Models\AssignedProjects::leftjoin('project_sites as ps', 'ps.id', 'assigned_projects.project_id')
    //                                         ->select('ps.id', 'ps.project_code', 'ps.project_name')
    //                                         ->where('ps.status', 1)
    //                                         ->where('assigned_projects.status', 1)
    //                                         ->where('assigned_projects.user_id', Auth::id())
    //                                         ->where('assigned_projects.pos', 'pe')
    //                                         ->get();

    $auth_pg = App\Models\ProjectSites::where('status', 1)->select('project_sites.id', 'project_sites.project_code', 'project_sites.project_name')->get();
    $pes = App\Models\user::where('status', 1)->where('user_type_id', 4)->select('id', 'fullname')->get();
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
    </style>
@endsection

@section('content-title', 'Upload Outstading Tools')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="loader-container" id="loader"
            style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1033;">
            <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
                style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal"
                loop autoplay>Loading</dotlottie-player>
        </div>
        <form id="importForm" enctype="multipart/form-data">
            <select class="col-12 col-md-6 col-lg-4" id="selectProjectSite" name="psite" required>
                <option value="" disabled selected>Select Project Site</option>
                @foreach ($auth_pg as $site)
                    <option value="{{ $site->id }}">{{ $site->project_code .' - '. $site->project_name }}</option>
                @endforeach
            </select>
             <select class="col-12 col-md-6 col-lg-4" id="selectPe" name="pe" required>
                <option value="" disabled selected>Select PE</option>
                @foreach ($pes as $pe)
                    <option value="{{ $pe->id }}">{{ $pe->fullname}}</option>
                @endforeach
            </select>
            <div class="d-flex mb-3 mt-3 justify-content-between flex-wrap align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <input type="file" class="form-control" name="file" id="fileInput" accept=".csv, .xlsx, .xls" required>
                    <button class="btn btn-sm btn-success w-100" type="submit">
                        <i class="fa fa-upload me-2"></i>Upload & Preview
                    </button>
                </div>

                <button id="downloadTemplate" class="btn btn-sm btn-info mt-3">
                    <i class="fa fa-download me-2"></i>Download Excel Template
                </button>
            </div>
        </form>
        
        <!-- Moved outside the form -->
        <button id="confirmImport" class="btn btn-sm btn-primary mt-3 mt-lg-0" style="display:none;">
            <i class="fa fa-file-import me-2"></i>Import Tools
        </button>
        
        <input type="hidden" id="upload_id" name="upload_id">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>Quantity</th>
                            <th>Teis# Reference</th>
                            @if (Auth::user()->user_type_id == 7)
                                <th>Asset Code</th>
                                <th>Cost</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- END Page Content -->

        @include('pages.modals.ongoing_teis_request_modal')
        @include('pages.modals.track_request_modal')

    @endsection




    @section('js')


        {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
        <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
        <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>
        <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>


        <script>
            $("#selectProjectSite").select2({
                placeholder: "Select Project site",
            });

            $("#selectPe").select2({
                placeholder: "Select PE",
            });
            // $(document).ready(function() {

            //     const user_type_id = {{ Auth::user()->user_type_id }}

            //     const table = $("#table").DataTable({
            //         processing: true,
            //         serverSide: false,
            //         scrollX: true,
            //         "aoColumnDefs": [
            //             {
            //                 "targets": [-1, -2],
            //                 "visible": user_type_id == 7,
            //                 "searchable": user_type_id == 7
            //             }
            //         ],
            //         ajax: {
            //             type: 'get',
            //             url: '{{ route('request_list') }}'
            //         },
            //         columns: [{
            //                 data: 'view_tools'
            //             },
            //             {
            //                 data: 'teis_number'
            //             },
            //             {
            //                 data: 'subcon'
            //             },
            //             {
            //                 data: 'project_name'
            //             },
            //             {
            //                 data: 'project_code'
            //             }
            //         ],
            //         drawCallback: function() {
            //             $(".trackBtn").tooltip();
            //         }
            //     });

            // })
            $(document).ready(function() {

                const user_type_id = {{ Auth::user()->user_type_id }}

                let previewTable = $('#table').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    ordering: true
                });

                // Preview Import
                $("#importForm").submit(function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    formData.append('_token', '{{ csrf_token() }}');
                    

                    $.ajax({
                        url: '{{ route('import_preview') }}',
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            previewTable.clear().draw();

                            response.data.forEach(row => {
                                previewTable.row.add([
                                    row[0],
                                    row[1],
                                    row[2],
                                    row[3]
                                ]).draw();
                            });

                            $("#upload_id").val(response.upload_id);

                            $("#confirmImport").show();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.error,
                                icon: "error"
                            });
                        }
                    });
                });

                // Confirm Import
                $("#confirmImport").click(function() {
                    let allData = previewTable.rows().data().toArray();
                    let psite = $("#selectProjectSite").val();
                    let upload_id = $("#upload_id").val();
                    let pe = $("#selectPe").val();

                    $.ajax({
                        url: '{{ route('import_excel') }}',
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            data: allData,
                            psite,
                            upload_id,
                            pe,
                        },
                        beforeSend() {
                            $("#loader").show()
                        },
                        success: function(response) {
                            $("#loader").hide()
                            alert(response.success);
                            
                            previewTable.clear().draw();
                            $("#confirmImport").hide();
                        },
                        error: function(xhr) {
                            alert("Error: " + xhr.responseText);
                        }
                    });
                });


                $("#downloadTemplate").click(function (e) {
                    e.preventDefault();
                    window.location.href = "{{ route('download_excel_template') }}";
                });
            });
        </script>
    @endsection
