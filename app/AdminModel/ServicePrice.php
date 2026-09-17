<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model {
    protected $table = 'qr_service_price';
    protected $primarykey = 'sp_id';
    protected $gaurded = [];

}
