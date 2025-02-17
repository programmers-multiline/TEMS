@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css"> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }
    </style>
@endsection

@php
    $companies = App\Models\Companies::where('status', 1)->get();
    $departments = App\Models\Departments::where('status', 1)->get();
    $positions = App\Models\Positions::where('status', 1)->get();
@endphp

@section('content-title', 'Users Management')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="d-flex w-50 mb-3 gap-3 align-items-center">
            <select class="js-select2 form-select w-100" id="selectStatus">
                <option selected disabled>Select Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <button type="button" class="btn btn-primary w-75" id="addUserBtn" data-bs-toggle="modal"
                data-bs-target="#addUserModal">Add User</button>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content-full overflow-x-auto">
                <div class="block block-rounded">
                    <div class="block-content">
                        <table id="users"
                            class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Employee ID</th>
                                    <th>username</th>
                                    <th>fullname</th>
                                    <th>email</th>
                                    <th>password</th>
                                    <th>user_type_id</th>
                                    <th>company</th>
                                    <th>department</th>
                                    <th>position</th>
                                    <th>Area</th>
                                    <th>status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->

    <div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Add User</h3>
                        <div class="block-options closeModalRfteis">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm mt-1 mb-6">
                        <input type="hidden" id="hiddenId">
                        <input type="hidden" id="hiddenTriggerBy">

                        <div id="editApproverModal" class="d-flex align-items-center gap-2 mb-4">
                            <span id="sequenceModal" class="badge rounded-pill bg-primary fs-6"></span>
                            <div>
                                <span id="fnModal" class="fs-4"></span>
                                <div id="compAndPosModal" style="font-size: 13px; margin-top: -3px; font-weight: bold">
                                </div>
                            </div>
                        </div>
                        <form id="userForm">
                            <div>
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label class="form-label" for="empId">Employee ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="empId" name="empId"
                                            placeholder="Enter Employee number" required>
                                    </div>
                                    <div class="col-8">
                                        <label class="form-label" for="fullname">Fullname <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fullname" name="fullname"
                                            placeholder="Enter fullname">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label" for="company">Company <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="company" name="company" size="1">
                                            <option value="" disabled selected>Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label" for="department">Department <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="department" name="department" size="1">
                                            <option value="" disabled selected>Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-7">
                                        <label class="form-label" for="position">Position <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="position" name="position" size="1">
                                            <option value="" disabled selected>Select Position</option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->position }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label" for="userType">Account Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="userType" name="userType" size="1">
                                            <option value="" disabled selected>Select Type</option>
                                            <option value='1'>Admin</option>
                                            <option value='2'>Warehouse</option>
                                            <option value='3'>PM</option>
                                            <option value='4'>PE/Requestor</option>
                                            <option value='5'>OM</option>
                                            <option value='6'>Approver</option>
                                            <option value='7'>Accounting</option>
                                            <option value='8'>Viewer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label" for="email">Email Address <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter Email">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label" for="username">Username <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Enter Username" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="password">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter Password">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="area">Area <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="area" name="area"
                                        placeholder="Enter Area">
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="addUserBtnModal" type="button" class="btn btn-alt-success">
                            Save
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
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}




    <script>
        $(document).ready(function() {

            const table = $("#users").DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_users_admin') }}'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'emp_id'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'fullname'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'password'
                    },
                    {
                        data: 'user_type_id'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'department_name'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'area'
                    },
                    {
                        data: 'user_status'
                    },
                    {
                        data: 'action'
                    },
                ],
            });



            // initiate select 2
            $("#selectCompany").select2({
                placeholder: "Select Company",
            });

            $("#selectRequestType").select2({
                placeholder: "Select Department",
            });

            $("#selectCompanyModal").select2({
                dropdownParent: $("#approverSetupModal"),
                placeholder: "Select Company",
            });

            $("#selectApproverModal").select2({
                dropdownParent: $("#approverSetupModal"),
                placeholder: "Select Approver",
                allowClear: true
            });

            $("#selectArea").select2({
                placeholder: "Select Area"
            });

            $("#selectRequestor").select2({
                placeholder: "Select Requestor"
            })



            $("#selectCompanyModal").change(function() {
                const comp = $(this).val();

                $.ajax({
                    url: '{{ route('fetch_users') }}',
                    method: 'post',
                    data: {
                        comp,
                        _token: '{{ csrf_token() }}'
                    },
                    success(result) {
                        $("#selectApproverModal").html(result);
                    }
                });
            })


            // Ito ang ginamit ko sa pag add ng approver at pag edit ng approver. tinatamad ako gumawa ng bagong modal e hahaha.
            $("#addUserBtnModal").click(function() {
                // const selectedComp = $("#selectCompany").val();
                // const selectedRT = $("#selectRequestType").val();
                // const selectedArea = $("#selectArea").val();
                // const selectedRequestor = $("#selectRequestor").val();
                // const selectedApprover = $("#selectApproverModal").val();
                // const hiddenTriggerBy = $("#hiddenTriggerBy").val();
                // const hiddenId = $("#hiddenId").val();

                // if(hiddenTriggerBy == 'edit'){
                //     $('#selectApproverModal').removeAttr('multiple');
                // }else{
                //     $('#selectApproverModal').attr('multiple', 'multiple');
                // }

                const inputData = $("#userForm").serializeArray();

                $.ajax({
                    url: '{{ route('user_add_edit') }}',
                    method: 'post',
                    data: inputData,
                    success() {
                        // showToast()
                        $("#addUserModal").modal('hide')
                        $("#users").DataTable().ajax.reload()
                    }
                });
            })

            $(document).on('click', '.deleteToolsBtn', function() {
                const userId = $(this).data('id');

                $.ajax({
                    url: '{{ route('change_status') }}',
                    method: 'post',
                    data: {
                        userId,
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        showToast('success', 'User Inactive!')
                        $("#users").DataTable().ajax.reload()
                    }
                })
            })


            $(document).on('click', '.editApprover', function() {
                $('#selectApproverModal').removeAttr('multiple');
                // $('#selectApproverModal').select2('destroy').select2({
                //     dropdownParent: $("#approverSetupModal"),
                //     placeholder: "Select Approver"
                // });

                const setupApproverId = $(this).data('id');
                const fullname = $(this).data('fn');
                const company = $(this).data('comp');
                const position = $(this).data('pos');
                const triggerBy = $(this).data('triggerby');

                const compAndPos = company + ' - ' + position

                $("#fnModal").text(fullname)
                $("#compAndPosModal").text(compAndPos)
                $("#hiddenId").val(setupApproverId)
                $("#hiddenTriggerBy").val(triggerBy)

            })

        })
    </script>
@endsection
