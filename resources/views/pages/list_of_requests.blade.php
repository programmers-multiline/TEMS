@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
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

@section('content-title', 'List of Request')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="d-flex align-items-center col-12 col-lg-4 mb-3">
            <i class="fa fa-filter fs-2 me-2 text-secondary"></i>
            <select class="form-select" id="selectRT" name="example-select">
                <option value="" disabled selected>Select Request Type</option>
                    <option value="rfteis">RFTEIS</option>
                    <option value="rttte">RTTTE</option>
                    <option value="pullout">PULLOUT</option>
            </select>
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Request#</th>
                            <th>Subcon</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Type</th>
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
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="{{ asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

    <script type="module">
        Codebase.helpersOnLoad(['jq-magnific-popup']);
    </script>


    <script>
        $(document).ready(function() {

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('request_list') }}'
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
                    }
                ],
                drawCallback: function() {
                    $(".trackBtn").tooltip();
                }
            });


            $("#selectRT").change(function() {
                const requestType = $(this).val();
                table.ajax.url('{{ route('request_list') }}?request_type=' + requestType).load();
            })


            let type;

            $(document).on('click', '.teisNumber', function() {
               

                const id = $(this).data("id");
                type = $(this).data("transfertype");

                if(type == 'pullout'){
                    const id = $(this).data("id");
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
                                ],
                                drawCallback: function() {

                                },
                                // scrollX: true,
                            });

                        }
                    })
                }

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
                                        data: 'item_code'
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
                                    }
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
