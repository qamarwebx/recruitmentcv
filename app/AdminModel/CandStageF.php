<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandStageF extends Model
{
    protected $table = 'qr_cand_stage_final_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
