@extends('layouts.simple')

@section('css')
    <style>
        html {
            font-family: arial, sans-serif;
            font-size: 14px;
            ;
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
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th {
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
        }

        /* tr:nth-child(even) {
                    background-color: #dddddd;
                } */
        tbody tr:nth-last-child(1) {
            border-bottom: 1px solid black;
            /* height: 300px; */

        }

        .firstRow td {
            padding: 5px;
        }

        .th-custom {
            padding-bottom: 0;
            /* margin-bottom: -10px; */
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
                margin: 9mm;
            }
        }
        #itemList > tr:nth-child(1) > td{
            border-right: unset;
            border-left: unset;
        }
    </style>
@endsection



@section('content')
<div class="page-wrapper">

    <div class="page-body">
        <div class="card py-3">
            <div style="width: 800px; height: 1000px;" class="containerPrint px-2">
                <div class="flex">
                    <img src="{{asset('media/logo.png')}}" width="200px"
                        alt="logo">
                    <h3 style="margin-block: 7px; font-size: 16px;">PURCHASE REQUEST FORM</h3>
                </div>
                <div class="borders" style="display: flex; border-top: 2px solid black">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">project engineer</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 60%">
                        <h6 style="">Mr Date</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                    <div style="padding-left: 3px; width: 60%">
                        <h6 style="">Date Needed</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                </div>
                <div class="borders" style="display: flex;">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">Company</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 60%">
                        <h6 style="">Mr Date</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                    <div style="padding-left: 3px; width: 60%">
                        <h6 style="">Date Needed</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                </div>
                <div class="borders" style="display: flex; border-bottom: 2px solid black">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 50%">
                        <h6 style="">Company</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                    <div style="padding-left: 3px; width: 100%">
                        <h6 style="">Date Needed</h6>
                        <p style="padding-left: 10px;margin-top: 5px;margin-bottom: 5px;">a</p>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="th-custom">No.</th>
                            <th class="th-custom">item code</th>
                            <th style="width: 550px;" class="th-custom">Item/Service Required</th>
                            <th class="th-custom">Unit</th>
                            <th colspan="2">For Purchase</br>
                            </th>
                            <th style="width: 150px;" class="th-custom">Remarks</th>
                        </tr>
                        <tr style="border-bottom: 1px solid black; font-size: 10px;" class="firstRow">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="borders">QTY</td>
                            <td class="borders">UNIT</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody style="position: relative" id="itemList">
                    </tbody>
                </table>

                <div class="borders" id="printFooter" style="display: flex; margin-top: 20px;">
                    <div style="border-right: 1px solid black; padding-inline: 3px; width: 100%">
                        <h6 style="">prepared by</h6>
                        <div style="text-align: center; padding-top: 10px;">
                            <p style="margin-bottom: 0px; font-weight: bold; text-transform: uppercase;">
                                a</p>
                            <p style="margin-top: 0px; font-size: 13px;">a</p>
                        </div>
                    </div>
                </div>
            </div>


        </div> <!-- End page body-->

    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#printBtn').click(function() {
                $('.containerPrint').printThis({
                    importCSS: true, // import parent page css
                    importStyle: true, // import style tags
                    copyTagClasses: true, // copy classes from the html & body tag
                    // footer: $("#printFooter")
                    header: null,
                    footer: null,


                });

            })





            show_loader();
            setTimeout(function() {
                hide_loader();
            }, 1000)

            var idval = $("#id_val").val();
            var token = $("#token_val").val();


            $.ajax({
                url: '/approver_list_print/' + idval,
                method: 'GET',
                dataType: 'json',
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = result[0].html;
                    $("#printFooter").append(html);
                },
                error: function(jqXHR, exception) {
                    alert('error');
                },
            });

            $.ajax({
                url: '/load_print_item/' + idval,
                method: 'GET',
                dataType: 'json',
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = result[0].html;
                    var emptyRow =
                        '<tr><td><br></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                    $("#itemList").append(html);


                    
                    let length = $("#itemList tr").length;
                    const newLength = length - 1;

                    if (newLength < 17) {
                        for (let count = 0; count < 17 - newLength; count++) {
                            $("#itemList").append(emptyRow);
                        }

                    }


                    if (newLength > 17) {
                        $("#itemList > tr:nth-child(18)").css("border-bottom","1px solid black");
                        $("#itemList > tr:nth-child(19)").css("page-break-before","always");
                    }

                },
                error: function(jqXHR, exception) {
                    alert('error');
                },
            });



        });
    </script>
@endsection
