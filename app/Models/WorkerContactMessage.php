<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A single "Contact Us" submission from the public Worker Portal
 * (worker.qamarhire.com/contact-us) - see
 * Worker\WorkerPageController::contactUsStore().
 */
class WorkerContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
    ];
}
