<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importexport extends Model
{
    use HasFactory;

    public $fillable = ['fname', 'lname', 'gender'];

}
