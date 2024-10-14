@if(count($approvers) > 0)
    @foreach ($approvers as $approver)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-primary fs-5">{{ $approver->sequence }}</span>
                <div>
                    <span class="fs-3">{{ $approver->fullname }}</span>
                    <div style="font-size: 15px; margin-top: -3px; font-weight: bold">{{ $approver->code }} - {{ $approver->position }}</div>
                </div>
            </div>
            <div>
                <button type="button" data-id="{{ $approver->sa_id }}" data-fn="{{ $approver->fullname }}" data-comp="{{ $approver->code }}" data-pos="{{ $approver->position }}" data-triggerby="edit" class="editApprover btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#approverSetupModal"><i class="fa fa-pen"></i></button>
                <button type="button" data-id="{{ $approver->sa_id }}" class="deleteApprover btn btn-sm btn-danger"><i class="fa fa-xmark"></i></button>
            </div>
        </li>
    @endforeach
@else
    <span class="fw-bold text-center text-secondary opacity-75 fs-4">No setup approver</span>
@endif
