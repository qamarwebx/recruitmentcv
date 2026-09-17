<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\FundAdvanceSettlement;
use App\Models\FundAdvanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundAdvanceReportController extends Controller
{
    private function currentPermission()
    {
        return Adminpermission::where('staff_id', Auth::guard('admin')->user()->id)->first();
    }

    private function hasFullAccess($permission): bool
    {
        return Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1);
    }

    private function baseTransactionQuery(Request $request, ?string $type = null)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_reports_view ?? false) == 1;

        $query = FundAdvanceTransaction::with('creator')
            ->withSum(['activeSettlements as settled_amount'], 'amount')
            ->when($type, fn ($q) => $q->where('transaction_type', $type))
            ->FilterType($request->transaction_type)
            ->FilterNature($request->transaction_nature)
            ->FilterPartyType($request->party_type)
            ->FilterCreateBy($request->created_by)
            ->FilterDateRange('transaction_date', $request->date_range)
            ->FilterSearchText($request->search_text);

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        return $query;
    }

    private function employeesForFilter()
    {
        return Admin::orderBy('name')->get(['id', 'name']);
    }

    public function outstanding(Request $request)
    {
        $rows = $this->baseTransactionQuery($request)
            ->where('status', '!=', 'Cancelled')
            ->hasOutstanding()
            ->orderBy('transaction_date', 'desc')
            ->paginate($request->page_list ?? 25)->withQueryString();

        $employees = $this->employeesForFilter();

        return view('admin.fund_advance.reports.outstanding', compact('rows', 'employees'));
    }

    public function advances(Request $request)
    {
        $rows = $this->baseTransactionQuery($request, 'Advance')->orderBy('transaction_date', 'desc')->paginate($request->page_list ?? 25)->withQueryString();
        $employees = $this->employeesForFilter();
        return view('admin.fund_advance.reports.advances', compact('rows', 'employees'));
    }

    public function loans(Request $request)
    {
        $rows = $this->baseTransactionQuery($request, 'Loan')->orderBy('transaction_date', 'desc')->paginate($request->page_list ?? 25)->withQueryString();
        $employees = $this->employeesForFilter();
        return view('admin.fund_advance.reports.loans', compact('rows', 'employees'));
    }

    public function funds(Request $request)
    {
        $rows = $this->baseTransactionQuery($request, 'Fund')->orderBy('transaction_date', 'desc')->paginate($request->page_list ?? 25)->withQueryString();
        $employees = $this->employeesForFilter();
        return view('admin.fund_advance.reports.funds', compact('rows', 'employees'));
    }

    public function settlements(Request $request)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_reports_view ?? false) == 1;

        $rows = FundAdvanceSettlement::with(['transaction', 'creator'])
            ->FilterStatus($request->status)
            ->FilterDateRange('settlement_date', $request->date_range)
            ->when($request->settlement_type, fn ($q) => $q->where('settlement_type', $request->settlement_type))
            ->when(!$canViewAll, fn ($q) => $q->where('created_by', $userId))
            ->orderBy('settlement_date', 'desc')
            ->paginate($request->page_list ?? 25)->withQueryString();

        return view('admin.fund_advance.reports.settlements', compact('rows'));
    }

    public function transactions(Request $request)
    {
        $rows = $this->baseTransactionQuery($request)->orderBy('transaction_date', 'desc')->paginate($request->page_list ?? 25)->withQueryString();
        $employees = $this->employeesForFilter();
        return view('admin.fund_advance.reports.transactions', compact('rows', 'employees'));
    }

    public function export(string $report, string $format, Request $request)
    {
        if ($report === 'settlements') {
            $permission = $this->currentPermission();
            $userId = Auth::guard('admin')->user()->id;
            $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_reports_view ?? false) == 1;

            $rows = FundAdvanceSettlement::with(['transaction', 'creator'])
                ->FilterStatus($request->status)
                ->FilterDateRange('settlement_date', $request->date_range)
                ->when(!$canViewAll, fn ($q) => $q->where('created_by', $userId))
                ->orderBy('settlement_date', 'desc')->get();

            return $this->exportSettlements($rows, $format);
        }

        $type = match ($report) {
            'advances' => 'Advance',
            'loans' => 'Loan',
            'funds' => 'Fund',
            default => null,
        };

        $query = $this->baseTransactionQuery($request, $type)->where('status', '!=', 'Cancelled');

        if ($report === 'outstanding') {
            $query->hasOutstanding();
        }

        $rows = $query->orderBy('transaction_date', 'desc')->get();

        return $this->exportTransactions($rows, $format, $report);
    }

    private function exportTransactions($rows, string $format, string $report)
    {
        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.fund_advance.reports.export_pdf', compact('rows', 'report'));
            return $pdf->download("fund-advance-{$report}.pdf");
        }

        $fileName = "fund-advance-{$report}_" . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Transaction No', 'Party/Employee', 'Type', 'Nature', 'Amount', 'Settled', 'Outstanding', 'Status', 'Created By']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->transaction_date->format('Y-m-d'),
                    $row->transaction_no,
                    $row->party_display_name,
                    $row->transaction_type,
                    $row->transaction_nature,
                    $row->amount,
                    $row->settled_amount ?? 0,
                    $row->outstanding,
                    $row->computed_status,
                    optional($row->creator)->name,
                ]);
            }

            fclose($handle);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$fileName\""]);
    }

    private function exportSettlements($rows, string $format)
    {
        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.fund_advance.reports.export_settlements_pdf', compact('rows'));
            return $pdf->download('fund-advance-settlements.pdf');
        }

        $fileName = 'fund-advance-settlements_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Settlement No', 'Original Transaction', 'Party/Employee', 'Original Amount', 'Settlement Amount', 'Payment Mode', 'Reference', 'Status', 'Created By']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->settlement_date->format('Y-m-d'),
                    $row->settlement_no,
                    optional($row->transaction)->transaction_no,
                    optional($row->transaction)->party_display_name,
                    optional($row->transaction)->amount,
                    $row->amount,
                    $row->payment_mode,
                    $row->reference_no,
                    $row->status,
                    optional($row->creator)->name,
                ]);
            }

            fclose($handle);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$fileName\""]);
    }
}
