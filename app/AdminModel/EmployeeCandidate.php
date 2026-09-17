<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class EmployeeCandidate extends Model {
    protected $table = 'qr_employee_candidate';
    protected $primaryKey = 'emp_cand_id';
    protected $gaurded = [];
}
