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

    $("#teisNumModalhidden").val(teisNum);
    $("#trTypeModalhidden").val(trType);
});

// SUBMIT FORM
$("#formRequest").on("submit", function (e) {
    e.preventDefault();

    var routeUrl = $("#formRequest #routeUrl").val();

    var frm = document.getElementById("formRequest");
    var form_data = new FormData(frm);

    pondteis = teisFormPond.getFiles();
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

    $("#pstersNumModalhidden").val(tersNum);
    $("#pstrTypeModalhidden").val(trType);


    
});


// SUBMIT FORM
$("#psUploadTersForm").on("submit", function (e) {
    e.preventDefault();

    var routeUrl = $("#psUploadTersForm #routeUrl").val();

    var frm = document.getElementById("psUploadTersForm");
    var form_data = new FormData(frm);

    pondters = psTersFormPond.getFiles();
    for (var i = 0; i < pondters.length; i++) {
        form_data.append("ters_upload[]", pondters[i].file);
    }
    console.log(...form_data);

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {},
    });
});




// TERS

var tersFormPond = FilePond.create(document.querySelector("#ters-fileupload"), {
    labelIdle: `Drag & Drop your TERS form here <span class="filepond--label-action">Browse</span>`,
    imagePreviewHeight: 600,
    imageCropAspectRatio: "1:1",
});

$(document).on("click", ".uploadTersBtn", function () {
    const pulloutnum = $(this).data("pulloutnum");

    $("#tersNumModalhidden").val(pulloutnum);
});

// SUBMIT FORM
$("#uploadTersForm").on("submit", function (e) {
    e.preventDefault();

    var routeUrl = $("#uploadTersForm #routeUrl").val();

    var frm = document.getElementById("uploadTersForm");
    var form_data = new FormData(frm);

    pondters = tersFormPond.getFiles();
    for (var i = 0; i < pondters.length; i++) {
        form_data.append("ters_upload[]", pondters[i].file);
    }
    console.log(...form_data);

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {},
    });
});
