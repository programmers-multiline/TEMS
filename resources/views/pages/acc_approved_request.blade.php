@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.1/css/select.dataTables.css">
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

@section('content-title', 'List of Approved Request')

@section('content')
<div class="loader-container" id="loader" style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1033;">
    <dotlottie-player src="{{asset('js/loader.json')}}" background="transparent" speed="1" style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal" loop autoplay>Loading</dotlottie-player>
</div>
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
                            <th>Request#</th>
                            <th>Subcon</th>
                            <th>Customer Name</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
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

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/select/2.0.1/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.1/js/select.dataTables.js"></script>

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


    <!-- Fileupload JS -->
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>

    {{-- <script type="module">
    Codebase.helpersOnLoad('cb-table-tools-checkable');
  </script> --}}


    <script>
        $(document).ready(function() {

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                // "aoColumnDefs": [
                //     {
                //         // visible ang approver_name kapag 3 or 5 ang userType.
                //         "targets": [1],
                //         "visible": [3, 5].includes(utid),
                //         "searchable": [3, 5].includes(utid)
                //     }
                // ],
                ajax: {
                    type: 'get',
                    url: '{{ route('acc_approved_request') }}',
                },
                columns: [{
                        data: 'view_tools'
                    },
                    {
                        data: 'request_number'
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
                ],
            });

            let type;

            $(document).on('click', '.teisNumber', function() {
               

                const id = $(this).data("id");
                type = $(this).data("trtype");

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

                    }
                })




            })

        })
    </script>
@endsection
