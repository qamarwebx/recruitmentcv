<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    protected $table = 'qr_permissions_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
