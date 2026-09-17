<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessPermissionModule2 extends Model
{
    use HasFactory;

    protected $table = 'access_module_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];
}
