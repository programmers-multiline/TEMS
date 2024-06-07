@extends('layouts.backend')

@section('content-title', 'Scan Barcode to Receive Tools')

@section('css')
    <style>
        /* In order to place the tracking correctly */
        canvas.drawing,
        canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }

        @media (max-width: 480px) {
            #scanner-container video {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')

    <div class="content">
        <!-- Div to show the scanner -->
        <div>
            <button type="button" id="btn" class="btn btn-primary mb-3 fs-6">Start/Stop the Scanner</button>
            <div class="my-3" id="scanner-container"></div>
        </div>

        <div id="tableHead" class="block-content block-content-full overflow-x-auto bg-body-extra-light d-none">
            <table id="scannedTools" class="table fs-sm table-bordered table-hover table-vcenter w-100">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Asset Code</th>
                        <th>Serial#</th>
                        <th>Item Code</th>
                        <th>Item Desc</th>
                        <th>Brand</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0420d5e0/dist/quagga.min.js"></script>

    <!-- Include the image-diff library -->
    {{-- <script src="{{ asset('js/plugins/barcode/quagga.min.js') }}"></script> --}}


    <script>
        $(function() {

            $("#scannedTools").DataTable()

            var _scannerIsRunning = false;

            function startScanner() {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#scanner-container'),
                        constraints: {
                            // width: 530,
                            // height: 370,
                            facingMode: "environment"
                        },
                    },
                    decoder: {
                        readers: [
                            "code_128_reader",
                            "ean_reader",
                            // "ean_8_reader",
                            "code_39_reader",
                            // "code_39_vin_reader",
                            // "codabar_reader",
                            "upc_reader",
                            // "upc_e_reader",
                            // "i2of5_reader"
                        ],
                        debug: {
                            showCanvas: true,
                            showPatches: true,
                            showFoundPatches: true,
                            showSkeleton: true,
                            showLabels: true,
                            showPatchLabels: true,
                            showRemainingPatchLabels: true,
                            boxFromPatches: {
                                showTransformed: true,
                                showTransformedBox: true,
                                showBB: true
                            }
                        }
                    },

                }, function(err) {
                    if (err) {
                        console.log(err);
                        return
                    }

                    console.log("Initialization finished. Ready to start");
                    Quagga.start();

                    // Set flag to is running
                    _scannerIsRunning = true;
                });

                Quagga.onProcessed(function(result) {
                    var drawingCtx = Quagga.canvas.ctx.overlay,
                        drawingCanvas = Quagga.canvas.dom.overlay;

                    // if (result) {
                    //     if (result.boxes) {
                    //         drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(
                    //             drawingCanvas.getAttribute("height")));
                    //         result.boxes.filter(function(box) {
                    //             return box !== result.box;
                    //         }).forEach(function(box) {
                    //             Quagga.ImageDebug.drawPath(box, {
                    //                 x: 0,
                    //                 y: 1
                    //             }, drawingCtx, {
                    //                 color: "green",
                    //                 lineWidth: 2
                    //             });
                    //         });
                    //     }

                    //     if (result.box) {
                    //         Quagga.ImageDebug.drawPath(result.box, {
                    //             x: 0,
                    //             y: 1
                    //         }, drawingCtx, {
                    //             color: "#00F",
                    //             lineWidth: 2
                    //         });
                    //     }

                    //     if (result.codeResult && result.codeResult.code) {
                    //         Quagga.ImageDebug.drawPath(result.line, {
                    //             x: 'x',
                    //             y: 'y'
                    //         }, drawingCtx, {
                    //             color: 'red',
                    //             lineWidth: 3
                    //         });
                    //     }
                    // }
                });

                // function to not scan again the already scannedBarcodes
                const scannedBarcodes = new Set();


                let lastScanTime = 0;


                Quagga.onDetected(function(result) {
                    const barcode = result.codeResult.code;
                    const currentTime = Date.now();

                    if (currentTime - lastScanTime >= 3500) {
                        if (!scannedBarcodes.has(barcode)) {
                            // New barcode detected
                            scannedBarcodes.add(barcode);
                            showToast("success", "Barcode Scanned Successfully");
                            $("#tableHead").removeClass('d-none')

                            const scannedToolsTable = $("#scannedTools").DataTable({
                                processing: true,
                                serverSide: false,
                                destroy: true,
                                ajax: {
                                    type: 'get',
                                    url: '{{ route('scanned_teis') }}',
                                    data: {
                                        barcode,
                                        _token: '{{ csrf_token() }}'
                                    }

                                },
                                columns: [{
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
                                    }
                                ],
                                scrollX: true,
                            });


                            $(document).on('click', '#ReceivedToolsBtn', function() {
                                const id = $(this).data('id');
                                const teis_num = $(this).data('teis');

                                const confirm = Swal.mixin({
                                    customClass: {
                                        confirmButton: "btn btn-success ms-2",
                                        cancelButton: "btn btn-danger"
                                    },
                                    buttonsStyling: false
                                });

                                confirm.fire({
                                    title: "Recieved?",
                                    text: "Are you sure you want to Received this tool?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes!",
                                    cancelButtonText: "Cancel",
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {

                                        $.ajax({
                                            url: '{{ route('scanned_teis_received') }}',
                                            method: 'post',
                                            data: {
                                                id,
                                                teis_num,
                                                _token: '{{ csrf_token() }}',
                                            },
                                            success(result) {
                                                scannedToolsTable.ajax.reload();
                                                showToast("success",
                                                    "Tool Received");

                                            }
                                        })

                                    } else if (
                                        /* Read more about handling dismissals below */
                                        result.dismiss === Swal.DismissReason.cancel
                                    ) {

                                    }
                                });


                            })
                        } else {
                            // Barcode already scanned
                            showToast("error", "This Barcode Already Scanned");
                        }

                        // Update the last scan time
                        lastScanTime = currentTime;
                    }
                });

            }


            // Start/stop scanner
            document.getElementById("btn").addEventListener("click", function() {
                if (_scannerIsRunning) {
                    Quagga.stop();
                } else {
                    startScanner();
                }
            }, false);




        })
    </script>

@endsection
