<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandStatuNew extends Model
{

    protected $table = 'qr_candidate_status_new_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
