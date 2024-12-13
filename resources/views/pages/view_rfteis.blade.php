    @php
        $user_info = App\Models\User::where('users.status', 1)
            ->leftJoin('companies', 'companies.id', 'users.comp_id')
            ->leftJoin('departments', 'departments.id', 'users.dept_id')
            ->select('users.*', 'companies.company_name', 'departments.department_name')
            ->where('users.id', $request_tools->pe)
            ->first();
    @endphp


    <style>
        html {
            font-family: arial, sans-serif;
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

        #modalTable tr td:first-child {
            text-align: center;
        }

        /* Approver table */
    .timeline-float {
        max-width: 900px;
        padding: 15px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        background-color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%; 
    }

    #approverTable {
        width: 100%; 
        border-collapse: collapse; 
    }

    #approverTable th, #approverTable td {
        padding: 8px;
        text-align: left;
        font-size: 14px; 
        word-wrap: break-word; 
    }

    #approverTable thead th {
        background-color: #f9f9f9; 
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        /* Adjust layout for tablets and small screens */
        .timeline-float {
            max-width: 100%; /* Full width on small screens */
            top: auto;
            left: auto;
            transform: none;
            position: relative;
            margin: 20px auto;
        }

        #approverTable th, #approverTable td {
            font-size: 12px; 
            padding: 6px; 
        }

        #approverTable thead {
            display: none; 
        }

        #approverTable tbody tr {
            display: block; 
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        #approverTable tbody td {
            display: flex;
            justify-content: space-between;
            font-size: 14px; 
        }

        #approverTable tbody td::before {
            content: attr(data-label); 
            font-weight: bold;
            margin-right: 10px;
            flex-basis: 30%;
        }

        #approverTable tbody td:last-child {
            text-align: right; 
        }
    }

    @media (max-width: 480px) {
        .timeline-float {
            padding: 10px;
        }

        #approverTable tbody td {
            font-size: 12px;
        }
    }
    </style>
    <div class="page-wrapper">

        <div class="page-body">
            <div class="py-3">
                <div style="width:1000px; height: 1056px;" class="containerPrint containerPrintRfteis px-2 mx-auto">
                    <div class="actionButtons">
                        <button id="view_approvers" class="btn btn-primary timeline-trigger mb-2">View Approvers</button>
                        @if (Auth::user()->user_type_id == 2)
                            <button id="rfteisPrintBtn" class="btn btn-success mb-2">Print RFTEIS</button>
                            <button id="dafPrintBtn" class="btn btn-success mb-2">Print DAF</button>
                        @endif
                    </div>
                    @if (Auth::user()->user_type_id == 2)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <img src="{{ asset('media/logo.png') }}" width="200px" alt="logo">
                                <h3 style="margin-block: 7px; font-size: 16px;">REQUEST FOR TOOLS AND EQUIPMENT ISSUANCE SLIP</h3>
                            </div>
                            <div id="qrCode">
                                
                            </div>
                        </div>
                    @else
                        <div class="flex">
                            <img src="{{ asset('media/logo.png') }}" width="200px" alt="logo">
                            <h3 style="margin-block: 7px; font-size: 16px;">REQUEST FOR TOOLS AND EQUIPMENT ISSUANCE SLIP
                            </h3>
                        </div>
                    @endif
                    
                    <div class="borders" style="display: flex; border-top: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">project engineer</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $requestor }}</p>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">Subcon</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->subcon }}</p>
                        </div>
                        <div style="padding-left: 3px; width: 60%">
                            <h6 style="">Date</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->date_requested }}</p>
                        </div>
                    </div>
                    <div class="borders" style="display: flex;">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">Customer Name</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->customer_name }}</p>
                        </div>
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                            <h6 style="">Project name</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->project_name }}</p>
                        </div>
                        <div style="padding-left: 3px; width: 60%">
                            <h6 style="">Project Code</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->project_code }}</p>
                        </div>
                    </div>
                    <div class="borders" style="display: flex; border-bottom: 2px solid black">
                        <div style="border-right: 1px solid black; padding-inline: 3px; width: 60%">
                            <h6 style="">Project Address</h6>
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $request_tools->project_address }}</p>
                        </div>
                        <div style="padding-left: 3px; width: 100%">
                            <h6 style="">Delivery Arrangement</h6>
                            <div style="display: flex;">
                                <div style="padding-left: 20px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="radio" id="example-radios-inline1"
                                        name="example-radios-inline" value="option1" disabled>
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-radios-inline1">Supplier to Project Site</label>
                                </div>
                                <div style="padding-left: 20px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="radio" id="example-radios-inline2"
                                        name="example-radios-inline" value="option2"
                                        @if ($request_tools->tr_type == 'rfteis') checked @endif>
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-radios-inline2">Warehouse to Project Site</label>
                                </div>
                                <div style="padding-left: 20px; margin-top: 5px; margin-bottom: 5px;">
                                    <input class="form-check-input" type="radio" id="example-radios-inline3"
                                        name="example-radios-inline" value="option2" disabled>
                                    <label class="form-check-label" style="font-size: 13px;"
                                        for="example-radios-inline3">Project Site to Project Site</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="modalTable" class="fs-sm table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center">QTY</th>
                                <th class="text-center">UNIT</th>
                                <th style="width: 40%" class="text-center">DESCRIPTION</th>
                                <th class="text-center">ITEM CODE</th>
                                @if ($path == 'pages/not_serve_items')
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Can Redeliver?</th>
                                @else
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="borders" id="printFooter" style="display: flex; margin-top: 20px; font-size: 13px;">
                        {!! $html !!}
                    </div>
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
                            <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">
                                {{ $user_info->fullname }}</p>
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
                            style="text-align: center;text-transform: uppercase; background-color: lightgrey; font-weight: bold">
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
                        <div style="border-right: 1px solid black; padding-left: 3px; width: 100%;">
                            <h6 style="">Signature</h6>
                            <p style="text-align: center; padding-left: 10px;margin-top: 5px;margin-bottom: 5px;"><span class="fw-bold text-secondary" style="font-size: 14px;">No Signature Required</span></p>
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

                <div class="timeline-float d-none"
                    style="max-width: 900px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h3 class="m-0" style="font-size: 16px; color: #333;">Approvers for Request
                            #{{ $request_tools->request_number }}</h3>
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
                                    <td data-label="Sequence">{{ $approver->sequence }}</td>
                                    <td data-label="Fullname">{{ $approver->fullname }}</td>
                                    <td data-label="Position">{{ $approver->position }}</td>
                                    <td data-label="Date Approved">{{ $approver->date_approved }}</td>
                                    <td data-label="Status">
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

            </div>

        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
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



                const request_number = {{ $request_tools->request_number }};
                const user_type = {{ Auth::user()->user_type_id }};

                if(user_type == 2){
                  const jsonData = JSON.stringify({
                        request_number
                    });

                    var qrcodeContainer = document.getElementById("qrCode");
                    var qrCode = new QRCode(qrcodeContainer, {
                        text: jsonData,
                        width: 128,
                        height: 128,
                    });
  
                }

                    



                $('#rfteisPrintBtn').click(function() {
                    $('.containerPrintRfteis').printThis({
                        importCSS: true, // import parent page css
                        importStyle: true, // import style tags
                        copyTagClasses: true, // copy classes from the html & body tag
                        // footer: $("#printFooter")
                        header: null,
                        footer: null,
                        beforePrint: function() {
                            $(".actionButtons").hide()
                            $("#modalTable").DataTable().column(4).visible(false)

                        },
                        afterPrint: function() {
                            $(".actionButtons").show()
                            $("#modalTable").DataTable().column(4).visible(true)
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





                //     show_loader();
                //     setTimeout(function() {
                //         hide_loader();
                //     }, 1000)

                //     var idval = $("#id_val").val();
                //     var token = $("#token_val").val();


                //     $.ajax({
                //         url: '/approver_list_print/' + idval,
                //         method: 'GET',
                //         dataType: 'json',
                //         processData: false,
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         },
                //         success: function(result) {
                //             var html = result[0].html;
                //             $("#printFooter").append(html);
                //         },
                //         error: function(jqXHR, exception) {
                //             alert('error');
                //         },
                //     });

                //     $.ajax({
                //         url: '/load_print_item/' + idval,
                //         method: 'GET',
                //         dataType: 'json',
                //         processData: false,
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         },
                //         success: function(result) {
                //             var html = result[0].html;
                //             var emptyRow =
                //                 '<tr><td><br></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                //             $("#itemList").append(html);



                //             let length = $("#itemList tr").length;
                //             const newLength = length - 1;

                //             if (newLength < 17) {
                //                 for (let count = 0; count < 17 - newLength; count++) {
                //                     $("#itemList").append(emptyRow);
                //                 }

                //             }


                //             if (newLength > 17) {
                //                 $("#itemList > tr:nth-child(18)").css("border-bottom", "1px solid black");
                //                 $("#itemList > tr:nth-child(19)").css("page-break-before", "always");
                //             }

                //         },
                //         error: function(jqXHR, exception) {
                //             alert('error');
                //         },
                //     });



            });
        </script>
