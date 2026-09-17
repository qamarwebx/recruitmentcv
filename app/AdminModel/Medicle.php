<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Medicle extends Model {
    
    protected $table = 'qr_medical_center_tbl';
    protected $primarykey = 'med_id';
    protected $gaurded = [];

}
