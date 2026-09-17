<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandCourier extends Model
{
    protected $table = 'qr_candidate_courier_tbl';
    protected $primaryKey = 'id';
    use HasFactory;
}
