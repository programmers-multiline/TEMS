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
                    <div class="container h-100">
                      <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col-12">
                          <div class="card card-stepper" style="border-radius: 16px;">
                  
                            <div class="card-body">
                  
                              {{-- <div class="d-flex justify-content-between align-items-center text-center mb-5">
                                <div>
                                  <h5 class="mb-0">Request Number <span class="text-primary font-weight-bold trackRequestNumber"></span></h5>
                                </div>
                                <div class="text-end">
                                  <p class="mb-0">Expected Arrival <span>01/12/19</span></p>
                                  <p class="mb-0">USPS <span class="font-weight-bold">234094567242423422898</span></p>
                                </div>
                              </div> --}}


                              <div class="block block-rounded">
                                <div class="block-header block-header-default">
                                    <div>
                                      <h5 class="mb-0">Request Number <span class="text-primary font-weight-bold trackRequestNumber"></span></h5>
                                    </div>
                                  <div class="block-options">
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                      <i class="si si-refresh"></i>
                                    </button>
                                  </div>
                                </div>
                                <div class="block-content">
                                  <ul class="timeline timeline-modern pull-t" id="requestProgress">
                                      
                                  </ul>
                                </div>
                              </div>



                              {{-- ?backup lang ng main --}}


                              {{-- <div class="block-content">
                                <ul class="timeline timeline-modern pull-t">
                                  <!-- Twitter Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">50 min ago</div>
                                    <i class="timeline-event-icon fa fab fa-twitter bg-info"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">+ 79 Followers</p>
                                      <p>You’re getting more and more followers, keep it up!</p>
                                    </div>
                                  </li>
                                  <!-- END Twitter Notification -->
                  
                                  <!-- Photo Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">2 hrs ago</div>
                                    <i class="timeline-event-icon fa fa-camera bg-elegance"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">+ 3 New photos</p>
                                      <p>Austria Travel Guide was updated with new images</p>
                                      <!-- Gallery (.js-gallery class is initialized in Helpers.jqMagnific()) -->
                                      <!-- For more info and examples you can check out http://dimsemenov.com/plugins/magnific-popup/ -->
                                      <div class="row items-push js-gallery img-fluid-100">
                                        <div class="col-sm-6 col-xl-3">
                                          <a class="img-link img-link-zoom-in img-lightbox" href="assets/media/photos/photo2@2x.jpg">
                                            <img class="img-fluid" src="assets/media/photos/photo2.jpg" alt="">
                                          </a>
                                        </div>
                                        <div class="col-sm-6 col-xl-3">
                                          <a class="img-link img-link-zoom-in img-lightbox" href="assets/media/photos/photo8@2x.jpg">
                                            <img class="img-fluid" src="assets/media/photos/photo8.jpg" alt="">
                                          </a>
                                        </div>
                                        <div class="col-sm-6 col-xl-3">
                                          <a class="img-link img-link-zoom-in img-lightbox" href="assets/media/photos/photo9@2x.jpg">
                                            <img class="img-fluid" src="assets/media/photos/photo9.jpg" alt="">
                                          </a>
                                        </div>
                                      </div>
                                    </div>
                                  </li>
                                  <!-- END Photos Notification -->
                  
                                  <!-- Facebook Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">5 hrs ago</div>
                                    <i class="timeline-event-icon fab fa-facebook-f bg-default"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">+ 160 Page likes</p>
                                      <p>You are doing great, keep it up!</p>
                                    </div>
                                  </li>
                                  <!-- END Facebook Notification -->
                  
                                  <!-- System Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">3 days ago</div>
                                    <i class="timeline-event-icon fa fa-database bg-pulse"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">Server backup completed!</p>
                                      <p>Download the <a href="javascript:void(0)">latest backup</a>.</p>
                                    </div>
                                  </li>
                                  <!-- END System Notification -->
                  
                                  <!-- Social Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">5 days ago</div>
                                    <i class="timeline-event-icon fa fa-user-plus bg-success"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">+ 4 new contacts</p>
                                      <p>It seems that you might know these professionals.</p>
                                      <div class="row">
                                        <div class="col-sm-6 col-xl-4">
                                          <ul class="nav-users push">
                                            <li class="timeline-event">
                                              <a href="be_pages_generic_profile.html">
                                                <img class="img-avatar" src="assets/media/avatars/avatar5.jpg" alt="">
                                                <i class="fa fa-circle text-success"></i>
                                                <div>Lisa Jenkins</div>
                                                <div class="fw-normal fs-xs text-muted">Web Designer</div>
                                              </a>
                                            </li>
                                            <li class="timeline-event">
                                              <a href="be_pages_generic_profile.html">
                                                <img class="img-avatar" src="assets/media/avatars/avatar8.jpg" alt="">
                                                <i class="fa fa-circle text-warning"></i>
                                                <div>Melissa Rice</div>
                                                <div class="fw-normal fs-xs text-muted">Photographer</div>
                                              </a>
                                            </li>
                                            <li class="timeline-event">
                                              <a href="be_pages_generic_profile.html">
                                                <img class="img-avatar" src="assets/media/avatars/avatar14.jpg" alt="">
                                                <i class="fa fa-circle text-warning"></i>
                                                <div>Jeffrey Shaw</div>
                                                <div class="fw-normal fs-xs text-muted">Copywriter</div>
                                              </a>
                                            </li>
                                            <li class="timeline-event">
                                              <a href="be_pages_generic_profile.html">
                                                <img class="img-avatar" src="assets/media/avatars/avatar10.jpg" alt="">
                                                <i class="fa fa-circle text-danger"></i>
                                                <div>Carl Wells</div>
                                                <div class="fw-normal fs-xs text-muted">UI Designer</div>
                                              </a>
                                            </li>
                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                  </li>
                                  <!-- END Social Notification -->
                  
                                  <!-- Photo Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">6 days ago</div>
                                    <i class="timeline-event-icon fa fa-camera bg-elegance"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">+ 2 New photos</p>
                                      <p>We had a great time!</p>
                  
                                      <!-- Gallery (.js-gallery class is initialized in Helpers.jqMagnific()) -->
                                      <!-- For more info and examples you can check out http://dimsemenov.com/plugins/magnific-popup/ -->
                                      <div class="row items-push js-gallery img-fluid-100">
                                        <div class="col-sm-6 col-xl-3">
                                          <a class="img-link img-link-zoom-in img-lightbox" href="assets/media/photos/photo4@2x.jpg">
                                            <img class="img-fluid" src="assets/media/photos/photo4.jpg" alt="">
                                          </a>
                                        </div>
                                        <div class="col-sm-6 col-xl-3">
                                          <a class="img-link img-link-zoom-in img-lightbox" href="assets/media/photos/photo18@2x.jpg">
                                            <img class="img-fluid" src="assets/media/photos/photo18.jpg" alt="">
                                          </a>
                                        </div>
                                      </div>
                                    </div>
                                  </li>
                                  <!-- END Photo Notification -->
                  
                                  <!-- System Notification -->
                                  <li class="timeline-event">
                                    <div class="timeline-event-time">2 weeks ago</div>
                                    <i class="timeline-event-icon fa fa-cog bg-gray-darker"></i>
                                    <div class="timeline-event-block">
                                      <p class="fw-semibold">System updated to v3.9</p>
                                      <p>Please check the complete changelog at the <a href="javascript:void(0)">activity page</a>.</p>
                                    </div>
                                  </li>
                                  <!-- END System Notification -->
                                </ul>
                              </div> --}}


                              {{-- ? luma tracking --}}
                  
                              {{-- <ul id="requestProgress" class="d-flex justify-content-between mx-0 mt-0 mb-5 px-0 pt-0 pb-2">
                                
                              </ul>
                  
                              <div class="d-flex justify-content-between">
                                {{-- <div class="d-lg-flex align-items-center">
                                  <i class="fas fa-hourglass-half fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                                  <div>
                                    <p class="fw-bold mb-1">Request</p>
                                    <p class="fw-bold mb-0">Pending</p>
                                  </div>
                                </div> --}}
                                {{-- <div class="d-lg-flex align-items-center">
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
                              </div> --}}
                  
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
