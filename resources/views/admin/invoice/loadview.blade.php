<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <style>
        .container{
            width: 100%;
        }
        .text-center{
            text-align: center;
        }
        .row{
            width: 100%;
        }
        .w-50{
            width: 50%;
        }
        .w-30{
            width: 30%;
        }
        .w-70{
            width: 70%;
        }
        .bg-light-purple{
            background-color: #CBC3E3;
        }
        .table-border td ,.table-border th{
            border: 1px solid #b8b7b9;
            padding: 3px 5px 10px 7px;
        }
        .table-collapse{
            border-collapse: collapse;
        }
        .float-end{
            float: right;
        }
        .mb-1{
            margin-bottom: -12px;
        }
        .mb-0{
            margin-bottom: 0;
        }
        .mt-3{
            margin-top: -30px;
        }
        .mt-2{
            margin-top: -20px;
        }
        .h4-m{
            font-size: 15px !important;
        }
        .invoice-header{
            /* width: 100%; */
            /* position: fixed; */
            margin-top: -50px;
            margin-left: -45px;
        }
        .invoice-header img{
            width: 795px;

        }

        .invoice-footer{
            position: fixed;
            bottom: -40px;
            left: -45px;
        }
        .img-sign img{
            width: 70%;
        }
        .table-width{
            width: 100%;
        }
        .table-td-text-size td{
            font-size: 12px;
        }
        .table-td-text-size-md td{
            font-size: 13px;
        }
        .table-td-text-size-md th{
            font-size: 14px;
        }
        .pt-3{
            padding-top: 30px;
        }
        .p-1005{
            padding: 5px 10px 5px 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        @if (isset($basepathSt) && $basepathSt->base_path_status == 1)
            <img src="{{ base_path('public/admin/assets/images/invoice_img/invoice_header_for_pdf_generate.png') }}" alt="">
        @else
            <img src="{{ asset('admin/assets/images/invoice_img/invoice_header_for_pdf_generate.png') }}" alt="">
        @endif
    </div>
    <div class="container">
        <h2 class="text-center">INVOICE</h2>
        <h3 class="bg-light-purple">Bill To</h3>
        <table class="row">
            <tr>
                <td class="w-50">
                    <div class="mt-3">
                        <p class="mb-1">{{ $invoice->partneroffice->owner_name }}</p>
                        <p class="mb-1">{{ $invoice->partneroffice->rec_off_name }}</p>
                        <p class="mb-1">{{ $invoice->partneroffice->info_eng_address }}</p>
                        <p class="mb-1">{{ $invoice->partneroffice->office_no }}</p>
                        <p class="mb-0">{{ $invoice->partneroffice->primary_email }}</p>
                    </div>
                </td>
                <td class="w-50">
                    <div class="float-end">
                        <table class="table-border bg-light-purple table-collapse">
                            <tr>
                                <td>Date</td>
                                <td>{{ date('d-M-Y',strtotime($invoice->invoice_date)) }}</td>
                            </tr>
                            <tr>
                                <td>Invoice</td>
                                <td>{{ $invoice->invoice_no }}</td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <table class="row">
            <tr>
                <td class="text-center">
                    <h4 class="bg-light-purple h4-m p-1005">SUBJECT: INVOICE FOR VISA PROCESSING AND TICKET OF EX-BOARD FAMILY DRIVER</h4>
                </td>
            </tr>
        </table>
        <table class="row table-border table-collapse table-td-text-size-md">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Sponsor Name</th>
                    <th>Visa No</th>
                    <th>Name</th>
                    <th>Passport No</th>
                    <th>Payment Status</th>
                    <th>Amount IN SR</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;

                    $service_charge = explode(",",$invoice->service_charge);

                @endphp
                @foreach ($empcands as $key => $empcand)
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td class="text-center">{{ $empcand->emp->employer_name }}</td>
                        <td class="text-center">{{ $empcand->emp->visa_no }}</td>
                        <td class="text-center">{{ $empcand->cand->cand_name }}</td>
                        <td class="text-center">{{ $empcand->cand->pass_no }}</td>
                        <td class="text-center">{{ $invoice->payment_status }}</td>
                        <td class="text-center">{{ 'SR'.$service_charge[$key] }}</td>
                    </tr>
                @endforeach
                    <tr>
                        <td></td>
                        <td colspan="5">Total Payment</td>
                        <td class="text-center">{{ 'SR '.$invoice->invoice_amount }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">In words:</td>
                        <td colspan="5" class="text-center">Lump Sum</td>
                    </tr>
            </tbody>
        </table>

        <table class="row">
            <p><strong>Notes:</strong></p>
            <p class="mt-2">Kindly transfer the Amount to the below given account number.</p>
        </table>

    </div>

    <div class="account-signature-area">
        <table class="row">
            <tr>
                <td class="w-50">
                    <table style="margin-top: 50px" class="table-border table-td-text-size table-collapse">
                        <tr>
                            <td colspan="2">ACCOUNT DETAILS</td>
                        </tr>
                        <tr>
                            <td>NAME.</td>
                            <td>INDRESH YADAV SUKHAI YADAV</td>
                        </tr>
                        <tr>
                            <td>BANK NAME</td>
                            <td>AL RAJHI BANK</td>
                        </tr>
                        <tr>
                            <td>ACCOUNT NO. </td>
                            <td>611000010006086142904</td>
                        </tr>
                    </table>
                </td>
                <td class="w-50">
                    <div class="float-end mt-3">
                        <h4 class="h4-m">THANKS YOU FOR YOUR BUSINESS!</h4>
                        <div class="img-sign">
                            @if (isset($basepathSt) && $basepathSt->base_path_status == 1)
                                <img src="{{ base_path('public/admin/assets/images/invoice_img/invoice_signature_for_pdf_generate.png') }}" alt="">
                            @else
                                <img src="{{ asset('admin/assets/images/invoice_img/invoice_signature_for_pdf_generate.png') }}" alt="">
                            @endif

                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-footer">
        @if (isset($basepathSt) && $basepathSt->base_path_status == 1)
            <img src="{{ base_path('public/admin/assets/images/invoice_img/invoice_footer_for_pdf_generate.png') }}" alt="">
        @else
            <img src="{{ asset('admin/assets/images/invoice_img/invoice_footer_for_pdf_generate.png') }}" alt="">
        @endif


    </div>
</body>
</html>
