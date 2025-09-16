/* filepond */
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateSize,
    FilePondPluginImageEdit,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform
);

/* single upload */
var teisFormPond = FilePond.create(document.querySelector(".teisUpload"), {
    labelIdle: `Drag & Drop your TEIS form here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadTeisBtn", function () {
    const teisNum = $(this).data("num");
    const trType = $(this).data("type");
    const pe = $(this).data("pe");
    const toolId = $(this).data("toolid");

    $("#teisNumModalhidden").val(teisNum);
    $("#trTypeModalhidden").val(trType);
    $("#peModalhidden").val(pe);
    $("#toolIdModalhidden").val(toolId);
});

// SUBMIT FORM
$("#formRequest").on("submit", function (e) {
    e.preventDefault();

    const prevCount = parseInt($("#rftteCount").text());

    var routeUrl = $("#formRequest #routeUrl").val();

    var frm = document.getElementById("formRequest");
    var form_data = new FormData(frm);

    pondteis = teisFormPond.getFiles();

    const teisNumber = $('#inputedTeisNum').val();

    if(!teisNumber){
        showToast("warning", "Please input Teis Number");
        return
    }

    if(!pondteis[0]){
        showToast("warning", "Select teis file first");
        return
    }

    for (var i = 0; i < pondteis.length; i++) {
        form_data.append("teis_upload[]", pondteis[i].file);
    }
    // console.log(form_data)
    const table = $("#table").DataTable();

    var $btn = $(this); // Get the clicked button
    if ($btn.prop('disabled')) return; // Prevent multiple clicks

    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        beforeSend(){
            $("#loader").show()
        },
        success: function (response) {
            $("#loader").hide()
            $("#createTeis").modal("hide");
            table.ajax.reload();
            showToast("success", "TEIS Uploaded");

            if(prevCount == 1){
                $(".countContainer").addClass("d-none")
            }else{
                $("#rfteisCount").text(prevCount - 1);
            }
        },
        complete: function() {
            $btn.prop('disabled', false).text('Upload');
        }
        
    });
});

// TERS - PS

// Initialize FilePond for multiple file uploads
var psTersFormPond = FilePond.create(document.querySelector("#ps-ters-fileupload"), {
    labelIdle: `Drag & Drop your TERS forms here <span class="filepond--label-action">Browse</span>`,
    allowMultiple: true,
    onaddfile: (error, file) => {
        if (error) return;
        
        $("#tersNumbersContainer").append(`
            <div class="ters-num-input mb-2" data-id="${file.id}">
                <label class="form-label">TERS Number for ${file.filename}</label>
                <input type="number" class="form-control ters-number" data-file-id="${file.id}">
            </div>
        `);
    },
    onremovefile: (error, file) => {
        if (error) return;
        $(`.ters-num-input[data-id="${file.id}"]`).remove();
    },
});

// When clicking the Upload button, capture necessary data
$(document).on("click", ".uploadTersBtn", function () {
    $("#pstersNumModalhidden").val($(this).data("num"));
    $("#pstrTypeModalhidden").val($(this).data("type"));
    $("#prevReqNumModalhidden").val($(this).data("prevreqnum"));
    $("#pstoolIdModalhidden").val($(this).data("toolid"));
    $("#prevPeModalhidden").val($(this).data("prevpe"));
});

// Submit Form
$("#psUploadTersForm").on("submit", function (e) {
    e.preventDefault();

    var routeUrl = $("#psUploadTersForm #routeUrl").val();
    var form_data = new FormData(this);
    var pondFiles = psTersFormPond.getFiles();

    if (pondFiles.length === 0) {
        showToast("warning", "Select TERS file(s) first");
        return;
    }

    let tersNumbers = [];
    let valid = true;

    pondFiles.forEach((file, index) => {
        let tersNum = $(`.ters-number[data-file-id="${file.id}"]`).val();
        if (!tersNum) {
            showToast("warning", `Please input TERS Number for ${file.filename}`);
            valid = false;
            return false;
        }
        tersNumbers.push({ file: file.file, tersNum });
    });

    if (!valid) return;

    tersNumbers.forEach((item, index) => {
        form_data.append(`ters_upload[${index}]`, item.file);
        form_data.append(`ters_numbers[${index}]`, item.tersNum);
    });

    const table = $("#table").DataTable();

    var $btn = $(this); // Get the clicked button
    if ($btn.prop('disabled')) return; // Prevent multiple clicks

    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {
            $("#uploadTers").modal("hide");
            table.ajax.reload();
            showToast("success", "TERS Uploaded Successfully");
        },
        complete: function() {
            $btn.prop('disabled', false).text('Upload');
        }
    });
});











// var psTersFormPond = FilePond.create(document.querySelector("#ps-ters-fileupload"), {
//     labelIdle: `Drag & Drop your TERS form here <span class="filepond--label-action">Browse</span>`,
//     imagePreviewHeight: 600,
//     imageCropAspectRatio: "1:1",
// });

// $(document).on("click", ".uploadTersBtn", function () {
//     const tersNum = $(this).data("num");
//     const trType = $(this).data("type");
//     const prevReqNum = $(this).data("prevreqnum");
//     const prevpe = $(this).data("prevpe");
//     const toolId = $(this).data("toolid");


//     $("#pstersNumModalhidden").val(tersNum);
//     $("#pstrTypeModalhidden").val(trType);
//     $("#prevReqNumModalhidden").val(prevReqNum);
//     $("#pstoolIdModalhidden").val(toolId);
//     $("#prevPeModalhidden").val(prevpe);


    
// });


// // SUBMIT FORM
// $("#psUploadTersForm").on("submit", function (e) {
//     e.preventDefault();

//     var routeUrl = $("#psUploadTersForm #routeUrl").val();
//     const psTersNum = $("#psInputedTersNum").val();

//     var frm = document.getElementById("psUploadTersForm");
//     var form_data = new FormData(frm);

//     pondters = psTersFormPond.getFiles();

//     if(!pondters[0]){
//         showToast("warning", "Select ters file first");
//         return
//     }

//     if(psTersNum == ''){
//         showToast("warning", "Please input Ters Number");
//         return
//     }

//     for (var i = 0; i < pondters.length; i++) {
//         form_data.append("ters_upload[]", pondters[i].file);
//     }
//     const table = $("#table").DataTable();

//     $.ajax({
//         type: "POST",
//         url: routeUrl,
//         processData: false,
//         contentType: false,
//         cache: false,
//         data: form_data,
//         success: function (response) {
//             $("#uploadTers").modal("hide");
//             table.ajax.reload();
//             showToast("success", "TERS Uploaded");
//         },
//     });
// });




// TERS

var tersFormPond = FilePond.create(document.querySelector("#ters-fileupload"), {
    labelIdle: `Drag & Drop your TERS form here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
    allowMultiple: true, // Allow multiple file uploads
});

$(document).on("click", ".uploadTersBtn", function () {

    if(path == 'pages/not_serve_items'){
        const rfteisNum = $(this).data("num");
        $("#tersNumModalhidden").val(rfteisNum);
    }else{
        const pulloutnum = $(this).data("pulloutnum");
        const prevReqData = $(this).data("prevreqdata");

        const prevReqDataString = JSON.stringify(prevReqData) 

        $("#tersNumModalhidden").val(pulloutnum);
        $("#prevReqDataModalhidden").val(prevReqDataString);
        console.log(prevReqDataString)
    }

    const trType = $(this).data("type");
    $("#trTypeModalhidden").val(trType);
});

// Handle File Selection and Generate Corresponding Inputs
tersFormPond.on("addfile", (error, file) => {
    if (error) return;
    const fileId = file.id;
    $("#ters-numbers-container").append(`
        <div class="mb-2 ters-number-input" data-file-id="${fileId}">
            <label for="ters-number-${fileId}">SAP TERS Number for ${file.filename}</label>
            <input class="form-control w-50" type="number" name="ters_numbers[]" id="ters-number-${fileId}" required>
        </div>
    `);
});

// Remove Input Field When File is Removed
tersFormPond.on("removefile", (error, file) => {
    if (error) return;
    $(`.ters-number-input[data-file-id="${file.id}"]`).remove();
});

// Submit Form
$("#uploadTersForm").on("submit", function (e) {
    e.preventDefault();

    const prevCount = parseInt($("#notServeCount").text());
    var routeUrl = $("#uploadTersForm #routeUrl").val();
    var form_data = new FormData(this);
    let files = tersFormPond.getFiles();

    if (files.length === 0) {
        showToast("warning", "Select at least one TERS file");
        return;
    }

    let tersNumbers = [];

    files.forEach((file, index) => {
        form_data.append("ters_upload[]", file.file);
        let tersNumber = $(`#ters-number-${file.id}`).val();
        tersNumbers.push(tersNumber);
    });

    form_data.append("ters_numbers", JSON.stringify(tersNumbers));

    const table = $("#table").DataTable();

    var $btn = $(this); // Get the clicked button
    if ($btn.prop('disabled')) return; // Prevent multiple clicks

    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        beforeSend() {
            $("#loader").show();
        },
        success(response) {
            $("#loader").hide();
            $("#uploadTers").modal("hide");
            table.ajax.reload();
            showToast("success", "TERS Uploaded");
            if(path == 'pages/not_serve_items'){
                if (prevCount == 1) {
                    $(".countContainer").addClass("d-none")
                } else {
                    $("#notServeCount").text(prevCount - 1);
                }
            }
        },
        complete: function() {
            $btn.prop('disabled', false).text('Upload');
        }
    });
});






// var tersFormPond = FilePond.create(document.querySelector("#ters-fileupload"), {
//     labelIdle: `Drag & Drop your TERS form here <span class="filepond--label-action">Browse</span>`,
//     imagePreviewHeight: 600,
//     imageCropAspectRatio: "1:1",
// });

// $(document).on("click", ".uploadTersBtn", function () {

//     if(path == 'pages/not_serve_items'){
//         const rfteisNum = $(this).data("num");
//         $("#tersNumModalhidden").val(rfteisNum);
//     }else{
//         const pulloutnum = $(this).data("pulloutnum");
//         const prevReqData = $(this).data("prevreqdata");

//         const prevReqDataString = JSON.stringify(prevReqData) 

//         $("#tersNumModalhidden").val(pulloutnum);
//         $("#prevReqDataModalhidden").val(prevReqDataString);
//         console.log(prevReqDataString)
//     }

//     const trType = $(this).data("type");
//     $("#trTypeModalhidden").val(trType);
// });

// // SUBMIT FORM
// $("#uploadTersForm").on("submit", function (e) {
//     e.preventDefault();

//     const prevCount = parseInt($("#notServeCount").text());
//     var routeUrl = $("#uploadTersForm #routeUrl").val();

//     var frm = document.getElementById("uploadTersForm");
//     var form_data = new FormData(frm);

//     pondters = tersFormPond.getFiles();

//     const pulloutNumber = $('#inputedTersNum').val();

//     if(!pulloutNumber){
//         showToast("warning", "Please input TERS Number");
//         return
//     }

//     if(!pondters[0]){
//         showToast("warning", "Select TERS file first");
//         return
//     }

//     for (var i = 0; i < pondters.length; i++) {
//         form_data.append("ters_upload[]", pondters[i].file);
//     }
    
//     const table = $("#table").DataTable();

//     $.ajax({
//         type: "POST",
//         url: routeUrl,
//         processData: false,
//         contentType: false,
//         cache: false,
//         data: form_data,
//         beforeSend(){
//             $("#loader").show()
//         },
//         success: function (response) {
//             $("#loader").hide()
//             $("#uploadTers").modal("hide");
//             table.ajax.reload();
//             showToast("success", "TERS Uploaded");
//             if(path == 'pages/not_serve_items'){
//                 if (prevCount == 1) {
//                     $(".countContainer").addClass("d-none")
//                 } else {
//                     $("#notServeCount").text(prevCount - 1);
//                 }
//             }
//         },
//     });
// });




// Upload Picture RTTTE

var uploadPicPond = FilePond.create(document.querySelector("#pictureUpload"), {
    labelIdle: `Drag & Drop your Picture of tool here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadPictureBtn", function () {
    const reqNum = $(this).data("num");
    const toolId = $(this).data("toolid");

    $("#reqNumModalhidden").val(reqNum);
    $("#toolIdModalhidden").val(toolId);


    
});



// SUBMIT FORM
$("#uploadPicForm").on("submit", function (e) {
    e.preventDefault();
    
    var routeUrl = $("#uploadPicForm #routeUrl").val();
    
    var frm = document.getElementById("uploadPicForm");
    var form_data = new FormData(frm);
    
    pondpicture = uploadPicPond.getFiles();
    if(!pondpicture[0]){
        showToast("warning", "Select Picture of tool first");
        return
    }
    
    for (var i = 0; i < pondpicture.length; i++) {
        form_data.append("picture_upload[]", pondpicture[i].file);
    }
    const table = $("#table").DataTable();

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {
            $("#uploadPicture").modal('hide')
            $("#ongoingTeisRequestModal").modal('show')
            $("#modalTable").DataTable().ajax.reload();
            table.ajax.reload();
            showToast("success", "Tool Picture Uploaded");

            // clear the selection in filepond
            uploadPicPond.removeFiles();

            const hasMissingPictures = $('#modalTable').find('.noPicture').length > 1;
            $('#peProceedBtn').prop('disabled', hasMissingPictures);
        },
    });
});




// Upload Receiving Proof RFTEIS

var uploadProofPond = FilePond.create(document.querySelector(".proofUpload"), {
    labelIdle: `Drag & Drop the proof of received here or <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadReceivingProofBtn", function () {
    const reqNum = $(this).data("num");
    const type = $(this).data("type");

    $("#requestNumberModalhidden").val(reqNum);
    $("#trTypeProofModalhidden").val(type);
    


    
});



// SUBMIT FORM
$("#receivingProofForm").on("submit", function (e) {
    e.preventDefault();

    const prevCount = parseInt($("#rftteSignedFormProofCount").text());
    
    var routeUrl = $("#receivingProofForm #routeUrl").val();
    
    var frm = document.getElementById("receivingProofForm");
    var form_data = new FormData(frm);
    
    pondProof = uploadProofPond.getFiles();
    if(!pondProof[0]){
        showToast("warning", "Select Proof of received first");
        return
    }
    
    for (var i = 0; i < pondProof.length; i++) {
        form_data.append("proof_upload[]", pondProof[i].file);
    }
    const table = $("#table").DataTable();

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {
            $("#uploadReceivingProof").modal('hide')
            table.ajax.reload();
            showToast("success", "Tool Picture Uploaded");

            if (prevCount == 1) {
                $(".countContainer").addClass("d-none")
            } else {
                $("#rftteSignedFormProofCount").text(prevCount - 1);
            }

            // clear the selection in filepond
            uploadProofPond.removeFiles();
        },
    });
});



// Pullout bulk upload

var multiUploadPicPond = FilePond.create(document.querySelector("#bulkPictureUpload"), {
    labelIdle: `Drag & Drop your Picture of tools here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 500,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".pullout_multi_upload", function () {
    const reqNum = $(this).data("reqnum");

    $("#reqNumModalhiddenfm").val(reqNum);
    
});


$("#multiUploadPicForm").on("submit", function (e) {
    e.preventDefault();
    
    var routeUrl = $("#multiUploadPicForm #routeUrl").val();
    
    var frm = document.getElementById("multiUploadPicForm");
    var form_data = new FormData(frm);
    
    pondpicture = multiUploadPicPond.getFiles();
    if(!pondpicture[0]){
        showToast("warning", "Select Picture of tool first");
        return
    }
    
    for (var i = 0; i < pondpicture.length; i++) {
        form_data.append("picture_upload[]", pondpicture[i].file);
    }
    const table = $("#table").DataTable();

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (reqNum) {
            $('#multi_upload_pullout').modal('hide');

            $('#multi_upload_pullout').on('hidden.bs.modal', function () {
                $('#ongoingPulloutRequestModal').modal('show');
            });

            
            $("#modalTable").DataTable().ajax.reload();
            table.ajax.reload();
            showToast("success", "Tool Pictures Uploaded");

            // clear the selection in filepond
            multiUploadPicPond.removeFiles();

            $('.teisNumber[data-id="' + reqNum + '"]').trigger('click');

            // const hasMissingPictures = $('#modalTable').find('.noPicture').length > 1;
            // $('#peProceedBtn').prop('disabled', hasMissingPictures);
        },
    });
});




// Upload Picture PULLOUT

var uploadPicPulloutPond = FilePond.create(document.querySelector("#pictureUploadPullout"), {
    labelIdle: `Drag & Drop your Picture of tool here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadPicturePulloutBtn", function () {
    const pri_id = $(this).data("pri_id");

    $("#pulloutItemIdModalhidden").val(pri_id);


    
});



// SUBMIT FORM
$("#uploadPicFormPullout").on("submit", function (e) {
    e.preventDefault();
    
    var routeUrl = $("#uploadPicFormPullout #routeUrl").val();
    
    var frm = document.getElementById("uploadPicFormPullout");
    var form_data = new FormData(frm);
    
    pondpicture = uploadPicPulloutPond.getFiles();
    if(!pondpicture[0]){
        showToast("warning", "Select Picture of tool first");
        return
    }
    
    for (var i = 0; i < pondpicture.length; i++) {
        form_data.append("picture_upload[]", pondpicture[i].file);
    }
    const table = $("#table").DataTable();

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (pulloutNum) {
            $("#uploadPicturePullout").modal('hide')
            // $("#ongoingTeisRequestModal").modal('show')
            $("#modalTable").DataTable().ajax.reload();
            table.ajax.reload();
            showToast("success", "Tool Picture Uploaded");

            // clear the selection in filepond
            uploadPicPulloutPond.removeFiles();

            // $('.pulloutNumber[data-id="' + pulloutNum + '"]').trigger('click');

        },
    });
});
