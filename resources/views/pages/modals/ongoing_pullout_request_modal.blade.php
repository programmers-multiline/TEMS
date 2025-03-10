<style>
    #requestFormLayout{
        height: 80vh;
        overflow-x: scroll;
    }
</style>

<div class="modal fade" id="ongoingPulloutRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal-popin"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">TOOLS AND EQUIPMENTS</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option closeModalBtn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" id="path" value="{{ request()->path() }}">
                <div class="block-content fs-sm">
                    {{-- @if (Request::is('pages/pullout_for_receiving'))
                        <button type="button" id="receiveBtnModal" class="btn btn-primary mb-3 d-block ms-auto"><i class="fa fa-clipboard-check me-1"></i>Receive</button>
                    @endif
                    <input type="hidden" id="path" value="{{ request()->path() }}">
                    <table id="modalTable" class="table fs-sm table-bordered table-hover table-vcenter w-100 js-dataTable-responsive">
                        <thead>
                            <tr>
                                @if (Request::is('pages/pullout_for_receiving'))
                                    <th id="selectToolsContainer" style="padding-right: 10px;"></th>
                                @endif
                                <th>PO Number</th>
                                <th class="test">Asset Code</th>
                                <th>Serial#</th>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Brand</th>
                                <th>Location</th>
                                <th>Status</th>
                                @if (Request::is('pages/pullout_for_receiving', 'pages/pullout_ongoing', 'pages/approved_pullout'))
                                <th>User Evaluation</th>
                                @endif
                                @if (Request::is('pages/pullout_ongoing', 'pages/approved_pullout', 'pages/pullout_completed'))
                                    <th>Action</th>
                                @endif
                                @if (Request::is('pages/pullout_for_receiving'))
                                <th>Add Tool Evaluation</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table> --}}
                    <div id="requestFormLayout">

                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalBtn" data-bs-dismiss="modal">
                        Close
                    </button>
                    {{-- <button id="btnAddTools" type="button" class="btn btn-alt-primary">
                        Done
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
