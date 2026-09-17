<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareOf extends Model
{
    protected $table = 'qr_care_of_tbl';
     protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
