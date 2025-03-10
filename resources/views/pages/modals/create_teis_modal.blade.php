<div class="modal fade" id="createTeis" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content">

            <form id="formRequest" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="routeUrl" value="{{route('upload_process')}}">

                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Upload TEIS</h3>
                        <div class="block-options closeModalRfteis">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <div class="block block-rounded">
                            <div class="block-content">
                                <label class="form-label d-block text-center" for="inputedTeisNum">SAP GENERATED TEIS NUMBER</label>
                                <input class="form-control w-50 mx-auto mb-4" type="number" id="inputedTeisNum" name="inputedTeisNum">
                                <input type="file" class="teisUpload" multiple
                                    data-allow-reorder="true" data-max-file-size="10MB" data-max-files="1" accept="application/pdf">


                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="teisNumModalhidden" name="teisNum">
                    <input type="hidden" id="trTypeModalhidden" name="trType">
                    <input type="hidden" id="peModalhidden" name="pe">
                    <input type="hidden" id="toolIdModalhidden" name="toolId">
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-alt-primary">
                            Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
