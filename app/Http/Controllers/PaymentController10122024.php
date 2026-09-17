<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Partner;
use App\Models\Invoice;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\Auth;
use App\Models\Employerplus;

class PaymentController extends Controller
{
    public function index(Request $request){
        $partners = Partner::where('status',1)->orderBy('rec_off_name')->get();
        $posts = Payment::with(['partneroffice','admin'])->paginate(10);

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

        $data = [
            'post' => $post,
            'invoices' => $invoices,
        ];

        return response()->json($data);
    }

    public function store(Request $request){
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
        // Request Data
        $invoice_ids = explode(",",$request->invoice_id);
        $payment_id = $request->payment_id;
        $amount = $request->amount;

        // Get Payment Details
        $payment = Payment::find($payment_id);

        // Get Invoices as per request
        // $invoices = Invoice::wherein('id',$invoice_ids)->get();

        // Get Payment Invoices
        $payment_invoices = PaymentInvoice::where('payment_id',$payment_id)->wherein('invoice_id',$invoice_ids)->get();

        // Update Payment
        $payment->amount = $amount;
        $payment->payment_date = $request->payment_date;
        $payment->payment_mode = $request->payment_mode;
        $payment->received_in = $request->received_in;
        $payment->transaction_id = $request->transaction_id;
        $payment->notes = $request->terms_condtion;
        $payment->save();

        // Update Invoice Amount and Status
        $balance_amount = $amount;
        foreach ($payment_invoices as $payment_invoice) {
            $getInvoice = Invoice::find($payment_invoice->invoice_id);

            $tobe_paid = $getInvoice->invoice_amount - ($getInvoice->paid_amount - $payment_invoice->amount_settled);

            if ($tobe_paid <= $balance_amount) {
                $getInvoice->paid_amount = $getInvoice->invoice_amount;
                $getInvoice->payment_status = "Paid";

                $settled_amount = $tobe_paid;
            }else{
                $getInvoice->paid_amount = $getInvoice->paid_amount + $balance_amount;
                $getInvoice->payment_status = "Partially Paid";

                $settled_amount = $balance_amount;
            }

            $balance_amount -= $tobe_paid;

            $getInvoice->save();

            $payment_invoice->amount_settled = $settled_amount;
            $payment_invoice->save();

            // Update Status in Employer Plus
            $employerps = Employerplus::wherein('id',explode(",",$getInvoice->emp_id))->get();
            if ($employerps->count() > 0) {
                foreach ($employerps as $employerp) {
                    if ($getInvoice->payment_status == 'Paid') {
                        $employerp->payment_status = 'Paid';
                    }else{
                        $employerp->payment_status = 'Unpaid';
                    }

                    $employerp->save();
                }
            }
        }

        // Delete and update invoice amout when
        $delPaymentInvoices = PaymentInvoice::where('payment_id','=',$payment_id)->whereNotIn('invoice_id',$invoice_ids)->get();

        // Update invoice and delete payment invoice
        foreach ($delPaymentInvoices as $delPaymentInvoice) {
            $getInvoice = Invoice::find($delPaymentInvoice->invoice_id);

            $tobe_update = $getInvoice->paid_amount - $delPaymentInvoice->amount_settled;

            $getInvoice->paid_amount = $tobe_update;
            $getInvoice->payment_status = "Partially Paid";
            $getInvoice->save();

            // Update Status in Employer Plus
            $employerps = Employerplus::wherein('id',explode(",",$getInvoice->emp_id))->get();
            if ($employerps->count() > 0) {
                foreach ($employerps as $employerp) {
                    if ($getInvoice->payment_status == 'Paid') {
                        $employerp->payment_status = 'Paid';
                    }else{
                        $employerp->payment_status = 'Unpaid';
                    }
                    $employerp->save();
                }
            }

            $delPaymentInvoice->delete();
        }

        return redirect()->back()->with('success','Payment in Successfully updated!');
    }
}
