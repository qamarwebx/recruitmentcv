<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A CRM staff member (admins row) who receives Partner Activity alerts
 * (CRM -> Website -> Partner Notification). Only admin_id is stored: name,
 * email and mobile always come from the admin, so they stay in sync.
 * Twin file in both apps.
 */
class PartnerNotificationRecipient extends Model
{
    protected $fillable = ['admin_id', 'created_by'];
}
