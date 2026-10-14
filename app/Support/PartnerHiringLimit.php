<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Partner Hiring Limit - twin file, identical in the CRM and RecruitmentCV.
 *
 * CRM -> Website -> Settings -> Partner Hiring Limit sets, per partner, the
 * most orders the partner may place per day, week and month. The Partner
 * Portal's Hire Now (Worker\PartnerHireController::store - the only place a
 * partner creates an order; "New Booking" leads there) refuses a new order
 * once ANY configured limit is used up. Existing orders are never touched.
 *
 * Stored like the other Website settings: partner_page_contents, page
 * 'hiring_limits', one row per partner (unique partner_id + page), content
 * {"daily": 5, "weekly": null, "monthly": 60, "updated_by": 2}.
 * A limit is a whole number >= 0: 0 = no orders allowed in that period;
 * blank (null) = no limit for that period. No row = no limits (as before).
 *
 * Counted: the partner's OWN orders - bookings.partner_id = the partner and
 * user_id = 0 (Hire Now's "partner order, no customer" marker; orders that
 * customers place on the partner's website are not the partner's hiring) -
 * that are not cancelled (booking_status 2), by created_at in the app
 * timezone: today, this week (Monday to Sunday), this calendar month. A
 * cancelled order frees its place; a failed or refused attempt never
 * creates a row, so it never counts. Read fresh on every call (no cache): a
 * changed limit applies to the very next attempt.
 */
final class PartnerHiringLimit
{
    public const PAGE = 'hiring_limits';

    public const PERIODS = ['daily', 'weekly', 'monthly'];

    /** Highest limit accepted. */
    public const MAX = 100000;

    /** Validation rules of the CRM form (blank = no limit). */
    public static function rules(): array
    {
        return array_fill_keys(self::PERIODS, ['nullable', 'integer', 'min:0', 'max:' . self::MAX]);
    }

    /** The partner's saved setting: limits [period => int|null] + who/when, or null when never set. */
    public static function config(int $partnerId): ?array
    {
        $row = DB::table('partner_page_contents')->where('partner_id', $partnerId)->where('page', self::PAGE)->first(['content', 'updated_at']);
        if (!$row) {
            return null;
        }
        $content = is_string($row->content) ? (json_decode($row->content, true) ?: []) : [];

        return [
            'limits' => self::normalize($content),
            'updated_by' => isset($content['updated_by']) ? (int) $content['updated_by'] : null,
            'updated_at' => $row->updated_at,
        ];
    }

    /** [period => int|null] - null = no limit. */
    public static function limits(int $partnerId): array
    {
        return self::config($partnerId)['limits'] ?? array_fill_keys(self::PERIODS, null);
    }

    /** Saved settings of many partners: [partner_id => config]. */
    public static function all(): array
    {
        $out = [];
        foreach (DB::table('partner_page_contents')->where('page', self::PAGE)->whereNotNull('partner_id')->get(['partner_id', 'content', 'updated_at']) as $row) {
            $content = is_string($row->content) ? (json_decode($row->content, true) ?: []) : [];
            $out[(int) $row->partner_id] = [
                'limits' => self::normalize($content),
                'updated_by' => isset($content['updated_by']) ? (int) $content['updated_by'] : null,
                'updated_at' => $row->updated_at,
            ];
        }

        return $out;
    }

    /** Saves one partner's limits (the same row is updated - never a second one). */
    public static function put(int $partnerId, array $limits, ?int $adminId = null): void
    {
        $content = self::normalize($limits);
        $content['updated_by'] = $adminId;
        $json = json_encode($content);

        DB::transaction(function () use ($partnerId, $json) {
            $row = DB::table('partner_page_contents')->where('partner_id', $partnerId)->where('page', self::PAGE)->lockForUpdate()->first(['id']);
            if ($row) {
                DB::table('partner_page_contents')->where('id', $row->id)->update(['content' => $json, 'updated_at' => now()]);
            } else {
                // partner_id + page is unique: a parallel first save fails here instead of adding a duplicate.
                DB::table('partner_page_contents')->insert(['partner_id' => $partnerId, 'page' => self::PAGE, 'content' => $json, 'created_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    /** Any limit set? */
    public static function hasLimits(array $limits): bool
    {
        return count(array_filter($limits, fn ($v) => $v !== null)) > 0;
    }

    /** [period => [start, next start (= when it resets)]] in the app timezone. */
    public static function windows(?Carbon $now = null): array
    {
        $now = $now ? $now->copy() : Carbon::now();
        $day = $now->copy()->startOfDay();
        $week = $now->copy()->startOfWeek(Carbon::MONDAY);
        $month = $now->copy()->startOfMonth();

        return [
            'daily' => [$day, $day->copy()->addDay()],
            'weekly' => [$week, $week->copy()->addWeek()],
            'monthly' => [$month, $month->copy()->addMonthNoOverflow()],
        ];
    }

    /** Orders counted in each period: [period => int] (one query). */
    public static function usage(int $partnerId, ?Carbon $now = null): array
    {
        $windows = self::windows($now);
        $from = $windows['weekly'][0]->lt($windows['monthly'][0]) ? $windows['weekly'][0] : $windows['monthly'][0];
        $select = [];
        $bindings = [];
        foreach ($windows as $period => [$start, $end]) {
            $select[] = "COALESCE(SUM(created_at >= ? AND created_at < ?), 0) AS {$period}";
            array_push($bindings, $start->toDateTimeString(), $end->toDateTimeString());
        }
        $row = self::counted($partnerId)->where('created_at', '>=', $from->toDateTimeString())->selectRaw(implode(', ', $select), $bindings)->first();

        return array_map(fn ($period) => (int) ($row->{$period} ?? 0), array_combine(self::PERIODS, self::PERIODS));
    }

    /** The orders that count (see the class comment). */
    public static function counted(int $partnerId)
    {
        return DB::table('bookings')->where('partner_id', $partnerId)->where('user_id', 0)->where('booking_status', '!=', 2);
    }

    /**
     * Limit and usage per period, for the pages and the check:
     * [period => [limit, used, remaining, reached, resets_at]] (limit/remaining null = no limit).
     * Orders are counted only when a limit is set (nothing to check otherwise),
     * unless $count asks for the usage anyway (the CRM tab shows it before any limit).
     */
    public static function status(int $partnerId, ?Carbon $now = null, bool $count = false): array
    {
        $limits = self::limits($partnerId);
        $windows = self::windows($now);
        $usage = $count || self::hasLimits($limits) ? self::usage($partnerId, $now) : array_fill_keys(self::PERIODS, 0);
        $out = [];
        foreach (self::PERIODS as $period) {
            $limit = $limits[$period];
            $out[$period] = [
                'limit' => $limit,
                'used' => $usage[$period],
                'remaining' => $limit === null ? null : max(0, $limit - $usage[$period]),
                'reached' => $limit !== null && $usage[$period] >= $limit,
                'resets_at' => $windows[$period][1],
            ];
        }

        return $out;
    }

    /**
     * Null when the partner may place another order now; otherwise the limit
     * that stops it - of the used-up ones, the one that resets last (when a
     * new order becomes possible again): [period, limit, used, resets_at, message, periods_reached].
     */
    public static function blocked(int $partnerId, ?Carbon $now = null, ?array $status = null): ?array
    {
        $status = $status ?? self::status($partnerId, $now);
        $reached = array_filter($status, fn ($s) => $s['reached']);
        if (!$reached) {
            return null;
        }
        $period = null;
        foreach (array_reverse(self::PERIODS) as $p) {   // monthly first wins a tie
            if (isset($reached[$p]) && ($period === null || $reached[$p]['resets_at']->gt($reached[$period]['resets_at']))) {
                $period = $p;
            }
        }

        return array_merge($reached[$period], ['period' => $period, 'message' => self::message($period), 'periods_reached' => array_keys($reached)]);
    }

    /**
     * Locks the partner's row for the rest of the transaction, so two orders
     * of one partner (any app, any tab) are counted one after the other.
     * Call it first in the order transaction (before locking bookings).
     */
    public static function lock(int $partnerId): void
    {
        DB::table('partners')->where('id', $partnerId)->lockForUpdate()->value('id');
    }

    /** "You have reached your monthly hiring limit." */
    public static function message(string $period): string
    {
        return __('locale.You have reached your ' . $period . ' hiring limit.');
    }

    public static function label(string $period): string
    {
        return __('locale.' . ucfirst($period));
    }

    /** [period => int|null] from form input / stored JSON: whole numbers 0..MAX, anything else = no limit. */
    private static function normalize(array $values): array
    {
        $out = [];
        foreach (self::PERIODS as $period) {
            $value = $values[$period] ?? null;
            if (is_string($value)) {
                $value = trim($value);
            }
            $out[$period] = (is_int($value) || (is_string($value) && ctype_digit($value))) && (int) $value <= self::MAX ? (int) $value : null;
        }

        return $out;
    }
}
