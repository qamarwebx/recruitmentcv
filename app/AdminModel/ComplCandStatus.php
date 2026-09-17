<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplCandStatus extends Model
{

    protected $table = 'qr_cand_final_status';
    protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
