
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
            margin-left: -1px; */
        /* margin-bottom: -1px; */
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

    #modalTable_wrapper {
        width: 100%;
        margin-top: 20px;
        border-color: black;
    }
    
    #modalTable_wrapper th{
        border: 1px solid black;
        text-transform: uppercase;
    }

    #modalTable tbody tr > *{
        border: 1px solid black;
        border-collapse: collapse;
    }

    /* td,
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
        height: 300px;

    } */

    #modalTable_wrapper thead tr.firstRow th{
        border-top: none;
    }

    /* #modalTable_wrapper > div.row.mt-2.justify-content-md-center > div > div > div.dt-scroll-head > div > table > thead > tr.firstRow > th{
        border-top: none;
    } */

    /*
        .th-custom {
            font-size: 13px;
            padding-bottom: 0;
            border: 1px solid black;
            border-collapse: collapse;
        } */

    .border-right {
        border-right: 1px solid black;
    }

    #printFooter div:nth-last-child(1) {
        border-right: none;
    }

    @media print {
        @page {
            size: 279.4mm 215.9mm;
            margin: 8mm;
        }
    }

    #itemList>tr:nth-child(1)>td {
        border-right: unset;
        border-left: unset;
    }

    .th-absolute {
        position: absolute;
        top: 105%;
        left: 50%;
        transform: translate(-50%, -50%);
        transform-origin: center center;
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
            <div style="width: 1100px; height: 1056px;" class="containerPrint px-2">
                <div class="actionButtons">
                    <button id="view_approvers" class="btn btn-primary timeline-trigger mb-2">View Approvers</button>
                    @if (Auth::user()->user_type_id == 2)
                        <button id="pulloutPrintBtn" class="btn btn-success mb-2">Print Pullout</button>
                    @endif
                </div>
                <div class="flex">
                    <img src="{{ asset('media/logo.png') }}" width="200px" alt="logo">
                    <p style="font-size: 12px; margin-bottom: 2px;">827 Calderon Bldg., EDSA, South Triangle, Quezon City
                    </p>
                    <h3 style="margin-block: 7px; font-size: 16px;">PULLOUT FORM</h3>
                </div>
                <div class="borders" style="display: flex; border-top: 2px solid black">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">client</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ ucwords(strtolower($pullout_tools->client)) }}</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">project</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ ucwords(strtolower($pullout_tools->project_name)) }}</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 40%">
                        <h6 style="">contact no.</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $pullout_tools->contact_number }}</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 40%">
                        <h6 style="">date needed to pickup</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $pullout_tools->pickup_date }}</p>
                    </div>
                    <div style="padding-left: 3px; width: 40%">
                        <h6 style="">Date</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $pullout_tools->date_requested }}</p>
                    </div>
                </div>
                <div class="borders" style="display: flex; border-bottom: 2px solid black">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">Address</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ ucwords(strtolower($pullout_tools->project_address)) }}</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 50%">
                        <h6 style="">project code</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ $pullout_tools->project_code }}</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 60%">
                        <h6 style="">subcontractor</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">{{ ucwords(strtolower($pullout_tools->subcon)) }}</p>
                    </div>
                    <div style="padding-left: 3px; width: 100%">
                        <h6 style="">company</h6>
                        <div style="display: flex;">
                            <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                <input class="form-check-input compCheckbox" type="checkbox" id="example-checkboxs-inline1"
                                    name="example-checkboxs-inline" value="option1" {{ $pullout_tools->comp_id == 1 ? "checked" : "disabled" }}>
                                <label class="form-check-label" style="font-size: 13px;"
                                    for="example-checkboxs-inline1">FMC</label>
                            </div>
                            <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                <input class="form-check-input compCheckbox" type="checkbox" id="example-checkboxs-inline2"
                                    name="example-checkboxs-inline" value="option2" {{ $pullout_tools->comp_id == 3 ? "checked" : "disabled" }}>
                                <label class="form-check-label" style="font-size: 13px;"
                                    for="example-checkboxs-inline2">MBI</label>
                            </div>
                            <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                <input class="form-check-input compCheckbox" type="checkbox" id="example-checkboxs-inline3"
                                    name="example-checkboxs-inline" value="option2" {{ $pullout_tools->comp_id == 2 ? "checked" : "disabled" }}>
                                <label class="form-check-label" style="font-size: 13px;"
                                    for="example-checkboxs-inline3">MSC</label>
                            </div>
                            <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                <input class="form-check-input compCheckbox" type="checkbox" id="example-checkboxs-inline3"
                                    name="example-checkboxs-inline" value="option2" {{ $pullout_tools->comp_id == 4 ? "checked" : "disabled" }}>
                                <label class="form-check-label" style="font-size: 13px;"
                                    for="example-checkboxs-inline3">CDO</label>
                            </div>
                            <div style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                                <input class="form-check-input" type="checkbox" id="example-checkboxs-inline3"
                                    name="example-checkboxs-inline" value="option2" {{ $pullout_tools->comp_id == 5 ? "checked" : "disabled" }}>
                                <label class="form-check-label" style="font-size: 13px;"
                                    for="example-checkboxs-inline3">DDO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="modalTable" class="fs-sm w-100" style="margin-top: 10px">
                    <thead>
                        <tr>
                            <th class=""
                                style="border-bottom: none; position: relative; width: 4%; font-size: 12px"><span
                                    class="th-absolute">ITEM NO.</span></th>
                            <th class=""
                                style="border-bottom: none; position: relative; width: 7%; font-size: 12px"><span
                                    class="th-absolute">PRODUCT CODE</span></th>
                            <th class=""
                                style=" border-bottom: none; position: relative; width: 7%; font-size: 12px">
                                <span class="th-absolute">TEIS NO./ MTS/DR/AR</span>
                            </th>
                            <th style=" border-bottom: none; position: relative; width: 15%; font-size: 12px"
                                class=""><span class="th-absolute">DESCRIPTION</span></th>
                            <th colspan="2" class="" style="width: 11%; font-size: 9px; text-align: center;">PM/TL/PE
                                RECOMMENDATION</th>
                            {{-- <th colspan="5" class="" style="width: 15%; font-size: 12px">PM/TL/PE
                                RECOMMENDATION ACTION</th> --}}
                            <th class=""
                                style="border-bottom: none; position: relative; width: 13%; font-size: 12px">
                                <span class="th-absolute">REASON FOR TRANSFER</span></th>
                            @if ($path == 'pages/pullout_for_receiving')
                            <th class=""
                                style="border-bottom: none; position: relative; width: 13%; font-size: 12px">
                                <span class="th-absolute">CHECKER</span></th>
                            <th class=""
                                style="border-bottom: none; position: relative; width: 9%; font-size: 12px">
                                <span class="th-absolute">WH EVAL</span></th>
                            <th class=""
                                style="width: 8%; border-bottom: none; position: relative; font-size: 12px">
                                <span class="th-absolute">Action</span></th> 
                            @endif
                        </tr>
                        <tr style="font-size: 10px;" class="firstRow">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th style="text-align: center">GOOD</th>
                            <th style="text-align: center">DEFECTIVE</th>
                            {{-- <th>SCRAP</th>
                            <th>FOR DISPOSAL</th> --}}
                            {{-- <th>RETURN TO SALEABLE STOCK</th>
                            <th>CLEANING/ REPAIR</th>
                            <th>STORE(can be use for repairs)</th>
                            <th>FOR KILO/ DISPOSAL</th>
                            <th>OTHERS</th> --}}
                            <th></th>
                            @if ($path == 'pages/pullout_for_receiving')
                            <th></th>
                            <th></th>
                            <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                {{-- <div class="borders">
                        <h3 style="margin-block: 3px; margin-left: 5px; font-size: 11px;">.</h3>
                    </div> --}}
                <div class="borders" id="printFooter" style="display: flex;">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">note by</h6>
                        <div style="text-align: center;">
                            <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                               &nbsp;</p>
                            <p style="margin-block: 0px; font-weight: 500; font-size: 11px; text-transform: uppercase">
                                client's authorized representative</p>
                        </div>
                    </div>
                    <div style="padding-inline: 3px; width: 100%">
                        <h6 style="">received by</h6>
                        <div style="text-align: center;">
                            <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                &nbsp;</p>
                            <p style="margin-block: 0px; font-weight: 500; font-size: 11px;">MULTI-LINE PERSONNEL</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex" style="margin-top: 10px;">
                    <div class="borders" style="width: 80%;">
                        <h3
                            style="margin-block: 7px; margin-left: 5px; font-size: 11px; text-align: center; font-weight: bold;">
                            TO BE FILLED-UP BY MULTI-LINE PERSONNEL</h3>
                        <div class="borders" id="printFooter"
                            style="display: flex; border-inline: none; height: 70px;">
                            <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                                <h6 style="">requested by project engineer</h6>
                                <div style="text-align: center; padding-top: 10px;">
                                    <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                        {{ $requestor }}</p>
                                </div>
                            </div>
                            <div style="padding-inline: 3px; width: 100%">
                                <h6 style="">accepted/pulled out by warehouse personnel</h6>
                                <div style="text-align: center;">
                                    <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                        &nbsp;</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="borders" style="width: 100%; border-left: none;">
                        <h3
                            style="margin-block: 7px; margin-left: 5px; font-size: 11px; text-align: center; font-weight: bold;">
                            APPROVERS</h3>
                        <div class="borders" id="printFooter"style="display: flex; border-inline: none;  height: 70px;">
                            {!! $html !!}
                        </div>
                    </div>
                </div>
                <h3 style="margin-block: 5px; font-size: 10px; text-transform: uppercase;">1st copy - PID; 2nd copy -
                    Subcon - Warehouse; 4th copy - CMG</h3>
            </div>


                <div class="timeline-float d-none"
                    style="max-width: 900px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h3 class="m-0" style="font-size: 16px; color: #333;">Approvers for Request
                            #{{ $pullout_tools->pullout_number }}</h3>
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

        </div> <!-- End page body-->

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


                $('#pulloutPrintBtn').click(function() {
                    $('.containerPrint').printThis({
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


            });
        </script>
