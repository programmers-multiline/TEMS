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

@section('content-title', 'Project Tagging')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="d-flex gap-3 w-50 mb-3">
            <select class="js-select2 form-select" id="selectProjectSite">
                <option selected disabled>Select Project Site</option>
                @foreach ($project_sites as $project_site)
                    <option value="{{ $project_site->id }}">{{ $project_site->project_name }}</option>
                @endforeach
            </select>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content-full overflow-x-auto">
                <div class="block block-rounded">
                    <div class="block-content">
                        <button type="button" class="btn btn-primary mb-3" id="assignPersonnel" data-triggerby="add"
                            data-bs-toggle="modal" data-bs-target="#projectAssignmentModal" disabled>Assign Personnel</button>
                        {{-- <ul class="list-group push" id="approvers">
                        </ul> --}}
                        <table id="projectSiteTable"
                            class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Position</th>
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

    <div class="modal fade" id="projectAssignmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Assign Personnel</h3>
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

                        <div class="d-flex flex-column align-content-center">
                            <label class="form-label">Select Personnel</label>
                            <select class="js-select2 form-select" style="width: 80%;" name="states[]" id="selectPersonnel"
                                multiple="multiple">
                                @foreach ($users as $user)
                                    <option value="{{ $user->emp_id }}">{{ $user->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="assignBtnModal" type="button" class="btn btn-alt-success">
                            Assign
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




    <script>
        $(document).ready(function() {


            // initiate select 2
            $("#selectProjectSite").select2({
                placeholder: "Select Project Site",
            });

            $("#selectRequestType").select2({
                placeholder: "Select Department",
            });

            $("#selectCompanyModal").select2({
                dropdownParent: $("#approverSetupModal"),
                placeholder: "Select Company",
            });

            $("#selectPersonnel").select2({
                dropdownParent: $("#projectAssignmentModal"),
                // placeholder: "Select Personnel",
            });

            $("#selectArea").select2({
                placeholder: "Select Area"
            });

            $("#selectRequestor").select2({
                placeholder: "Select Requestor"
            });

            $("#selectProjectSite").change(function() {
                $("#assignPersonnel").prop('disabled', false)

                const projectsiteId = $(this).val()


                const table = $("#projectSiteTable").DataTable({
                    processing: true,
                    serverSide: false,
                    searchable: true,
                    pagination: true,
                    destroy: true,
                    ajax: {
                        type: 'get',
                        url: '{{ route('fetch_assigned_personnel') }}',
                        data: {
                            projectsiteId,
                            _token: '{{ csrf_token() }}'
                        },
                    },
                    columns: [{
                            data: 'emp_id'
                        },
                        {
                            data: 'fullname'
                        },
                        {
                            data: 'code'
                        },
                        {
                            data: 'position'
                        },
                        {
                            data: 'action'
                        },
                    ],
                });


            })





            $("#selectRequestType").change(function() {
  
                
            })

            $("#selectArea").change(function() {
                const selectArea = $(this).val();

                $.ajax({
                    url: '{{ route('user_per_area') }}',
                    method: 'post',
                    data: {
                        selectArea,
                        _token: '{{ csrf_token() }}'
                    },
                    success(result) {
                        $("#selectRequestor").prop('disabled', false);
                        $("#selectRequestor").html(result);
                    }
                });
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
            $("#assignBtnModal").click(function() {
                const selectedProjectSite = $("#selectProjectSite").val();
                const selectedPersonnel = $("#selectPersonnel").val();

                // get data of the table
                const data = $("#projectSiteTable").DataTable().rows().data();
                // return emp ID and fullname
                const empIdArray = data.columns([0, 1]).data().toArray();

                let isEmpAlreadyExist = false;

                //check kung ang idadagdag ni om is nasa table na
                for (let index = 0; index < empIdArray[0].length; index++) {
                    const empId = empIdArray[0][index];
                    const empName = empIdArray[1][index];

                    if (selectedPersonnel.includes(empId.toString())) {
                        showToast('error', `${empName} is already in the table.`);
                        isEmpAlreadyExist = true;
                        break;
                    }
                }
                // check lang kung meron emp id na nasa table na
                if(isEmpAlreadyExist){
                    return;
                }
             
                // const hiddenTriggerBy = $("#hiddenTriggerBy").val();
                // const hiddenId = $("#hiddenId").val();

                // if(hiddenTriggerBy == 'edit'){
                //     $('#selectApproverModal').removeAttr('multiple');
                // }else{
                //     $('#selectApproverModal').attr('multiple', 'multiple');
                // }

                const stringPersonnel = JSON.stringify(selectedPersonnel)

                $.ajax({
                    url: '{{ route('assign_personnel') }}',
                    method: 'post',
                    data: {
                        arrPersonnel: stringPersonnel,
                        selectedProjectSite,
               
                        // hiddenTriggerBy,
                        // hiddenId,
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        // showToast()
                        $("#projectAssignmentModal").modal('hide')
                        $("#projectSiteTable").DataTable().ajax.reload()
                        $('#selectPersonnel').val(null).trigger('change');
                    }
                });
            })

            $(document).on('click', '.deletePersonnel', function() {
                const personnelId = $(this).data('id');

                $.ajax({
                    url: '{{ route('delete_personnel') }}',
                    method: 'post',
                    data: {
                        personnelId,
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        showToast('success', 'Personnel deleted!')
                        // button.closest('li').remove();
                        $("#projectSiteTable").DataTable().ajax.reload()
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

            $("#addZeroSequenceBtn").click(function() {
                const requestType = $("#selectRequestType").val();
                const company = $("#selectCompany").val();
                const area = $("#selectArea").val();
                const requestor = $("#selectRequestor").val();

                $.ajax({
                    url: '{{ route('add_zero_sequence') }}',
                    method: 'post',
                    data: {
                        requestType,
                        company,
                        area,
                        requestor,
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        $("#tableApprovers").DataTable().ajax.reload();
                    }
                })
            })

        })
    </script>
@endsection
