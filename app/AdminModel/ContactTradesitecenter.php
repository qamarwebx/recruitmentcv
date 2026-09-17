<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTradesitecenter extends Model
{

    protected $table = 'qr_contact_tradesitecenter_tbl';

    protected $primaryKey = 'id';
    
    protected $gaurded = [];

    use HasFactory;
}
