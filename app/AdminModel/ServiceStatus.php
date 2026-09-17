<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model {
    protected $table = 'qr_services_status';
    protected $primaryKey = 'st_id';
    protected $fillable = [];

}
