<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class ServicePaymentStatus extends Model {
    protected $table = 'qr_services_payment_status';
    protected $gaurded = [];

    // public function candidate() {
    //     return $this->belongsTo( App\AdminModel\Candidate::class );
    // }
}
