<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOpening extends Model
{
    protected $table = 'emp_opening';
    protected $primaryKey = 'id';
    protected $gaurded = [];
}
