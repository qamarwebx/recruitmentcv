<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\FundAdvanceTransaction;
use App\Models\FundAdvanceTransactionFilter;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FundAdvanceController extends Controller
{
    private function currentPermission()
    {
        return Adminpermission::where('staff_id', Auth::guard('admin')->user()->id)->first();
    }

    private function hasFullAccess($permission): bool
    {
        return Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1);
    }

    public function dashboard(Request $request)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_ledger_view ?? false) == 1;

        [$start, $end] = $this->resolveDashboardRange($request);

        $base = FundAdvanceTransaction::query()->where('status', '!=', 'Cancelled');

        if (!$canViewAll) {
            $base->where('created_by', $userId);
        }

        $base->when($start && $end, fn ($q) => $q->whereBetween('transaction_date', [$start, $end]))
            ->when($request->party_type, fn ($q) => $q->where('party_type', $request->party_type))
            ->when($request->party_id, fn ($q) => $q->where('party_id', $request->party_id))
            ->when($request->transaction_type, fn ($q) => $q->where('transaction_type', $request->transaction_type));

        $settledSub = DB::table('fund_advance_settlements')
            ->select('transaction_id', DB::raw('SUM(amount) as settled_amount'))
            ->where('status', 'Active')
            ->groupBy('transaction_id');

        $rows = (clone $base)
            ->leftJoinSub($settledSub, 'settled', 'settled.transaction_id', '=', 'fund_advance_transactions.id')
            ->selectRaw(
                "transaction_type, transaction_nature,
                SUM(amount) as total_amount,
                SUM(amount - COALESCE(settled.settled_amount, 0)) as total_outstanding"
            )
            ->groupBy('transaction_type', 'transaction_nature')
            ->get();

        $sum = fn ($type, $nature, $col = 'total_amount') => (float) $rows
            ->where('transaction_type', $type)
            ->where('transaction_nature', $nature)
            ->sum($col);

        $pendingSettlements = (clone $base)
            ->leftJoinSub($settledSub, 'settled', 'settled.transaction_id', '=', 'fund_advance_transactions.id')
            ->whereRaw('amount - COALESCE(settled.settled_amount, 0) > 0')
            ->count();

        $stats = [
            'funds_given' => $sum('Fund', 'Given'),
            'funds_received' => $sum('Fund', 'Received'),
            'advances_given' => $sum('Advance', 'Given'),
            'advances_received' => $sum('Advance', 'Received'),
            'advance_outstanding' => $sum('Advance', 'Given', 'total_outstanding'),
            'loans_given' => $sum('Loan', 'Given'),
            'loan_recovery' => $sum('Loan', 'Given') - $sum('Loan', 'Given', 'total_outstanding'),
            'loan_outstanding' => $sum('Loan', 'Given', 'total_outstanding'),
            'receivables' => $sum('Receivable', 'Given', 'total_outstanding'),
            'payables' => $sum('Payable', 'Given', 'total_outstanding'),
            'pending_settlements' => $pendingSettlements,
        ];

        $employees = Admin::orderBy('name')->get(['id', 'name']);

        return view('admin.fund_advance.dashboard', compact('stats', 'employees', 'permission'));
    }

    private function resolveDashboardRange(Request $request): array
    {
        $range = $request->range ?? 'month';

        return match ($range) {
            'today' => [now()->startOfDay()->toDateString(), now()->endOfDay()->toDateString()],
            'week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            'custom' => $request->filled('start_date') && $request->filled('end_date')
                ? [$request->start_date, $request->end_date]
                : [null, null],
            'all' => [null, null],
            default => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }

    public function index(Request $request)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_ledger_view ?? false) == 1;

        $savedFilter = FundAdvanceTransactionFilter::where('admin_id', $userId)->first();

        // On the initial (non-AJAX) page load there's no query string yet, so the
        // admin's saved filter (if any) is what seeds the listing - same convention
        // as Expense's/Leads' saved-filter panels. Live AJAX reloads always carry the
        // current in-modal selections in the request itself and take precedence.
        $filters = $request->ajax() ? [
            'transaction_type' => $request->transaction_type,
            'transaction_nature' => $request->transaction_nature,
            'party_type' => $request->party_type,
            'status' => $request->status,
            'payment_mode' => $request->payment_mode,
            'created_by' => $request->created_by,
            'date_range' => $request->date_range,
            'amount_min' => $request->amount_min,
            'amount_max' => $request->amount_max,
        ] : [
            'transaction_type' => optional($savedFilter)->transaction_type,
            'transaction_nature' => optional($savedFilter)->transaction_nature,
            'party_type' => optional($savedFilter)->party_type,
            'status' => optional($savedFilter)->status,
            'payment_mode' => optional($savedFilter)->payment_mode,
            'created_by' => optional($savedFilter)->created_by,
            'date_range' => optional($savedFilter)->date_range,
            'amount_min' => optional($savedFilter)->amount_min,
            'amount_max' => optional($savedFilter)->amount_max,
        ];

        $query = FundAdvanceTransaction::with(['creator'])
            ->withSum(['activeSettlements as settled_amount'], 'amount')
            ->FilterType($filters['transaction_type'])
            ->FilterNature($filters['transaction_nature'])
            ->FilterPartyType($filters['party_type'])
            ->FilterPaymentMode($filters['payment_mode'])
            ->FilterCreateBy($filters['created_by'])
            ->FilterDateRange('transaction_date', $filters['date_range'])
            ->FilterAmountRange($filters['amount_min'], $filters['amount_max'])
            ->FilterSearchText($request->search_text);

        if (!empty($filters['status'])) {
            $this->applyStatusFilter($query, $filters['status']);
        }

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        $posts = $query->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.fund_advance.transactions.indexload', compact('posts', 'permission'));
        }

        $employees = Admin::orderBy('name')->get(['id', 'name']);

        return view('admin.fund_advance.transactions.index', compact('posts', 'permission', 'employees', 'savedFilter'));
    }

    public function saveFilter(Request $request)
    {
        $filter = FundAdvanceTransactionFilter::firstOrNew(['admin_id' => Auth::guard('admin')->user()->id]);
        $filter->transaction_type = $request->transaction_type ?: null;
        $filter->transaction_nature = $request->transaction_nature ?: null;
        $filter->party_type = $request->party_type ?: null;
        $filter->status = $request->status ?: null;
        $filter->payment_mode = $request->payment_mode ?: null;
        $filter->created_by = $request->created_by ?: null;
        $filter->date_range = $request->date_range ?: null;
        $filter->amount_min = $request->amount_min ?: null;
        $filter->amount_max = $request->amount_max ?: null;
        $wasNew = !$filter->exists;
        $filter->save();

        return response()->json([
            'message' => $wasNew ? 'Filter saved successfully!' : 'Filter updated successfully!',
        ]);
    }

    private function applyStatusFilter($query, string $status)
    {
        if ($status === 'Cancelled') {
            $query->where('status', 'Cancelled');
            return;
        }

        $query->where('status', '!=', 'Cancelled');

        match ($status) {
            'Settled' => $query->whereRaw(FundAdvanceTransaction::OUTSTANDING_SQL . ' <= 0'),
            'Partially Settled' => $query->whereRaw(FundAdvanceTransaction::SETTLED_SQL . ' > 0')->whereRaw(FundAdvanceTransaction::OUTSTANDING_SQL . ' > 0'),
            'Pending' => $query->whereRaw(FundAdvanceTransaction::SETTLED_SQL . ' = 0'),
            default => null,
        };
    }

    private function validationRules(): array
    {
        return [
            'transaction_date' => 'required|date',
            'transaction_type' => ['required', Rule::in(array_keys(FundAdvanceTransaction::TYPE_NATURE_MAP))],
            'transaction_nature' => 'required|string',
            'party_type' => ['required', Rule::in(FundAdvanceTransaction::PARTY_TYPES)],
            'party_id' => 'nullable|integer',
            'party_name' => 'required_if:party_type,other|nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:100',
            'reference_no' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function validateTypeNature(Validator $validator, string $type, string $nature): void
    {
        if (!FundAdvanceTransaction::isValidTypeNature($type, $nature)) {
            $validator->errors()->add('transaction_nature', 'This nature is not valid for the selected transaction type.');
        }
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), $this->validationRules());
        $validator->after(fn ($v) => $this->validateTypeNature($v, $request->transaction_type, $request->transaction_nature));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $file->storeAs('fund_advance/transactions', $fileName, 'public');
            $attachment = $fileName;
        }

        $post = new FundAdvanceTransaction();
        $post->transaction_no = FundAdvanceTransaction::generateTransactionNo($request->transaction_type);
        $post->transaction_date = $request->transaction_date;
        $post->transaction_type = $request->transaction_type;
        $post->transaction_nature = $request->transaction_nature;
        $post->party_type = $request->party_type;
        $post->party_id = $request->party_type === 'other' ? null : $request->party_id;
        $post->party_name = $request->party_name;
        $post->amount = $request->amount;
        $post->payment_mode = $request->payment_mode;
        $post->reference_no = $request->reference_no;
        $post->description = $request->description;
        $post->attachment = $attachment;
        $post->status = 'Active';
        $post->created_by = Auth::guard('admin')->user()->id;
        $post->save();

        Helper::fundAdvanceActivityLog($post->id, [
            'action' => 'Transaction Created',
            'transaction_no' => $post->transaction_no,
            'amount' => (float) $post->amount,
        ], null, Auth::guard('admin')->user()->id, 'transaction');

        return redirect()->back()->with('success', 'Transaction ' . $post->transaction_no . ' created successfully!');
    }

    public function edit(Request $request)
    {
        $post = FundAdvanceTransaction::findOrFail($request->id);

        return response()->json(['transaction' => $post]);
    }

    public function update(Request $request)
    {
        $post = FundAdvanceTransaction::findOrFail($request->id);

        $validator = validator($request->all(), $this->validationRules());
        $validator->after(fn ($v) => $this->validateTypeNature($v, $request->transaction_type, $request->transaction_nature));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $settledAmount = (float) $post->activeSettlements()->sum('amount');
        if ((float) $request->amount < $settledAmount) {
            return response()->json([
                'errors' => ['amount' => ['Amount cannot be less than the already settled amount (' . $settledAmount . ').']],
            ], 422);
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $file->storeAs('fund_advance/transactions', $fileName, 'public');
            $post->attachment = $fileName;
        }

        $post->transaction_date = $request->transaction_date;
        $post->transaction_type = $request->transaction_type;
        $post->transaction_nature = $request->transaction_nature;
        $post->party_type = $request->party_type;
        $post->party_id = $request->party_type === 'other' ? null : $request->party_id;
        $post->party_name = $request->party_name;
        $post->amount = $request->amount;
        $post->payment_mode = $request->payment_mode;
        $post->reference_no = $request->reference_no;
        $post->description = $request->description;
        $post->updated_by = Auth::guard('admin')->user()->id;
        $post->save();

        Helper::fundAdvanceActivityLog($post->id, [
            'action' => 'Transaction Updated',
            'transaction_no' => $post->transaction_no,
            'amount' => (float) $post->amount,
        ], null, Auth::guard('admin')->user()->id, 'transaction');

        return redirect()->back()->with('success', 'Transaction updated successfully!');
    }

    public function show($id)
    {
        $transaction = FundAdvanceTransaction::with(['creator', 'updater', 'settlements' => function ($q) {
            $q->orderBy('settlement_date', 'desc');
        }])->findOrFail($id);

        return view('admin.fund_advance.transactions.view', compact('transaction'));
    }

    public function cancel(Request $request)
    {
        $post = FundAdvanceTransaction::findOrFail($request->id);

        if ((float) $post->activeSettlements()->sum('amount') > 0) {
            return response()->json(['res' => 'A transaction with active settlements cannot be cancelled. Reverse the settlements first.'], 422);
        }

        $request->validate(['cancel_reason' => 'required|string|max:500']);

        $post->status = 'Cancelled';
        $post->cancel_reason = $request->cancel_reason;
        $post->updated_by = Auth::guard('admin')->user()->id;
        $post->save();

        Helper::fundAdvanceActivityLog($post->id, [
            'action' => 'Transaction Cancelled',
            'reason' => $request->cancel_reason,
        ], null, Auth::guard('admin')->user()->id, 'transaction');

        return response()->json(['res' => 'Transaction cancelled successfully!']);
    }

    /**
     * Paginated party/employee search, 20 per page, used by every party dropdown in the
     * module (Transaction form's party picker, Party Ledger's party picker). Response
     * shape - {results:[{id,text}], pagination:{more}} - matches Select2's AJAX
     * contract, so any dropdown can page through it the same way.
     */
    public function partiesSearch(Request $request)
    {
        $type = $request->party_type;
        $q = trim((string) $request->q);
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 20;

        $builder = match ($type) {
            'partner' => Partner::query()
                ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('rec_off_name', 'like', "%{$q}%")->orWhere('owner_name', 'like', "%{$q}%")))
                ->orderBy('rec_off_name'),
            'contact' => Allcontact::query()
                ->when($q, fn ($query) => $query->where('full_name', 'like', "%{$q}%"))
                ->orderBy('full_name'),
            'employee' => Admin::query()
                ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
                ->orderBy('name'),
            default => null,
        };

        if (!$builder) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $mapper = match ($type) {
            'partner' => fn ($p) => ['id' => $p->id, 'text' => $p->rec_off_name ?? $p->owner_name],
            'contact' => fn ($c) => ['id' => $c->id, 'text' => $c->full_name],
            'employee' => fn ($a) => ['id' => $a->id, 'text' => $a->name],
        };

        $paginator = $builder->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $paginator->getCollection()->map($mapper)->values(),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    private function resolveAttachmentPath(FundAdvanceTransaction $transaction): string
    {
        if (!$transaction->attachment) {
            abort(404, 'No attachment found for this transaction.');
        }

        $path = storage_path('app/public/fund_advance/transactions/' . basename($transaction->attachment));

        if (!file_exists($path)) {
            abort(404, 'Attachment file not found.');
        }

        return $path;
    }

    public function viewAttachment($id)
    {
        $transaction = FundAdvanceTransaction::findOrFail($id);
        return response()->file($this->resolveAttachmentPath($transaction));
    }

    public function downloadAttachment($id)
    {
        $transaction = FundAdvanceTransaction::findOrFail($id);
        return response()->download($this->resolveAttachmentPath($transaction), $transaction->transaction_no . '_' . basename($transaction->attachment));
    }

    public function export($format, Request $request)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_ledger_view ?? false) == 1;

        $query = FundAdvanceTransaction::with('creator')
            ->withSum(['activeSettlements as settled_amount'], 'amount')
            ->FilterType($request->transaction_type)
            ->FilterNature($request->transaction_nature)
            ->FilterPartyType($request->party_type)
            ->FilterPaymentMode($request->payment_mode)
            ->FilterDateRange('transaction_date', $request->date_range)
            ->FilterSearchText($request->search_text);

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        $rows = $query->orderBy('id', 'DESC')->get();

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.fund_advance.transactions.export_pdf', compact('rows'));
            return $pdf->download('fund-advance-transactions.pdf');
        }

        $fileName = 'fund-advance-transactions_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Transaction No', 'Party/Employee', 'Type', 'Nature', 'Description', 'Amount', 'Settled', 'Outstanding', 'Status', 'Created By']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->transaction_date->format('Y-m-d'),
                    $row->transaction_no,
                    $row->party_display_name,
                    $row->transaction_type,
                    $row->transaction_nature,
                    $row->description,
                    $row->amount,
                    $row->settled_amount ?? 0,
                    $row->outstanding,
                    $row->computed_status,
                    optional($row->creator)->name,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
