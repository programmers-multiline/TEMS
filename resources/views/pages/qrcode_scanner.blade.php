@extends('layouts.backend')

@section('content-title', 'Scan Qr code to Receive Tools')

@section('css')
    <style>

        #reader {
            width: 300px;
            margin: 20px auto;
            border: 2px solid #007BFF;
            border-radius: 10px;
            display: none;
        }
        .content button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;

        }
        #startScanner {
            background-color: #28a745;
            color: white;
        }
        #stopScanner {
            background-color: #dc3545;
            color: white;
            display: none;
        }
        #scannedData {
            font-weight: bold;
            color: #007BFF;
            margin-top: 20px;
        }

        video{
            width: 400px;
            height: auto;
        }
    </style>
@endsection

@section('content')

    <div class="content">
        <div id="reader" style=" display: none;"></div>
        <p>Scanned Data:</p>
        <p id="driverName"></p>
        <p id="requestNumber"></p>
        <button id="startScanner">Start Scanning</button>
        <button id="stopScanner" style="display: none;">Stop Scanning</button>
    </div>

@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>



    <script>
        $(function() {

            let html5QrCode;
        let scanningPaused = false; // Control scan interval

        // Start scanning
        $("#startScanner").on("click", function () {
            const readerElement = $("#reader");
            readerElement.show(); // Show the scanner
            $("#startScanner").hide(); // Hide the Start button
            $("#stopScanner").show(); // Show the Stop button

            // Initialize the scanner
            html5QrCode = new Html5Qrcode("reader");

            html5QrCode.start(
                { facingMode: "environment" }, // Use rear camera
                { fps: 10, qrbox: 250 },
                (decodedText) => {
                    if (!scanningPaused) {
                        scanningPaused = true;
                        
                        const data = JSON.parse(decodedText);
                        // Display scanned data
                        $("#driverName").text(data.name);
                        $("#requestNumber").text(data.request_number);

                        // Send the scanned data to Laravel via AJAX
                        // $.ajax({
                        //     url: "/your-laravel-route", // Replace with your Laravel route
                        //     method: "POST",
                        //     data: {
                        //         scannedData: decodedText,
                        //         _token: "{{ csrf_token() }}" // Include CSRF token
                        //     },
                        //     success: function (response) {
                        //         alert("Data sent successfully: " + response.message);
                        //     },
                        //     error: function (xhr) {
                        //         alert("Error: " + xhr.responseText);
                        //     }
                        // });

                        // Reset scanningPaused after 5 seconds
                        setTimeout(() => {
                            scanningPaused = false;
                        }, 3000);
                    }
                },
                (error) => {
                    console.warn(`QR Code scan error: ${error}`);
                }
            ).catch((err) => {
                console.error(`Error starting QR scanner: ${err}`);
            });
        });

        // Stop scanning
        $("#stopScanner").on("click", function () {
            html5QrCode.stop()
                .then(() => {
                    $("#reader").hide(); // Hide the scanner
                    $("#startScanner").show(); // Show the Start button
                    $("#stopScanner").hide(); // Hide the Stop button
                })
                .catch((err) => {
                    console.error(`Error stopping QR scanner: ${err}`);
                });
        });



            // $("#scannedTools").DataTable()

            // var _scannerIsRunning = false;

            // function startScanner() {
            //     Quagga.init({
            //         inputStream: {
            //             name: "Live",
            //             type: "LiveStream",
            //             target: document.querySelector('#scanner-container'),
            //             constraints: {
            //                 // width: 530,
            //                 // height: 370,
            //                 facingMode: "environment"
            //             },
            //         },
            //         decoder: {
            //             readers: [
            //                 "code_128_reader",
            //                 "ean_reader",
            //                 // "ean_8_reader",
            //                 "code_39_reader",
            //                 // "code_39_vin_reader",
            //                 // "codabar_reader",
            //                 "upc_reader",
            //                 // "upc_e_reader",
            //                 // "i2of5_reader"
            //             ],
            //             debug: {
            //                 showCanvas: true,
            //                 showPatches: true,
            //                 showFoundPatches: true,
            //                 showSkeleton: true,
            //                 showLabels: true,
            //                 showPatchLabels: true,
            //                 showRemainingPatchLabels: true,
            //                 boxFromPatches: {
            //                     showTransformed: true,
            //                     showTransformedBox: true,
            //                     showBB: true
            //                 }
            //             }
            //         },

            //     }, function(err) {
            //         if (err) {
            //             console.log(err);
            //             return
            //         }

            //         console.log("Initialization finished. Ready to start");
            //         Quagga.start();

            //         // Set flag to is running
            //         _scannerIsRunning = true;
            //     });

            //     Quagga.onProcessed(function(result) {
            //         var drawingCtx = Quagga.canvas.ctx.overlay,
            //             drawingCanvas = Quagga.canvas.dom.overlay;

            //         // if (result) {
            //         //     if (result.boxes) {
            //         //         drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(
            //         //             drawingCanvas.getAttribute("height")));
            //         //         result.boxes.filter(function(box) {
            //         //             return box !== result.box;
            //         //         }).forEach(function(box) {
            //         //             Quagga.ImageDebug.drawPath(box, {
            //         //                 x: 0,
            //         //                 y: 1
            //         //             }, drawingCtx, {
            //         //                 color: "green",
            //         //                 lineWidth: 2
            //         //             });
            //         //         });
            //         //     }

            //         //     if (result.box) {
            //         //         Quagga.ImageDebug.drawPath(result.box, {
            //         //             x: 0,
            //         //             y: 1
            //         //         }, drawingCtx, {
            //         //             color: "#00F",
            //         //             lineWidth: 2
            //         //         });
            //         //     }

            //         //     if (result.codeResult && result.codeResult.code) {
            //         //         Quagga.ImageDebug.drawPath(result.line, {
            //         //             x: 'x',
            //         //             y: 'y'
            //         //         }, drawingCtx, {
            //         //             color: 'red',
            //         //             lineWidth: 3
            //         //         });
            //         //     }
            //         // }
            //     });

            //     // function to not scan again the already scannedBarcodes
            //     const scannedBarcodes = new Set();


            //     let lastScanTime = 0;


            //     Quagga.onDetected(function(result) {
            //         const barcode = result.codeResult.code;
            //         const currentTime = Date.now();

            //         if (currentTime - lastScanTime >= 3500) {
            //             if (!scannedBarcodes.has(barcode)) {
            //                 // New barcode detected
            //                 scannedBarcodes.add(barcode);
            //                 showToast("success", "Barcode Scanned Successfully");
            //                 $("#tableHead").removeClass('d-none')

            //                 const scannedToolsTable = $("#scannedTools").DataTable({
            //                     processing: true,
            //                     serverSide: false,
            //                     destroy: true,
            //                     ajax: {
            //                         type: 'get',
            //                         url: '{{ route('scanned_teis') }}',
            //                         data: {
            //                             barcode,
            //                             _token: '{{ csrf_token() }}'
            //                         }

            //                     },
            //                     columns: [{
            //                             data: 'po_number'
            //                         },
            //                         {
            //                             data: 'asset_code'
            //                         },
            //                         {
            //                             data: 'serial_number'
            //                         },
            //                         {
            //                             data: 'item_code'
            //                         },
            //                         {
            //                             data: 'item_description'
            //                         },
            //                         {
            //                             data: 'brand'
            //                         },
            //                         {
            //                             data: 'warehouse_name'
            //                         },
            //                         {
            //                             data: 'tools_status'
            //                         },
            //                         {
            //                             data: 'action'
            //                         }
            //                     ],
            //                     scrollX: true,
            //                 });


            //                 $(document).on('click', '#ReceivedToolsBtn', function() {
            //                     const id = $(this).data('id');
            //                     const teis_num = $(this).data('teis');

            //                     const confirm = Swal.mixin({
            //                         customClass: {
            //                             confirmButton: "btn btn-success ms-2",
            //                             cancelButton: "btn btn-danger"
            //                         },
            //                         buttonsStyling: false
            //                     });

            //                     confirm.fire({
            //                         title: "Receive?",
            //                         text: "Are you sure you want to Received this tool?",
            //                         icon: "warning",
            //                         showCancelButton: true,
            //                         confirmButtonText: "Yes!",
            //                         cancelButtonText: "Back",
            //                         reverseButtons: true
            //                     }).then((result) => {
            //                         if (result.isConfirmed) {

            //                             $.ajax({
            //                                 url: '{{ route('scanned_teis_received') }}',
            //                                 method: 'post',
            //                                 data: {
            //                                     id,
            //                                     teis_num,
            //                                     _token: '{{ csrf_token() }}',
            //                                 },
            //                                 success(result) {
            //                                     scannedToolsTable.ajax.reload();
            //                                     showToast("success",
            //                                         "Tool Received");

            //                                 }
            //                             })

            //                         } else if (
            //                             /* Read more about handling dismissals below */
            //                             result.dismiss === Swal.DismissReason.cancel
            //                         ) {

            //                         }
            //                     });


            //                 })
            //             } else {
            //                 // Barcode already scanned
            //                 showToast("error", "This Barcode Already Scanned");
            //             }

            //             // Update the last scan time
            //             lastScanTime = currentTime;
            //         }
            //     });

            // }


            // // Start/stop scanner
            // document.getElementById("btn").addEventListener("click", function() {
            //     if (_scannerIsRunning) {
            //         Quagga.stop();
            //     } else {
            //         startScanner();
            //     }
            // }, false);




        })
    </script>

@endsection
