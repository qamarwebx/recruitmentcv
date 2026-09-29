<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Controllers\InvoiceController;
use App\Models\Employercandidate;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Partner Portal -> Payment: the logged-in partner's invoices, read-only,
 * straight from the CRM Sales Invoice data (invoices.partneroffice_id =
 * partners.id, same link the CRM uses). View mirrors the CRM invoice page;
 * Download reuses the CRM's own invoice PDF generator
 * (InvoiceController::generatedInvoicePDF). Both check ownership here first -
 * the partner always comes from the partner guard.
 */
class PartnerPaymentController extends Controller
{
    private function partnerId(): int
    {
        return Auth::guard('partner')->id();
    }

    /** This partner's invoices only. */
    private function invoices()
    {
        return Invoice::where('partneroffice_id', $this->partnerId());
    }

    public function index(Request $request)
    {
        // The Invoice date-range scope expects the picker's "MM/DD/YYYY - MM/DD/YYYY".
        $dateRange = preg_match('#^\d{2}/\d{2}/\d{4} - \d{2}/\d{2}/\d{4}$#', (string) $request->date_range)
            ? $request->date_range
            : null;

        $invoices = $this->invoices()
            ->filterSearchText($request->search)
            ->filterByPaymentStatus($request->payment_status)
            ->filterByDateRange($dateRange)
            // An invoice can cover several employers; it keeps their names
            // (comma-separated employer_name) even after the CRM employer
            // records are removed, so filter on those names.
            ->when(trim((string) $request->employer), fn ($query, $employer) => $query
                ->whereRaw('FIND_IN_SET(?, employer_name)', [$employer]))
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('worker.partner.payment.partial', compact('invoices'));
        }

        // Filter options come from this partner's own invoices.
        $employers = $this->invoices()->pluck('employer_name')
            ->flatMap(fn ($names) => explode(',', (string) $names))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $statuses = $this->invoices()->whereNotNull('payment_status')->distinct()->orderBy('payment_status')->pluck('payment_status');

        return view('worker.partner.payment.index', compact('invoices', 'employers', 'statuses'));
    }

    /**
     * View = HTML invoice page with the same content as the CRM's
     * /admin/sales-invoice/show/{id} (InvoiceController::show loads the
     * invoice + its employer-candidate rows the same way), read-only.
     */
    public function show($id)
    {
        // 404 for any invoice that isn't this partner's.
        $invoice = $this->invoices()->with(['partneroffice', 'admin'])->findOrFail($id);
        $empcands = Employercandidate::with(['emp', 'cand', 'proff'])
            ->whereIn('id', explode(',', (string) $invoice->empcand_id))
            ->get();

        return view('worker.partner.payment.show', compact('invoice', 'empcands'));
    }

    /** Download = the CRM's /admin/sales-invoice/generate-pdf/{id} PDF. */
    public function download($id)
    {
        // 404 for any invoice that isn't this partner's.
        $invoice = $this->invoices()->findOrFail($id);

        // DomPDF only reads local files inside its chroot (this project). The
        // CRM invoice header/signature/footer images are under
        // public/admin/assets, which is a symlink to the CRM's shared assets
        // folder, so their real path is outside it and DomPDF silently drops
        // them. Also allow that folder's real location, for this call only.
        config(['dompdf.options.chroot' => array_values(array_unique(array_filter([
            realpath(base_path()),
            realpath(public_path('admin/assets')),
        ])))]);

        try {
            $response = app(InvoiceController::class)->generatedInvoicePDF($invoice->id);
        } catch (\Throwable $e) {
            Log::warning('Partner invoice PDF failed', ['invoice_id' => $invoice->id, 'partner_id' => $this->partnerId(), 'error' => $e->getMessage()]);

            return redirect()->route('worker.partner.payment')
                ->withErrors(['invoice' => __('locale.The invoice could not be opened right now. Please try again later.')]);
        }

        $response->headers->set('Content-Disposition', 'attachment; filename="invoice_' . preg_replace('/[^A-Za-z0-9_-]/', '', $invoice->invoice_no) . '.pdf"');
        // Same URL pattern for every partner - never cache.
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');

        return $response;
    }
}
