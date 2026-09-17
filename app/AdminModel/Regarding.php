<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regarding extends Model
{
    
    protected $table = 'qr_regarding_c_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];

    use HasFactory;

}
