<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {

    protected $table = 'qr_employee_tbl';
    protected $primaryKey = 'emp_id';
    protected $gaurded = [];

    // public function candidate() {
    //     return $this->belongsTo( 'App\AdminModel\Candidate' );
    // }
}
