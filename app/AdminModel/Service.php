<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {
    protected $table = 'qr_service_tbl';
    protected $primaryKey = 'ser_id';
    protected $gaurded = [];
}
