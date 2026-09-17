<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class EmployeeServiceType extends Model {
    protected $table = 'qr_emp_service_tbl';
    protected $primaryKey = 'ser_id';
    protected $gaurded = [];
}
