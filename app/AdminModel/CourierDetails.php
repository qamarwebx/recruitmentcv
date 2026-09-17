<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierDetails extends Model
{

    protected $table = 'qr_courier_sd_tbl';
    protected $primaryKey = 'id';

    use HasFactory;
}
