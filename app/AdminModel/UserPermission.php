<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{

    protected $table = 'qr_user_permission_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
