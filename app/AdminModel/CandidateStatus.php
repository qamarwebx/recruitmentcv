<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateStatus extends Model
{

    protected $table = 'qr_candidate_status_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];
    
    use HasFactory;

}
