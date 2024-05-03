
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
    var teisFormPond = FilePond.create(
        document.querySelector('.single-fileupload'),
        {
            labelIdle: `Drag & Drop your TEIS form here <span class="filepond--label-action">Browse</span>`,
            imagePreviewHeight: 600,
            imageCropAspectRatio: '1:1',
        }
    );

    $(document).on('click','.uploadTeisBtn', function(){
        const teisNum = $(this).data('teisnum');
        
        
        $("#teisNumModalhidden").val(teisNum);
    })

    // SUBMIT FORM
$('#formRequest').on('submit', function(e){
    e.preventDefault();

    var routeUrl =  $('#formRequest #routeUrl').val();


    var frm = document.getElementById("formRequest");
    var form_data = new FormData(frm);
    
    pondteis = teisFormPond.getFiles();
    for (var i = 0; i < pondteis.length; i++) {
        form_data.append('teis_upload[]', pondteis[i].file);
    }
    // console.log(form_data)


    $.ajax({
        type: 'POST',
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data:  form_data,
        success: function(response) {
        }
    })
})