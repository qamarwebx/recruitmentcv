<?php

namespace App\AdminModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model {
    use HasFactory;
    protected $table = 'qr_company_contact';

    protected $fillable = ['full_name','phone','email'];
}
