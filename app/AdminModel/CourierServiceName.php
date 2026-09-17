<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierServiceName extends Model
{
    protected $table = 'qr_courier_ser_name_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];
    
    use HasFactory;


    
}
