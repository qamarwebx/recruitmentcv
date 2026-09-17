<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Party extends Model {
    protected $table = 'qr_party_tbl';
     protected $primaryKey = 'pty_id';
    protected $gaurded = [];

    public function employee() {
        return $this->hasMany( 'App\AdminModel\Employee' );
    }
}
