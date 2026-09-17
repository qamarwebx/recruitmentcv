<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandIncomF extends Model
{
    protected $table = 'qr_cand_incom_final_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];
    use HasFactory;
}
