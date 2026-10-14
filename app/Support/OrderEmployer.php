<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Employerplus;
use App\Models\Visadetails;
use Illuminate\Support\Facades\DB;

/**
 * The employer of a PARTNER order in that partner's Employer module. Twin
 * file, identical in the CRM and RecruitmentCV.
 *
 * "Assign Employer" on an order (Partner Portal: PartnerPortalController::
 * orderVisaStore(); CRM: BookingController::visaStr() "Add Visa Details")
 * saves the order's Visadetails. The CRM's rule is that a visa assignment
 * also creates an employer record for the order (BookingController::visaStr()
 * writes one to `employers`); a partner's Employer module lists that
 * partner's Employer Plus records (employerpluses, partneroffice_id), whose
 * booking_id / visadetails_id columns exist for exactly this link - but no
 * flow ever filled them, so an assigned employer never appeared there.
 *
 * sync() keeps ONE Employer Plus record per partner order, found by
 * booking_id + the order's partner and updated in place (never a second one,
 * also when the same assignment is submitted again), carrying the visa
 * details (employer name, visa no., ID no., profession, issuing authority,
 * city, salary). It belongs to the order's own partner only. The order's
 * candidate is NOT added to it as an Employercandidate: that row would let
 * the Employer module's Delete / Deassign put a booked candidate back on the
 * site. Customer (non-partner) orders are left alone.
 */
final class OrderEmployer
{
    /**
     * Creates / updates the order's Employer Plus record from its visa
     * details. Null for a customer order or a visa without an employer name.
     * $adminId: the CRM admin making the change (stored on a new record).
     */
    public static function sync(Booking $booking, Visadetails $visa, ?int $adminId = null): ?Employerplus
    {
        $partnerId = (int) $booking->partner_id;
        if ($partnerId <= 0 || trim((string) $visa->employer_name) === '' || (int) $visa->booking_id !== (int) $booking->id) {
            return null;
        }

        return DB::transaction(function () use ($booking, $visa, $partnerId, $adminId) {
            // One at a time per order: a repeated / parallel submit finds the record the first one made.
            Booking::whereKey($booking->id)->lockForUpdate()->first();

            $employer = Employerplus::where('booking_id', $booking->id)
                ->where('partneroffice_id', $partnerId)
                ->orderBy('id')
                ->first();

            if (!$employer) {
                // Employerplus has no $fillable: plain property assignment (as everywhere else).
                $employer = new Employerplus();
                $employer->booking_id = $booking->id;
                $employer->partneroffice_id = $partnerId;
                $employer->status = 1;
                $employer->openings = '1';
                if ($adminId) {
                    $employer->admin_id = $adminId;
                }
            }

            $employer->visadetails_id = $visa->id;
            $employer->employer_name = $visa->employer_name;
            $employer->employer_ar_name = $visa->employer_ar_name;
            $employer->visa_no = $visa->visa_no;
            $employer->id_no = $visa->id_no;
            $employer->proff_id = $visa->proff_id !== null ? (string) $visa->proff_id : null;
            $employer->issuing_authority = $visa->issuing_authority;
            $employer->wpcity_id = $visa->wpcity_id;
            $employer->salary = $visa->salary;
            if (filled($visa->visa_date)) {
                $employer->visa_date = $visa->visa_date;
            }
            if (filled($visa->businesstype)) {
                $employer->businesstype = $visa->businesstype;
            }
            $employer->cand_id = $booking->cand_id;
            $employer->user_id = $booking->user_id;
            $employer->save();

            return $employer;
        });
    }

    /** An Employer Plus record that mirrors an order's employer (maintained from the order, not edited here). */
    public static function isOrderLinked(?Employerplus $employer): bool
    {
        return $employer !== null && !empty($employer->booking_id);
    }

    /**
     * Partner orders whose visa details have an employer but no linked
     * Employer Plus record yet (assigned before this link existed) - for the
     * one-off, separately approved repair (orders:link-employers).
     */
    public static function missingQuery(?int $partnerId = null)
    {
        return DB::table('visadetails')
            ->join('bookings', 'bookings.id', '=', 'visadetails.booking_id')
            ->whereNotNull('bookings.partner_id')
            ->where('bookings.partner_id', '>', 0)
            ->where('visadetails.employer_name', '!=', '')
            ->whereNotNull('visadetails.employer_name')
            ->when($partnerId, fn ($q) => $q->where('bookings.partner_id', $partnerId))
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('employerpluses')
                    ->whereColumn('employerpluses.booking_id', 'bookings.id')
                    ->whereColumn('employerpluses.partneroffice_id', 'bookings.partner_id');
            })
            ->select('visadetails.id as visa_id', 'bookings.id as booking_id', 'bookings.partner_id', 'visadetails.employer_name', 'visadetails.visa_no', 'bookings.booking_status');
    }
}
