@extends('layouts.backend')
@php
    $all_pg = App\Models\ProjectSites::select('id', 'project_name', 'project_code')->where('status', 1)->get();

    if(Auth::user()->user_type_id == 3){
        $projectIds = App\Models\AssignedProjects::where('status', 1)
            ->where('user_id', Auth::id())
            ->where('pos', 'pm')
            ->pluck('project_id'); 

        // Get PE user IDs associated with those project IDs
        $peUserIds = App\Models\AssignedProjects::where('status', 1)
            ->whereIn('project_id', $projectIds)
            ->where('pos', 'pe')
            ->pluck('user_id');

        $PEs = App\Models\User::select('id', 'fullname')->where('status', 1)->whereIn('id', $peUserIds)->get();
    }elseif(Auth::user()->user_type_id == 5){
        $get_pe = App\Models\AssignedProjects::where('status', 1)->where('assigned_by', Auth::id())->where('pos', 'pe')->pluck('user_id')->toArray();

        $PEs = App\Models\User::select('id', 'fullname')->where('status', 1)->whereIn('id', $get_pe)->get();
    }else{
        $PEs = App\Models\User::select('id', 'fullname')->where('status', 1)->where('user_type_id', 4)->get();
    }
   
@endphp
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
        .form-select{
            width: unset !important;
            max-width: 500px;
        }
        .pictureContainer{
        display: block;
        white-space: nowrap; 
        width: 90px !important; 
        overflow-x: hidden;
        text-overflow: ellipsis;
    }
    </style>
@endsection

@section('content-title', 'Item Logs')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="d-flex flex-wrap mb-3">
            @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 5)
               <select class="js-select2 form-select w-100 mb-2" id="selectTools">
                    <option disabled selected>Select Tools</option>
            
                </select>
            @endif
            @if (Auth::user()->user_type_id != 4 || Auth::user()->user_type_id == 7)
                <select class="js-select2 form-select w-100 mb-2 ms-3" id="selectPe">
                    <option disabled selected>Select PE</option>
                    @foreach ($PEs as $pe)
                        <option value="{{ $pe->id }}">{{ $pe->fullname }}</option>
                    @endforeach
                </select>
            @endif
        
           @if (Auth::user()->user_type_id == 7)
              <select class="js-select2 form-select w-100 mb-3" id="selectProjectCode">
                <option disabled selected>Select Project code</option>
                    @foreach ($all_pg as $site)
                        <option value="{{ $site->id }}">{{ $site->project_code .' - '. $site->project_name }}</option>
                    @endforeach
                </select> 
           @endif
            
        </div>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Request#</th>
                            <th>Assigned to</th>
                            {{-- <th>PO Number</th> --}}
                            <th>Asset Code</th>
                            <th>Item Code</th>
                            <th>Item Desc</th>
                            <th>TEIS</th>
                            <th>TERS</th>
                            <th>Remarks</th>
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

@endsection




@section('js')


    {{-- <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script> --}}
    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>


    <script>
        $(document).ready(function() {

            $("#selectTools").select2({
                placeholder: "Select Tool",
            });

            $("#selectPe").select2({
                placeholder: "Select PE",
            });

            $("#selectProjectCode").select2({
                placeholder: "Select Project Site",
            });


            const path = $("#path").val();

            const userId = {{ Auth::user()->user_type_id }}

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                destroy: true,
                scrollX: true,
                "aoColumnDefs": [
                    {
                        "targets": [1],
                        "visible": [5, 7].includes(userId),
                        "searchable": [5, 7].includes(userId)
                    },
                    {
                        "targets": [-3],
                        "visible": userId != 7,
                        "searchable": userId != 7
                    }
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('report_pe_logs') }}', 
                },
                columns: [
                    {
                        data: 'request_number'
                    },
                    {
                        data: 'fullname'
                    },
                    // {
                    //     data: 'po_number'
                    // },
                    {
                        data: 'asset_code'
                    },
                    {
                        data: 'item_code'
                    },
                    {
                        data: 'item_description'
                    },
                    {
                        data: 'teis'
                    },
                    {
                        data: 'ters'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'action'
                    },
                ],
                initComplete: function() {
                    const data = table.rows().data();

                    /// para di maulit ang ilalagay sa select

                    // Step 1: Use a Set to store unique IDs
                    const uniqueIds = new Set();

                    // Step 2: Filter the data to include only rows with unique IDs
                    const filteredData = [];

                    data.each(item => {
                        if (!uniqueIds.has(item.id)) {
                            uniqueIds.add(item.id); // Add the unique ID to the Set
                            filteredData.push(item); // Add the row to the filtered data
                        }
                    });

                    for (var i = 0; i < filteredData.length; i++) {

                    $("#selectTools").append(
                        `<option value="${filteredData[i].id}">${filteredData[i].asset_code} - ${filteredData[i].item_description}</option>`
                    );
                    
                    }

                },
                drawCallback: function() {
                    $(".uploadTeisBtn").tooltip();
                    $(".uploadTersBtn").tooltip();
                }
            });
            //? filter sa Tools
            $("#selectTools").change(function() {
                const toolId = $(this).val();
                table.ajax.url('{{ route('report_pe_logs') }}?toolId=' + toolId).load();
            })
            //? filter sa PE
            $("#selectPe").change(function() {
                const PeId = $(this).val();
                table.ajax.url('{{ route('report_pe_logs') }}?PeId=' + PeId).load();
            })

            $("#selectProjectCode").change(function() {
                const projectSiteId = $(this).val();
                table.ajax.url('{{ route('report_pe_logs') }}?projectSiteId=' + projectSiteId).load();
            })

            $(document).on('click', '.teisNumber', function() {

                const id = $(this).data("id");
                const type = $(this).data("transfertype");

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
                                columns: [{
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
                                        data: 'tools_delivery_status'
                                    },
                                    {
                                        data: 'action'
                                    },

                                ],
                                // scrollX: true,
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
                                        type,
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
