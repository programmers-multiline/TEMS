@extends('layouts.backend')

@section('content-title', 'QR Code Generator for Driver')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="col-xl-4 col-10 mx-auto mt-5">
        <input class="form-control" type="text" id="driverName" placeholder="Driver Name">
        <input class="form-control my-3" type="text" id="requestNumber" placeholder="Request Number">
        <button class="btn btn-primary d-block mx-auto" id="generateQRCode">Generate QR Code</button>  
        <div id="qrcode-container">
            <div class="my-4" id="qrcode"></div>
            <button class="btn bg-elegance text-white" id="downloadButton" style="display: none;">Download QR Code</button>
            <button class="btn btn-success" id="printButton" style="display: none;">Print QR Code</button>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
        <script>
            $(function() {
                let qrCodeInstance = null;

                // Generate QR Code
                $("#generateQRCode").click(function() {
                    const driverName = $("#driverName").val();
                    const requestNumber = $("#requestNumber").val();

                    if (!driverName || !requestNumber) {
                        alert("Please enter both Driver Name and Request Number!");
                        return;
                    }
                    const name = driverName;
                    const request_number = requestNumber;
                    const jsonData = JSON.stringify({
                        name,
                        request_number
                    });

                    // Clear any previous QR code
                    $("#qrcode").empty();

                    // Generate QR code
                    qrCodeInstance = new QRCode(document.getElementById("qrcode"), {
                        text: jsonData,
                        width: 256,
                        height: 256,
                    });

                    // Show Download and Print buttons
                    $("#downloadButton").show();
                    $("#printButton").show();
                });

                // Download QR Code
                $("#downloadButton").click(function() {
                    if (!qrCodeInstance) {
                        alert("No QR code generated to download!");
                        return;
                    }

                    const driverName = $("#driverName").val();
                    const requestNumber = $("#requestNumber").val();

                    const qrCodeCanvas = $("#qrcode canvas")[0]; // Get the canvas element
                    if (qrCodeCanvas) {
                        // Create an offscreen canvas to redraw the QR code with a white border
                        const borderSize = 20; // Adjust border size as needed
                        const canvas = document.createElement("canvas");
                        const context = canvas.getContext("2d");

                        // Set the canvas size larger to include the white border
                        canvas.width = qrCodeCanvas.width + borderSize * 2;
                        canvas.height = qrCodeCanvas.height + borderSize * 2;

                        // Fill the entire canvas with white
                        context.fillStyle = "#ffffff";
                        context.fillRect(0, 0, canvas.width, canvas.height);

                        // Draw the original QR code in the center of the new canvas
                        context.drawImage(qrCodeCanvas, borderSize, borderSize);

                        // Convert the updated canvas to a downloadable PNG file
                        const link = document.createElement("a");
                        link.href = canvas.toDataURL("image/png"); // Save as PNG
                        link.download = `${driverName} - ${requestNumber}.png`; // Set file name
                        link.click();
                    }


                });

                // Print QR Code
                $("#printButton").click(function() {
                    if (!qrCodeInstance) {
                        alert("No QR code generated to print!");
                        return;
                    }

                });



            });
        </script>

    @endsection
