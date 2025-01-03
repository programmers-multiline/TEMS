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

@section('content-title', 'List of RTTTE')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Request#</th>
                            <th>Subcon</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Project Address</th>
                            <th>Date Requested</th>
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
    @include('pages.modals.ps_ongoing_transfer_request')

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
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_teis_request_ps') }}'
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
                        data: 'action'
                    },
                ],
            });

            $(document).on('click', '.teisNumber', function() {
                const id = $(this).data("id");
                const trid = $(this).data("pstrid");
                const pe = $(this).data("pe");
                const type = $(this).data("trtype");
                const path = $('#path').val();

                $.ajax({
                    url: '{{ route('view_transfer_request') }}',
                    method: 'get',
                    data: {
                        id,
                        trid,
                        type,
                        pe,
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
                                url: '{{ route('ps_ongoing_teis_request_modal') }}',
                                data: {
                                    id,
                                    path,
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

                            }
                        });

                    }
                })
            })
            // old viewing of tools
            // $(document).on('click', '.teisNumber', function() {

            //     const id = $(this).data("id");


            //     const modalTable = $("#modalTable").DataTable({
            //         scrollX: true,
            //         processing: true,
            //         serverSide: false,
            //         destroy: true,
            //         ajax: {
            //             type: 'get',
            //             url: '{{ route('ps_ongoing_teis_request_modal') }}',
            //             data: {
            //                 id,
            //                 _token: '{{ csrf_token() }}'
            //             }

            //         },
            //         columns: [
            //             {
            //                 data: 'picture'
            //             },
            //             {
            //                 data: 'po_number'
            //             },
            //             {
            //                 data: 'asset_code'
            //             },
            //             {
            //                 data: 'serial_number'
            //             },
            //             {
            //                 data: 'item_code'
            //             },
            //             {
            //                 data: 'item_description'
            //             },
            //             {
            //                 data: 'brand'
            //             },
            //             {
            //                 data: 'warehouse_name'
            //             },
            //             {
            //                 data: 'add_price'
            //             },
            //             {
            //                 data: 'tools_status'
            //             },
            //             {
            //                 data: 'action'
            //             },
            //         ],
            //         drawCallback: function() {
            //                 $('table thead th.pictureHeader').show();
            //         }
            //     });
            // })


            $(document).on('dblclick', '.toolPrice', function () {
                let currentValue = $(this).text().trim();
                let id = $(this).data('id');
                let reqnum = $(this).data('reqnum');

                // Replace span with an input
                let input = $('<input>', {
                    type: 'text',
                    class: 'price-input form-control',
                    value: currentValue,
                    'data-id': id,
                    'data-reqnum': reqnum,
                    css: {
                        width: '100px',
                        textAlign: 'right',
                        display: 'unset !important',
                    }
                });

                $(this).replaceWith(input);
                input.focus();
            });


            $(document).on('keydown', '.price-input', function(e) {
                // Allow: Backspace, Delete, Tab, Escape, Enter, Arrow keys
                if (
                    $.inArray(e.key, ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                        'ArrowLeft', 'ArrowRight'
                    ]) !== -1 ||
                    // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X (for copy-pasting)
                    (e.ctrlKey && $.inArray(e.key, ['a', 'c', 'v', 'x']) !== -1)
                ) {
                    return; // Do not block the key
                }

                // Allow numbers and single period, block if multiple periods
                if (!/[0-9.]/.test(e.key) || (e.key === '.' && $(this).val().includes('.'))) {
                    e.preventDefault();
                }
            });


             // On Enter, save the new price
            $(document).on('keypress', '.price-input', function (e) {
                if (e.which == 13) { // Enter key
                    let newPrice = $(this).val().trim();
                    let id = $(this).data('id'); // Get the item's ID
                    let reqnum = $(this).data('reqnum');
                    // Basic validation
                    if (isNaN(newPrice) || newPrice <= 0) {
                        alert('Please enter a valid price.');
                        return;
                    }

                    // $.ajax({
                    //     url: '{{ route('add_price_acc') }}',
                    //     method: 'post',
                    //     data: {
                    //         type,
                    //         priceDatas: stringData,
                    //         _token: "{{ csrf_token() }}"
                    //     },
                    //     success() {
                    //         showToast("success", "Price Added!");
                    //         $("#ongoingTeisRequestModal").modal('hide')
                    //         $("#table").DataTable().ajax.reload();
                    //     }
                    // })

                    const type = 'rttte';

                    // AJAX to save to database
                    $.ajax({
                        url: '{{ route('add_price_acc') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id,
                            reqnum,
                            price: newPrice,
                            type
                        },
                        success: function (response) {
                            // Replace input back with span
                            let span = $('<span>', {
                                class: 'toolPrice',
                                'data-id': id,
                                'data-reqnum': reqnum,
                                text: newPrice
                            });

                            showToast("success", "Price updated!");
                            $(e.target).replaceWith(span);
                        },
                        error: function () {
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });


            $(document).on('blur', '.price-input', function () {
                let $input = $(this);
                let value = $input.val().trim();
                let id = $input.data('id'); 
                let reqnum = $input.data('reqnum'); 

                // Replace with the original value if no changes are made
                let span = $('<span>', {
                    class: 'toolPrice',
                    'data-id': id,
                    'data-reqnum': reqnum,
                    text: value || 'No Price' // Default to 'No Price' if the input is empty
                });

                $input.replaceWith(span);
            });


            $("#AddPriceBtn").click(function() {

                const table = $("#table").DataTable()

                const allData = [];

                $('.price').each(function(i, obj) {

                   
                    const id = $(this).data('id') 
                    const price = obj.value 

                    const data = {
                        id,
                        price
                    }

                    allData.push(data)
                    

                });


                const stringData = JSON.stringify(allData)

                $.ajax({
                    url: '{{ route('add_price_acc') }}',
                    method: 'post',
                    data: {
                        priceDatas: stringData,
                        _token: "{{ csrf_token() }}"
                    },
                    success() {
                        showToast("success", "Price Added!");
                        $("#psOngoingTeisRequestModal").modal('hide')
                        table.ajax.reload();
                    }
                })

            })

            $(document).on('click', '.approveBtn', function() {
                const requestNum = $(this).data('requestnum');

                const prevCount = parseInt($("#rttteAccCount").text());

                const confirm = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ms-2",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                confirm.fire({
                    title: "Approve?",
                    text: "Are you sure you want to approved this tools?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!",
                    cancelButtonText: "Close",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('ps_approve_rttte') }}',
                            method: 'post',
                            data: {
                                requestNum,
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend(){
                                $("#loader").show()
                            },
                            success() {
                                $("#loader").hide()
                                table.ajax.reload();
                                confirm.fire({
                                    title: "Approved!",
                                    text: "Items Approved Successfully.",
                                    icon: "success"
                                });

                                if (prevCount == 1) {
                                    $(".countContainer").addClass("d-none")
                                } else {
                                    $("#rttteAccCount").text(prevCount - 1);
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

        })
    </script>
@endsection
