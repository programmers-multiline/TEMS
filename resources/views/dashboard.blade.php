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
                            <i class="si si-bag fa-2x text-primary-light"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold text-primary">1500</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Sales</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="si si-wallet fa-2x text-earth-light"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold text-earth">$780</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Earnings</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="si si-envelope-open fa-2x text-elegance-light"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold text-elegance">15</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Messages</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="si si-users fa-2x text-pulse"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold text-pulse">4252</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Online</div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- END Row #1 -->
        </div>
        {{-- <div class="row">
            <!-- Row #3 -->
            <div class="col-md-6">
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default border-bottom">
                        <h3 class="block-title">Latest Orders</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-toggle="block-option"
                                data-action="state_toggle" data-action-mode="demo">
                                <i class="si si-refresh"></i>
                            </button>
                            <button type="button" class="btn-block-option">
                                <i class="si si-wrench"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">ID</th>
                                    <th>Status</th>
                                    <th class="d-none d-sm-table-cell">Customer</th>
                                    <th class="d-none d-sm-table-cell text-end">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1851</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Laura Carr</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$490</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1850</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Carl Wells</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$348</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1849</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Andrea Gardner</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$906</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1848</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Alice Moore</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$658</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1847</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">Canceled</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Susan Day</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$890</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1846</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Completed</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Sara Fields</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$571</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1845</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Completed</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Adam McCoy</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$284</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1844</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Susan Day</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$438</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1843</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Jose Mills</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$786</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="be_pages_ecom_order.html">ORD.1842</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="be_pages_ecom_customer.html">Thomas Riley</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-end">
                                        <span>$201</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default border-bottom">
                        <h3 class="block-title">Top Products</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-toggle="block-option"
                                data-action="state_toggle" data-action-mode="demo">
                                <i class="si si-refresh"></i>
                            </button>
                            <button type="button" class="btn-block-option">
                                <i class="si si-wrench"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th class="d-none d-sm-table-cell" style="width: 100px;">ID</th>
                                    <th>Product</th>
                                    <th class="text-center">Orders</th>
                                    <th class="d-none d-sm-table-cell text-center">Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.258</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Dark Souls III</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">912</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.198</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Bioshock Collection</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">895</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.852</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Alien Isolation</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">820</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.741</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Bloodborne</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">793</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.985</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Forza Motorsport 7</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">782</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.056</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Fifa 18</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">776</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.036</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Gears of War 4</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">680</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.682</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Minecraft</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">670</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.478</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Dishonored 2</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">640</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        <a class="fw-semibold" href="be_pages_ecom_product_edit.html">PID.952</a>
                                    </td>
                                    <td>
                                        <a href="be_pages_ecom_product_edit.html">Gran Turismo Sport</a>
                                    </td>
                                    <td class="text-center">
                                        <a class="text-gray-dark" href="be_pages_ecom_orders.html">630</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <div class="text-warning">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END Row #3 -->
        </div> --}}
    </div>
    <!-- END Page Content -->
@endsection
