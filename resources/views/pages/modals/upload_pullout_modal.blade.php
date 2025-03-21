<div class="modal fade" id="uploadTers" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content">

            <form id="uploadTersForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="routeUrl" value="{{ route('upload_process_ters') }}">
            
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Upload TERS</h3>
                        <div class="block-options closeModalRfteis">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <div class="block block-rounded">
                            <div class="block-content">
                                <label class="form-label d-block text-center">SAP GENERATED TERS NUMBER</label>
                                <div id="ters-numbers-container">
                                    <!-- Dynamic TERS Number Inputs will be added here -->
                                </div>
                                <input type="file" id="ters-fileupload" multiple
                                    data-allow-reorder="true" data-max-file-size="10MB" accept="application/pdf">
                            </div>
                        </div>
                    </div>
            
                    <input type="hidden" id="tersNumModalhidden" name="tersNum">
                    <input type="hidden" id="trTypeModalhidden" name="trType">
                    <input type="hidden" id="prevReqDataModalhidden" name="prevreqdata">
                    <input type="hidden" id="path" value="{{ request()->path() }}" name="path">
            
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-alt-primary">Upload</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
