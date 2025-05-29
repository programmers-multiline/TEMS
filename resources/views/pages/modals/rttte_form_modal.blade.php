@php
    $dept = App\Models\Departments::where('status', 1)->where('id', Auth::user()->dept_id)->first();
    $comp = App\Models\Companies::where('status', 1)->where('id', Auth::user()->comp_id)->first();
@endphp
<div class="modal fade" id="rttteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
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
                        <input type="hidden" name="hiddenProjectId" id="hiddenProjectId">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="projectName">Project Name <span
                                    class="text-danger">*</span></label>
                            <select class="form-select w-100" id="projectName" name="projectName" size="1">
                                <option value="" disabled selected>Select Project Name</option>
                                @foreach ($pg as $project_detail)
                                    <option data-pid="{{ $project_detail->id }}"
                                    data-pcode="{{ Str::title($project_detail->project_code) }}"
                                        data-paddress="{{ Str::title($project_detail->project_address) }}"
                                        value="{{ $project_detail->project_name }}">{{ $project_detail->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="pe">Project Enginner <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pe" name="pe"
                                    value="{{ Auth::user()->fullname }}" disabled placeholder="Enter PE" required>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="projectCode">Project Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectCode" name="projectCode"
                                    placeholder="Enter Project code" disabled>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="projectAddress">Project Address <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="projectAddress" name="projectAddress"
                                placeholder="Enter Project Address" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="reason">Reason for transfer <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reasonForTransfer" name="reason" rows="3" placeholder="Enter Your Reason.."></textarea>
                        </div>
                        <hr class="mt-5 mb-3">
                        <div>
                            <h3 class='mt-2 mb-4'>DAF</h3>
                            <div class="row mb-3">
                                <div class="col-lg-5 mb-3">
                                    <label class="form-label" for="pe">Project Enginner <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pe" name="pe"
                                        value="{{ Auth::user()->fullname }}" disabled placeholder="Enter PE" required>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="empId">Employee id</label>
                                    <input value="{{ Auth::user()->emp_id }}" type="text" class="form-control" id="empId" name="empId"
                                        placeholder="(Optional)" disabled>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="department">Department</label>
                                    <input value="{{ $dept->department_name }}" disabled type="text" class="form-control" id="department" name="department"
                                        placeholder="Enter Department">
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="date"
                                        value="{{ now()->format('m-d-Y') }}" disabled name="date" placeholder="">
                                </div>
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
                    </form>
                    <hr class="mt-5">
                    <h3 class="mb-1 text-secondary">Selected Tools</h3>
                    <div class="table-respossive">
                        <table class="table table-hover table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Asset Code</th>
                                    <th class="d-lg-table-cell" style="width: 60%;">Item Description</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyModal">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button id="psRequestToolsModalBtn" type="button" class="btn btn-alt-primary" disabled>
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
                    data-bs-target="#rttteModal" data-bs-toggle="modal">
                        Back
                    </button>
                    <button id="agree" class="btn btn-alt-primary" data-bs-target="#rttteModal" data-bs-toggle="modal">
                        Agree
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
