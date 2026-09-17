<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfessionOpening extends Model
{
    protected $table = 'emp_prof_opp_tbl';
    protected $primaryKey = 'id';
    protected $gaurded = [];
}
