<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundAdvanceTransactionFilter extends Model
{
    protected $fillable = [
        'admin_id',
        'transaction_type',
        'transaction_nature',
        'party_type',
        'status',
        'payment_mode',
        'created_by',
        'date_range',
        'amount_min',
        'amount_max',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
