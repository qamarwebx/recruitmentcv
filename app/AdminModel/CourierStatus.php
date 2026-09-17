<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierStatus extends Model
{

    protected $table = 'qr_courier_status_tbl';
    protected $primaryKey = 'id';

    use HasFactory;
}
