<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRegarding extends Model
{
    protected $table = 'qr_contact_regarding_tbl';

    protected $primaryKey = 'id';
    
    protected $gaurded = [];

    use HasFactory;
}
