<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FundAdvanceTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Valid transaction_type => transaction_nature combinations. Fund/Advance/Loan can
     * move either direction; Receivable/Payable are always "raised" (Given) or entered
     * as a standalone write-in (Adjustment) - Recovery/Settlement are settlement-table
     * events only, never a transaction's own nature (see fund_advance_settlements).
     */
    const TYPE_NATURE_MAP = [
        'Fund' => ['Given', 'Received'],
        'Advance' => ['Given', 'Received'],
        'Loan' => ['Given', 'Received'],
        'Receivable' => ['Given', 'Adjustment'],
        'Payable' => ['Given', 'Adjustment'],
    ];

    const NUMBER_PREFIXES = [
        'Fund' => 'FUND',
        'Advance' => 'ADV',
        'Loan' => 'LOAN',
        'Receivable' => 'REC',
        'Payable' => 'PAY',
    ];

    const PARTY_TYPES = ['partner', 'contact', 'employee', 'other'];

    /**
     * Raw-SQL equivalents of settled_amount/outstanding for use in WHERE (not HAVING)
     * clauses. MySQL's ONLY_FULL_GROUP_BY mode rejects a HAVING clause that references
     * a plain column (amount) once Laravel's paginator wraps the query in a COUNT(*)
     * subquery, so outstanding-based filtering has to happen in WHERE against a
     * correlated subquery instead of against a withSum() SELECT alias.
     */
    const SETTLED_SQL = "COALESCE((SELECT SUM(fas.amount) FROM fund_advance_settlements fas WHERE fas.transaction_id = fund_advance_transactions.id AND fas.status = 'Active'), 0)";
    const OUTSTANDING_SQL = '(fund_advance_transactions.amount - ' . self::SETTLED_SQL . ')';

    public function scopeHasOutstanding($query)
    {
        return $query->whereRaw(self::OUTSTANDING_SQL . ' > 0');
    }

    public static function isValidTypeNature(string $type, string $nature): bool
    {
        return in_array($nature, self::TYPE_NATURE_MAP[$type] ?? [], true);
    }

    public static function generateTransactionNo(string $type): string
    {
        $prefix = self::NUMBER_PREFIXES[$type] ?? 'TXN';

        return DB::transaction(function () use ($prefix) {
            $last = self::where('transaction_no', 'LIKE', $prefix . '-%')
                ->lockForUpdate()
                ->orderByRaw("CAST(SUBSTRING(transaction_no, ?) AS UNSIGNED) DESC", [strlen($prefix) + 2])
                ->first();

            $nextNumber = $last
                ? ((int) substr($last->transaction_no, strlen($prefix) + 1)) + 1
                : 1;

            return $prefix . '-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function settlements()
    {
        return $this->hasMany(FundAdvanceSettlement::class, 'transaction_id');
    }

    public function activeSettlements()
    {
        return $this->hasMany(FundAdvanceSettlement::class, 'transaction_id')->where('status', 'Active');
    }

    public function activityLogs()
    {
        return $this->hasMany(FundAdvanceActivityLog::class, 'fund_advance_transaction_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'party_id');
    }

    public function contact()
    {
        return $this->belongsTo(Allcontact::class, 'party_id');
    }

    public function employee()
    {
        return $this->belongsTo(Admin::class, 'party_id');
    }

    public function getPartyDisplayNameAttribute(): string
    {
        if ($this->party_type === 'other') {
            return $this->party_name ?? '-';
        }

        $related = match ($this->party_type) {
            'partner' => $this->partner,
            'contact' => $this->contact,
            'employee' => $this->employee,
            default => null,
        };

        if (!$related) {
            return $this->party_name ?? '-';
        }

        return match ($this->party_type) {
            'partner' => $related->rec_off_name ?? $related->owner_name ?? '-',
            'contact' => $related->full_name ?? '-',
            'employee' => $related->name ?? '-',
            default => '-',
        };
    }

    public function getSettledAmountAttribute(): float
    {
        if (array_key_exists('settled_amount', $this->attributes)) {
            return (float) $this->attributes['settled_amount'];
        }

        return (float) $this->activeSettlements()->sum('amount');
    }

    public function getOutstandingAttribute(): float
    {
        return round((float) $this->amount - $this->settled_amount, 2);
    }

    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'Cancelled') {
            return 'Cancelled';
        }

        $outstanding = $this->outstanding;

        if ($outstanding <= 0) {
            return 'Settled';
        }

        if ($this->settled_amount > 0) {
            return 'Partially Settled';
        }

        return 'Pending';
    }

    public function scopeFilterType($query, $type)
    {
        return $query->when($type, function ($query, $type) {
            $query->whereIn('transaction_type', (array) $type);
        });
    }

    public function scopeFilterNature($query, $nature)
    {
        return $query->when($nature, function ($query, $nature) {
            $query->whereIn('transaction_nature', (array) $nature);
        });
    }

    public function scopeFilterPartyType($query, $partyType)
    {
        return $query->when($partyType, function ($query, $partyType) {
            $query->whereIn('party_type', (array) $partyType);
        });
    }

    public function scopeFilterParty($query, $partyType, $partyId)
    {
        return $query->when($partyType && $partyId, function ($query) use ($partyType, $partyId) {
            $query->where('party_type', $partyType)->where('party_id', $partyId);
        });
    }

    public function scopeFilterPaymentMode($query, $paymentMode)
    {
        return $query->when($paymentMode, function ($query, $paymentMode) {
            $query->whereIn('payment_mode', (array) $paymentMode);
        });
    }

    public function scopeFilterCreateBy($query, $adminId)
    {
        return $query->when($adminId, function ($query, $adminId) {
            $query->whereIn('created_by', (array) $adminId);
        });
    }

    public function scopeFilterDateRange($query, $column, $dateRange)
    {
        return $query->when($dateRange, function ($query) use ($column, $dateRange) {
            [$start, $end] = array_map('trim', explode('-', $dateRange));
            $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }

    public function scopeFilterAmountRange($query, $min, $max)
    {
        return $query
            ->when($min !== null && $min !== '', fn ($query) => $query->where('amount', '>=', $min))
            ->when($max !== null && $max !== '', fn ($query) => $query->where('amount', '<=', $max));
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('transaction_no', 'like', $searchText)
                    ->orWhere('party_name', 'like', $searchText)
                    ->orWhere('reference_no', 'like', $searchText)
                    ->orWhere('description', 'like', $searchText)
                    ->orWhere('amount', 'like', $searchText)
                    ->orWhereHas('creator', fn ($q) => $q->where('name', 'like', $searchText));
            });
        });
    }
}
