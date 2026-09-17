<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSmtp extends Model
{
    use HasFactory;

    protected $table = 'email_smtps'; // or your actual table name

    protected $fillable = [
        'smtp_name',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'from_address',
        'from_name',
        'notes',
        'smtp_assign_to',
        'staff_id',
    ];
}
