<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {
    protected $table = 'qr_country';

    protected $primaryKey = 'country_id';
    
    protected $gaurded = [];
}
