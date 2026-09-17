<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FundAdvanceSettlement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settlement_date' => 'date',
        'amount' => 'decimal:2',
        'reversed_at' => 'datetime',
    ];

    public static function generateSettlementNo(): string
    {
        return DB::transaction(function () {
            $last = self::where('settlement_no', 'LIKE', 'SET-%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(settlement_no, 5) AS UNSIGNED) DESC')
                ->first();

            $nextNumber = $last ? ((int) substr($last->settlement_no, 4)) + 1 : 1;

            return 'SET-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function transaction()
    {
        return $this->belongsTo(FundAdvanceTransaction::class, 'transaction_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function reverser()
    {
        return $this->belongsTo(Admin::class, 'reversed_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(FundAdvanceActivityLog::class, 'fund_advance_settlement_id');
    }

    /**
     * A Settlement row against a Loan-type transaction is labeled "Recovery"
     * everywhere in the UI/reports; against anything else, "Settlement" or
     * "Adjustment". This is a display concern only - the DB stores settlement_type.
     */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->settlement_type === 'adjustment') {
            return 'Adjustment';
        }

        return $this->transaction && $this->transaction->transaction_type === 'Loan' ? 'Recovery' : 'Settlement';
    }

    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            $query->whereIn('status', (array) $status);
        });
    }

    public function scopeFilterDateRange($query, $column, $dateRange)
    {
        return $query->when($dateRange, function ($query) use ($column, $dateRange) {
            [$start, $end] = array_map('trim', explode('-', $dateRange));
            $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }
}
