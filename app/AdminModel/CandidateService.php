<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class CandidateService extends Model {
    protected $table = 'qr_candidate_service';
    protected $primaryKey = 'cs_id';
    protected $gaurded = [];
}
