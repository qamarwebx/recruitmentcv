<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * May this partner open this candidate? The Partner Portal's existing rule
 * (PartnerPortalController::accessibleCandidate()), in one place so the
 * passport rule (App\Support\PassportAccess) applies exactly the same
 * candidate access: an "available" candidate (active, published, CV
 * executed), or one from this partner's own non-cancelled booking (even if
 * since deployed / unpublished).
 */
final class PartnerCandidateAccess
{
    /** The partner's live (non-cancelled, booking_status != 2) booking of the candidate, or null. */
    public static function booking(int $partnerId, int $candidateId): ?object
    {
        return DB::table('bookings')
            ->where('partner_id', $partnerId)
            ->where('cand_id', $candidateId)
            ->where('booking_status', '!=', 2)
            ->first();
    }

    /**
     * Is the candidate currently hired by someone else - a live
     * (non-cancelled, booking_status != 2) booking by another partner or by
     * a customer not booked through this partner? Same "live" rule as
     * booking() above: expired / released / rejected reservations cancel
     * their booking (booking_status = 2), so they never count. This
     * partner's own bookings (incl. its website customers' - partner_id =
     * this partner) are its own, as everywhere else in the Partner Portal.
     * Hire Now's "Already Hired" notice; never says who.
     */
    public static function hiredByOther(int $partnerId, int $candidateId): bool
    {
        return self::hiredByOthers($candidateId, $partnerId);
    }

    /**
     * The same rule for any viewer ("Already Hired" badge + notice on the
     * Partner candidate page and the public resume page): a live booking
     * (booking_status != 2) that is not the viewer's own - a partner's own
     * bookings (partner_id = it) and a customer's own bookings (user_id = it)
     * don't count; a guest sees any live booking. RecruitmentCV bookings
     * only - never the CRM reservation queue. Yes / no only, never who.
     */
    public static function hiredByOthers(int $candidateId, ?int $partnerId = null, ?int $userId = null): bool
    {
        return DB::table('bookings')
            ->where('cand_id', $candidateId)
            ->where('booking_status', '!=', 2)
            ->when($partnerId, fn ($q) => $q->where(fn ($q) => $q->whereNull('partner_id')->orWhere('partner_id', '!=', $partnerId)))
            ->when($userId, fn ($q) => $q->where('user_id', '!=', $userId))
            ->exists();
    }

    /**
     * The viewer for the "Already Hired" badge: [partnerId, userId] - the
     * signed-in partner, else the signed-in customer, else a guest [null, null].
     */
    public static function viewer(): array
    {
        $partnerId = (int) \Illuminate\Support\Facades\Auth::guard('partner')->id();
        if ($partnerId) {
            return [$partnerId, null];
        }
        $userId = (int) \Illuminate\Support\Facades\Auth::guard('web')->id();

        return [null, $userId ?: null];
    }

    /**
     * The "Already Hired" rule as a query condition on candidates - the ONE
     * definition behind the badges and the Partner Portal's Available /
     * Already Hired filter + counts:
     *   hired by someone other than the viewer (a live booking, booking_status
     *   != 2, that is not the viewer's: partner_id = partner / user_id = customer)
     *   AND the viewer has no live order of their own for that candidate
     *   (then it is "theirs" - Hired / View Order - not "Already Hired").
     * $hired false = the opposite (everything else). Works on Eloquent and
     * query builders; $table = the candidates table's name / alias.
     */
    public static function whereAlreadyHired($query, ?int $partnerId = null, ?int $userId = null, bool $hired = true, string $table = 'candidates')
    {
        $other = function ($bookings) use ($partnerId, $userId, $table) {
            $bookings->selectRaw('1')->from('bookings')
                ->whereColumn('bookings.cand_id', $table . '.id')
                ->where('bookings.booking_status', '!=', 2)
                ->when($partnerId, fn ($q) => $q->where(fn ($q) => $q->whereNull('bookings.partner_id')->orWhere('bookings.partner_id', '!=', $partnerId)))
                ->when($userId, fn ($q) => $q->where('bookings.user_id', '!=', $userId));
        };
        $own = function ($bookings) use ($partnerId, $userId, $table) {
            $bookings->selectRaw('1')->from('bookings')
                ->whereColumn('bookings.cand_id', $table . '.id')
                ->where('bookings.booking_status', '!=', 2)
                ->where(function ($q) use ($partnerId, $userId) {
                    if ($partnerId) {
                        $q->orWhere('bookings.partner_id', $partnerId);
                    }
                    if ($userId) {
                        $q->orWhere('bookings.user_id', $userId);
                    }
                });
        };
        $viewer = (bool) ($partnerId || $userId);

        if ($hired) {
            return $query->whereExists($other)->when($viewer, fn ($q) => $q->whereNotExists($own));
        }

        return $query->where(function ($q) use ($other, $own, $viewer) {
            $q->whereNotExists($other);
            if ($viewer) {
                $q->orWhereExists($own);
            }
        });
    }

    /**
     * "Already Hired" for a set of candidates (badges on a listing / detail
     * page): ONE query, whereAlreadyHired() above. Returns the ids that get
     * the badge.
     */
    public static function alreadyHiredIds(iterable $candidateIds, ?int $partnerId = null, ?int $userId = null): array
    {
        $ids = collect($candidateIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        if (!$ids) {
            return [];
        }

        return self::whereAlreadyHired(DB::table('candidates')->whereIn('candidates.id', $ids), $partnerId, $userId)
            ->pluck('candidates.id')->map(fn ($id) => (int) $id)->all();
    }

    /** alreadyHiredIds() for whoever is looking at the page (viewer()). */
    public static function alreadyHiredIdsForViewer(iterable $candidateIds): array
    {
        [$partnerId, $userId] = self::viewer();

        return self::alreadyHiredIds($candidateIds, $partnerId, $userId);
    }

    /** Card hiring statuses (hiringStatuses()). */
    public const STATUS_OWN = 'own';
    public const STATUS_HIRED = 'hired';
    public const STATUS_AVAILABLE = 'available';

    /**
     * The hiring status of each listed candidate for one viewer, [id => status]
     * - two queries for the whole page, whatever its size:
     *   own       the viewer's own live order (partner_id = partner / user_id = customer)
     *   hired     held by another customer / partner (whereAlreadyHired(), the badge rule)
     *   available neither, and hireable now (isAvailable() + no CRM reservation_lock -
     *             the same check Hire Now makes)
     * A candidate that is none of these (e.g. held by the CRM reservation queue) is left out.
     * Cancelled orders (booking_status = 2) never count. $candidates: the page's Candidate models.
     */
    public static function hiringStatuses(iterable $candidates, ?int $partnerId = null, ?int $userId = null): array
    {
        if ($candidates instanceof \Illuminate\Pagination\AbstractPaginator) {
            $candidates = $candidates->getCollection();
        }
        $candidates = collect($candidates)->filter()->keyBy(fn ($c) => (int) $c->id);
        if ($candidates->isEmpty()) {
            return [];
        }
        $ids = $candidates->keys()->all();

        $own = ($partnerId || $userId)
            ? DB::table('bookings')->whereIn('cand_id', $ids)->where('booking_status', '!=', 2)
                ->where(fn ($q) => $q->when($partnerId, fn ($q) => $q->orWhere('partner_id', $partnerId))->when($userId, fn ($q) => $q->orWhere('user_id', $userId)))
                ->distinct()->pluck('cand_id')->map(fn ($id) => (int) $id)->all()
            : [];
        $hired = self::alreadyHiredIds($ids, $partnerId, $userId);

        $statuses = [];
        foreach ($candidates as $id => $candidate) {
            if (in_array($id, $own, true)) {
                $statuses[$id] = self::STATUS_OWN;
            } elseif (in_array($id, $hired, true)) {
                $statuses[$id] = self::STATUS_HIRED;
            } elseif (self::isAvailable($candidate) && empty($candidate->reservation_lock)) {
                $statuses[$id] = self::STATUS_AVAILABLE;
            }
        }

        return $statuses;
    }

    /** hiringStatuses() for whoever is looking at the page (viewer()). */
    public static function hiringStatusesForViewer(iterable $candidates): array
    {
        [$partnerId, $userId] = self::viewer();

        return self::hiringStatuses($candidates, $partnerId, $userId);
    }

    public static function isAvailable($candidate): bool
    {
        return (int) $candidate->status === 1 && (int) $candidate->publish === 1 && (int) $candidate->cv_execute === 1;
    }

    public static function allows(int $partnerId, $candidate): bool
    {
        return $candidate !== null
            && (int) ($candidate->isdelete ?? 0) === 0
            && (self::isAvailable($candidate) || self::booking($partnerId, (int) $candidate->id) !== null);
    }
}
