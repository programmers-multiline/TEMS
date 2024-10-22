<div class="modal fade" id="trackRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Track Request Item</h3>
                    <div class="block-options closeModalRfteis">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <div class="container py-3 h-100">
                      <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col-12">
                          <div class="card card-stepper" style="border-radius: 16px;">
                  
                            <div class="card-body p-5">
                  
                              <div class="d-flex justify-content-between align-items-center text-center mb-5">
                                <div>
                                  <h5 class="mb-0">Request Number <span class="text-primary font-weight-bold trackRequestNumber"></span></h5>
                                </div>
                                {{-- <div class="text-end">
                                  <p class="mb-0">Expected Arrival <span>01/12/19</span></p>
                                  <p class="mb-0">USPS <span class="font-weight-bold">234094567242423422898</span></p>
                                </div> --}}
                              </div>
                  
                              <ul id="requestProgress" class="d-flex justify-content-between mx-0 mt-0 mb-5 px-0 pt-0 pb-2">
                                
                              </ul>
                  
                              <div class="d-flex justify-content-between">
                                {{-- <div class="d-lg-flex align-items-center">
                                  <i class="fas fa-hourglass-half fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                                  <div>
                                    <p class="fw-bold mb-1">Request</p>
                                    <p class="fw-bold mb-0">Pending</p>
                                  </div>
                                </div> --}}
                                <div class="d-lg-flex align-items-center">
                                  <i class="fas fa-clipboard-list fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                                  <div>
                                    <p class="fw-bold mb-1">Request</p>
                                    <p class="fw-bold mb-0">Approved</p>
                                  </div>
                                </div>
                                <div class="d-lg-flex align-items-center">
                                  <i class="fas fa-box-open fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                                  <div>
                                    <p class="fw-bold mb-1">Request</p>
                                    <p class="fw-bold mb-0">To Ship</p>
                                  </div>
                                </div>
                                <div class="d-lg-flex align-items-center">
                                  <i class="fas fa-shipping-fast fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                                  <div>
                                    <p class="fw-bold mb-1">Request</p>
                                    <p class="fw-bold mb-0">En Route</p>
                                  </div>
                                </div>
                                <div class="d-lg-flex align-items-center">
                                  <i class="fas fa-building fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                                  <div>
                                    <p class="fw-bold mb-1">Request</p>
                                    <p class="fw-bold mb-0">Received</p>
                                  </div>
                                </div>
                              </div>
                  
                            </div>
                  
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
