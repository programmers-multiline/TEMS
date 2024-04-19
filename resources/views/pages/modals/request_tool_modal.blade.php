<div class="modal fade" id="requestToolsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">REQUEST TOOLS AND EQUIPMENT(RFTEIS)</h3>
                    <div class="block-options closeModalRfteis">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form id="addToolsForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-5">
                                <label class="form-label" for="pe">Project Enginner <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pe" name="pe"
                                    placeholder="Enter PE" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="subcon">Subcon <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subcon" name="subcon"
                                    placeholder="Enter Subcon">
                            </div>
                            <div class="col-3">
                                <label class="form-label" for="date">Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="date" value="{{ now()->format('m-d-Y') }}" disabled name="date"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5">
                                <label class="form-label" for="customerName">Customer Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerName" name="customerName"
                                    placeholder="Enter Customer Name">
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="projectName">Project Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectName" name="projectName"
                                    placeholder="Enter Project Name">
                            </div>
                            <div class="col-3">
                                <label class="form-label" for="projectCode">Project Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectCode" name="projectCode"
                                    placeholder="Enter Project code">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label" for="projectAddress">Project Address <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectAddress" name="projectAddress"
                                    placeholder="Enter Project Address">
                            </div>
                            {{-- <div class="col-4">
                                <label class="form-label" for="itemDescription">Delivery Arrangement <span
                                        class="text-danger">*</span></label>
                                <div class="space-x-1 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="example-radios-inline1"
                                            name="example-radios-inline" value="option1" checked="">
                                        <label class="form-check-label" for="example-radios-inline1">Supplier to PS</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="example-radios-inline2"
                                            name="example-radios-inline" value="option2">
                                        <label class="form-check-label" for="example-radios-inline2">WH to PS</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="example-radios-inline3"
                                            name="example-radios-inline" value="option2">
                                        <label class="form-check-label" for="example-radios-inline3">PS to PS</label>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </form>
                    <hr class="mt-5">
                    <h3 class="mb-1 text-secondary">Selected Tools</h3>
                    <table class="table table-hover table-borderless table-vcenter">
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
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button id="requestToolsModalBtn" type="button" class="btn btn-alt-primary">
                        Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
