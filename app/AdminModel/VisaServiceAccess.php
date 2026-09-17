<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaServiceAccess extends Model
{
    protected $table = 'qr_visaservice_access_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
