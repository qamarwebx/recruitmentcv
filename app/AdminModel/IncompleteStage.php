<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncompleteStage extends Model
{

    protected $table = 'table_qr_incomplete_stage_tbl';

    protected $primaryKey = 'id';

    use HasFactory;
}
