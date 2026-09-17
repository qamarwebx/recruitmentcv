<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tradeitecenter extends Model
{
    use HasFactory;

    protected $table = 'qr_tradesitecenter_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];
}
