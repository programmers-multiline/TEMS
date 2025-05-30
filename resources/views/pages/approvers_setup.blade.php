@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">
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

@section('content-title', 'Approver Setup')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="d-flex gap-3 w-75 mb-3">
            <select class="js-select2 form-select" id="selectCompany">
                <option selected disabled>Select Company</option>
                @foreach ($companies as $comp)
                    <option value="{{ $comp->id }}">{{ $comp->code }}</option>
                @endforeach
            </select>
            <select class="js-select2 form-select" id="selectRequestType">
                <option selected disabled>Select Request Type</option>
                @foreach ($request_types as $rt)
                    <option value="{{ $rt->id }}">{{ Str::upper($rt->name) }}</option>
                @endforeach
            </select>
            <select class="js-select2 form-select" id="selectArea" disabled>
                <option selected disabled>Select Area</option>
                <option value="south">South</option>
                <option value="north">North</option>
            </select>
            <select class="js-select2 form-select" id="selectRequestor" disabled>
            </select>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content-full overflow-x-auto">
                <div class="block block-rounded">
                    <div class="block-content">
                        <button type="button" class="btn btn-primary mb-3" id="addApproverBtn" data-triggerby="add"
                            data-bs-toggle="modal" data-bs-target="#approverSetupModal" disabled>Add Approver</button>
                        {{-- <button type="button" class="btn btn-success mb-3" id="addZeroSequenceBtn" disabled>Add 0 Sequence</button> --}}
                        <p id="result"></p>
                        {{-- <ul class="list-group push" id="approvers">
                        </ul> --}}
                        <table id="tableApprovers"
                            class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                            <thead>
                                <tr>
                                    <th>Sequence</th>
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

    <div class="modal fade" id="approverSetupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Setup Approver</h3>
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

                        <div class="d-flex align-content-center justify-content-between gap-2">
                            <select class="js-select2 form-select" style="width: 30%;" id="selectCompanyModal">
                                <option selected disabled>Select Company</option>
                                @foreach ($companies as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->code }}</option>
                                @endforeach
                            </select>
                            <select class="js-select2 form-select" style="width: 70%;" name="states[]"
                                id="selectApproverModal" multiple="multiple">
                            </select>
                        </div>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="addApproverModal" type="button" class="btn btn-alt-success">
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
            });

            $("#selectRequestType, #selectCompany, #selectArea, #selectRequestor").change(function() {

                let table;

                const RT = $("#selectRequestType").val()
                const company = $("#selectCompany").val();

                if (RT == 4) {
                    table = $("#tableApprovers").DataTable({
                        processing: true,
                        serverSide: false,
                        searchable: true,
                        pagination: true,
                        destroy: true,
                        ajax: {
                            type: 'get',
                            url: '{{ route('fetch_approvers') }}',
                            data: {
                                company,
                                RT,
                                _token: '{{ csrf_token() }}'
                            },
                        },
                        columns: [{
                                data: 'sequence'
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
                        rowReorder: {
                            dataSrc: 'sequence',
                            cancelable: true

                        }
                    });

                    return
                }


                if ($("#selectRequestType").val() && $("#selectCompany").val() && $("#selectArea").val() &&
                    $("#selectRequestor").val()) {
                    $("#addApproverBtn").prop("disabled", false);
                    $("#addZeroSequenceBtn").prop("disabled", false);
                    const requestType = $("#selectRequestType").val();
                    const company = $("#selectCompany").val();
                    const area = $("#selectArea").val();
                    const requestor = $("#selectRequestor").val();

                    // $.ajax({
                    //     url: '{{ route('fetch_approvers') }}',
                    //     method: 'post',
                    //     data: {
                    //         requestType,
                    //         company,
                    //         _token: '{{ csrf_token() }}'
                    //     },
                    //     success(result) {
                    //         $("#approvers").html(result);
                    //     }
                    // });


                    table = $("#tableApprovers").DataTable({
                        processing: true,
                        serverSide: false,
                        searchable: true,
                        pagination: true,
                        destroy: true,
                        // "aoColumnDefs": [{
                        //         "bSortable": false,
                        //         "aTargets": [0]
                        //     },
                        //     {
                        //         "targets": [1],
                        //         "visible": false,
                        //         "searchable": false
                        //     },
                        //     {
                        //         "targets": [0], 
                        //         "visible": userId != 2,
                        //         "searchable": userId != 2
                        //     }
                        // ],
                        ajax: {
                            type: 'get',
                            url: '{{ route('fetch_approvers') }}',
                            data: {
                                requestType,
                                company,
                                area,
                                requestor,
                                _token: '{{ csrf_token() }}'
                            },
                        },
                        columns: [{
                                data: 'sequence'
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

                        rowReorder: {
                            dataSrc: 'sequence',
                            cancelable: true
                        }
                    });

                    const newSequence = [];

                    table.on('row-reorder', function(e, diff, edit) {
                        let triggerRowData = edit.triggerRow.data();

                        let result = 'Reorder started on row: ' + triggerRowData.sa_id + '(' + triggerRowData.id + ')' +
                            triggerRowData.fullname +
                            '<br>';

                        for (var i = 0, ien = diff.length; i < ien; i++) {
                            let rowData = table.row(diff[i].node).data();

                            result +=
                                `${rowData.sa_id}.(${rowData.id})${rowData.fullname} updated to be in position ${diff[i].newData} ` +
                                `(was ${diff[i].oldData})<br>`;

                            let data = {
                                id: rowData.sa_id,
                                newSec: diff[i].newData
                            }

                            newSequence.push(data)
                        }

                        document.querySelector('#result').innerHTML = result;

                        $.ajax({
                            url: '{{ route('update_sequence') }}',
                            method: 'post',
                            data: {
                                newSequence,
                                _token: '{{ csrf_token() }}'
                            },
                            success() {
                                table.ajax.reload();
                            }
                        })
                    });


                }

            })





            $("#selectRequestType").change(function() {
                if ($(this).val() != 4) {
                    $("#selectArea").prop('disabled', false)
                    $("#selectRequestor").prop('disabled', false)
                    $("#addApproverBtn").prop('disabled', true)
                    $("#addZeroSequenceBtn").prop("disabled", true);
                } else {
                    $("#selectArea").prop('disabled', true)
                    $("#selectRequestor").prop('disabled', true)
                    $("#addApproverBtn").prop('disabled', false)
                    $("#addZeroSequenceBtn").prop("disabled", false);
                }
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
            $("#addApproverModal").click(function() {
                const selectedComp = $("#selectCompany").val();
                const selectedRT = $("#selectRequestType").val();
                const selectedArea = $("#selectArea").val();
                const selectedRequestor = $("#selectRequestor").val();
                const selectedApprover = $("#selectApproverModal").val();
                const hiddenTriggerBy = $("#hiddenTriggerBy").val();
                const hiddenId = $("#hiddenId").val();

                // if(hiddenTriggerBy == 'edit'){
                //     $('#selectApproverModal').removeAttr('multiple');
                // }else{
                //     $('#selectApproverModal').attr('multiple', 'multiple');
                // }

                const stringApprover = JSON.stringify(selectedApprover)

                $.ajax({
                    url: '{{ route('add_approvers') }}',
                    method: 'post',
                    data: {
                        arrApprover: stringApprover,
                        selectedComp,
                        selectedRT,
                        selectedArea,
                        selectedRequestor,
                        hiddenTriggerBy,
                        hiddenId,
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        // showToast()
                        $("#approverSetupModal").modal('hide')
                        $("#tableApprovers").DataTable().ajax.reload()
                    }
                });
            })

            $(document).on('click', '.deleteApprover', function() {
                const setupApproverId = $(this).data('id');
                const button = $(this);

                $.ajax({
                    url: '{{ route('delete_approver') }}',
                    method: 'post',
                    data: {
                        setupApproverId,
                        _token: '{{ csrf_token() }}'
                    },
                    success() {
                        showToast('success', 'Approver deleted!')
                        // button.closest('li').remove();
                        $("#tableApprovers").DataTable().ajax.reload()
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

            $("#addZeroSequenceBtn").click(function(){
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
                    success(){
                        $("#tableApprovers").DataTable().ajax.reload();
                    }
                })
            })

        })
    </script>
@endsection
