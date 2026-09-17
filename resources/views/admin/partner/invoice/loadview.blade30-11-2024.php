<!DOCTYPE html>
<html>
<head>
    <style>
        /* body { font-family: 'DejaVu Sans', sans-serif; } */
        /* table { width: 100%; border-collapse: collapse; } */
        /* table, th, td { border: 1px solid black; } */
        /* th, td { padding: 10px; text-align: left; } */
        /* .logo { text-align: center; margin-bottom: 20px; } */

    </style>
</head>
<body>






    <div class="row">
        <div class="col-md-6">
            <div class="logo">

                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="width: 150px;">
            </div>
        </div>
        <div class="col-md-6">
            <p><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
            <p><strong>Date:</strong> {{ date('d-m-Y',strtotime($invoice->invoice_date)) }}</p>
        </div>
    </div>




    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Services</th>
                <th>Service Charge</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @foreach($empcands as $empcand)
                @php
                    $partner_sc = DB::table('partnerservicecharges')->where('partner_id','=',$empcand->partneroffice_id)->where('profession_id','=',$empcand->proff_id)->where('status',true)->first();
                @endphp
            <tr>
                <td >{{ $i++ }}</td>
                <td >
                    Employer: {{ $empcand->emp->employer_name }} <br>(Visa No: {{ $empcand->emp->visa_no }} | ID No: {{ $empcand->emp->id_no }})<br>Candidate: {{ $empcand->cand->cand_name }} (PP: {{ $empcand->cand->pass_no }})<br>Profession: {{ $empcand->proff->eng_name }}
                </td>
                <td >
                    {{ $partner_sc->service_charge }}
                </td>
            </tr>
            @endforeach





        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align:right;"><strong>Grand Total</strong></td>
                <td>{{ $invoice->invoice_amount }}</td>
            </tr>
        </tfoot>
    </table>
    <p>Thank you for your business!</p>
</body>
</html>
