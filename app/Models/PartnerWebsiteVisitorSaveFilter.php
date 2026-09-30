<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A partner's saved Website Visitor filter (Partner Portal -> Website
 * Visitor), one row per partner - the partner-side counterpart of the CRM's
 * WebsiteVisitorAdminSaveFilter.
 */
class PartnerWebsiteVisitorSaveFilter extends Model
{
    public const FIELDS = ['by_custom_date', 'by_visitor', 'by_device', 'by_browser'];

    protected $fillable = ['partner_id', 'by_custom_date', 'by_visitor', 'by_device', 'by_browser'];
}
