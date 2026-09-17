<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'invoice_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_partner_office',
        'by_payment_status',
        'invoice_date_range',
    ];

    public function getByPartnerOfficeArrayAttribute()
    {
        return $this->by_partner_office ? explode(',', $this->by_partner_office) : [];
    }

    public function getByPaymentStatusArrayAttribute()
    {
        return $this->by_payment_status ? explode(',', $this->by_payment_status) : [];
    }
}
