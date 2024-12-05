@php
    $dept = App\Models\Departments::where('status', 1)
        ->where('id', Auth::user()->dept_id)
        ->first();
    $comp = App\Models\Companies::where('status', 1)
        ->where('id', Auth::user()->comp_id)
        ->first();
@endphp
<div class="modal fade" id="requestToolsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
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
                        <div>
                            <div class="row mb-3">
                                <div class="col-5">
                                    <label class="form-label" for="pe">Project Enginner <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pe" name="pe"
                                        value="{{ Auth::user()->fullname }}" disabled placeholder="Enter PE" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label" for="subcon">Subcon</label>
                                    <input type="text" class="form-control" id="subcon" name="subcon"
                                        placeholder="Enter Subcon">
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="date">Date <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="date"
                                        value="{{ now()->format('m-d-Y') }}" disabled name="date" placeholder="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="form-label" for="projectCode">Project Code <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="projectCode" name="projectCode" size="1">
                                        <option value="" disabled selected>Select Project Code</option>
                                        @foreach ($pg as $project_detail)
                                            <option data-custname="{{ Str::title($project_detail->customer_name) }}"
                                                data-pname="{{ Str::title($project_detail->project_name) }}"
                                                data-paddress="{{ Str::title($project_detail->project_address) }}"
                                                value="{{ $project_detail->project_code }}">
                                                {{ $project_detail->project_code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="form-label" for="projectName">Project Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="projectName" name="projectName"
                                        placeholder="Enter Project Name" disabled>
                                </div>
                                <div class="col-5">
                                    <label class="form-label" for="customerName">Customer Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customerName" name="customerName"
                                        placeholder="Enter Customer Name" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-8">
                                    <label class="form-label" for="projectAddress">Project Address <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="projectAddress" name="projectAddress"
                                        placeholder="Enter Project Address" disabled>
                                </div>
                                <div class="col-4">
                                    <label class="form-label" for="durationDate">Usage End Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="durationDate" name="durationDate"
                                        placeholder="Enter How Long you will use this tool" >
                                </div>
                            </div>
                        </div>
                        <hr class="mt-5 mb-3">
                        <div>
                            <h3 class='mt-2 mb-4'>DAF</h3>
                            <div class="row mb-3">
                                <div class="col-5">
                                    <label class="form-label" for="pe">Project Enginner <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pe" name="pe"
                                        value="{{ Auth::user()->fullname }}" disabled placeholder="Enter PE" required>
                                </div>
                                <div class="col-2">
                                    <label class="form-label" for="empId">Employee id</label>
                                    <input value="{{ Auth::user()->emp_id }}" type="text" class="form-control"
                                        id="empId" name="empId" placeholder="(Optional)" disabled>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="department">Department</label>
                                    <input value="{{ $dept->department_name }}" disabled type="text"
                                        class="form-control" id="department" name="department"
                                        placeholder="Enter Department">
                                </div>
                                <div class="col-2">
                                    <label class="form-label" for="date">Date <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="date"
                                        value="{{ now()->format('m-d-Y') }}" disabled name="date" placeholder="">
                                </div>

                                {{-- <div id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="block block-rounded mb-2">
                                      <div class="ps-1 mt-4" role="tab" id="accordion_h1">
                                        <input type="checkbox" id="inputCheck" class="me-2"><a class="fw-semibold text-dark" data-bs-toggle="collapse" data-bs-parent="#accordion" href="#accordion_tac" aria-expanded="true" aria-controls="accordion_tac">I hereby authorize the company <span class="text-primary font-bold">{{ $comp->company_name }}</span>, to deduct the following for the purposes indicated below;</a>
                                      </div>
                                      <div id="accordion_tac" class="collapse" role="tabpanel" aria-labelledby="accordion_h1" data-bs-parent="#accordion">
                                        <div class="block-content ps-1 pt-2">
                                          <p class="mb-2"> I understand and acknowledge that the authorized deductions will be made on a monthly or bi-monthly basis. </p>
                                    
                                          <span> In the event that my employment ends for any reason, and the outstanding amount herein has not yet been fully deducted, I agree that any remaining balance shall be deducted from my final pay. Furthermore, if my final pay is insufficient to cover the remaining balance, I acknowledge and consent that the company shall have the right and remedies to collect the remaining balance by any lawful means available to them.</span>
                                        </div>
                                      </div>
                                    </div>
                                </div> --}}

                                <div class="ps-3 mt-4"><input type="checkbox" id="inputCheck" class="me-2"
                                        data-bs-target="#TAA" data-bs-toggle="modal">I hereby authorize the
                                    company <span class="text-primary font-bold">{{ $comp->company_name }}</span>, to
                                    deduct the following for the purposes indicated below;</div>

                            </div>
                        </div>
                    </form>
                    <hr class="mt-5">
                    <h3 class="mb-1 text-secondary">Selected Tools</h3>
                    <table class="table table-hover table-bordered table-vcenter">
                        <thead>
                            <tr>
                                <th>Asset Code</th>
                                <th>Serial Number</th>
                                <th>Item Code</th>
                                <th class="d-none d-lg-table-cell" style="width: 50%;">Item Description</th>
                                <th>Action</th>
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
                    <button id="requestToolsModalBtn" type="button" class="btn btn-alt-primary" disabled>
                        Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="TAA" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">TERMS AND AGREEMENT</h3>
                </div>
                <div class="fs-sm">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <div class="px-5">
                                <p class="fw-bold">I hereby authorize the company <span class="text-primary font-bold">{{ $comp->company_name }}</span>, to deduct the following for the purposes indicated below;</p>
                                <p class="mb-2"> I understand and acknowledge that the authorized deductions will be made on a monthly or bi-monthly basis. </p>
                          
                                <span> In the event that my employment ends for any reason, and the outstanding amount herein has not yet been fully deducted, I agree that any remaining balance shall be deducted from my final pay. Furthermore, if my final pay is insufficient to cover the remaining balance, I acknowledge and consent that the company shall have the right and remedies to collect the remaining balance by any lawful means available to them.</span>
                              </div>
                        </div>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="backAgreement" class="btn btn-alt-secondary"
                    data-bs-target="#requestToolsModal" data-bs-toggle="modal">
                        Back
                    </button>
                    <button id="agree" class="btn btn-alt-primary" data-bs-target="#requestToolsModal" data-bs-toggle="modal">
                        Agree
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
