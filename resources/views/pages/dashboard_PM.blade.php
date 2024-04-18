@extends('layouts.backend')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="row">
            <!-- Row #1 -->
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-primary">150</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total Approved RFTTE</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-check fa-2x text-primary-light"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-earth">70</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total TEIS</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-doc fa-2x text-earth-light"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-elegance">15</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total Pull Out Request</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-action-undo fa-2x text-elegance-light"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-corporate">80</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total Request for DAF</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-envelope fa-2x text-corporate-light"></i>
                        </div>
                    </div>
                </a>
            </div>
            <!-- END Row #1 -->
        </div>
        <div class="row">
            <!-- Row #1 -->
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-info">400</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total Pending Pull out Request</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-social-dropbox fa-2x text-info"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-pulse">252</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total Approved Pull out Request</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-note fa-2x text-pulse"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-warning">15</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total Pending Request for DAF</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-envelope-open fa-2x text-warning"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <div class="fs-3 fw-semibold text-success">422</div>
                            <div class="fs-xs fw-semibold text-uppercase text-muted">Total RTTTE for <br>Approval</div>
                        </div>
                        <div class="text-end">
                            <i class="si si-event fa-2x text-success"></i>
                        </div>
                    </div>
                </a>
            </div>
            <!-- END Row #1 -->
        </div>
    </div>
    <!-- END Page Content -->
@endsection
