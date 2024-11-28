@php
    $user_info = App\Models\User::where('users.status', 1)
        ->leftJoin('companies', 'companies.id', 'users.comp_id')
        ->leftJoin('departments', 'departments.id', 'users.dept_id')
        ->select('users.*', 'companies.company_name', 'departments.department_name')
        ->where('users.id', $request_tools->user_id)
        ->first();
@endphp

<style>
    html {
        font-family: arial, sans-serif;
        font-size: 14px;
    }

    label {
        font-family: Arial, Helvetica, sans-serif;
    }

    .flex {
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    .d-flex {
        display: flex;
    }

    .borders {
        border: 1px solid black;
        margin-top: -1px;
        /* margin-right: -1px;
        margin-left: -1px; 
        margin-bottom: -1px; */
    }

    .borders p {
        padding-left: 10px;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .containerPrint h3 {
        margin-block: 7px;
    }

    .containerPrint h4 {
        margin-bottom: 7px;
        margin-top: 0px;
    }

    .containerPrint h6 {
        margin-block: 0px;
        margin-top: 2px;
        text-transform: uppercase;
        font-size: 10px;
    }

    /* table */

    table {
        width: 100%;
        margin-top: 15px;
        border-color: black;
    }

    table th,
    table td {
        text-align: center !important;
        vertical-align: middle;
    }

    /* th {
        border: 1px solid black;
        border-bottom: none;
        text-transform: uppercase;
    }

    td,
    th {
        border-left: 1px solid black;
        border-right: 1px solid black;
        text-align: center;
        padding: 4px;
    } */

    /* tr:nth-child(even) {
                background-color: #dddddd;
            } */
    /* tbody tr:nth-last-child(1) {
        border-bottom: 1px solid black;

    } */

    .firstRow td {
        padding: 5px;
    }

    .th-custom {
        font-size: 13px;
        padding-bottom: 0;
        border: 1px solid black;
        border-collapse: collapse;
    }

    .border-right {
        border-right: 1px solid black;
    }

    #printFooter div:nth-last-child(1) {
        border-right: none;
    }

    @media print {
        @page {
            size: 215.9mm 279.4mm;
            margin: 8mm;
        }
    }

    #itemList>tr:nth-child(1)>td {
        border-right: unset;
        border-left: unset;
    }

    #modalTable th {
        text-align: center;
    }
</style>
<div class="page-wrapper">

    <div class="page-body">
        <div class="py-3">
            <div style="width: 1000px; height: 1056px; position: relative;" class="containerPrint containerPrintRttte px-2 mx-auto"> 
                <div class="actionButtons">
                    <button id="view_approvers" class="btn btn-primary timeline-trigger mb-2">View Approvers</button>
                    @if (Auth::user()->user_type_id == 2)
                        <button id="rtttePrintBtn" class="btn btn-success mb-2">Print RTTTE</button>
                        <button id="dafPrintBtn" class="btn btn-success mb-2">Print DAF</button>
                    @endif
                </div> 
                <div class="d-flex justify-content-between align-items-center borders" style="border-top: 1px solid black">
                    <img style="padding-left: 10px" src="{{ asset('media/logo.png') }}" width="auto" height="37px"
                        alt="logo">
                    <div style="border-left: 1px solid black; width: 15%; padding-left: 10px;">
                        <h6 style="font-weigth: 700;">Date</h6>
                        <p>{{ $request_tools->date_requested }}</p>
                    </div>
                </div>
                <div class="borders">
                    <h3 style="margin-block: 7px; font-size: 16px; text-align: center;">REQUEST TO TRANSFER TOOLS AND
                        EQUIPMENT</h3>
                </div>
                <div class="d-flex">
                    <div class="borders"
                        style="width: 100%; display: flex; flex-direction: column; border-top: 1px solid black">
                        <div style="border-bottom: 1px solid black">
                            <h3 style="margin-block: 2px; margin-left: 5px; font-size: 13px; font-weight: bold">FROM
                            </h3>
                        </div>
                        <div style="border-bottom: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">project name</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $tools_owner->project_name }}</p>
                        </div>
                        <div style="border-bottom: 1px solid black; padding-inline: 3px;">
                            <h6 style="">Location</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $tools_owner->project_location }}</p>
                        </div>
                        <div style="padding-left: 3px;">
                            <h6 style="">Name of Project Engineer</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $tools_owner->fullname }}</p>
                        </div>
                    </div>
                    <div class="borders" style="width: 100%; display: flex; flex-direction: column">
                        <div style="border-bottom: 1px solid black">
                            <h3 style="margin-block: 2px; margin-left: 5px; font-size: 13px; font-weight: bold">TO</h3>
                        </div>
                        <div style="border-bottom: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">project name</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->project_name }}</p>
                        </div>
                        <div style="border-bottom: 1px solid black; padding-inline: 3px;">
                            <h6 style="">Location</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->project_address }}</p>
                        </div>
                        <div style="padding-left: 3px; width: 60%">
                            <h6 style="">Name of Project Engineer</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $requestor }}</p>
                        </div>
                    </div>
                </div>
                <div class="borders">
                    <h3 style="margin-block: 7px; margin-left: 5px; font-size: 11px;">Listed below are the tools and
                        equipment for which i am currently responsible, and i hereby request the transfer of the
                        possession and accountability to the designated successor whose signature appears below.</h3>
                </div>
                <table id="modalTable" class="fs-sm table-bordered w-100">
                    <thead>
                        <tr>
                            <th class="">PICTURE</th>
                            <th class="">ITEM NO.</th>
                            <th class="">TEIS NO.</th>
                            <th class="">ITEM CODE</th>
                            <th style="width: 150px;" class="">DESCRIPTION</th>
                            <th class="">SERIAL NUMBER</th>
                            <th class="">QTY</th>
                            <th class="">UNIT</th>
                            <th style="width: 50px;">TOOLS & EQUIPMENT CONDITION ASSESSMENT</th>
                            <th class="">REASON FOR TRANSFER</th>
                            <th class="">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                {{-- <div class="borders">
                    <h3 style="margin-block: 3px; margin-left: 5px; font-size: 11px;">.</h3>
                </div> --}}
                <div class="borders" id="printFooter" style="display: flex;">
                    {!! $html !!}
                    <h3 style="margin-block: 5px; font-size: 10px; text-transform: uppercase;">instruction submit this
                        signed document to the operation manager, warehouse and accounting department managers</h3>
                </div>


            </div> <!-- End page body-->

            <h1 style="text-align:center">DAF</h1>
            <div class="py-3">
                <div style="width: 1000px; height: 1056px;" class="containerPrint containerPrintDaf px-2 mx-auto">
                    <div class="flex">
                        <img src="{{ asset('media/logo.png') }}" width="200px" alt="logo">
                        <h3 style="margin-block: 7px; font-size: 16px; text-transform: uppercase;">Deduction
                            Authorazation Form</h3>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">Name</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $user_info->fullname }}
                            </p>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">Employee ID no.</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $user_info->emp_id }}
                            </p>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 40%">
                            <h6 style="">department</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $user_info->department_name }}</p>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 40%">
                            <h6 style="">date</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->date_requested }}</p>
                        </div>
                        <div style="padding-left: 3px; width: 40%">
                            <h6 style="">daf no.</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->request_number }}</p>
                        </div>
                    </div>
                    <div class="borders">
                        <h3 style="margin-block: 7px; margin-left: 5px; font-size: 15px;">
                            <p class="fw-bold">I hereby authorize the company <span
                                    class="text-primary font-bold text-decoration-underline"
                                    style="font-size: 18px; text-transform: uppercase;">{{ $user_info->company_name }}</span>,
                                to deduct the following for the purposes indicated below;</p>
                        </h3>
                    </div>
                    <div class="borders">
                        <div
                            style="text-align: center;text-transform: uppercase; background-color: lightgrey; font-weight: bold; border-right: 1px solid black !important;">
                            deduction details</div>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">deduction purpose</h6>
                            <div id="itemListDaf">
                            </div>
                        </div>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">amount in words</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">&nbsp;</p>
                        </div>
                        <div style="padding-left: 3px; width: 40%">
                            <h6 style="">amount in figure</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">&nbsp;</p>
                        </div>
                    </div>
                    <div class="borders">
                        <div
                            style="text-align: center;text-transform: uppercase; background-color: lightgrey; font-weight: bold">
                            payment details</div>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">deduction purpose</h6>
                            <div style="display: flex; justify-content: center;">
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline1"
                                        name="example-checkboxs-inline" value="option1">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline1">Salary</label>
                                </div>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline2"
                                        name="example-checkboxs-inline" value="option2">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline2">Commissions</label>
                                </div>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline3"
                                        name="example-checkboxs-inline" value="option2">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline3">Others</label>
                                    <span>__________</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">for deductions charged to salary, fill the details below</h6>
                            <div style="display: flex;">
                                <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px; font-size: 13px;">
                                    terms of payment:</p>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline1"
                                        name="example-checkboxs-inline" value="option1">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline1">Salary</label>
                                </div>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline3"
                                        name="example-checkboxs-inline" value="option2">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline3">Others</label>
                                    <span>__________</span>
                                </div>
                            </div>
                            <div style="display: flex;">
                                <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px; font-size: 13px;">
                                    Schedule of deduction:</p>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline1"
                                        name="example-checkboxs-inline" value="option1">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline1">Monthly</label>
                                </div>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline3"
                                        name="example-checkboxs-inline" value="option2">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline3">Bi-Monthly</label>
                                </div>
                            </div>
                        </div>
                        <div style="border-right: 1px solid black; padding-left: 3px; width: 100%">
                            <h6 style="">for deductions charged to commissions, fill the details below</h6>
                            <div style="display: flex;">
                                <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px; font-size: 13px;">
                                    terms of payment:</p>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline1"
                                        name="example-checkboxs-inline" value="option1">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline1">Full</label>
                                </div>
                                <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="checkbox" id="example-checkboxs-inline3"
                                        name="example-checkboxs-inline" value="option2">
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-checkboxs-inline3">Installments</label>
                                    <span>__________</span>
                                </div>
                            </div>
                            <div style="display: flex;">
                                <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px; font-size: 13px;">
                                    Maximum amount to be deducted:__________</p>
                            </div>
                        </div>
                        <div style=" padding-left: 3px; width: 50%">
                            <h6 style="">start of deduction</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;"></p>
                        </div>
                    </div>
                    <div class="borders" style="font-size: 13px; font-weight: bold; padding-block: 8px;">
                        <p class="mb-2"> I understand and acknowledge that the authorized deductions will be made on
                            a monthly or bi-monthly basis. </p>

                        <p> In the event that my employment ends for any reason, and the outstanding amount herein has
                            not yet been fully deducted, I agree that any remaining balance shall be deducted from my
                            final pay. Furthermore, if my final pay is insufficient to cover the remaining balance, I
                            acknowledge and consent that the company shall have the right and remedies to collect the
                            remaining balance by any lawful means available to them.</p>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">printed name</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $user_info->fullname }}</p>
                        </div>
                        <div style="border-right: 1px solid black; padding-left: 3px; width: 100%">
                            <h6 style="">Signature</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;"></p>
                        </div>
                        <div style=" padding-left: 3px; width: 50%">
                            <h6 style="">Date</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->date_requested }}</p>
                        </div>
                    </div>
                    <div class="borders"
                        style="font-size: 13px; margin-top: 5px; padding-block: 8px; font-weight: bold">
                        <p class="mb-2"> Please return this signed authorization form to the HR Department. If you
                            have any question or require additional information, please contact the HR Department or
                            your supervisor </p>

                        <p> Note: This authorization form remains valid until revoked in writing by the employee or
                            until further notice.</p>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">Noted by</h6>
                            <div>&nbsp;</div>
                            <div class="d-flex" style="justify-content: center;">
                                <p style="margin-block: 0px; font-weight: 700; font-size: 10px;">HR MANAGER</p>
                            </div>
                        </div>
                        <div style="border-right: 1px solid black; padding-left: 3px; width: 100%">
                            <h6 style="">received by</h6>
                            <div>&nbsp;</div>
                            <div class="d-flex" style="justify-content: center;">
                                <p style="margin-block: 0px; font-weight: 700; font-size: 10px;">HR ASSISTANT</p>
                            </div>
                        </div>
                        <div style=" padding-left: 3px; width: 50%">
                            <h6 style="">date received</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">&nbsp;</p>
                        </div>
                    </div>
                    <div class="borders">
                        <h6 style="">remarks</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">&nbsp;</p>
                    </div>
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">acknowledged by</h6>
                            <div>&nbsp;</div>
                            <div class="d-flex" style="justify-content: center;">
                                <p style="margin-block: 0px; font-weight: 700; font-size: 10px;">PAYROLL PERSONNEL</p>
                            </div>
                        </div>
                        <div style=" padding-left: 3px; width: 100%">
                            <h6 style="">acknowledged by</h6>
                            <div>&nbsp;</div>
                            <div class="d-flex" style="justify-content: center;">
                                <p style="margin-block: 0px; font-weight: 700; font-size: 10px;">ACCOUNTING PERSONNEL
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- <table id="modalTable" class="fs-sm table-bordered w-100">
                    <thead>
                        <tr>
                            <th class="">PICTURE</th>
                            <th class="">ITEM NO.</th>
                            <th class="">TEIS NO.</th>
                            <th class="">ITEM CODE</th>
                            <th style="width: 150px;" class="">DESCRIPTION</th>
                            <th class="">SERIAL NUMBER</th>
                            <th class="">QTY</th>
                            <th class="">UNIT</th>
                            <th style="width: 50px;">TOOLS & EQUIPMENT CONDITION ASSESSMENT</th>
                            <th class="">REASON FOR TRANSFER</th>
                            <th class="">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table> --}}
                    <h3 style="margin-block: 5px; font-size: 10px; text-transform: uppercase;">Note: HR Department
                        (White), Employee (Pink), Payroll Department (Yellow)</h3>
                </div>


            </div>
        </div>


        {{-- <!-- Floating Timeline -->
        <div class="timeline-float d-none" style="max-width: 300px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: white; position: absolute; top: 10%">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="m-0" style="font-size: 16px; color: #333;">Timeline for Request #1002</h3>
            <button class="btn-close close-timeline" aria-label="Close"></button>
            </div>
            <ul class="list-unstyled">
            <li class="d-flex align-items-center mb-3 completed">
                <span class="fs-4 me-2">📝</span>
                <p class="m-0" style="color: green;">Request Approved - 2024-11-15</p>
            </li>
            <li class="d-flex align-items-center mb-3">
                <span class="fs-4 me-2">📦</span>
                <p class="m-0">Request To Ship - Pending</p>
            </li>
            <li class="d-flex align-items-center mb-3">
                <span class="fs-4 me-2">🚚</span>
                <p class="m-0">Request En Route - Pending</p>
            </li>
            <li class="d-flex align-items-center mb-3">
                <span class="fs-4 me-2">🏢</span>
                <p class="m-0">Request Received - Pending</p>
            </li>
            </ul>
        </div> --}}



        <!-- Floating Timeline -->
        <div class="timeline-float d-none" style="max-width: 900px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="m-0" style="font-size: 16px; color: #333;">Approvers for Request #{{ $request_tools->request_number }}</h3>
            <button class="btn-close close-timeline" aria-label="Close"></button>
            </div>
              <table id="approverTable" class="fs-sm table table-hover table-vcenter">
                    <thead>
                        <tr>
                            <th class="">Sequence</th>
                            <th style="width: 150px;" class="">Fullname</th>
                            <th class="">Position</th>
                            <th class="">Date Approved</th>
                            <th class="">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($approvers as $approver)
                        <tr>
                            <td>{{ $approver->sequence}}</td>
                            <td>{{ $approver->fullname}}</td>
                            <td>{{ $approver->position}}</td>
                            <td>{{ $approver->date_approved}}</td>
                            <td>
                                @if ($approver->approver_status == 1)
                                <span class="badge bg-success">Approved</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>
        <script>
            $(document).ready(function() {


                const $timelineFloat = $(".timeline-float");
                const $trigger = $(".timeline-trigger");
                const $closeButton = $(".close-timeline");

                // Show timeline on trigger click
                $trigger.on("click", function() {
                    $timelineFloat.toggleClass("d-none");
                });

                // Close timeline on button click
                $closeButton.on("click", function() {
                    $timelineFloat.addClass("d-none");
                });

                // Optional: Close the timeline if clicking outside of it
                $(document).on("click", function(event) {
                    if (!$(event.target).closest($timelineFloat).length && !$(event.target).closest($trigger)
                        .length) {
                        $timelineFloat.addClass("d-none");
                    }
                });


                $('#rtttePrintBtn').click(function() {
                    $('.containerPrintRttte').printThis({
                        importCSS: true, // import parent page css
                        importStyle: true, // import style tags
                        copyTagClasses: true, // copy classes from the html & body tag
                        // footer: $("#printFooter")
                        header: null,
                        footer: null,
                        beforePrint: function() {
                            $(".actionButtons").hide()
                            $("#modalTable").DataTable().column(-1).visible(false)

                        },
                        afterPrint: function() {
                            $(".actionButtons").show()
                            $("#modalTable").DataTable().column(-1).visible(true)
                        },

                    });

                })

                
                $('#dafPrintBtn').click(function() {
                    $('.containerPrintDaf').printThis({
                        importCSS: true, // import parent page css
                        importStyle: true, // import style tags
                        copyTagClasses: true, // copy classes from the html & body tag
                        // footer: $("#printFooter")
                        header: null,
                        footer: null,


                    });

                })





                // show_loader();
                // setTimeout(function() {
                //     hide_loader();
                // }, 1000)

                // var idval = $("#id_val").val();
                // var token = $("#token_val").val();


                // $.ajax({
                //     url: '/approver_list_print/' + idval,
                //     method: 'GET',
                //     dataType: 'json',
                //     processData: false,
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     },
                //     success: function(result) {
                //         var html = result[0].html;
                //         $("#printFooter").append(html);
                //     },
                //     error: function(jqXHR, exception) {
                //         alert('error');
                //     },
                // });

                // $.ajax({
                //     url: '/load_print_item/' + idval,
                //     method: 'GET',
                //     dataType: 'json',
                //     processData: false,
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     },
                //     success: function(result) {
                //         var html = result[0].html;
                //         var emptyRow =
                //             '<tr><td><br></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                //         $("#itemList").append(html);



                //         let length = $("#itemList tr").length;
                //         const newLength = length - 1;

                //         if (newLength < 17) {
                //             for (let count = 0; count < 17 - newLength; count++) {
                //                 $("#itemList").append(emptyRow);
                //             }

                //         }


                //         if (newLength > 17) {
                //             $("#itemList > tr:nth-child(18)").css("border-bottom", "1px solid black");
                //             $("#itemList > tr:nth-child(19)").css("page-break-before", "always");
                //         }

                //     },
                //     error: function(jqXHR, exception) {
                //         alert('error');
                //     },
                // });



            });
        </script>
