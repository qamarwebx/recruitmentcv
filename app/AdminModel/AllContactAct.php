<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllContactAct extends Model
{

    protected $table = 'qr_all_contacts_activity_tbl';

    protected $primaryKey = 'id';

    use HasFactory;
}
