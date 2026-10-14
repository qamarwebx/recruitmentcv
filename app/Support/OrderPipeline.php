<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * The Partner Portal's order stages - the CRM booking workflow (Qamar Hire
 * CRM: Booking Detail stage bar + Candidate -> Status workflow) - for the
 * Orders card / table and the order detail stepper. Display only: every
 * state is read from persisted CRM data, nothing is advanced here.
 *
 *   Order Placed         the order exists
 *   Order Confirmed      bookings.booking_status = 1 (the CRM's "Confirmed")
 *   Visa Authorization   the order's visa details saved (bookings.visa_status
 *                        = 1, Assign Visa / "Add Visa Details") or the
 *                        workflow's Visa Received (cand_statuses.visa_received)
 *   Visa Stamped         cand_statuses.visa_stamped
 *   Immigration Cleared  cand_statuses.emigration_approved (Emigration Approved)
 *   Ticket Confirmed     cand_statuses.ticket_confirmed
 *   Deployed             cand_statuses.deployed
 *
 * The Candidate Status workflow (cand_statuses, one row per candidate, set by
 * the CRM's CandidateController stage actions) counts for an order only when
 * the order is not cancelled AND the workflow's Recruitment Partner
 * (cand_statuses.partneroffice_id - required at Selected / Visa Received) is
 * the order's own partner: a candidate's progress or documents for another
 * partner's / customer's order are never shown.
 *
 * Stage documents (DOCUMENT_LABELS): the candidatefiles rows each workflow
 * stage action writes (CandidateController::storeCandidateFile() labels),
 * listed for a COMPLETED stage only and never used to decide a stage.
 * A cancelled order (booking_status = 2) shows its done stages and no current one.
 */
final class OrderPipeline
{
    public const STAGES = ['placed', 'confirmed', 'visa_authorization', 'visa_stamped', 'immigration_cleared', 'ticket_confirmed', 'deployed'];

    /** Workflow flag per stage (cand_statuses column). */
    private const WORKFLOW_FLAGS = [
        'visa_authorization' => 'visa_received',
        'visa_stamped' => 'visa_stamped',
        'immigration_cleared' => 'emigration_approved',
        'ticket_confirmed' => 'ticket_confirmed',
        'deployed' => 'deployed',
    ];

    /**
     * Documents per stage = the candidatefiles labels its CRM stage action stores:
     *   visastampedupdate / emigrationapprovedupdate -> 'emegartion pdf' (one shared file)
     *   waitingforticketupdate (required by ticketconfirmedupdate) -> 'flight ticket'
     *   deployedupdate -> 'Candidate Work Agreement'
     */
    public const DOCUMENT_LABELS = [
        'visa_stamped' => ['emegartion pdf'],
        'immigration_cleared' => ['emegartion pdf'],
        'ticket_confirmed' => ['flight ticket'],
        'deployed' => ['Candidate Work Agreement'],
    ];

    /**
     * The activities headlines the CRM stage actions write when a stage is SET
     * (CandidateController visareceived / visastamped / appliedforemigration
     * "Not Required" / emigrationapproved / ticketconfirmed / deployed) - the
     * only persisted time of each workflow stage (cand_statuses has no per-stage
     * dates). "Visa Receieved" is an older spelling still in the data.
     */
    private const STAGE_HEADLINES = [
        'visa_authorization' => ['Update Status Visa Received', 'Update Status Visa Receieved'],
        'visa_stamped' => ['Update Status Visa Stamped'],
        'immigration_cleared' => ['Update Status Emigration Approved', 'Update Status Applied For Emigration - Not Required'],
        'ticket_confirmed' => ['Update Status Ticket Confirmed'],
        'deployed' => ['Update Status Deployed'],
    ];

    /** Workflow rows by candidate id (preload() / workflow()). */
    private static array $workflows = [];

    /** One query for a page of orders (the Orders list) instead of one per order. */
    public static function preload(iterable $orders): void
    {
        $ids = [];
        foreach ($orders as $order) {
            $id = (int) ($order->cand_id ?? 0);
            if ($id > 0 && !array_key_exists($id, self::$workflows)) {
                $ids[$id] = true;
            }
        }
        if (!$ids) {
            return;
        }
        $rows = DB::table('cand_statuses')->whereIn('cand_id', array_keys($ids))->orderBy('id')->get()->unique('cand_id')->keyBy('cand_id');
        foreach (array_keys($ids) as $id) {
            self::$workflows[$id] = $rows->get($id);
        }
    }

    /** Forget preloaded rows (a fresh read). */
    public static function flush(): void
    {
        self::$workflows = [];
    }

    /**
     * The candidate's CRM workflow row when it belongs to this order (not
     * cancelled, Recruitment Partner = the order's partner), else null.
     */
    public static function workflow(object $order): ?object
    {
        $partnerId = (int) ($order->partner_id ?? 0);
        $candId = (int) ($order->cand_id ?? 0);
        if ((int) ($order->booking_status ?? 0) === 2 || $partnerId <= 0 || $candId <= 0) {
            return null;
        }
        if (!array_key_exists($candId, self::$workflows)) {
            self::$workflows[$candId] = DB::table('cand_statuses')->where('cand_id', $candId)->orderBy('id')->first();
        }
        $row = self::$workflows[$candId];

        return $row && (int) $row->partneroffice_id === $partnerId ? $row : null;
    }

    /**
     * ['stages' => [key => ['label', 'short', 'state']], 'step' => 1..7, 'total' => 7,
     *  'summary' => text, 'tone' => info|warning|attention|success|critical,
     *  'cancelled' => bool, 'workflow' => linked cand_statuses row or null]
     * state: done | current | pending | cancelled
     */
    public static function for(object $order): array
    {
        $cancelled = (int) ($order->booking_status ?? 0) === 2;
        $workflow = self::workflow($order);
        $flag = fn (string $stage) => $workflow !== null && (int) ($workflow->{self::WORKFLOW_FLAGS[$stage]} ?? 0) === 1;

        $done = [
            'placed' => true,
            'confirmed' => (int) ($order->booking_status ?? 0) === 1,
            'visa_authorization' => (bool) ($order->visa_status ?? false) || $flag('visa_authorization'),
            'visa_stamped' => $flag('visa_stamped'),
            'immigration_cleared' => $flag('immigration_cleared'),
            'ticket_confirmed' => $flag('ticket_confirmed'),
            'deployed' => $flag('deployed'),
        ];
        $labels = [
            'placed' => [__('locale.Order Placed'), __('locale.Placed')],
            'confirmed' => [__('locale.Order Confirmed'), __('locale.Confirmed')],
            'visa_authorization' => [__('locale.Visa Authorization'), __('locale.Visa auth.')],
            'visa_stamped' => [__('locale.Visa Stamped'), __('locale.Stamped')],
            'immigration_cleared' => [__('locale.Immigration Cleared'), __('locale.Immigration')],
            'ticket_confirmed' => [__('locale.Ticket Confirmed'), __('locale.Ticket')],
            'deployed' => [__('locale.Deployed'), __('locale.Deployed')],
        ];

        // The current stage: the first stage not done yet (none once cancelled).
        $current = null;
        foreach (self::STAGES as $key) {
            if (!$done[$key]) {
                $current = $key;
                break;
            }
        }

        $stages = [];
        foreach (self::STAGES as $key) {
            $state = $done[$key] ? 'done' : ($cancelled ? 'cancelled' : ($key === $current ? 'current' : 'pending'));
            $stages[$key] = ['label' => $labels[$key][0], 'short' => $labels[$key][1], 'state' => $state];
        }

        $position = array_flip(self::STAGES);
        $pending = [
            'confirmed' => __('locale.Awaiting confirmation'),
            'visa_authorization' => __('locale.Visa pending'),
            'visa_stamped' => __('locale.Visa stamping pending'),
            'immigration_cleared' => __('locale.Immigration pending'),
            'ticket_confirmed' => __('locale.Ticket pending'),
            'deployed' => __('locale.Deployment pending'),
        ];
        if ($cancelled) {
            $summary = __('locale.Cancelled');
            $tone = 'critical';
            $step = $current ? $position[$current] + 1 : count(self::STAGES);
        } elseif ($current === null) {
            $summary = __('locale.Deployed');
            $tone = 'success';
            $step = count(self::STAGES);
        } else {
            $summary = $pending[$current] ?? '';
            $tone = in_array($current, ['confirmed', 'visa_authorization'], true) ? 'warning' : 'attention';
            $step = $position[$current] + 1;
        }

        return ['stages' => $stages, 'step' => $step, 'total' => count(self::STAGES), 'summary' => $summary, 'tone' => $tone, 'cancelled' => $cancelled, 'workflow' => $workflow];
    }

    /**
     * Documents of the order's COMPLETED stages, from the linked workflow only:
     * [stage key => [['id', 'label', 'name', 'ext', 'uploaded_at'], ...]], newest
     * first, files that exist on disk only. Empty when the workflow is not this
     * order's. A document never changes a stage.
     */
    public static function documents(object $order, ?array $pipeline = null): array
    {
        $pipeline ??= self::for($order);
        if ($pipeline['workflow'] === null) {
            return [];
        }

        $labels = [];
        foreach ($pipeline['stages'] as $key => $stage) {
            if ($stage['state'] !== 'done') {
                continue;
            }
            foreach (self::DOCUMENT_LABELS[$key] ?? [] as $label) {
                $labels[$label][] = $key;
            }
        }
        if (!$labels) {
            return [];
        }

        $files = DB::table('candidatefiles')
            ->where('cand_id', (int) $order->cand_id)
            ->whereIn('label', array_keys($labels))
            ->orderByDesc('id')
            ->get(['id', 'label', 'filename', 'created_at']);

        $titles = [
            'emegartion pdf' => __('locale.Emigration / visa stamping copy'),
            'flight ticket' => __('locale.Flight ticket'),
            'Candidate Work Agreement' => __('locale.Work agreement'),
        ];

        $documents = [];
        foreach ($files as $file) {
            $name = (string) $file->filename;
            if (!self::filePath($name)) {
                continue;
            }
            foreach ($labels[$file->label] as $stageKey) {
                $documents[$stageKey][] = [
                    'id' => (int) $file->id,
                    'label' => $titles[$file->label] ?? $file->label,
                    'name' => $name,
                    'ext' => strtolower(pathinfo($name, PATHINFO_EXTENSION)),
                    'uploaded_at' => $file->created_at,
                ];
            }
        }

        // In stage order.
        $ordered = [];
        foreach (self::STAGES as $key) {
            if (!empty($documents[$key])) {
                $ordered[$key] = $documents[$key];
            }
        }

        return $ordered;
    }

    /** Absolute path of a candidate file (the CRM candidate folder, shared with this site), null when missing / not a plain name. */
    public static function filePath(string $name): ?string
    {
        if ($name === '' || basename($name) !== $name || str_starts_with($name, '.')) {
            return null;
        }
        $path = public_path('admin/assets/images/candidate/' . $name);

        return is_file($path) ? $path : null;
    }

    /**
     * When each COMPLETED stage happened - persisted data only, nothing guessed:
     *   Order Placed        bookings.booking_date
     *   Order Confirmed     bookings.partner_order_confirm_date (when set)
     *   Visa Authorization  the order's visa details first saved ($order->visa_created_at),
     *                       else the linked workflow's Visa Received time
     *   workflow stages     the latest STAGE_HEADLINES activity of the candidate at or
     *                       after this order was placed (linked workflow only)
     * [stage => datetime string]; a stage without a recorded time is left out.
     */
    public static function stageTimes(object $order, ?array $pipeline = null): array
    {
        $pipeline ??= self::for($order);
        $done = fn (string $key) => ($pipeline['stages'][$key]['state'] ?? '') === 'done';

        $times = [];
        if (!empty($order->booking_date)) {
            $times['placed'] = $order->booking_date;
        }
        if ($done('confirmed') && !empty($order->partner_order_confirm_date)) {
            $times['confirmed'] = $order->partner_order_confirm_date;
        }
        if ($done('visa_authorization') && !empty($order->visa_created_at)) {
            $times['visa_authorization'] = $order->visa_created_at;
        }

        if ($pipeline['workflow'] !== null) {
            $wanted = [];
            foreach (self::STAGE_HEADLINES as $key => $headlines) {
                if ($done($key) && !isset($times[$key])) {
                    foreach ($headlines as $headline) {
                        $wanted[$headline] = $key;
                    }
                }
            }
            if ($wanted) {
                $latest = DB::table('activities')
                    ->where('cand_id', (int) $order->cand_id)
                    ->whereIn('headline', array_keys($wanted))
                    ->when(!empty($order->created_at), fn ($q) => $q->where('created_at', '>=', $order->created_at))
                    ->groupBy('headline')
                    ->selectRaw('headline, MAX(created_at) as at')
                    ->pluck('at', 'headline');
                foreach ($latest as $headline => $at) {
                    $key = $wanted[$headline];
                    if (!isset($times[$key]) || $at > $times[$key]) {
                        $times[$key] = $at;
                    }
                }
            }
        }

        return $times;
    }

    /**
     * Fingerprint of everything the order detail page shows: the order row
     * (booking + candidate + visa fields, incl. payment and timestamps), the
     * linked workflow row (flags, flight, updated_at - every CRM stage change
     * touches it) and the stage documents present. Same value = nothing to
     * refresh (PartnerPortalController::orderLive()).
     */
    public static function version(object $order): string
    {
        $pipeline = self::for($order);
        $documents = self::documents($order, $pipeline);

        return sha1(json_encode([(array) $order, $pipeline['workflow'] ? (array) $pipeline['workflow'] : null, $documents]));
    }

    /** The flight booked at Waiting For Flight Ticket (linked workflow only): ['from', 'to', 'at'] or null. */
    public static function flight(?object $workflow): ?array
    {
        if (!$workflow || (!$workflow->flight_from_city && !$workflow->flight_to_city && !$workflow->flight_datetime)) {
            return null;
        }
        $cities = DB::table('cities')->whereIn('id', array_filter([$workflow->flight_from_city, $workflow->flight_to_city]))->pluck('name', 'id');

        return [
            'from' => $workflow->flight_from_city ? ($cities[$workflow->flight_from_city] ?? null) : null,
            'to' => $workflow->flight_to_city ? ($cities[$workflow->flight_to_city] ?? null) : null,
            'at' => $workflow->flight_datetime,
        ];
    }

    /**
     * The order amount to show: the amount the CRM recorded with the payment
     * (bookingpayments.amount of the booking's payment record, selected as
     * paid_amount - what CRM Booking Detail -> Payment Details -> Amount shows),
     * else the order's own amount (bookings.amount); null = none (callers show
     * their "—" / "Not set"). Shown as stored - no conversion or calculation.
     */
    public static function amount(object $order): ?string
    {
        foreach ([$order->paid_amount ?? null, $order->amount ?? null] as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    /**
     * The order amount for display, in SAR: "100 SAR", "2,000 SAR" - see
     * PaymentAmount::format() (shared with the CRM). Null = no amount
     * (callers show "—").
     */
    public static function amountLabel(object $order): ?string
    {
        return PaymentAmount::format(self::amount($order));
    }

    /** Initials for an avatar without a photo ("Ansari Mohammad Furkan" -> "AM"). */
    public static function initials(?string $name): string
    {
        $words = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);

        return mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2)))) ?: '?';
    }
}
