<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfession extends Model
{
    protected $table = 'emp_prof';
    protected $primaryKey = 'id';
    protected $gaurded = [];
}
