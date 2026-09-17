<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandDocs extends Model
{
    
    protected $table = 'qr_cand_docs_tbl';

    protected $primaryKey = 'id';

    use HasFactory;
}
