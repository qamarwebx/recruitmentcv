<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'client_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_country',
        'by_city',
        'by_created_date',
    ];
}
