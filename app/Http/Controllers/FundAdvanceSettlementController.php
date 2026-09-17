<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Adminpermission;
use App\Models\FundAdvanceSettlement;
use App\Models\FundAdvanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FundAdvanceSettlementController extends Controller
{
    private function currentPermission()
    {
        return Adminpermission::where('staff_id', Auth::guard('admin')->user()->id)->first();
    }

    private function hasFullAccess($permission): bool
    {
        return Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1);
    }

    public function pending(Request $request)
    {
        $permission = $this->currentPermission();
        $posts = $this->pendingQuery($request)->paginate($request->page_list ?? 10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.fund_advance.settlements.pendingload', compact('posts', 'permission'));
        }

        return view('admin.fund_advance.settlements.pending', compact('posts', 'permission'));
    }

    private function pendingQuery(Request $request)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_ledger_view ?? false) == 1;

        $query = FundAdvanceTransaction::with('creator')
            ->withSum(['activeSettlements as settled_amount'], 'amount')
            ->where('status', '!=', 'Cancelled')
            ->FilterType($request->transaction_type)
            ->FilterPartyType($request->party_type)
            ->FilterSearchText($request->search_text)
            ->hasOutstanding()
            ->orderBy('id', 'DESC');

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        return $query;
    }

    public function store(Request $request)
    {
        $isAdjustment = $request->settlement_type === 'adjustment';

        $request->validate([
            'transaction_id' => 'required|integer|exists:fund_advance_transactions,id',
            'settlement_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:100',
            'reference_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $permission = $this->currentPermission();
        $requiredFlag = $isAdjustment ? 'fund_advance_adjustment_create' : 'fund_advance_settlement_create';
        if (!$this->hasFullAccess($permission) && ($permission->$requiredFlag ?? false) != 1) {
            return response()->json(['res' => 'You are not authorized to perform this action.'], 403);
        }

        return DB::transaction(function () use ($request, $isAdjustment) {
            $transaction = FundAdvanceTransaction::where('id', $request->transaction_id)->lockForUpdate()->firstOrFail();

            if ($transaction->status === 'Cancelled') {
                return response()->json(['res' => 'This transaction has been cancelled.'], 422);
            }

            $settledAmount = (float) $transaction->activeSettlements()->sum('amount');
            $outstanding = round((float) $transaction->amount - $settledAmount, 2);

            if ((float) $request->amount > $outstanding) {
                return response()->json(['res' => 'Amount cannot exceed the outstanding balance of ' . $outstanding . '.'], 422);
            }

            $attachment = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
                $file->storeAs('fund_advance/settlements', $fileName, 'public');
                $attachment = $fileName;
            }

            $settlement = new FundAdvanceSettlement();
            $settlement->settlement_no = FundAdvanceSettlement::generateSettlementNo();
            $settlement->transaction_id = $transaction->id;
            $settlement->settlement_type = $isAdjustment ? 'adjustment' : 'settlement';
            $settlement->settlement_date = $request->settlement_date;
            $settlement->amount = $request->amount;
            $settlement->payment_mode = $request->payment_mode;
            $settlement->reference_no = $request->reference_no;
            $settlement->remarks = $request->remarks;
            $settlement->attachment = $attachment;
            $settlement->status = 'Active';
            $settlement->created_by = Auth::guard('admin')->user()->id;
            $settlement->save();

            Helper::fundAdvanceActivityLog($transaction->id, [
                'action' => $isAdjustment ? 'Adjustment Recorded' : 'Settlement Recorded',
                'settlement_no' => $settlement->settlement_no,
                'amount' => (float) $settlement->amount,
            ], $settlement->id, Auth::guard('admin')->user()->id, 'settlement');

            return response()->json([
                'res' => ($isAdjustment ? 'Adjustment' : 'Settlement') . ' ' . $settlement->settlement_no . ' recorded successfully!',
                'outstanding' => round($outstanding - (float) $request->amount, 2),
                'status' => $transaction->fresh()->computed_status,
            ]);
        });
    }

    public function history(Request $request)
    {
        $permission = $this->currentPermission();
        $posts = $this->historyQuery($request)->paginate($request->page_list ?? 10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.fund_advance.settlements.historyload', compact('posts', 'permission'));
        }

        return view('admin.fund_advance.settlements.history', compact('posts', 'permission'));
    }

    private function historyQuery(Request $request)
    {
        $permission = $this->currentPermission();
        $userId = Auth::guard('admin')->user()->id;
        $canViewAll = $this->hasFullAccess($permission) || ($permission->fund_advance_ledger_view ?? false) == 1;

        $query = FundAdvanceSettlement::with(['transaction', 'creator'])
            ->FilterStatus($request->status)
            ->FilterDateRange('settlement_date', $request->date_range)
            ->when($request->settlement_type, fn ($q) => $q->where('settlement_type', $request->settlement_type))
            ->orderBy('id', 'DESC');

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        return $query;
    }

    public function reverse(Request $request)
    {
        $request->validate(['reversed_reason' => 'required|string|max:500']);

        $settlement = FundAdvanceSettlement::findOrFail($request->id);

        if ($settlement->status === 'Reversed') {
            return response()->json(['res' => 'This settlement has already been reversed.'], 422);
        }

        $settlement->status = 'Reversed';
        $settlement->reversed_reason = $request->reversed_reason;
        $settlement->reversed_by = Auth::guard('admin')->user()->id;
        $settlement->reversed_at = now();
        $settlement->save();

        Helper::fundAdvanceActivityLog($settlement->transaction_id, [
            'action' => 'Settlement Reversed',
            'settlement_no' => $settlement->settlement_no,
            'reason' => $request->reversed_reason,
        ], $settlement->id, Auth::guard('admin')->user()->id, 'settlement');

        return response()->json([
            'res' => 'Settlement reversed successfully!',
            'status' => $settlement->transaction->fresh()->computed_status,
        ]);
    }

    private function resolveAttachmentPath(FundAdvanceSettlement $settlement): string
    {
        if (!$settlement->attachment) {
            abort(404, 'No attachment found for this settlement.');
        }

        $path = storage_path('app/public/fund_advance/settlements/' . basename($settlement->attachment));

        if (!file_exists($path)) {
            abort(404, 'Attachment file not found.');
        }

        return $path;
    }

    public function viewAttachment($id)
    {
        $settlement = FundAdvanceSettlement::findOrFail($id);
        return response()->file($this->resolveAttachmentPath($settlement));
    }

    public function downloadAttachment($id)
    {
        $settlement = FundAdvanceSettlement::findOrFail($id);
        return response()->download($this->resolveAttachmentPath($settlement), $settlement->settlement_no . '_' . basename($settlement->attachment));
    }
}
