@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{asset("js/plugins/datatables-select/css/select.dataTables.css")}}">
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
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-scroll">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Items</th>
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
                            <div class="col-6">
                                <label class="form-label" for="pe">Project Enginner <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pe" name="pe" disabled
                                    placeholder="Enter PE" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="pickupDate">Pick-up Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="pickupDate" name="pickupDate" min="{{ date('Y-m-d') }}"
                                    placeholder="Enter Pick-up Date">
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
    <script src="{{asset('js/plugins/datatables-select/js/dataTables.select.js')}}"></script>
    <script src="{{asset('js/plugins/datatables-select/js/select.dataTables.js')}}"></script>

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
                            '<p><strong>Project Name:</strong> ' + info.event.extendedProps.project_name + '</p>' +
                            '<p><strong>Project PE:</strong> ' + info.event.extendedProps.pe + '</p>' +
                            '<p><strong>Client:</strong> ' + info.event.extendedProps.client + '</p>' +
                            '<p><strong>Address:</strong> ' + info.event.extendedProps.project_address + '</p>' +
                            '<p><strong>Contact Number:</strong> ' + info.event.extendedProps.contact + '</p>' +
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


            $("#btnAddSched").click(function() {
                const pickupDate = $("#pickupDate").val();

                $.ajax({
                    url: '{{ route('add_schedule') }}',
                    method: 'post',
                    data: {
                        pickupDate,
                        pulloutNum,
                        _token: '{{ csrf_token() }}',

                    },
                    success() {
                        calendar.refetchEvents();
                        $("#addSched").modal('hide')
                    }
                })
            })



            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_pullout_request') }}'
                },
                columns: [{
                        data: 'view_tools'
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

            let type;

            $(document).on('click', '.teisNumber', function() {


                const id = $(this).data("id");
                type = $(this).data("transfertype");
                const path = $("#path").val();

                const modalTable = $("#modalTable").DataTable({
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    "aoColumnDefs": [{
                            "bSortable": false,
                            "aTargets": [0]
                        }
                    ],
                    ajax: {
                        type: 'get',
                        url: '{{ route('ongoing_pullout_request_modal') }}',
                        data: {
                            id,
                            type,
                            path,
                            _token: '{{ csrf_token() }}'
                        }

                    },
                    columns: [{
                            data: null,
                            render: DataTable.render.select(),
                            className: 'selectTools'
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
                            data: 'new_tools_status'
                        },
                        {
                            data: 'action'
                        }
                    ],
                    select: {
                        style: 'multi+shift',
                        selector: 'td'
                    },
                    scrollX: true,
                    drawCallback: function() {
                        $(".receivedBtn").tooltip();
                    }
                });

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

                        const selectedItemId = [];

                        for (var i = 0; i < data.length; i++) {
                            selectedItemId.push(data[i].pri_id)
                        }

                        const arrayToString = JSON.stringify(selectedItemId);

                        const modalTable = $("#modalTable").DataTable()

                        $.ajax({
                            url: '{{ route('received_pullout_tools') }}',
                            method: 'post',
                            data: {
                                id,
                                multi,
                                priIdArray: arrayToString,
                                _token: "{{ csrf_token() }}"
                            },
                            success() {
                                modalTable.ajax.reload();
                                showToast("success", "Received Successful");
                            }
                        })
                    })

            })

            $(document).on('click', '#addSchedBtn', function() {
                const pe = $(this).data('pe');
                const location = $(this).data('location');
                const pickUpdate = $(this).data('pickupdate');

                $("#pe").val(pe)
                $("#pickupDate").val(pickUpdate)
                $("#location").val(location)

            })




            // $("#poNumber").keypress(function(e) {
            //     if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
            // });

            // $("#btnAddTools").click(function() {

            //     // $("#poNumber").val("");
            //     // $("#assetCode").val("");
            //     // $("#serialNumber").val("");
            //     // $("#itemCode").val("");
            //     // $("#itemDescription").val("");
            //     // $("#brand").val("");



            //     var input = $("#addToolsForm").serializeArray();
            //     $.ajax({
            //         url: '{{ route('add_tools') }}',
            //         method: 'post',
            //         data: input,
            //         success() {
            //             $("#modal-tools").modal('hide')
            //             table.ajax.reload();
            //             $("#addToolsForm")[0].reset();
            //             // $('#closeModal').click();

            //         }
            //     })
            // })


            // $(document).on('click', '#editBtn', function() {
            //     const id = $(this).data('id');
            //     const po = $(this).data('po');
            //     const asset = $(this).data('asset');
            //     const serial = $(this).data('serial');
            //     const itemCode = $(this).data('itemcode');
            //     const itemDesc = $(this).data('itemdesc');
            //     const brand = $(this).data('brand');
            //     const location = $(this).data('location');
            //     const status = $(this).data('status');

            //     $("#editPo").val(po);
            //     $("#editAssetCode").val(asset);
            //     $("#editSerialNumber").val(serial);
            //     $("#editItemCode").val(itemCode)
            //     $("#editItemDescription").val(itemDesc)
            //     $("#editBrand").val(brand)
            //     $("#editLocation").val(location)
            //     $("#editStatus").val(status)
            //     $("#hiddenId").val(id)

            // })

            // $("#updateBtnModal").click(function() {

            //     const hiddenToolsId = $("#hiddenId").val()
            //     const updatePo = $("#editPo").val();
            //     const updateAsset = $("#editAssetCode").val();
            //     const updateSerial = $("#editSerialNumber").val();
            //     const updateItemCode = $("#editItemCode").val()
            //     const updateItemDesc = $("#editItemDescription").val()
            //     const updateBrand = $("#editBrand").val()
            //     const updateLocation = $("#editLocation").val()
            //     const updateStatus = $("#editStatus").val()

            //     $.ajax({
            //         url: '{{ route('edit_tools') }}',
            //         method: 'post',
            //         data: {
            //             hiddenToolsId,
            //             updatePo,
            //             updateAsset,
            //             updateSerial,
            //             updateItemCode,
            //             updateItemDesc,
            //             updateBrand,
            //             updateLocation,
            //             updateStatus,
            //             _token: "{{ csrf_token() }}"
            //         },
            //         success(e) {
            //             table.ajax.reload();
            //             $('#closeEditToolsModal').click();
            //             // console.log(e)
            //             alert()
            //         }
            //     })
            // })

            // $(document).on('click','#deleteToolsBtn', function(){
            //     const id = $(this).data('id');

            //     $.ajax({
            //         url: '{{ route('delete_tools') }}',
            //         method: 'post',
            //         data: {
            //             id,
            //             _token: "{{ csrf_token() }}"
            //         },
            //         success(){
            //             table.ajax.reload();
            //         }
            //     })

            // })

            // $("#tableContainer").click(function(){
            //     const dataCount = table.rows({ selected: true }).count();

            //     if(dataCount > 0){
            //         $("#requesToolstBtn").prop('disabled', false);
            //     }else{
            //         $("#requesToolstBtn").prop('disabled', true);

            //     }
            // })


            // $("#requesToolstBtn").click(function(){
            //     const data = table.rows({ selected: true }).data();

            //     // const arrItem = []


            //     for(var i = 0; i < data.length; i++ ){
            //         // arrItem.push({icode: data[i].item_code, idesc: data[i].item_description})

            //     $("#tbodyModal").append(`<tr><td>${data[i].item_code}</td><td class="d-none d-sm-table-cell">${data[i].item_description}</td></tr>`);
            //         // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
            //     }

            // })

            // $(".closeModalRfteis").click(function(){
            //     $("#tbodyModal").empty()
            // })

        })
    </script>
@endsection
