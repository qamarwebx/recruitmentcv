<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userwhatsappapi extends Model
{
    use HasFactory;

    protected $table = 'userwhatsappapis';
    protected $primarykey = 'id';
    protected $gaurded = [];
}
