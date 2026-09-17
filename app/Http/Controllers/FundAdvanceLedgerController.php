<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\FundAdvanceSettlement;
use App\Models\FundAdvanceTransaction;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundAdvanceLedgerController extends Controller
{
    private function currentPermission()
    {
        return Adminpermission::where('staff_id', Auth::guard('admin')->user()->id)->first();
    }

    /**
     * Fund/Advance/Loan "Given" and Receivable "Given" (raised) increase what the
     * party owes us -> Debit. "Received" natures and Payable "Given" (raised)
     * increase what we owe the party -> Credit. Settlement/adjustment rows always
     * post the opposite side of their parent transaction.
     */
    private function isDebitTransaction(FundAdvanceTransaction $transaction): bool
    {
        if ($transaction->transaction_type === 'Payable') {
            return false;
        }

        return $transaction->transaction_nature !== 'Received';
    }

    private function buildLedgerRows($transactions, $settlements, float $openingBalance = 0)
    {
        $rows = collect();

        foreach ($transactions as $t) {
            $isDebit = $this->isDebitTransaction($t);
            $rows->push([
                'date' => $t->transaction_date,
                'reference' => $t->transaction_no,
                'type' => $t->transaction_type . ' (' . $t->transaction_nature . ')',
                'debit' => $isDebit ? (float) $t->amount : 0,
                'credit' => $isDebit ? 0 : (float) $t->amount,
            ]);
        }

        foreach ($settlements as $s) {
            $parent = $transactions->firstWhere('id', $s->transaction_id);
            if (!$parent) {
                continue;
            }
            $isDebitOrigin = $this->isDebitTransaction($parent);
            $rows->push([
                'date' => $s->settlement_date,
                'reference' => $s->settlement_no . ' (' . $parent->transaction_no . ')',
                'type' => $s->display_label,
                'debit' => $isDebitOrigin ? 0 : (float) $s->amount,
                'credit' => $isDebitOrigin ? (float) $s->amount : 0,
            ]);
        }

        $rows = $rows->sortBy('date')->values();

        $balance = $openingBalance;
        $rows = $rows->map(function ($row) use (&$balance) {
            $balance += $row['debit'] - $row['credit'];
            $row['balance'] = $balance;
            return $row;
        });

        return $rows;
    }

    /**
     * Balance contributed by a party's transactions/settlements dated strictly before
     * $beforeDate, so a filtered date range still starts from a correct running balance
     * instead of silently resetting to zero.
     */
    private function openingBalanceFor(string $partyType, $partyId, ?string $beforeDate): float
    {
        if (!$beforeDate) {
            return 0;
        }

        $priorTransactions = FundAdvanceTransaction::where('party_type', $partyType)
            ->where('party_id', $partyId)
            ->where('status', '!=', 'Cancelled')
            ->where('transaction_date', '<', $beforeDate)
            ->get();

        $priorSettlements = FundAdvanceSettlement::whereIn('transaction_id', $priorTransactions->pluck('id'))
            ->where('status', 'Active')
            ->where('settlement_date', '<', $beforeDate)
            ->get();

        $lastRow = $this->buildLedgerRows($priorTransactions, $priorSettlements)->last();

        return $lastRow ? (float) $lastRow['balance'] : 0;
    }

    private function rangeStart(Request $request): ?string
    {
        if (!$request->date_range) {
            return null;
        }

        return trim(explode('-', $request->date_range)[0]);
    }

    public function partyLedger(Request $request)
    {
        $partyType = $request->party_type ?: 'partner';
        $partyId = $request->party_id;

        $party = $this->resolveParty($partyType, $partyId);

        $openingBalance = $partyType && $partyId ? $this->openingBalanceFor($partyType, $partyId, $this->rangeStart($request)) : 0;

        $transactions = FundAdvanceTransaction::where('party_type', $partyType)
            ->where('party_id', $partyId)
            ->where('status', '!=', 'Cancelled')
            ->when($request->date_range, fn ($q) => $q->FilterDateRange('transaction_date', $request->date_range))
            ->orderBy('transaction_date')
            ->get();

        $settlements = FundAdvanceSettlement::whereIn('transaction_id', $transactions->pluck('id'))
            ->where('status', 'Active')
            ->get();

        $rows = $this->buildLedgerRows($transactions, $settlements, $openingBalance);
        $totalDebit = $rows->sum('debit');
        $totalCredit = $rows->sum('credit');
        $closingBalance = $openingBalance + $totalDebit - $totalCredit;

        if ($request->export === 'csv') {
            return $this->exportLedgerCsv($rows, $openingBalance, 'party-ledger_' . now()->format('Ymd_His') . '.csv');
        }

        return view('admin.fund_advance.ledgers.party', compact('rows', 'totalDebit', 'totalCredit', 'closingBalance', 'openingBalance', 'party', 'partyType', 'partyId'));
    }

    public function employeeLedger(Request $request)
    {
        $adminId = $request->admin_id;
        $employees = Admin::orderBy('name')->get(['id', 'name']);

        if (!$adminId) {
            return view('admin.fund_advance.ledgers.employee', [
                'rows' => collect(), 'summary' => [], 'totalDebit' => 0, 'totalCredit' => 0,
                'closingBalance' => 0, 'openingBalance' => 0, 'employee' => null, 'adminId' => null, 'employees' => $employees,
            ]);
        }

        $employee = Admin::find($adminId);
        $openingBalance = $this->openingBalanceFor('employee', $adminId, $this->rangeStart($request));

        $transactions = FundAdvanceTransaction::where('party_type', 'employee')
            ->where('party_id', $adminId)
            ->where('status', '!=', 'Cancelled')
            ->when($request->date_range, fn ($q) => $q->FilterDateRange('transaction_date', $request->date_range))
            ->orderBy('transaction_date')
            ->get();

        $settlements = FundAdvanceSettlement::whereIn('transaction_id', $transactions->pluck('id'))
            ->where('status', 'Active')
            ->get();

        $rows = $this->buildLedgerRows($transactions, $settlements, $openingBalance);
        $totalDebit = $rows->sum('debit');
        $totalCredit = $rows->sum('credit');
        $closingBalance = $openingBalance + $totalDebit - $totalCredit;

        $summary = [
            'advances' => (float) $transactions->where('transaction_type', 'Advance')->sum('amount'),
            'loans' => (float) $transactions->where('transaction_type', 'Loan')->sum('amount'),
            'receivables' => (float) $transactions->where('transaction_type', 'Receivable')->sum('amount'),
            'payables' => (float) $transactions->where('transaction_type', 'Payable')->sum('amount'),
            'settlements' => (float) $settlements->where('settlement_type', 'settlement')->sum('amount'),
            'recoveries' => (float) $settlements->filter(fn ($s) => optional($transactions->firstWhere('id', $s->transaction_id))->transaction_type === 'Loan')->sum('amount'),
            'net_outstanding' => $closingBalance,
        ];

        if ($request->export === 'csv') {
            return $this->exportLedgerCsv($rows, $openingBalance, 'employee-ledger_' . now()->format('Ymd_His') . '.csv');
        }

        return view('admin.fund_advance.ledgers.employee', compact('rows', 'summary', 'totalDebit', 'totalCredit', 'closingBalance', 'openingBalance', 'employee', 'adminId', 'employees'));
    }

    public function fundLedger(Request $request)
    {
        [$start, $end] = $request->filled('start_date') && $request->filled('end_date')
            ? [$request->start_date, $request->end_date]
            : [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];

        $base = FundAdvanceTransaction::where('transaction_type', 'Fund')
            ->where('status', '!=', 'Cancelled')
            ->whereBetween('transaction_date', [$start, $end]);

        $fundsReceived = (float) (clone $base)->where('transaction_nature', 'Received')->sum('amount');
        $fundsGiven = (float) (clone $base)->where('transaction_nature', 'Given')->sum('amount');

        $fundTransactionIds = (clone $base)->pluck('id');
        $adjustments = (float) FundAdvanceSettlement::whereIn('transaction_id', $fundTransactionIds)
            ->where('settlement_type', 'adjustment')
            ->where('status', 'Active')
            ->sum('amount');

        $openingBalance = (float) FundAdvanceTransaction::where('transaction_type', 'Fund')
            ->where('status', '!=', 'Cancelled')
            ->where('transaction_date', '<', $start)
            ->selectRaw("SUM(CASE WHEN transaction_nature = 'Received' THEN amount ELSE -amount END) as bal")
            ->value('bal') ?? 0;

        $closingBalance = $openingBalance + $fundsReceived - $fundsGiven + $adjustments;

        $rows = FundAdvanceTransaction::where('transaction_type', 'Fund')
            ->where('status', '!=', 'Cancelled')
            ->whereBetween('transaction_date', [$start, $end])
            ->with('creator')
            ->orderBy('transaction_date')
            ->get();

        return view('admin.fund_advance.ledgers.fund', compact('openingBalance', 'fundsReceived', 'fundsGiven', 'adjustments', 'closingBalance', 'rows', 'start', 'end'));
    }

    private function exportLedgerCsv($rows, float $openingBalance, string $filename)
    {
        return response()->stream(function () use ($rows, $openingBalance) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Reference', 'Type', 'Debit', 'Credit', 'Balance']);
            fputcsv($handle, ['', '', 'Opening Balance', '', '', $openingBalance]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    \Carbon\Carbon::parse($row['date'])->format('Y-m-d'),
                    $row['reference'],
                    $row['type'],
                    $row['debit'] ?: '',
                    $row['credit'] ?: '',
                    $row['balance'],
                ]);
            }

            fclose($handle);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""]);
    }

    private function resolveParty($partyType, $partyId)
    {
        return match ($partyType) {
            'partner' => Partner::find($partyId),
            'contact' => Allcontact::find($partyId),
            'employee' => Admin::find($partyId),
            default => null,
        };
    }
}
