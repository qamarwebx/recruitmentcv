<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandCompFinalStatus extends Model
{

    protected $table = 'qr_cand_complete_status_tbl';
    protected $primaryKey = 'id';

    use HasFactory;
}
