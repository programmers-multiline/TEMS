    {{-- modal add tools --}}

    <div class="modal fade" id="ongoingTeisRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <input type="hidden" id="path" value="{{ request()->path() }}">
                        <h3 class="block-title">TOOLS AND EQUIPMENTS</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option closeModalBtn" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="aaa" class="block-content fs-sm">
                        @if (Request::is('pages/request_for_receiving'))
                            <button type="button" id="receiveBtnModal" class="btn btn-primary mb-3 d-block ms-auto"><i class="fa fa-clipboard-check me-1"></i>Receive</button>
                        @endif
                        {{-- approved btn --}}
                        @if (Request::is(['pages/rfteis', 'pages/site_to_site_transfer']) && Auth::user()->user_type_id != 4)
                            <button type="button" id="approveBtnModal" class="btn btn-primary mb-3 d-block ms-auto"><i class="fa fa-clipboard-check me-1"></i>Approve</button>
                        @endif
                        <table id="modalTable"
                        class="table fs-sm table-bordered table-hover table-vcenter w-100">
                        <thead>
                            <tr>
                                @if (Request::is('pages/request_for_receiving'))
                                    <th id="selectToolsContainer" style="padding-right: 10px;"></th>
                                @endif
                                @if (!Request::is('pages/rfteis') && !Request::is('pages/rfteis_approved') && !Request::is('pages/rfteis_acc'))
                                <th class="pictureHeader">Picture</th>
                                @endif
                                <th>PO Number</th>
                                <th class="test">Asset Code</th>
                                <th>Serial#</th>
                                <th>Item Code</th>
                                <th>Item Desc</th>
                                <th>Brand</th>
                                <th>Location</th>
                                @if (Auth::user()->user_type_id == 7)
                                <th>Add Price</th>
                                @elseif (Auth::user()->user_type_id != 7)
                                <th>Price</th>
                                @endif
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
    
                        </tbody>
                    </table>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalBtn" data-bs-dismiss="modal">
                            Close
                        </button>
                        @if (Auth::user()->user_type_id == 7)
                            <button id="addPriceBtn" type="button" class="btn btn-alt-primary">
                                Add Price
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>