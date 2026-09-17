<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
protected $table = 'qr_branch_details';

protected $primaryKey = 'br_id';


    protected $gaurded = [];
}
