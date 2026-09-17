<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model {
    protected $table = 'qr_profession_tbl';
    protected $primaryKey = 'prof_id';
    protected $gaurded = [];

}
