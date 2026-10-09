<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Booking reference numbers (bookings.reference_no) - one allocator for every
 * place that creates a booking (twin file, identical in the CRM and
 * RecruitmentCV; both apps write the same bookings table).
 *
 * The old "latest() + 1, read outside any lock" let two simultaneous orders
 * (in either app) get the same number. Now the insert runs inside
 * withLock() - one MySQL named lock shared by both apps - and next() hands
 * out the highest existing number + 1. The lock must be held until the new
 * booking is COMMITTED, so wrap the whole transaction that inserts it.
 * Re-entrant within a request (nested withLock() calls don't re-acquire).
 */
final class BookingReference
{
    private const LOCK = 'bookings_reference_no';
    private const FIRST = 801;

    private static int $depth = 0;

    /** Run $work while holding the shared lock; throws if it can't be obtained in time. */
    public static function withLock(callable $work)
    {
        if (self::$depth === 0) {
            $locked = (int) (DB::selectOne('SELECT GET_LOCK(?, 15) AS l', [self::LOCK])->l ?? 0) === 1;
            if (!$locked) {
                throw new \RuntimeException('Could not allocate an order number right now. Please try again.');
            }
        }
        self::$depth++;

        try {
            return $work();
        } finally {
            self::$depth--;
            if (self::$depth === 0) {
                DB::selectOne('SELECT RELEASE_LOCK(?) AS r', [self::LOCK]);
            }
        }
    }

    /** The next free number - call inside withLock(). */
    public static function next(): int
    {
        $max = DB::table('bookings')->max(DB::raw('CAST(reference_no AS UNSIGNED)'));

        return $max ? (int) $max + 1 : self::FIRST;
    }
}
