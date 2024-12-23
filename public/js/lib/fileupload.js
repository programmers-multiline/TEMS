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

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {
            $("#createTeis").modal("hide");
            table.ajax.reload();
            showToast("success", "TEIS Uploaded");

            if(prevCount == 1){
                $(".countContainer").addClass("d-none")
            }else{
                $("#rfteisCount").text(prevCount - 1);
            }
        },
    });
});

// TERS - PS

var psTersFormPond = FilePond.create(document.querySelector("#ps-ters-fileupload"), {
    labelIdle: `Drag & Drop your TERS form here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadTersBtn", function () {
    const tersNum = $(this).data("num");
    const trType = $(this).data("type");
    const prevReqNum = $(this).data("prevreqnum");
    const prevpe = $(this).data("prevpe");
    const toolId = $(this).data("toolid");


    $("#pstersNumModalhidden").val(tersNum);
    $("#pstrTypeModalhidden").val(trType);
    $("#prevReqNumModalhidden").val(prevReqNum);
    $("#pstoolIdModalhidden").val(toolId);
    $("#prevPeModalhidden").val(prevpe);


    
});


// SUBMIT FORM
$("#psUploadTersForm").on("submit", function (e) {
    e.preventDefault();

    var routeUrl = $("#psUploadTersForm #routeUrl").val();

    var frm = document.getElementById("psUploadTersForm");
    var form_data = new FormData(frm);

    pondters = psTersFormPond.getFiles();

    if(!pondters[0]){
        showToast("warning", "Select ters file first");
        return
    }

    for (var i = 0; i < pondters.length; i++) {
        form_data.append("ters_upload[]", pondters[i].file);
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
            $("#uploadTers").modal("hide");
            table.ajax.reload();
            showToast("success", "TERS Uploaded");
        },
    });
});




// TERS

var tersFormPond = FilePond.create(document.querySelector("#ters-fileupload"), {
    labelIdle: `Drag & Drop your TERS form here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadTersBtn", function () {

    if(path == 'pages/not_serve_items'){
        const rfteisNum = $(this).data("num");
        $("#tersNumModalhidden").val(rfteisNum);
    }else{
        const pulloutnum = $(this).data("pulloutnum");
        $("#tersNumModalhidden").val(pulloutnum);
    }

    const trType = $(this).data("type");
    $("#trTypeModalhidden").val(trType);
});

// SUBMIT FORM
$("#uploadTersForm").on("submit", function (e) {
    e.preventDefault();

    const prevCount = parseInt($("#notServeCount").text());
    var routeUrl = $("#uploadTersForm #routeUrl").val();

    var frm = document.getElementById("uploadTersForm");
    var form_data = new FormData(frm);

    pondters = tersFormPond.getFiles();

    if(!pondters[0]){
        showToast("warning", "Select ters file first");
        return
    }

    for (var i = 0; i < pondters.length; i++) {
        form_data.append("ters_upload[]", pondters[i].file);
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
    });
});




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
