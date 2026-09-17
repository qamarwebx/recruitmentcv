<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubModuleP extends Model
{

    protected $table = 'qr_sub_module_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
