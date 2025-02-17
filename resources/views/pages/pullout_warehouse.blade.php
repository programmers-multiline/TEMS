@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/filepond/filepond.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css') }}">


    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        .filepond--credits {
            display: none;
        }
    </style>
@endsection

@section('content-title', 'List of Pullout Request')

@section('content')
    <div class="loader-container" id="loader"
        style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1056;">
        <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
            style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal"
            loop autoplay>Loading</dotlottie-player>
    </div>
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-scroll">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Pullout#</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Pickup Date</th>
                            <th>Contact</th>
                            <th>Reason</th>
                            <th>TERS</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="block block-rounded p-4">
            <div id="calendar"></div>
        </div>
    </div>
    <!-- END Page Content -->

    <div class="modal fade" id="addSched" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">ADD SCHEDULE</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <div class="row mb-3">
                            <input type="hidden" value="{{ date('Y-m-d') }}" id="currentDate">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="pe">Project Enginner <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pe" name="pe" disabled
                                    placeholder="Enter PE" required>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="pickupDate">Pick-up Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="pickupDate" name="pickupDate"
                                    min="{{ date('Y-m-d') }}" placeholder="Enter Pick-up Date">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="location">Location <span class="text-danger">*</span></label>
                            <input disabled type="text" class="form-control" id="location" name="location"
                                placeholder="Enter Location">
                        </div>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button id="btnAddSched" type="button" class="btn btn-alt-primary">
                            Add
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>




    @include('pages.modals.upload_pullout_modal')
    @include('pages.modals.ongoing_pullout_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>

    <script src="{{ asset('js/plugins/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script
        src="{{ asset('js/plugins/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js') }}">
    </script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <script src="{{ asset('js/plugins/fullcalendar/index.global.min.js') }}"></script>

    <!-- Fileupload JS -->
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        $(document).ready(function() {

            var calendar = new FullCalendar.Calendar($("#calendar")[0], {
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                dateClick: function(info) {
                    alert('Date clicked: ' + info.dateStr);
                },
                events: function(start, callback) {
                    $.ajax({
                        url: '{{ route('fetch_sched_date') }}',
                        // method: 'post'
                        dataType: 'json',
                        success: function(data) {
                            events = data.map(function(event) {
                                return {
                                    title: event.project_address,
                                    start: event.approved_sched_date,
                                    pe: event.fullname,
                                    contact: event.contact_number,
                                    client: event.client,
                                    project_name: event.project_name,
                                    project_address: event.project_address
                                };
                            });
                            console.log(data)
                            callback(events);
                        }
                    });
                },
                eventMouseEnter: function(info) {
                    $(info.el).popover({
                        title: info.event.extendedProps.project_name,
                        placement: 'top',
                        trigger: 'hover',
                        content: '<div class="event-details">' +
                            '<p><strong>Project Name:</strong> ' + info.event.extendedProps
                            .project_name + '</p>' +
                            '<p><strong>Project PE:</strong> ' + info.event.extendedProps.pe +
                            '</p>' +
                            '<p><strong>Client:</strong> ' + info.event.extendedProps.client +
                            '</p>' +
                            '<p><strong>Address:</strong> ' + info.event.extendedProps
                            .project_address + '</p>' +
                            '<p><strong>Contact Number:</strong> ' + info.event.extendedProps
                            .contact + '</p>' +
                            '</div>',
                        container: 'body',
                        html: true
                    }).popover('show');
                }
            });

            calendar.render();


            let pulloutNum;

            $(document).on('click', '#addSchedBtn', function() {
                pulloutNum = $(this).data('pulloutnum')
            })


            const path = $("#path").val();

            console.log(path)

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_pullout_request') }}',
                    data: {
                        path
                    }
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'pullout_number'
                    },
                    {
                        data: 'subcon'
                    },
                    {
                        data: 'client'
                    },
                    {
                        data: 'project_name'
                    },
                    {
                        data: 'project_code'
                    },
                    {
                        data: 'project_address'
                    },
                    {
                        data: 'date_requested'
                    },
                    {
                        data: 'pickup_date'
                    },
                    {
                        data: 'contact_number'
                    },
                    {
                        data: 'reason'
                    },
                    {
                        data: 'ters'
                    },
                    {
                        data: 'action'
                    },
                ],
            });


            $("#btnAddSched").click(function() {
                const pickupDate = $("#pickupDate").val();
                const prevCount = parseInt($("#pulloutForSchedCount").text());

                $.ajax({
                    url: '{{ route('add_schedule') }}',
                    method: 'post',
                    data: {
                        pickupDate,
                        pulloutNum,
                        _token: '{{ csrf_token() }}',

                    },
                    beforeSend() {
                        $("#loader").show();
                    },
                    success() {
                        $("#loader").hide();
                        calendar.refetchEvents();
                        $("#addSched").modal('hide')
                        table.ajax.reload()

                        if (prevCount == 1) {
                            $(".countContainer").addClass("d-none")
                        } else {
                            $("#pulloutForSchedCount").text(prevCount - 1);
                        }
                    }
                })
            })

            let type;

            $(document).on('click', '.teisNumber', function() {


                const id = $(this).data("id");
                type = $(this).data("transfertype");
                const path = $("#path").val();

                $.ajax({
                    url: '{{ route('view_pullout_request') }}',
                    method: 'get',
                    data: {
                        id,
                        path,
                        _token: '{{ csrf_token() }}',
                    },
                    success(result) {
                        $("#requestFormLayout").html(result)


                        const modalTable = $("#modalTable").DataTable({
                            paging: false,
                            order: false,
                            searching: false,
                            info: false,
                            sort: false,
                            processing: true,
                            serverSide: false,
                            destroy: true,
                            ajax: {
                                type: 'get',
                                url: '{{ route('ongoing_pullout_request_modal') }}',
                                data: {
                                    id,
                                    path,
                                    _token: '{{ csrf_token() }}'
                                }

                            },
                            columns: [{
                                    data: 'item_no'
                                },
                                {
                                    data: 'asset_code'
                                },
                                {
                                    data: 'item_code'
                                },
                                {
                                    data: 'teis_no_dr_ar'
                                },
                                {
                                    data: 'item_description'
                                },
                                {
                                    data: 'new_tools_status'
                                },
                                {
                                    data: 'new_tools_status_defective'
                                },
                                {
                                    data: 'reason'
                                },
                                // {
                                //     data: 'action'
                                // }
                            ],
                            drawCallback: function() {

                            },
                            // scrollX: true,
                        });

                    }
                })

                /// old viewing of tools

                // let showColumns = [];

                // if (path == "pages/pullout_for_receiving") {
                //     showColumns = [{
                //             data: null,
                //             render: DataTable.render.select(),
                //             className: 'selectTools'
                //         },
                //         {
                //             data: 'po_number'
                //         },
                //         {
                //             data: 'asset_code'
                //         },
                //         {
                //             data: 'serial_number'
                //         },
                //         {
                //             data: 'item_code'
                //         },
                //         {
                //             data: 'item_description'
                //         },
                //         {
                //             data: 'brand'
                //         },
                //         {
                //             data: 'warehouse_name'
                //         },
                //         {
                //             data: 'tools_status'
                //         },
                //         {
                //             data: 'new_tools_status'
                //         },
                //         {
                //             data: 'action'
                //         }
                //     ]
                // } else {
                //     showColumns = [{
                //             data: 'po_number'
                //         },
                //         {
                //             data: 'asset_code'
                //         },
                //         {
                //             data: 'serial_number'
                //         },
                //         {
                //             data: 'item_code'
                //         },
                //         {
                //             data: 'item_description'
                //         },
                //         {
                //             data: 'brand'
                //         },
                //         {
                //             data: 'warehouse_name'
                //         },
                //         {
                //             data: 'tools_status'
                //         },
                //     ]

                // }


                // const modalTable = $("#modalTable").DataTable({
                //     processing: true,
                //     serverSide: false,
                //     destroy: true,
                //     "aoColumnDefs": [{
                //         "bSortable": false,
                //         "aTargets": [0]
                //     }],
                //     ajax: {
                //         type: 'get',
                //         url: '{{ route('ongoing_pullout_request_modal') }}',
                //         data: {
                //             id,
                //             type,
                //             path,
                //             _token: '{{ csrf_token() }}'
                //         }

                //     },
                //     columns: showColumns,
                //     select: {
                //         style: 'multi+shift',
                //         selector: 'td'
                //     },
                //     scrollX: true,
                //     drawCallback: function() {
                //         $(".receivedBtn").tooltip();
                //     }
                // });

                modalTable.select.selector('td:first-child');

                $(".test").click()
                $(".test").click()
                $(".test").click()

                let data;


                $(document).on("change", ".selectTools", function() {

                    data = modalTable.rows({
                        selected: true
                    }).data();

                })


                $(document).on("click", "#receiveBtnModal", function() {
                    const multi = "multi";

                    const allData = [];

                    console.log(data)

                    for (var i = 0; i < data.length; i++) {

                        const tool_eval = $('.whEval').eq([i]).val()
                        const pri_id = data[i].pri_id
                        const user_eval = data[i].tool_status_eval

                        const datas = {
                            tool_eval,
                            pri_id,
                            user_eval
                        }
                        allData.push(datas)
                    }


                    const arrayToString = JSON.stringify(allData);

                    const modalTable = $("#modalTable").DataTable()

                    $.ajax({
                        url: '{{ route('received_pullout_tools') }}',
                        method: 'post',
                        data: {
                            id,
                            multi,
                            dataArray: arrayToString,
                            _token: "{{ csrf_token() }}"
                        },
                        success() {
                            modalTable.ajax.reload();
                            showToast("success", "Received Successful");
                            if (modalTable.data().count() == 1) {
                                $("#receiveBtnModal").prop('disabled', 'true')
                                setTimeout(function() {
                                    $("#ongoingPulloutRequestModal").modal('hide')
                                }, 1000);
                            }
                        }
                    })
                })

            })

            $(document).on('click', '#addSchedBtn', function() {
                const pe = $(this).data('pe');
                const location = $(this).data('location');
                const pickUpdate = $(this).data('pickupdate');
                const currentDate = $("#currentDate").val();


                if (pickUpdate < currentDate) {

                    $("#pickupDate").val(currentDate);
                } else {

                    $("#pickupDate").val(pickUpdate)
                }

                $("#pe").val(pe)
                $("#location").val(location)

            })

        })
    </script>
@endsection
