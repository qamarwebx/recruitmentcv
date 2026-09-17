<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model {
    protected $table = 'qr_sector';
   protected $primaryKey = 'sc_id'; 
    protected $gaurded = [];

}
