<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    protected $table = 'payment_invoice';
    protected $primaryKey = 'id';

    use HasFactory;
}
