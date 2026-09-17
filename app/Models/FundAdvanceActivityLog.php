<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundAdvanceActivityLog extends Model
{
    /**
     * Microsecond precision so activity across transactions and settlements can be
     * merged and sorted purely by created_at - same reasoning as TestimonialActivityLog.
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'fund_advance_transaction_id',
        'fund_advance_settlement_id',
        'admin_id',
        'module',
        'activity',
    ];

    protected $casts = [
        'activity' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(FundAdvanceTransaction::class, 'fund_advance_transaction_id');
    }

    public function settlement()
    {
        return $this->belongsTo(FundAdvanceSettlement::class, 'fund_advance_settlement_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
