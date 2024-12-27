@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">
    <link rel="stylesheet" href="{{ asset('js/plugins/magnific-popup/magnific-popup.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/track_request.css') }}"> --}}

    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order {
            display: none;
        }
        .pictureContainer{
            display: block;
            white-space: nowrap; 
            width: 110px !important; 
            overflow-x: hidden;
            text-overflow: ellipsis;
        }

        /* for popover */
        /* Reduce popover header text size and padding */
        .popover-header {
            font-size: 12px !important; /* Smaller text */
            padding: 4px 5px !important; /* Reduced padding */
            line-height: 1.2 !important; /* Adjust line spacing */
        }

        /* Reduce popover body text size and padding */
        .popover-body {
            font-size: 12px !important; /* Smaller text */
            padding: 5px !important; /* Reduced padding */
            line-height: 1.4 !important; /* Adjust line spacing */
        }

        /* Optional: Adjust overall padding of the popover */
        .popover {
            padding: 4px !important; /* Minimal space around the popover */
        }


    </style>
@endsection

@section('content-title', 'Ongoing TEIS Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Request#</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>TEIS</th>
                            {{-- <th>TERS</th> --}}
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

    @include('pages.modals.ongoing_teis_request_modal')
    @include('pages.modals.track_request_modal')

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>
    <script src="{{ asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

    <script type="module">
        Codebase.helpersOnLoad(['jq-magnific-popup']);
    </script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        $(document).ready(function() {

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                // scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('ongoing_teis_request') }}'
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'teis_number'
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
                        data: 'request_status'
                    },
                    {
                        data: 'request_type'
                    },
                    {
                        data: 'teis'
                    },
                    // {
                    //     data: 'ters'
                    // },
                    {
                        data: 'action'
                    },
                ],
                drawCallback: function() {
                    $(".trackBtn").tooltip();


                    $(".trackBtn").click(function() {
                        const requestNumber = $(this).data('requestnumber');
                        const trType = $(this).data('trtype');

                        $.ajax({
                            url: "{{ route('track_request') }}",
                            method: "post",
                            data: {
                                requestNumber,
                                trType,
                                _token: "{{ csrf_token() }}"
                            },
                            success(result) {


                                $("#requestProgress").html(result)
                                $(".trackRequestNumber").text('#' + requestNumber)

                                // $("#requestProgress li").each(function(index) {
                                //     if (index < result) {
                                //         $(this).addClass("active");
                                //     }
                                // });

                            }
                        })
                    })
                }
            });

            let type;

            $(document).on('click', '.teisNumber', function() {
               

                const id = $(this).data("id");
                type = $(this).data("transfertype");

                $.ajax({
                    url: '{{ route('view_transfer_request') }}',
                    method: 'get',
                    data: {
                        id,
                        type,
                        _token: '{{ csrf_token() }}',
                    },
                    success(result) {
                        $("#requestFormLayout").html(result)
                        $('.popoverInPending').popover({ trigger: 'hover' })
                        

                        // const table = $("#modalTable").DataTable({
                        //     paging: false,
                        //     order: false,
                        //     searching: false,
                        //     info: false,
                        //     sort: false,
                        //     processing: true,
                        //     serverSide: false,
                        //     destroy: true,
                        // });
                        
                        /// Check if DataTable is already initialized and destroy it
                        if ($.fn.DataTable.isDataTable("#modalTable")) {
                            $("#modalTable").DataTable().clear().destroy();
                        }



                        if (type == 'rfteis') {
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
                                    url: '{{ route('ongoing_teis_request_modal') }}',
                                    data: {
                                        id,
                                        type,
                                        _token: '{{ csrf_token() }}'
                                    }

                                },
                                columns: [
                                    // {
                                    //     data: 'picture'
                                    // },
                                    // {
                                    //     data: 'po_number'
                                    // },
                                    // {
                                    //     data: 'asset_code'
                                    // },
                                    // {
                                    //     data: 'serial_number'
                                    // },
                                    {
                                        data: 'qty'
                                    },
                                    {
                                        data: 'asset_code'
                                    },
                                    {
                                        data: 'item_description'
                                    },
                                    {
                                        data: 'item_code'
                                    },

                                    // {
                                    //     data: 'brand'
                                    // },
                                    // {
                                    //     data: 'warehouse_name'
                                    // },
                                    // {
                                    //     data: 'price'
                                    // },
                                    // {
                                    //     data: 'tools_status'
                                    // },
                                    {
                                        data: 'action'
                                    }
                                ],
                                scrollX: true,
                                initComplete: function() {

                                        $('.popoverInRfteis').popover({ trigger: 'hover' })
                                        const data = modalTable.rows().data();

                                        console.log(data)

                                        let totalAmount = 0;

                                        
                                        for (var i = 0; i < data.length; i++) {

                                            let formattedNumber = pesoFormat(data[i].price);

                                            totalAmount = totalAmount + Number(data[i].price);
                                            
                                            $("#itemListDaf").append(
                                                `<p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;"> 
                                                    ${data[i].qty} ${data[i].unit ? data[i].unit : ''} - ${data[i].asset_code} ${data[i].item_description} 
                                                    (${data[i].price ? `<span class="toolPrice" data-id="${data[i].tool_id}" data-reqnum="${data[i].r_number}" > ${formattedNumber} </span>` : `<span class="text-danger toolPrice" data-id="${data[i].tool_id}  data-reqnum="${data[i].r_number}""> No Price </span>`})
                                                    </p>`
                                                );
                                                
                                                // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                                        }


                                        const amountInWord = numberstowords.toInternationalWords(totalAmount, {
                                            integerOnly: false, 
                                            useCurrency: true,
                                            majorCurrencySymbol: 'pesos',
                                            minorCurrencySymbol: 'centavos',
                                            majorCurrencyAtEnd: true,
                                            minorCurrencyAtEnd: true,
                                            // useOnlyWord: true,
                                            useCase: 'upper', // Converts the result to uppercase
                                            useComma: true,   // Adds commas for readability
                                            useAnd: true
                                        })

                                            
                                        $('#amountInFigure').text(pesoFormat(totalAmount));
                                        $('#amountInWord').text(amountInWord);
                                        // console.log(data)
                                    },
                                drawCallback: function() {
                                    $(".receivedBtn").tooltip();

                                    // if(type == 'rttte'){
                                    //     $('table thead th.pictureHeader').show();
                                    // }else{
                                    //     $('table thead th.pictureHeader').hide();
                                    // }
                                }
                            });
                        } else {
                            const modalTable = $("#modalTable").DataTable({
                                paging: false,
                                order: false,
                                searching: false,
                                info: false,
                                sort: false,
                                processing: true,
                                serverSide: false,
                                destroy: true,
                                // scrollX: true,
                                ajax: {
                                    type: 'get',
                                    url: '{{ route('ps_ongoing_teis_request_modal') }}',
                                    data: {
                                        id,
                                        _token: '{{ csrf_token() }}'
                                    }

                                },
                                columns: [{
                                        data: 'picture'
                                    },
                                    {
                                        data: 'item_no'
                                    },
                                    {
                                        data: 'teis_no'
                                    },
                                    {
                                        data: 'asset_code'
                                    },
                                    {
                                        data: 'item_description'
                                    },
                                    {
                                        data: 'serial_number'
                                    },
                                    {
                                        data: 'qty'
                                    },
                                    {
                                        data: 'unit'
                                    },
                                    {
                                        data: 'tools_status'
                                    },
                                    {
                                        data: 'reason_for_transfer'
                                    },
                                    {
                                        data: 'action'
                                    },
                                ],
                                initComplete: function() {
                                const data = modalTable.rows().data();

                                console.log(data)

                                let totalAmount = 0;

                                
                                for (var i = 0; i < data.length; i++) {

                                    let formattedNumber = pesoFormat(data[i].price);

                                    totalAmount = totalAmount + Number(data[i].price);
                                    
                                    $("#itemListDaf").append(
                                        `<p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;"> 
                                            ${data[i].qty} ${data[i].unit ? data[i].unit : ''} - ${data[i].asset_code} ${data[i].item_description} 
                                            (${data[i].price ? `<span class="toolPrice" data-id="${data[i].tool_id}" data-reqnum="${data[i].r_number}" > ${formattedNumber} </span>` : `<span class="text-danger toolPrice" data-id="${data[i].tool_id}  data-reqnum="${data[i].r_number}""> No Price </span>`})
                                            </p>`
                                        );
                                        
                                        // $("#tbodyModal").append('<td></td><td class="d-none d-sm-table-cell"></td><td class="text-center"><div class="btn-group"><button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" title="Delete"><i class="fa fa-times"></i></button></div></td>');
                                }


                                const amountInWord = numberstowords.toInternationalWords(totalAmount, {
                                    integerOnly: false, 
                                    useCurrency: true,
                                    majorCurrencySymbol: 'pesos',
                                    minorCurrencySymbol: 'centavos',
                                    majorCurrencyAtEnd: true,
                                    minorCurrencyAtEnd: true,
                                    // useOnlyWord: true,
                                    useCase: 'upper', // Converts the result to uppercase
                                    useComma: true,   // Adds commas for readability
                                    useAnd: true
                                })

                                    
                                $('#amountInFigure').text(pesoFormat(totalAmount));
                                $('#amountInWord').text(amountInWord);
                                // console.log(data)
                            },
                                drawCallback: function() {
                                    $('table thead th.pictureHeader').show();
                                }
                            });
                        }

                        // if (type == 'rttte') {
                        //     modalTable.column(0).visible(true);
                        //     modalTable.column(0).searchable(true);
                        // } else {
                        //     modalTable.column(0).visible(false);
                        //     modalTable.column(0).searchable(false);
                        // }

                    }
                })




            })

            $(document).on('click', '.receivedBtn', function() {
                const id = $(this).data('triid');
                const teis_num = $(this).data('number');

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ms-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Receive?",
                    text: "Are you sure you want to Received this tool?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Back",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('scanned_teis_received') }}',
                            method: 'post',
                            data: {
                                id,
                                teis_num,
                                type,
                                _token: '{{ csrf_token() }}',
                            },
                            success(result) {
                                showToast("success",
                                    "Tool Received");
                                $("#modalTable").DataTable().ajax.reload();

                            }
                        })

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {

                    }
                });


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
