<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakalaStatusDetails extends Model
{

    protected $table = 'qr_wakala_service_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
