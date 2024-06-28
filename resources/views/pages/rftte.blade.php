@extends('layouts.backend')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">
<link rel="stylesheet" href="{{ asset('js/plugins/filepond/filepond.min.css') }}">
<link rel="stylesheet" href="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link rel="stylesheet" href="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css') }}">

<style>
    #table > thead > tr > th.text-center.dt-orderable-none.dt-ordering-asc > span.dt-column-order{
        display: none;
    }

    #table > thead > tr > th.dt-orderable-none.dt-select.dt-ordering-asc > span.dt-column-order{
        display: none;
    }

    .filepond--credits{
        display: none;
    }

</style>
@endsection

@section('content-title', 'List of Request for Transfer')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Type</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>TEIS</th>
                            <th>TERS</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END Page Content -->


@include('pages.modals.create_teis_modal')
@include('pages.modals.ps_upload_ters_modal')
@include('pages.modals.ongoing_teis_request_modal')

@endsection




@section('js')


{{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
<script src="{{asset('js/plugins/datatables-select/js/dataTables.select.js')}}"></script>
<script src="{{asset('js/plugins/datatables-select/js/select.dataTables.js')}}"></script>

<script src="{{ asset('js/plugins/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js') }}"></script>
<script src="{{ asset('js/plugins/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js') }}"></script>


<!-- Fileupload JS -->


{{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
</script> --}}


<script>
    $(document).ready(function() {
        const table = $("#table").DataTable({
            processing: true,
            serverSide: false,
            destroy: true,
            ajax: {
                type: 'get',
                url: '{{ route('fetch_teis_request') }}'
            },
            columns: [
                {
                    data: 'view_tools'
                },
                {
                    data: 'tr_type'
                },
                {
                    data: 'subcon'
                },
                {
                    data: 'customer_name'
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
                    data: 'teis'
                },
                {
                    data: 'ters'
                },
                {
                    data: 'action'
                },
                ],
                drawCallback: function() {
                    $(".deliverBtn").tooltip();
                    $(".uploadTeisBtn").tooltip();
                }
            });
            
            $(document).on('click','.teisNumber',function(){
                
                const id = $(this).data("id");
                const type = $(this).data("type");
                
                
                const modalTable = $("#modalTable").DataTable({
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    ajax: {
                        type: 'get',
                        url: '{{ route('ongoing_teis_request_modal') }}',
                        data: {
                            id, 
                            type,
                            _token:'{{csrf_token()}}'
                        }
                        
                    },
                    columns: [
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
                            data: 'location'
                        },
                        {
                            data: 'tools_status'
                        },
                        {
                            data: 'action'
                        },
                    ],
                });
            })
            
            $(document).on('click', '.deliverBtn, .proceedBtn', function(){
                const requestNum = $(this).data('num');
                const type = $(this).data('type');

                const prevCount = parseInt($("#rftteCount").text());

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ms-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Deliver?",
                    text: "Are you sure you want to deliver this tools?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Close",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('tools_deliver') }}',
                            method: 'post',
                            data: {
                                requestNum,
                                type,
                                _token: '{{ csrf_token() }}'
                            },
                            success() {
                                table.ajax.reload();
                                if(type == 'rfteis'){
                                    confirm.fire({
                                        title: "En Route!",
                                        text: "The tools are out for Delivery.",
                                        icon: "success"
                                    });
                                }else{
                                    confirm.fire({
                                        title: "Success!",
                                        icon: "success"
                                    });
                                }

                                if(prevCount == 1){
                                    $(".countContainer").addClass("d-none")
                                }else{
                                    $("#rftteCount").text(prevCount - 1);
                                }
                            }
                        })

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {

                    }
                });

            })

            // $(document).on('click', '.approveBtn', function() {
            //     const requestNum = $(this).data('num');
            //     const type = $(this).data('type');

            //     const confirm = Swal.mixin({
            //         customClass: {
            //             confirmButton: "btn btn-success ms-2",
            //             cancelButton: "btn btn-danger"
            //         },
            //         buttonsStyling: false
            //     });

            //     confirm.fire({
            //         title: "Approve?",
            //         text: "Are you sure you want to approved this tools?",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: "Yes!",
            //         cancelButtonText: "Close",
            //         reverseButtons: true
            //     }).then((result) => {
            //         if (result.isConfirmed) {

            //             $.ajax({
            //                 url: '{{ route('ps_approve_rttte') }}',
            //                 method: 'post',
            //                 data: {
            //                     requestNum,
            //                     type,
            //                     _token: '{{ csrf_token() }}'
            //                 },
            //                 beforeSend(){
            //                     $("#loader").show()
            //                 },
            //                 success() {
            //                     $("#loader").hide()
            //                     table.ajax.reload();
            //                     confirm.fire({
            //                         title: "Approved!",
            //                         text: "Items Approved Successfully.",
            //                         icon: "success"
            //                     });
            //                 }
            //             })

            //         } else if (
            //             /* Read more about handling dismissals below */
            //             result.dismiss === Swal.DismissReason.cancel
            //         ) {

            //         }
            //     });


            // })
            
            
            
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
                    //         url: '{{ route("edit_tools") }}',
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
                        //         url: '{{route("delete_tools")}}',
                        //         method: 'post',
                        //         data: {
                            //             id,
                            //             _token: "{{csrf_token()}}"
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
<script src="{{ asset('js\lib\fileupload.js') }}"></script>
@endsection
