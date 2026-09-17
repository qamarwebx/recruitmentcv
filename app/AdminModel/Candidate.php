<?php

namespace App\AdminModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model {

    protected $table = 'qr_candidate_tbl';

    protected $primaryKey = 'cand_id';

    protected $fillable = [
        'cand_fname', 'cand_mname', 'cand_lname',
    ];


/**
 * Prepare a date for array / JSON serialization.
 *
 * @param  \DateTimeInterface  $date
 * @return string
 */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    
    
}
