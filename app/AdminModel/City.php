<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
    protected $table = 'qr_city';
    protected $primaryKey = 'city_id';
    protected $gaurded = [];
}
