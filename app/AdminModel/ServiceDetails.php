<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class ServiceDetails extends Model {
    protected $primaryKey = 'sd_id';
    protected $table = 'qr_services_details';

    protected $gaurded = [];
}
