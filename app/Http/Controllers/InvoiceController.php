<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Partnerservicecharge;
use App\Models\Todo;
use App\Models\Employercandidate;
use App\Models\Payment;
use App\Models\PaymentInvoice;
use DB;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Basepathstatus;
use App\Models\Invoiceaccountdet;
use App\Models\InvoiceAdminSaveFilter;
use ArPHP\I18N\Arabic;
use NumberToWords\NumberToWords;

class InvoiceController extends Controller
{



    public function index(Request $request){
        $partners = Partner::where('status','1')->orderBy('rec_off_name')->get();

        $bankdetails = Invoiceaccountdet::where('status','1')->orderBy('id','DESC')->get();
        $invoiceCount = Invoice::count();

        $adminId = Auth::guard('admin')->user()->id;
        $saveadminfilter = InvoiceAdminSaveFilter::where('admin_id', $adminId)->first();

        if ($request->ajax()) {
            $posts = Invoice::with(['admin','employer','partneroffice'])
                ->filterSearchText($request->search_text)
                ->filterByPartner($request->by_partner_office)
                ->filterByPaymentStatus($request->by_payment_status)
                ->filterByDateRange($request->invoice_date_range)
                ->orderBy('updated_at','desc')
                ->paginate($request->page_list ?? 10)
                ->withQueryString();

            return view('admin.invoice.loadinvoice',['posts' => $posts]);
        }



        $reserved_invoice = 4649 + $invoiceCount + 1;

        // $generateInvoiceNumber = $invoiceCount + 1;
        $generateInvoiceNumber = 'QR'.$reserved_invoice;
        $invoicedata = [
            'generate_invoice' => $generateInvoiceNumber
        ];
        $posts = Invoice::with(['admin','employer','partneroffice'])
            ->filterByPartner($saveadminfilter->by_partner_office_array ?? [])
            ->filterByPaymentStatus($saveadminfilter->by_payment_status_array ?? [])
            ->filterByDateRange($saveadminfilter->invoice_date_range ?? null)
            ->orderBy('updated_at','desc')
            ->paginate(10);


        // // Get Invoice
        // $post = Invoice::find(8);
        // // Candidate List which is not available different invoice
        // $assigncands = Employercandidate::with(['emp','cand','proff'])->where('partneroffice_id','=',$post->partneroffice_id)->where('status',1)->get();

        // $totalAmount = 0;
        // $invoiceData = [];
        // foreach ($assigncands as $assigncand) {
        //     $invoices = Invoice::where('partneroffice_id','=',$post->partneroffice_id)->where('id','!=',8)->WhereRaw("FIND_IN_SET(?, emp_id)", [$assigncand->emp_id])->WhereRaw("FIND_IN_SET(?, cand_id)", [$assigncand->cand_id])->get();

        //     if ($invoices->count() == 0) {
        //         if ($assigncand->partnersc != '') {
        //             $partner_Sc = $assigncand->partnersc;
        //         } elseif (isset($partnerSc)) {
        //             $partner_Sc = $partnerSc->service_charge;
        //         } else{
        //             $partner_Sc = "0";
        //         }

        //         $totalAmount +=  $partner_Sc;
        //         $invoiceData[] = [
        //             'empcand_id' => $assigncand->id,
        //             'employer_id' => $assigncand->emp->id,
        //             'employer_name' => $assigncand->emp->employer_name,
        //             'employer_arabic_name' => $assigncand->emp->employer_ar_name,
        //             'emp_visa_no' => $assigncand->emp->visa_no,
        //             'emp_id_no' => $assigncand->emp->id_no,
        //             'cand_id' => $assigncand->cand->id,
        //             'cand_name' => $assigncand->cand->cand_name,
        //             'cand_pass_no' => $assigncand->cand->pass_no,
        //             'profession_eng' => $assigncand->proff->eng_name,
        //             'profession_ar' => $assigncand->proff->ar_name,
        //             'profession_id' => $assigncand->proff_id,
        //             'service_charge' => $partner_Sc
        //         ];
        //     }

        // }

        // dd($invoiceData);



        return view('admin.invoice.index',compact('posts','partners','invoicedata','bankdetails','saveadminfilter'));
    }

    public function saveFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;

        $filter = InvoiceAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);

        $filter->by_partner_office = $request->by_partner_office ? implode(',', (array) $request->by_partner_office) : '';
        $filter->by_payment_status = $request->by_payment_status ? implode(',', (array) $request->by_payment_status) : '';
        $filter->invoice_date_range = $request->invoice_date_range ?? '';

        $filter->save();

        return response()->json([
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!'
        ]);
    }

    public function show($id){
        $invoice = Invoice::find($id);
        $invoice_payment = PaymentInvoice::where('invoice_id','=',$id)->get();
        $empcands = Employercandidate::wherein('id',explode(",",$invoice->empcand_id))->get();
        $partners = Partner::where('status','1')->orderBy('rec_off_name')->get();
        $staffs = Admin::where('status', 1)->orderBy('name')->where('status',1)->get();
        return view('admin.invoice.show',compact('invoice','empcands','partners','staffs'));
    }

    public function generatedInvoicePDF($id){
        $invoice = Invoice::find($id);
        $empcands = Employercandidate::wherein('id',explode(",",$invoice->empcand_id))->get();
        $basepathSt = Basepathstatus::first();

        $bankdetail = Invoiceaccountdet::find($invoice->invoiceaccountdet_id);

        // return view('admin.invoice.loadview',compact('invoice','empcands'));
        // $html = view('admin.invoice.loadview',compact('invoice','empcands'))->render();

        // DOMPDF
        // $pdf = Pdf::loadView('admin.invoice.loadview',compact('invoice','empcands','basepathSt')); 12-12-2024

        if ($bankdetail) {
            $acount_holder_name = $bankdetail->name;
            $bank_name = $bankdetail->bank_name;
            $account_number = $bankdetail->account_number;
            $iban_account_number = $bankdetail->iban_account_number;
        } else {
            $acount_holder_name = "INDRESH YADAV SUKHAI YADAV";
            $bank_name = "AL RAJHI BANK";
            $account_number = "611000010006086142904";
            $iban_account_number = "---";
        }



        // Partner Office Data
        $owner_name = $invoice->partneroffice_id != "" && $invoice->partneroffice->owner_name != '' ? $invoice->partneroffice->owner_name :'NA';
        $sponsor_name = $invoice->partneroffice_id != "" && $invoice->partneroffice->rec_office_arname != '' ? $invoice->partneroffice->rec_office_arname :'NA';
        $partner_address = $invoice->partneroffice_id != "" && $invoice->partneroffice->info_eng_address != '' ? $invoice->partneroffice->info_eng_address :'NA';
        if ($invoice->partneroffice_id != "" && $invoice->partneroffice->owner_mobile_no != '') {
            $partner_number = $invoice->partneroffice->owner_mobile_no;
        }elseif ($invoice->partneroffice_id != "" && $invoice->partneroffice->office_no != '') {
            $partner_number = $invoice->partneroffice->office_no;
        }elseif ($invoice->partneroffice_id != "" && $invoice->partneroffice->primary_mob != '') {
            $partner_number = $invoice->partneroffice->primary_mob;
        }elseif ($invoice->partneroffice_id != "" && $invoice->partneroffice->secondary_mob != '') {
            $partner_number = $invoice->partneroffice->secondary_mob;
        }else{
            $partner_number = "NA";
        }


        // $partner_number = $invoice->partneroffice_id != "" && $invoice->partneroffice->office_no != '' ? $invoice->partneroffice->office_no :'NA';
        // $partner_email = $invoice->partneroffice_id != "" && $invoice->partneroffice->primary_email != '' ? $invoice->partneroffice->primary_email :'NA';
        $customer_no = $invoice->partneroffice_id != '' && $invoice->partneroffice->customer_no != '' ? $invoice->partneroffice->customer_no : 'NA';

        if ($basepathSt && $basepathSt->base_path_status == 1) {
            // $header_image = public_path('admin/assets/images/invoice_img/invoice_header_for_pdf_generate.PNG');
            // $footer_imgae = public_path('admin/assets/images/invoice_img/invoice_footer_for_pdf_generate.PNG');

            $header_image = public_path('admin/assets/images/invoice_img/head_11_03_2025.png');
            $footer_imgae = public_path('admin/assets/images/invoice_img/footer_11_01_2025_615.png');
            $sign_image = public_path('admin/assets/images/invoice_img/invoice_signature_for_pdf_generate.PNG');
        } else {

            // $header_image = asset('admin/assets/images/invoice_img/invoice_header_for_pdf_generate.PNG');
            // $footer_imgae = asset('admin/assets/images/invoice_img/invoice_footer_for_pdf_generate.PNG');

            $header_image = asset('admin/assets/images/invoice_img/head_11_03_2025.png');
            $footer_imgae = asset('admin/assets/images/invoice_img/footer_11_01_2025_615.png');
            $sign_image = asset('admin/assets/images/invoice_img/invoice_signature_for_pdf_generate.PNG');
        }



        $inv_date = date('d-M-Y',strtotime($invoice->invoice_date));

        $result = "";
        $i = 1;
        $service_charge = explode(",",$invoice->service_charge);
        foreach ($empcands as $key => $empcand) {
            $result .= '<tr><td class="text-center">'.$i++.'</td>';
            // $result .= '<td class="text-center">'.$empcand->emp->employer_name.'</td>';
            $result .= '<td class="text-center">'.$empcand->emp->employer_ar_name.'</td>';
            $result .= '<td class="text-center">'.$empcand->emp->visa_no.'</td>';
            $result .= '<td class="text-center">'.$empcand->cand->cand_name.'</td>';
            $result .= '<td class="text-center">'.$empcand->cand->pass_no.'</td>';
            // $result .= '<td class="text-center">'.$invoice->payment_status.'</td>';
            $result .= '<td class="text-center">SR'.$service_charge[$key].'</td></tr>';

        }

        $final_reslt = $result;

        $inv_amount = 'SR '.$invoice->invoice_amount;

        $font_path = url('admin/assets/custom_fonts/dejavu-sans/DejaVuSans.ttf');

        $invoice_header = "INVOICE - فاتورة";
        // dd($font_path);
        $invoiceNu = 'invoice_'.$invoice->invoice_no.'.pdf';

        // Invoice Subject
        $invoice_subject = "INVOICE FOR VISA PROCESSING AND TICKET OF EX-BOARD FAMILY DRIVER";

        // Invoice Amount in words
        $invoice_amt_words = new NumberToWords();
        $numberTransformer = $invoice_amt_words->getNumberTransformer('en');
        $numberInWords = $numberTransformer->toWords($invoice->invoice_amount);

        $final_inv_amt_text = ucfirst($numberInWords).' Saudi Riyal Only';

        $html = "
            <!DOCTYPE html>
            <html lang='ar'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                <title>{$invoiceNu}</title>
                <style>
                    @font-face{
                        font-family: 'DejaVu Sans';
                        src: url('/admin/assets/custom_fonts/dejavu-sans/DejaVuSans.ttf') format('truetype');
                    }
                    body{
                        font-family: 'DejaVu Sans', sans-serif;

                    }
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
                        background-color: #e6f0ff;
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
                        // 11-01-2025 margin-top: -50px;
                        // 11-01-2025 margin-left: -45px;
                        // margin-left: -70px;
                        margin-left: -40px;
                        margin-top: -40px;
                    }
                    .invoice-header img{
                        // 11-01-2025 width: 795px;
                        width: 780px;

                    }

                    .invoice-footer{
                        position: fixed;
                        bottom: -40px;
                        left: -40px;
                    }

                    .invoice-footer img{
                        width: 780px;
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
                    .mt-450{
                        margin-top: 20px;
                    }

                    .text-grey-dark{
                        color: #1B1212 !important;
                    }
                </style>
            </head>
            <body>
                <div class='invoice-header'>
                    <img src='{$header_image}' alt=''>
                </div>
                <div class='container'>
                    <h2 class='text-center text-grey-dark' style='font-size:16px;margin-top:-20px;'>{$invoice_header}</h2>
                    <h3 class='bg-light-purple text-grey-dark'>Bill To</h3>
                    <table class='row'>
                        <tr>
                            <td class='w-50'>
                                <div class='mt-3'>
                                    <p class='mb-1' style='text-align:right;'>{$owner_name}</p>
                                    <p class='mb-1' style='text-align:right;'>{$sponsor_name}</p>
                                    <p class='mb-1'>{$partner_address}</p>
                                    <p class='mb-1'>Mobile Number: {$partner_number}</p>
                                </div>
                            </td>
                            <td class='w-50'>
                                <div class='float-end'>
                                    <table class='table-border bg-light-purple table-td-text-size-md table-collapse'>
                                        <tr>
                                            <td>Date</td>
                                            <td>{$inv_date}</td>
                                        </tr>
                                        <tr>
                                            <td>Invoice</td>
                                            <td>{$invoice->invoice_no}</td>
                                        </tr>
                                        <tr>
                                            <td>Customer</td>
                                            <td>{$customer_no}</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class='row' style='margin-top:15px;'>
                        <tr>
                            <td class='text-center'>
                                <h4 class='bg-light-purple h4-m p-1005 text-grey-dark' style='font-size:15px;'>{$invoice_subject}</h4>
                            </td>
                        </tr>
                    </table>

                    <table class='row table-border table-collapse table-td-text-size-md'>
                        <thead>
                            <tr>
                                <th class='text-grey-dark'>No</th>
                                <th class='text-grey-dark'>Sponsor</th>
                                <th class='text-grey-dark'>Visa No</th>
                                <th class='text-grey-dark'>Candidate</th>
                                <th class='text-grey-dark'>Passport No</th>
                                <th class='text-grey-dark'>Amount IN SR</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$final_reslt}
                            <tr>
                                <td></td>
                                <td colspan='4'>Total Payment</td>
                                <td class='text-center'>{$inv_amount}</td>
                            </tr>
                            <tr>
                                <td colspan='2' class='text-center'>In words:</td>
                                <td colspan='4' class='text-center'>{$final_inv_amt_text}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class='row'>
                        <p style='font-size:14px;'><strong class='text-grey-dark'>Notes:</strong> Kindly transfer the Amount to the below given account number.</p>

                    </table>
                </div>
                <div class='account-signature-area'>
                    <table class='row'>
                        <tr>
                            <td class='w-50'>
                                <table style='margin-top: 50px' class='table-border table-td-text-size table-collapse'>
                                    <tr>
                                        <td colspan='2'>ACCOUNT DETAILS</td>
                                    </tr>
                                    <tr>
                                        <td>NAME.</td>
                                        <td>{$acount_holder_name}</td>
                                    </tr>
                                    <tr>
                                        <td>BANK NAME</td>
                                        <td>{$bank_name}</td>
                                    </tr>
                                    <tr>
                                        <td>ACCOUNT NO. </td>
                                        <td>{$account_number}</td>
                                    </tr>
                                    <tr>
                                        <td>IBAN ACCOUNT NO. </td>
                                        <td>{$iban_account_number}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class='w-50'>
                                <div class='float-end mt-3 '>
                                    <h4 class='h4-m text-grey-dark'>THANKS YOU FOR YOUR BUSINESS!</h4>
                                    <div class='img-sign'>
                                        <img src='{$sign_image}' alt=''>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='invoice-footer'>
                    <img src='{$footer_imgae}' alt=''>
                </div>
            </body>
            </html>
        ";

        // dd($header_image);
        $arabic = new Arabic();
        $p = $arabic->arIdentify($html);
        for ($i = count($p)-1; $i >= 0; $i-=2) {
            $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i-1], $p[$i] - $p[$i-1]));
            $html = substr_replace($html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
        }

        $pdf = PDF::loadHTML($html);

        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isFontSubsettingEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');



        return $pdf->stream($invoiceNu);
        // return $pdf->download('invoice_'.$id.'.pdf');
    }

    // public function generatedInvoicePDF($id) {
        //     // Get Invoice Data
        //     $invoice = Invoice::find($id);

        //     // Get Employer and Candidate Date
        //     $empcands = Employercandidate::with(['emp','cand'])->wherein('id',explode(",",$invoice->empcand_id))->get();

        //     $html = view('admin.partner.invoice.loadview',compact('invoice','empcands'))->render();


        //     $pdf = new TCPDF();

        //     // Set document information
        //     $pdf->SetCreator('Qamr International');
        //     $pdf->SetAuthor('Qamr International');
        //     $pdf->SetTitle('Invoice No '.$invoice->invoice_no);
        //     $pdf->SetSubject('Invoice Generation');

        //     // Set default header and footer
        //     $pdf->setPrintHeader(false);
        //     $pdf->setPrintFooter(false);

        //     // Set margins
        //     $pdf->SetMargins(15, 10, 15);

        //     // Add a page
        //     $pdf->AddPage();

        //     // Add space below the logo
        //     // $pdf->Ln(35);

        //     // Add the HTML content
        //     $pdf->writeHTML($html, true, false, true, false, '');

        //     // Output the PDF (D for download, I for inline view)
        //     $pdf->Output('invoice_'.$id.'.pdf', 'I');



    // }

    public function getCandidate(Request $request) {
        $id = $request->id;


        $assincands = Employercandidate::with(['emp','cand','proff'])->where('partneroffice_id','=',$id)->where('status',1)->get();


        $partner = Partner::find($id);

        $invoiceData = [];
        $totalAmount = 0;

        foreach ($assincands as $assincand) {
            // Get Partner Service Charge as per Profession
            $partnerSc = Partnerservicecharge::where('profession_id','=',$assincand->proff_id)->where('partner_id','=',$id)->where('status',1)->first();

            // Get Invoice

            $invoices = Invoice::where('partneroffice_id','=',$id)->WhereRaw("FIND_IN_SET(?, emp_id)", [$assincand->emp_id])->WhereRaw("FIND_IN_SET(?, cand_id)", [$assincand->cand_id])->get();

            if ($invoices->count() == 0) {
                if ($assincand->partnersc != '') {
                    $partner_Sc = $assincand->partnersc;
                } elseif (isset($partnerSc)) {
                    $partner_Sc = $partnerSc->service_charge;
                } else{
                    $partner_Sc = "0";
                }

                $totalAmount +=  $partner_Sc;

                $invoiceData[] = [
                    'empcand_id' => $assincand->id,
                    'employer_id' => $assincand->emp->id,
                    'employer_name' => $assincand->emp->employer_name,
                    'employer_arabic_name' => $assincand->emp->employer_ar_name,
                    'emp_visa_no' => $assincand->emp->visa_no,
                    'emp_id_no' => $assincand->emp->id_no,
                    'cand_id' => $assincand->cand->id,
                    'cand_name' => $assincand->cand->cand_name,
                    'cand_pass_no' => $assincand->cand->pass_no,
                    'profession_eng' => $assincand->proff->eng_name,
                    'profession_ar' => $assincand->proff->ar_name,
                    'profession_id' => $assincand->proff_id,
                    'service_charge' => $partner_Sc

                ];
            }


        }

        return response()->json([
            'partnes' => $partner,
            'invoice_data' => $invoiceData,
            'total_amount' => $totalAmount
        ]);

    }

    public function store(Request $request){

        // $invoice_det  = Invoice::orderBy('invoice_no')->latest();

        // dd($invoice_det);

        // Get Employe Cand

        $empcands = DB::table('employercandidates as empcand')
            ->leftJoin('candidates as cand','cand.id','=','empcand.cand_id')
            ->leftJoin('employerpluses as emp','emp.id','=','empcand.emp_id')
            ->select("empcand.*",'cand.cand_name','cand.pass_no','emp.employer_name','emp.employer_ar_name','emp.visa_no','emp.id_no')
            ->whereIn('empcand.id',explode(",",$request->empcand_id))
            ->get();



        $employer_name = [];
        $employer_ar_name = [];
        $visa_no = [];
        $id_no = [];
        $cand_name = [];
        $pass_no = [];
        $profession_id = [];
        $cand_id = [];
        $emp_id = [];

        foreach ($empcands as $empcand) {
            $employer_name [] = $empcand->employer_name;
            $employer_ar_name [] = $empcand->employer_ar_name;
            $visa_no [] = $empcand->visa_no;
            $id_no [] = $empcand->id_no;
            $cand_name [] = $empcand->cand_name;
            $pass_no [] = $empcand->pass_no;
            $profession_id [] = $empcand->proff_id;
            $cand_id [] = $empcand->cand_id;
            $emp_id [] = $empcand->emp_id;

        }


        $post = new Invoice();
        $post->invoice_no = $request->invoice_no;
        $post->partneroffice_id = $request->partneroffice_id;
        $post->invoice_amount = $request->invoice_amount;
        $post->invoice_date = $request->invoice_date;
        $post->empcand_id = $request->empcand_id;
        $post->service_charge = $request->service_charge;
        $post->terms_condtion = $request->terms_condtion;
        $post->payment_status = "Unpaid";
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->cand_id = implode(",",$cand_id);
        $post->emp_id = implode(",",$emp_id);
        $post->candidate_name = implode(",", $cand_name);
        $post->candidate_pass_no = implode(",",$pass_no);
        $post->employer_name = implode(",",$employer_name);
        $post->employer_ar_name = implode(",",$employer_ar_name);
        $post->employer_visa_no = implode(",",$visa_no);
        $post->employer_id_no = implode(",", $id_no);
        $post->profession_id = implode(",", $profession_id);
        $post->invoiceaccountdet_id = $request->invoiceaccountdet_id;

        $post->save();

        return redirect()->back()->with('success','Partner invoice created!');

    }

    public function edit(Request $request){
        $post = Invoice::find($request->id);

        // Get Assign Candidate List which is not available on previous invoice
        $assincands = Employercandidate::with(['emp','cand','proff'])->where('partneroffice_id','=',$post->partneroffice_id)->where('status',1)->get();

        $invoiceData = [];

        $totalAmount = 0;

        foreach ($assincands as $assincand) {
            // Get Partner Service Charge as per Profession
            $partnerSc = Partnerservicecharge::where('profession_id','=',$assincand->proff_id)->where('partner_id','=',$post->partneroffice_id)->where('status',1)->first();

            // Get Invoice

            $invoices = Invoice::where('id','!=',$request->id)->where('partneroffice_id','=',$post->partneroffice_id)->WhereRaw("FIND_IN_SET(?, emp_id)", [$assincand->emp_id])->WhereRaw("FIND_IN_SET(?, cand_id)", [$assincand->cand_id])->get();

            if ($invoices->count() == 0) {
                if ($assincand->partnersc != '') {
                    $partner_Sc = $assincand->partnersc;
                } elseif (isset($partnerSc)) {
                    $partner_Sc = $partnerSc->service_charge;
                } else{
                    $partner_Sc = "0";
                }

                $totalAmount +=  $partner_Sc;

                $invoiceData[] = [
                    'empcand_id' => $assincand->id,
                    'employer_id' => $assincand->emp->id,
                    'employer_name' => $assincand->emp->employer_name,
                    'employer_arabic_name' => $assincand->emp->employer_ar_name,
                    'emp_visa_no' => $assincand->emp->visa_no,
                    'emp_id_no' => $assincand->emp->id_no,
                    'cand_id' => $assincand->cand->id,
                    'cand_name' => $assincand->cand->cand_name,
                    'cand_pass_no' => $assincand->cand->pass_no,
                    'profession_eng' => $assincand->proff->eng_name,
                    'profession_ar' => $assincand->proff->ar_name,
                    'profession_id' => $assincand->proff_id,
                    'service_charge' => $partner_Sc

                ];
            }

        }
        
        // dd($post);

        return response()->json([
            'post' => $post,
            'invoice_data' => $invoiceData
        ]);
    }

    public function update(Request $request) {
        // Get Employe Cand

        $empcands = DB::table('employercandidates as empcand')
            ->leftJoin('candidates as cand','cand.id','=','empcand.cand_id')
            ->leftJoin('employerpluses as emp','emp.id','=','empcand.emp_id')
            ->select("empcand.*",'cand.cand_name','cand.pass_no','emp.employer_name','emp.employer_ar_name','emp.visa_no','emp.id_no')
            ->whereIn('empcand.id',explode(",",$request->empcand_id))
            ->get();

        $employer_name = [];
        $employer_ar_name = [];
        $visa_no = [];
        $id_no = [];
        $cand_name = [];
        $pass_no = [];
        $profession_id = [];
        $cand_id = [];
        $emp_id = [];

        foreach ($empcands as $empcand) {
            $employer_name [] = $empcand->employer_name;
            $employer_ar_name [] = $empcand->employer_ar_name;
            $visa_no [] = $empcand->visa_no;
            $id_no [] = $empcand->id_no;
            $cand_name [] = $empcand->cand_name;
            $pass_no [] = $empcand->pass_no;
            $profession_id [] = $empcand->proff_id;
            $cand_id [] = $empcand->cand_id;
            $emp_id [] = $empcand->emp_id;

        }

        $post = Invoice::find($request->invoice_id);
        $post->invoice_no = $request->invoice_no;
        $post->invoice_amount = $request->invoice_amount;
        $post->invoice_date = $request->invoice_date;
        $post->empcand_id = $request->empcand_id;
        $post->service_charge = $request->service_charge;
        $post->terms_condtion = $request->terms_condtion;
        $post->cand_id = implode(",",$cand_id);
        $post->emp_id = implode(",",$emp_id);
        $post->candidate_name = implode(",", $cand_name);
        $post->candidate_pass_no = implode(",",$pass_no);
        $post->employer_name = implode(",",$employer_name);
        $post->employer_ar_name = implode(",",$employer_ar_name);
        $post->employer_visa_no = implode(",",$visa_no);
        $post->employer_id_no = implode(",", $id_no);
        $post->profession_id = implode(",", $profession_id);
        $post->invoiceaccountdet_id = $request->invoiceaccountdet_id;


        $post->save();

        return redirect()->back()->with('success','Partner invoice updated!');

    }

    public function destroy(Request $request)
    {
        $invoice = Invoice::findOrFail($request->id);

        PaymentInvoice::where('invoice_id', $invoice->id)->delete();

        $invoice->delete();

        return redirect()->back()->with('success', 'Invoice deleted successfully.');
    }

    // Check Invoice Number is exist
    public function checkinvoicenumber(Request $request) {
        if ($request->invoice_id) {
            $post = Invoice::where('invoice_no','=',$request->invoice_no)->where('id','!=',$request->invoice_id)->count();
        } else {
            $post = Invoice::where('invoice_no','=',$request->invoice_no)->count();
        }




        if ($post == 0) {
            echo "true";
        } else {
            echo "false";
        }

    }

    public function accountdetailslist(){
        return view('admin.invoice.accountdetails.list');
    }

    public function accountdetailslistJson(Request $request){
        $post = Invoiceaccountdet::orderBy('id','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function accountdetailsStore(Request $request){
        $post = new Invoiceaccountdet();
        $post->name = $request->name;
        $post->bank_name = $request->bank_name;
        $post->account_number = $request->account_number;
        $post->iban_account_number = $request->iban_account_number;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Account Bank Details added!');

    }

    public function  accountdetailsEdit(Request $request) {
        $post = Invoiceaccountdet::find($request->id);

        return response()->json($post);
    }

    public function  accountdetailsUpdate(Request $request) {
        $post = Invoiceaccountdet::find($request->edit_id);
        $post->name = $request->name;
        $post->bank_name = $request->bank_name;
        $post->account_number = $request->account_number;
        $post->iban_account_number = $request->iban_account_number;
        $post->save();

        return redirect()->back()->with('success','Account Bank Details updated!');
    }

    public function delete(Request $request)
    {
        Invoiceaccountdet::findOrFail($request->id)->delete();

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    public function accountdetailcheckname(Request $request){

    }

    public function accountdetailcheckbankname(Request $request){

    }


}
