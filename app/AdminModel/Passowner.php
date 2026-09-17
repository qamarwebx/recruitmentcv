<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passowner extends Model
{
    protected $table = 'qr_pass_owner_tbl';
     protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
