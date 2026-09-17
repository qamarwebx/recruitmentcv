<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleP extends Model
{
    protected $table = 'qr_module_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
