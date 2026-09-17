<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class SubmissionPlace extends Model {
    protected $table = 'qr_submission_place_tbl';
    protected $primaryKey = 'sub_pl_id';
    protected $gaurded = [];

}
