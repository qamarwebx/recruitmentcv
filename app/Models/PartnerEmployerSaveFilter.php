<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Mirrors ClientAdminSaveFilter's one-row-per-user pattern - the same
 * "sticky default filter" concept the admin's own Employer Plus listing
 * uses (EmployerController@indexp always merges Employeradminsavefilter's
 * values when present), scoped to the logged-in partner instead of an
 * admin, and only ever applied when the current request has no filter
 * params of its own (see PartnerPortalController::employerPlus()).
 */
class PartnerEmployerSaveFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'status',
        'profession',
        'wpcity_id',
        'businesstype',
        'wakala_status',
        'payment_status',
        'created_date',
    ];
}
