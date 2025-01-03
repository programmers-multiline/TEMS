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

        .content video {
            width: 400px;
            height: auto;
        }

        #reader video {
            transform: scaleX(-1);
            -webkit-transform: scaleX(-1);
            /* For Safari */
        }



        .camera-container {
            position: relative;
            width: 90vw;
            height: auto;
            overflow: hidden;
            margin-top: 2%;
        }

        #cameraModal video, #cameraModal canvas, #cameraModal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .controls {
            position: absolute;
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .controls button {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
        }

        .controls button:disabled {
            background-color: #555;
            cursor: not-allowed;
        }

        .controls button:hover:not(:disabled) {
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
        }

        @media only screen and (max-width: 420px) {
            .controls {
            /* position: relative; */
            /* margin-top: 100%;  */
            bottom: 24%;
            /* left: unset;
            transform: unset;
            align-items: center;
            justify-content: center; */
            }
            .camera-container {
            margin-top: 20%;
            }
            }

        #cameraModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 11050;
        justify-content: center;
        align-items: center;
        text-align: center;
        }

        .camera-container {
            display: inline-block;
            position: relative;
            background: white;
            /* padding: 20px; */
            border-radius: 8px;
        }

        .controls {
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')

    <div class="content">
        <button id="startScanner">Start Scanning</button>
        <button id="stopScanner" style="display: none;">Stop Scanning</button>
        <div id="reader" style=" display: none;"></div>
        <div class="d-flex gap-3 fs-5 mb-2">
            {{-- <div style="display: none;" >Driver name: <span class="fw-bold" id="driverName"></span></div> --}}
            <div style="display: none;" >Request Number: <span class="fw-bold" id="requestNumber"></span></div>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


     <!-- Camera Modal -->
     <div id="cameraModal" style="display: none;">
        <div class="camera-container">
            <video id="webcam" autoplay playsinline></video>
            <canvas id="canvas" style="display: none;"></canvas>
            <img id="photo" src="" alt="Captured Image" style="display: none;" />
        </div>
        <div class="controls">
            <button id="start-camera">Start Camera</button>
            <button id="capture" style="display: none;">Capture</button>
            <button id="retake" style="display: none;">Retake</button>
            <button id="upload" style="display: none;">Upload</button>
            <button id="cancel" style="display: none;">Cancel</button>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/webcam-easy/dist/webcam-easy.min.js"></script>



    <script>
        $(function() {

            const webcamElement = document.getElementById('webcam');
            const canvasElement = document.getElementById('canvas');
            const photoElement = document.getElementById('photo');
            let pictureDataURL = null; // Store the captured photo
            const webcam = new Webcam(webcamElement, 'environment', canvasElement);

            function showCameraModal() {
                pictureDataURL = null;
                photoElement.style.display = 'none'; // Hide previous photo
                webcamElement.style.display = 'block'; // Show camera
                document.getElementById('cameraModal').style.display = 'block';
                document.getElementById('start-camera').style.display = 'inline-block';
                document.getElementById('capture').style.display = 'none';
                document.getElementById('retake').style.display = 'none';
                document.getElementById('upload').style.display = 'none';
                document.getElementById('cancel').style.display = 'inline-block';
                $('#cameraModal').fadeIn();
            }

            function hideCameraModal() {
                document.getElementById('cameraModal').style.display = 'none';
                webcam.stop();
            }

            // Start the camera
            document.getElementById('start-camera').addEventListener('click', () => {


                navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } // Use the back camera
                })
                .then((stream) => {
                    const video = document.querySelector('video');
                    video.srcObject = stream;
                    video.play();
                });

                webcam.start()
                    .then(() => {
                        document.getElementById('start-camera').style.display = 'none';
                        document.getElementById('capture').style.display = 'inline-block';
                    })
                    .catch(err => console.error(err));
            });

            // Capture photo
            document.getElementById('capture').addEventListener('click', () => {
                pictureDataURL = webcam.snap();
                photoElement.src = pictureDataURL;
                photoElement.style.display = 'block';
                webcamElement.style.display = 'none';
                document.getElementById('capture').style.display = 'none';
                document.getElementById('retake').style.display = 'inline-block';
                document.getElementById('upload').style.display = 'inline-block';
            });

            // Retake photo
            document.getElementById('retake').addEventListener('click', () => {
                photoElement.style.display = 'none';
                webcamElement.style.display = 'block';
                webcam.start();
                document.getElementById('capture').style.display = 'inline-block';
                document.getElementById('retake').style.display = 'none';
                document.getElementById('upload').style.display = 'none';
            });

            // Cancel camera modal
            document.getElementById('cancel').addEventListener('click', () => {
                hideCameraModal();
            });




            let html5QrCode;
            let scanningPaused = false; // Control scan interval

            // Start scanning
            $("#startScanner").on("click", function() {
                const readerElement = $("#reader");
                readerElement.show(); // Show the scanner
                $("#startScanner").hide(); // Hide the Start button
                $("#stopScanner").show(); // Show the Stop button

                // Initialize the scanner
                html5QrCode = new Html5Qrcode("reader");

                html5QrCode.start({
                        facingMode: "environment"
                    }, // Use rear camera
                    {
                        fps: 10,
                        qrbox: 250
                    },
                    (decodedText) => {
                        if (!scanningPaused) {
                            scanningPaused = true;

                            // $("#driverName").parent().show();
                            $("#requestNumber").parent().show();

                            showToast("success", "Qr Code Scanned Successfully");
                            $("#tableHead").removeClass('d-none')

                            // const data = JSON.parse(decodedText);
                            // Display scanned data
                            // $("#driverName").text(data.name);
                            $("#requestNumber").text(decodedText);

                            const request_number = decodedText;
                            // const driver_name = data.name;
                            const scannedToolsTable = $("#scannedTools").DataTable({
                                processing: true,
                                serverSide: false,
                                destroy: true,
                                ajax: {
                                    type: 'get',
                                    url: '{{ route('scanned_teis') }}',
                                    data: {
                                        request_number,
                                        // driver_name,
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
                                        data: 'action'
                                    }
                                ],
                                scrollX: true,
                            });

                            $(document).on('click', '.receivedBtn', function() {
                                const id = $(this).data('triid');
                                const teis_num = $(this).data('number');
                                const type = $(this).data('trtype');

                                const confirm = Swal.mixin({
                                    customClass: {
                                        confirmButton: "btn btn-success ms-2",
                                        cancelButton: "btn btn-danger"
                                    },
                                    buttonsStyling: false
                                });

                                confirm.fire({
                                    title: "Receive?",
                                    text: "Are you sure you want to Receive this tool?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes!",
                                    cancelButtonText: "Back",
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {



                                        // Show camera modal
                                        showCameraModal();

                                        // Handle upload click
                                        $(document).off('click', '#upload').on('click',
                                            '#upload',
                                            function() {

                                                hideCameraModal();

                                                if (!pictureDataURL) {
                                                    alert("No photo captured!");
                                                    return;
                                                }

                                                // Execute AJAX after photo is captured
                                                $.ajax({
                                                    url: '{{ route('scanned_teis_received') }}',
                                                    method: 'post',
                                                    data: {
                                                        id,
                                                        teis_num,
                                                        type,
                                                        photo: pictureDataURL, // Pass photo as base64
                                                        _token: '{{ csrf_token() }}',
                                                    },
                                                    success(result) {
                                                        showToast("success",
                                                            "Tool Received"
                                                            );
                                                        $("#scannedTools")
                                                            .DataTable()
                                                            .ajax.reload();
                                                        $("#table")
                                                            .DataTable()
                                                            .ajax.reload();
                                                    },
                                                    error(err) {
                                                        showToast("error",
                                                            "An error occurred. Please try again."
                                                            );
                                                    }
                                                });
                                            });
                                    }
                                });
                            });


                            $(document).on('click', '.notReceivedBtn', function() {
                                const id = $(this).data('triid');
                                const teis_num = $(this).data('number');
                                const type = $(this).data('trtype');

                                const confirm = Swal.mixin({
                                    customClass: {
                                        confirmButton: "btn btn-success ms-2",
                                        cancelButton: "btn btn-danger"
                                    },
                                    buttonsStyling: false
                                });

                                confirm.fire({
                                    title: "Not Serve?",
                                    text: "Are you sure this tool is not serve?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes!",
                                    cancelButtonText: "Back",
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {

                                        $.ajax({
                                            url: '{{ route('teis_not_received') }}',
                                            method: 'post',
                                            data: {
                                                id,
                                                teis_num,
                                                type,
                                                _token: '{{ csrf_token() }}',
                                            },
                                            success(result) {
                                                showToast("success",
                                                    "Tool not Received");
                                                $("#scannedTools").DataTable()
                                                    .ajax.reload();

                                            }
                                        })

                                    } else if (
                                        /* Read more about handling dismissals below */
                                        result.dismiss === Swal.DismissReason.cancel
                                    ) {

                                    }
                                });


                            })


                            // $(document).on('click', '#ReceivedToolsBtn', function() {
                            //     const id = $(this).data('id');
                            //     const teis_num = $(this).data('teis');

                            //     const confirm = Swal.mixin({
                            //         customClass: {
                            //             confirmButton: "btn btn-success ms-2",
                            //             cancelButton: "btn btn-danger"
                            //         },
                            //         buttonsStyling: false
                            //     });

                            //     confirm.fire({
                            //         title: "Receive?",
                            //         text: "Are you sure you want to Received this tool?",
                            //         icon: "warning",
                            //         showCancelButton: true,
                            //         confirmButtonText: "Yes!",
                            //         cancelButtonText: "Back",
                            //         reverseButtons: true
                            //     }).then((result) => {
                            //         if (result.isConfirmed) {

                            //             $.ajax({
                            //                 url: '{{ route('scanned_teis_received') }}',
                            //                 method: 'post',
                            //                 data: {
                            //                     id,
                            //                     teis_num,
                            //                     _token: '{{ csrf_token() }}',
                            //                 },
                            //                 success(result) {
                            //                     scannedToolsTable.ajax.reload();
                            //                     showToast("success",
                            //                         "Tool Received");

                            //                 }
                            //             })

                            //         } else if (
                            //             /* Read more about handling dismissals below */
                            //             result.dismiss === Swal.DismissReason.cancel
                            //         ) {

                            //         }
                            //     });


                            // })


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
            $("#stopScanner").on("click", function() {
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

        })
    </script>

@endsection