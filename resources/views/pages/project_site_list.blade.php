@php

    $pg = App\Models\ProjectSites::select('project_sites.id', 'project_sites.project_code', 'project_sites.project_name')
        ->where('project_sites.status', 1)
        ->get();
    
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
    </style>
@endsection

@section('content-title', 'List of Project sites')

@section('content')
    <!-- Page Content -->
    <div class="content">
        {{-- <select class="col-12 col-md-6 col-lg-4" id="selectProjectSite" name="psite">
            <option value="" disabled selected>Select Project Site</option>
            @foreach ($auth_pg as $site)
                <option value="{{ $site->id }}">{{ $site->project_code . ' - ' . $site->project_name }}</option>
            @endforeach
        </select>

        <select class="col-12 col-md-6 col-lg-4" id="selectProgress" name="progress">
            <option value="" disabled selected>Select Status</option>
            <option value="0">Pending</option>
            <option value="1">Completed</option>
        </select> --}}

        <button id="confirmImport" class="btn btn-sm btn-primary mt-3 mt-lg-0" data-bs-toggle="modal" data-bs-target="#addProjectSiteModal">
            <i class="fa fa-plus me-2"></i>Add Project site
        </button>

        <div id="tableContainer" class="block block-rounded mt-3">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Company</th>
                            <th>Project code</th>
                            <th>Project name</th>
                            <th>project address</th>
                            <th>PE</th>
                            <th>PM</th>
                            <th>area</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>


        <div class="modal fade" id="addProjectSiteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
                <div class="modal-content">
                    <div class="block block-rounded shadow-none mb-0">
                        <div class="block-header block-header-default">
                            <input type="hidden" id="path" value="{{ request()->path() }}">
                            <h3 class="block-title">ADD PROJECT SITE</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option closeModalBtn" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content fs-sm">
                            <form id="addProjectForm">
                                <div>
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <label class="form-label" for="company">Company <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="company" name="company" size="1">
                                                <option value="" disabled selected>Select Company</option>
                                                    <option value="3">MBI</option>
                                                    <option value="2">MSC</option>
                                            </select>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label" for="area">area <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="area" name="area" size="1">
                                                <option value="" disabled selected>Select area</option>
                                                    <option value="south">South</option>
                                                    <option value="north">North</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label class="form-label" for="pcode">Project Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="pcode" name="pcode"
                                                placeholder="Enter Project Code" required>
                                        </div>
                                        <div class="col-8">
                                            <label class="form-label" for="pname">Project Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="pname" name="pname"
                                                placeholder="Enter Project Name">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label" for="plocation">Project Location <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="plocation" name="plocation"
                                                placeholder="Enter Project Location">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label" for="paddress">Project Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="paddress" name="paddress"
                                                placeholder="Enter Project Address">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label" for="customerName">Customer Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customerName" name="customerName"
                                                placeholder="Enter Customer Name" required>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="block-content block-content-full block-content-sm text-end border-top">
                            <button type="button" id="addProjectSite" class="btn btn-primary">Add</button>
                            <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalBtn"
                                data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>




        <!-- END Page Content -->
    </div>
@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $("#selectProjectSite").select2({
                placeholder: "Select Project site",
            });


            const user_type_id = {{ Auth::user()->user_type_id }}

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_project_site_list') }}'
                },
                order: [],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'company_code'
                    },
                    {
                        data: 'project_code'
                    },
                    {
                        data: 'project_name'
                    },
                    {
                        data: 'project_address'
                    },
                    {
                        data: 'pe_name'
                    },
                    {
                        data: 'pm_name'
                    },
                    {
                        data: 'area'
                    },
                    {
                        data: 'progress'
                    }
                    
                ],
                drawCallback: function() {
                    
                }
            });


            $("#selectProjectSite").change(function() {
                const projectSiteId = $(this).val();
                table.ajax.url('{{ route('fetch_upload_tools') }}?projectSiteId=' + projectSiteId).load();
            })

            $(document).on('click', '#addProjectSite', function(){
                const inputData = $("#addProjectForm").serializeArray();

                $.ajax({
                    url: '{{ route('add_project_code') }}',
                    method: 'post',
                    data: inputData,
                    success() {
                        // showToast()
                        $("#addProjectSiteModal").modal('hide')
                        $("#table").DataTable().ajax.reload()
                        $('#addProjectForm')[0].reset()
                    }
                });
            })

        })

    </script>
@endsection
