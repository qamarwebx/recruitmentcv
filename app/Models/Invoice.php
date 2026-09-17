<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Get the partneroffice that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partneroffice()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the employer that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employer()
    {
        return $this->belongsTo(Employerplus::class);
    }

    /**
     * Get the admin that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * The payments that belong to the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payments()
    {
        return $this->belongsToMany(Payment::class);
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query) use ($searchText) {
            $searchText = '%' . $searchText . '%';

            $query->where(function ($q) use ($searchText) {
                $q->orWhere('invoice_no', 'like', $searchText)
                    ->orWhere('invoice_amount', 'like', $searchText)
                    ->orWhere('payment_status', 'like', $searchText)
                    ->orWhere('invoice_date', 'like', $searchText)
                    ->orWhere('candidate_name', 'like', $searchText)
                    ->orWhere('candidate_pass_no', 'like', $searchText)
                    ->orWhere('employer_name', 'like', $searchText)
                    ->orWhere('employer_ar_name', 'like', $searchText)
                    ->orWhere('employer_visa_no', 'like', $searchText)
                    ->orWhereHas('partneroffice', fn($q) => $q->where('rec_off_name', 'like', $searchText));
            });
        });
    }

    public function scopeFilterByPartner($query, $partnerIds)
    {
        return $query->when($partnerIds, function ($query, $partnerIds) {
            $query->whereIn('partneroffice_id', (array) $partnerIds);
        });
    }

    public function scopeFilterByPaymentStatus($query, $statuses)
    {
        return $query->when($statuses, function ($query, $statuses) {
            $query->whereIn('payment_status', (array) $statuses);
        });
    }

    public function scopeFilterByDateRange($query, $dateRange)
    {
        return $query->when($dateRange, function ($query) use ($dateRange) {
            [$start, $end] = array_map('trim', explode('-', $dateRange));
            $query->whereBetween('invoice_date', [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }

}
