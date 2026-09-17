<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceOfIssue extends Model
{
    protected $table = 'qr_candidate_place_issue';

    protected $primaryKey = 'id';
    use HasFactory;
}
