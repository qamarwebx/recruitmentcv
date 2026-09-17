<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandStage extends Model
{

    protected $table = 'qr_candidate_stage_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];
    
    use HasFactory;
}
