<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Visa extends Model {
    protected $table = 'qr_emp_visa_tbl';
     protected $primaryKey = 'visa_id';
    protected $gaurded = [];

}
