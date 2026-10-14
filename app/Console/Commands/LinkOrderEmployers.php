<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Visadetails;
use App\Support\OrderEmployer;
use Illuminate\Console\Command;

/**
 * One-off repair, for SEPARATE APPROVAL: partner orders whose employer was
 * assigned (visa details saved) before App\Support\OrderEmployer linked an
 * Employer Plus record to every partner order - so those employers are
 * missing from the partner's Employer module.
 *
 * Default = DRY RUN: lists the affected orders and changes nothing.
 * --apply creates the missing linked record for each listed order through
 * OrderEmployer::sync() (the same code the Assign Employer forms use): one
 * new Employer Plus row per order, owned by that order's own partner, no
 * other record touched (no order, visa, candidate or existing employer is
 * changed; no Employercandidate row is created). Running it twice adds
 * nothing (orders that already have their record are skipped).
 *
 *   php artisan orders:link-employers                  # dry run, all partners
 *   php artisan orders:link-employers --partner=123    # dry run, one partner
 *   php artisan orders:link-employers --partner=123 --apply
 */
class LinkOrderEmployers extends Command
{
    protected $signature = 'orders:link-employers {--partner= : Only this partner id} {--apply : Create the missing records (default: dry run)}';

    protected $description = 'Lists (or, with --apply, creates) the Employer Plus records missing for partner orders with assigned employers';

    public function handle(): int
    {
        $partnerId = $this->option('partner') !== null ? (int) $this->option('partner') : null;
        $rows = OrderEmployer::missingQuery($partnerId)->orderBy('bookings.partner_id')->orderBy('bookings.id')->get();

        if ($rows->isEmpty()) {
            $this->info('Nothing to do: every partner order with an assigned employer already has its Employer Plus record.');

            return self::SUCCESS;
        }

        $this->table(['Order (booking id)', 'Partner id', 'Employer', 'Visa no.', 'Order status'], $rows->map(fn ($r) => [
            $r->booking_id, $r->partner_id, $r->employer_name, $r->visa_no, (int) $r->booking_status === 2 ? 'cancelled' : 'active',
        ])->all());

        if (!$this->option('apply')) {
            $this->warn($rows->count() . ' order(s) listed. DRY RUN - nothing was changed. Re-run with --apply (after approval) to create their records.');

            return self::SUCCESS;
        }

        $created = 0;
        foreach ($rows as $row) {
            $booking = Booking::find($row->booking_id);
            $visa = Visadetails::find($row->visa_id);
            if ($booking && $visa && OrderEmployer::sync($booking, $visa)) {
                $created++;
            }
        }
        $this->info("$created Employer Plus record(s) created.");

        return self::SUCCESS;
    }
}
