<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Partner;
use App\Models\Invoice;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\Auth;
use App\Models\Employerplus;
use App\Models\Basepathstatus;

class PaymentController extends Controller
{
    public function index(Request $request){
        $partners = Partner::where('status',1)->orderBy('rec_off_name')->get();
        $posts = Payment::with(['partneroffice','admin'])->orderBy('id','desc')->paginate(10);

        return view('admin.payment.index',compact('posts','partners'));
    }

    public function getInvoices(Request $request) {
        $posts = Invoice::where('partneroffice_id','=',$request->id)->where('payment_status','!=','Paid')->get();

        return response()->json([
            'post' => $posts
        ]);

    }

    public function edit(Request $request){
        $post = Payment::find($request->id);

        $payment_invoices = PaymentInvoice::where('payment_id','=',$request->id)->get();

        $ids = [];
        $invoices = [];


        foreach ($payment_invoices as $payment_invoice) {
            $ids [] = $payment_invoice->invoice_id;

            $getInvoice = Invoice::find($payment_invoice->invoice_id);

            $invoices[] = [
                'invoice_id' => $getInvoice->id,
                'invoice_no' => $getInvoice->invoice_no,
                'invoice_amount' => $getInvoice->invoice_amount,
                'invoice_date' => $getInvoice->invoice_date,
                'paid_amount' => $getInvoice->paid_amount,
                'payment_status' => $getInvoice->payment_status,
                'amount_settled' => $payment_invoice->amount_settled,
                'checked' => '1'
            ];

        }

        // All unpaid invoices
        $unpaidInvs = Invoice::whereNotIn('id',$ids)->where('payment_status','!=','Paid')->get();

        if ($unpaidInvs->count() > 0) {
            foreach ($unpaidInvs as $unpaidInv) {
                $invoices[] = [
                    'invoice_id' => $unpaidInv->id,
                    'invoice_no' => $unpaidInv->invoice_no,
                    'invoice_amount' => $unpaidInv->invoice_amount,
                    'invoice_date' => $unpaidInv->invoice_date,
                    'paid_amount' => $unpaidInv->paid_amount,
                    'payment_status' => $unpaidInv->payment_status,
                    'amount_settled' => "0",
                    'checked' => '0'
                ];
            }
        }

        $basepath = '/admin/assets/images/payment/slip/';
        $data = [
            'post' => $post,
            'invoices' => $invoices,
            'basepath' => $basepath,
            'slip_full_path' => $basepath . $post->payment_slip

        ];

        return response()->json($data);
    }

    public function store(Request $request){
        $basepathstatus = Basepathstatus::first();

        // Payment Amount
        $payment_amt = $request->amount;

        // Get Invoices
        $invoices = Invoice::wherein('id',explode(",",$request->invoice_id))->get();

        // Store Payment Details
        $postpayment = new Payment();
        $postpayment->partneroffice_id = $request->partneroffice_id;
        $postpayment->amount = $payment_amt;
        $postpayment->payment_date = $request->payment_date;
        $postpayment->admin_id = Auth::guard('admin')->user()->id;
        $postpayment->payment_mode = $request->payment_mode;
        $postpayment->received_in = $request->received_in;
        $postpayment->transaction_id = $request->transaction_id;
        $postpayment->notes = $request->terms_condtion;

        if ($request->hasFile('payment_slip')) {

            $file = $request->file('payment_slip');
        
            // original name without extension
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = str_replace(' ', '_', $filename);
        
            // extension
            $extension = $file->getClientOriginalExtension();
        
            // unique file name
            $new_file1 = $filename . '_' . time() . '_' . uniqid() . '.' . $extension;
        
            // move file
            if ($basepathstatus->base_path_status == 1) {
                $file->move(base_path('public/admin/assets/images/payment/slip'), $new_file1);
            } else {
                $file->move(base_path('public_html/admin/assets/images/payment/slip'), $new_file1);
            }
        
            $payment_slip = $new_file1;
        
        } else {
            $payment_slip = '';
        }
        

        $postpayment->payment_slip = $payment_slip;

        $postpayment->save();


        $balance_amount = $payment_amt;

        foreach ($invoices as $invoice) {
            // Update Paid Payment and Also Status
            $tobe_paid = $invoice->invoice_amount - $invoice->paid_amount;

            if ($tobe_paid <= $balance_amount) {
                $invoice->paid_amount = $invoice->invoice_amount;
                $invoice->payment_status = "Paid";

                $settled_amount = $tobe_paid;
            }else{
                $invoice->paid_amount = $invoice->paid_amount + $balance_amount;
                $invoice->payment_status = "Partially Paid";

                $settled_amount = $balance_amount;
            }

            $balance_amount -= $tobe_paid;

            $invoice->save();

            // Update and store in PaymentInvoice

            $paymentinvoice = New PaymentInvoice();
            $paymentinvoice->payment_id = $postpayment->id;
            $paymentinvoice->invoice_id = $invoice->id;
            $paymentinvoice->amount_settled = $settled_amount;
            $paymentinvoice->save();

            // Update Status in Employer Plus
            $employerps = Employerplus::wherein('id',explode(",",$invoice->emp_id))->get();
            if ($employerps->count() > 0) {
                foreach ($employerps as $employerp) {
                    if ($invoice->payment_status == 'Paid') {
                        $employerp->payment_status = 'Paid';
                    }else{
                        $employerp->payment_status = 'Unpaid';
                    }

                    $employerp->save();
                }
            }

        }

        return redirect()->back()->with('success','Payment in Successfully added!');


    }

    public function update(Request $request){
        $post = Payment::find($request->payment_id);

        $basepathstatus = Basepathstatus::first();

        // folder path
        $folder = $basepathstatus->base_path_status == 1
            ? base_path('public/admin/assets/images/payment/slip/')
            : base_path('public_html/admin/assets/images/payment/slip/');

        // if new file uploaded → replace old one
        if ($request->hasFile('payment_slip')) {

            // delete old slip if exists
            if (!empty($post->payment_slip) && file_exists($folder . $post->payment_slip)) {
                unlink($folder . $post->payment_slip);
            }

            $file = $request->file('payment_slip');

            // sanitized original file name
            $filename = str_replace(" ", "_", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

            // file extension
            $ext = $file->getClientOriginalExtension();

            // unique file name
            $newname = $filename . '_' . time() . '_' . uniqid() . '.' . $ext;

            // upload file
            $file->move($folder, $newname);

            // save name in DB
            $post->payment_slip = $newname;
        }

        $post->save();

        // Request Data
        $invoice_ids = explode(",", $request->invoice_id);
        $payment_id = $request->payment_id;
        $amount = $request->amount;

        return redirect()->back()->with('success', 'Payment Successfully Updated!');
    }

    public function delete(Request $request)
    {

        $payment = Payment::findOrFail($request->delete_payment_id);

        // Get all related payment invoices
        $paymentInvoices = PaymentInvoice::where('payment_id', $payment->id)->get();

        foreach ($paymentInvoices as $pi) {

            $invoice = Invoice::find($pi->invoice_id);

            if ($invoice) {

                // 🔁 Reverse paid amount
                $invoice->paid_amount -= $pi->amount_settled;

                if ($invoice->paid_amount <= 0) {
                    $invoice->paid_amount = 0;
                    $invoice->payment_status = "Unpaid";
                } elseif ($invoice->paid_amount < $invoice->invoice_amount) {
                    $invoice->payment_status = "Partially Paid";
                } else {
                    $invoice->payment_status = "Paid";
                }

                $invoice->save();

                // 🔁 Update Employerplus status
                $employerps = Employerplus::whereIn('id', explode(",", $invoice->emp_id))->get();

                foreach ($employerps as $employerp) {
                    if ($invoice->payment_status == 'Paid') {
                        $employerp->payment_status = 'Paid';
                    } else {
                        $employerp->payment_status = 'Unpaid';
                    }
                    $employerp->save();
                }
            }

            // ❌ Delete PaymentInvoice record
            $pi->delete();
        }

        // 🗑 Delete payment slip file
        if (!empty($payment->payment_slip)) {

            $basepathstatus = Basepathstatus::first();

            if ($basepathstatus->base_path_status == 1) {
                $filePath = base_path('public/admin/assets/images/payment/slip/' . $payment->payment_slip);
            } else {
                $filePath = base_path('public_html/admin/assets/images/payment/slip/' . $payment->payment_slip);
            }

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // ❌ Delete payment
        $payment->delete();
        
        return redirect()->back()->with('success','Payment deleted successfully!');


    }

}
