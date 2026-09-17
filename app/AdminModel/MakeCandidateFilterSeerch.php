<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeCandidateFilterSeerch extends Model
{
    

    protected $table = 'candidate_filter_search_tbl';
    protected $primarykey = 'id';
    protected $gaurded = [];

    use HasFactory;
}
