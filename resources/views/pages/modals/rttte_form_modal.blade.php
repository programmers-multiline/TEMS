<div class="modal fade" id="rttteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">RTTTE Form</h3>
                    <div class="block-options closeModalRfteis">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form id="rttteForm">
                        @csrf
                        <div class="col-12 mb-3">
                            <label class="form-label" for="projectName">Project Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="projectName" name="projectName"
                                placeholder="Enter Project Name">
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label" for="pe">Project Enginner <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pe" name="pe"
                                    value="{{ Auth::user()->fullname }}" disabled placeholder="Enter PE" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="projectCode">Project Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectCode" name="projectCode"
                                    placeholder="Enter Project code">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="projectAddress">Project Address <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="projectAddress" name="projectAddress"
                                placeholder="Enter Project Address">
                        </div>
                    </form>
                    <hr class="mt-5">
                    <h3 class="mb-1 text-secondary">Selected Tools</h3>
                    <table class="table table-hover table-bordered table-vcenter">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th class="d-none d-lg-table-cell" style="width: 60%;">Item Description</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyModal">
                        </tbody>
                    </table>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button id="psRequestToolsModalBtn" type="button" class="btn btn-alt-primary">
                        Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
