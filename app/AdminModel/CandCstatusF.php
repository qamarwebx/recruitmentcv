<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandCstatusF extends Model
{
    protected $table = 'qr_cand_cstatus_final_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
