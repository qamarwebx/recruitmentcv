<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\PartnerWebsiteVisitorSaveFilter;
use App\Models\WebsiteVisitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Partner Portal -> Website Visitor: the logged-in partner's own website
 * visits, read from the SAME website_visitors records the CRM's Website
 * Visitor page lists (recorded by TrackWebsiteVisitor - unchanged). Every
 * query starts from partner_id = the partner guard's id; no partner/website
 * value from the request is ever used. Filters mirror the CRM's (minus
 * Website/Partner, which are fixed to this partner).
 */
class PartnerWebsiteVisitorController extends Controller
{
    /** Same device values as the CRM's WebsiteVisitorController::DEVICES. */
    public const DEVICES = ['desktop' => 'Desktop', 'mobile' => 'Mobile', 'tablet' => 'Tablet'];

    private function partnerId(): int
    {
        return Auth::guard('partner')->id();
    }

    /** This partner's visits only. */
    private function visits()
    {
        return WebsiteVisitor::where('partner_id', $this->partnerId());
    }

    public function index(Request $request)
    {
        // First page load uses the saved filter; the AJAX filter/search/
        // pagination requests carry their own values.
        if ($request->ajax() || $request->hasAny(array_merge(PartnerWebsiteVisitorSaveFilter::FIELDS, ['search', 'page']))) {
            $filters = $this->filters($request->all());
        } else {
            $saved = PartnerWebsiteVisitorSaveFilter::where('partner_id', $this->partnerId())->first();
            $filters = $this->filters($saved ? $saved->only(PartnerWebsiteVisitorSaveFilter::FIELDS) : []);
        }
        $search = mb_substr(trim((string) $request->input('search')), 0, 100);

        $query = $this->filtered($filters, $search);
        $uniqueVisitors = (clone $query)->distinct()->count('visitor_hash');
        // Chart View: built from this same filtered query (all matching
        // visits, not just this page), so chart and table always agree.
        $chart = $this->chartData($query, $filters);

        $visits = $query
            ->select(['id', 'host', 'path', 'referrer', 'ip_address', 'user_id', 'browser', 'platform', 'device', 'visited_at'])
            ->with('user:id,name,email')
            ->orderByDesc('visited_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        if ($request->ajax()) {
            return view('worker.partner.website-visitors.partial', compact('visits', 'uniqueVisitors', 'chart'));
        }

        $browsers = $this->visits()->whereNotNull('browser')->distinct()->orderBy('browser')->pluck('browser');
        $devices = self::DEVICES;

        return view('worker.partner.website-visitors.index', compact('visits', 'uniqueVisitors', 'chart', 'filters', 'search', 'browsers', 'devices'));
    }

    /**
     * Chart View aggregates over the filtered visits ($query is cloned per
     * aggregate, never modified): visits + unique visitors over time (daily,
     * or monthly past ~3 months; gaps filled with 0 across the filter's date
     * range or the data's own span), and top pages / devices / traffic
     * sources (referrer host, "Direct" when none; the tail folded into
     * "Other"). Labels are visitor data - the chart script escapes them.
     */
    private function chartData($query, array $filters): array
    {
        $rows = (clone $query)
            ->selectRaw('visited_on as d, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as uniques')
            ->groupBy('visited_on')->orderBy('visited_on')->get();

        if ($filters['by_custom_date']) {
            [$from, $to] = array_map(fn ($d) => Carbon::createFromFormat('m/d/Y', trim($d))->startOfDay(), explode(' - ', $filters['by_custom_date']));
            if ($from->gt($to)) {
                [$from, $to] = [$to, $from];
            }
        } elseif ($rows->isNotEmpty()) {
            [$from, $to] = [Carbon::parse($rows->first()->d), Carbon::parse($rows->last()->d)];
        } else {
            $from = $to = null;
        }

        $labels = $visits = $uniques = [];
        $monthly = $from && $from->diffInDays($to) > 92;
        if ($monthly) {
            $byMonth = (clone $query)
                ->selectRaw("DATE_FORMAT(visited_on, '%Y-%m') as m, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as uniques")
                ->groupBy('m')->get()->keyBy('m');
            for ($month = $from->copy()->startOfMonth(); $month->lte($to); $month->addMonth()) {
                $row = $byMonth->get($month->format('Y-m'));
                $labels[] = $month->translatedFormat('M Y');
                $visits[] = (int) ($row->visits ?? 0);
                $uniques[] = (int) ($row->uniques ?? 0);
            }
        } elseif ($from) {
            $byDay = $rows->keyBy(fn ($r) => Carbon::parse($r->d)->toDateString());
            for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
                $row = $byDay->get($day->toDateString());
                $labels[] = $day->translatedFormat('d M');
                $visits[] = (int) ($row->visits ?? 0);
                $uniques[] = (int) ($row->uniques ?? 0);
            }
        }

        $top = function ($counts, int $limit) {
            $counts = collect($counts)->sortDesc();
            $shown = $counts->take($limit);
            $rest = $counts->slice($limit)->sum();
            if ($rest > 0) {
                $shown[__('locale.Other')] = $rest;
            }

            return ['labels' => $shown->keys()->map(fn ($k) => (string) $k)->values()->all(), 'values' => $shown->values()->map(fn ($v) => (int) $v)->all()];
        };

        $pages = (clone $query)->selectRaw('path, COUNT(*) as c')->groupBy('path')->pluck('c', 'path');

        $devices = [];
        foreach ((clone $query)->selectRaw('device, COUNT(*) as c')->groupBy('device')->get() as $row) {
            $label = isset(self::DEVICES[$row->device]) ? __('locale.' . self::DEVICES[$row->device]) : __('locale.Unknown');
            $devices[$label] = ($devices[$label] ?? 0) + (int) $row->c;
        }

        $sources = [];
        foreach ((clone $query)->selectRaw('referrer, COUNT(*) as c')->groupBy('referrer')->get() as $row) {
            $host = $row->referrer ? strtolower((string) parse_url($row->referrer, PHP_URL_HOST)) : '';
            $label = $host !== '' ? preg_replace('/^www\./', '', $host) : __('locale.Direct');
            $sources[$label] = ($sources[$label] ?? 0) + (int) $row->c;
        }

        return [
            'granularity' => $monthly ? 'monthly' : 'daily',
            'trend' => ['labels' => $labels, 'visits' => $visits, 'uniques' => $uniques],
            'pages' => $top($pages, 7),
            'devices' => $top($devices, 3),
            'sources' => $top($sources, 6),
        ];
    }

    public function saveFilter(Request $request)
    {
        PartnerWebsiteVisitorSaveFilter::updateOrCreate(
            ['partner_id' => $this->partnerId()],
            $this->filters($request->all())
        );

        return response()->json(['status' => 'success', 'message' => __('locale.Filter saved successfully.')]);
    }

    public function resetFilter()
    {
        PartnerWebsiteVisitorSaveFilter::where('partner_id', $this->partnerId())
            ->update(array_fill_keys(PartnerWebsiteVisitorSaveFilter::FIELDS, null));

        return response()->json(['status' => 'success', 'message' => __('locale.Filter reset successfully.')]);
    }

    /**
     * The recognised filter values (same rules as the CRM's); anything
     * invalid is dropped (= no filter) rather than rejected.
     */
    private function filters(array $input): array
    {
        $rules = [
            'by_custom_date' => ['string', 'max:30', 'regex:#^\d{2}/\d{2}/\d{4} - \d{2}/\d{2}/\d{4}$#'],
            'by_visitor' => ['in:customers,guests'],
            'by_device' => ['in:' . implode(',', array_keys(self::DEVICES))],
            'by_browser' => ['string', 'max:50'],
        ];

        $filters = [];
        foreach ($rules as $field => $rule) {
            $value = $input[$field] ?? null;
            $filters[$field] = (filled($value) && Validator::make([$field => $value], [$field => $rule])->passes()) ? $value : null;
        }

        return $filters;
    }

    private function filtered(array $filters, string $search)
    {
        $query = $this->visits();

        if ($filters['by_custom_date']) {
            [$from, $to] = array_map(fn ($d) => Carbon::createFromFormat('m/d/Y', trim($d))->startOfDay(), explode(' - ', $filters['by_custom_date']));
            if ($from->gt($to)) {
                [$from, $to] = [$to, $from];
            }
            $query->whereBetween('visited_on', [$from->toDateString(), $to->toDateString()]);
        }

        if ($filters['by_visitor'] === 'customers') {
            $query->whereNotNull('user_id');
        } elseif ($filters['by_visitor'] === 'guests') {
            $query->whereNull('user_id');
        }

        if ($filters['by_device']) {
            $query->where('device', $filters['by_device']);
        }

        if ($filters['by_browser']) {
            $query->where('browser', $filters['by_browser']);
        }

        if ($search !== '') {
            $term = '%' . addcslashes($search, '%_\\') . '%';
            // Nested so it can never widen the partner_id scope above.
            $query->where(function ($q) use ($term) {
                $q->where('path', 'like', $term)
                    ->orWhere('ip_address', 'like', $term)
                    ->orWhere('browser', 'like', $term)
                    ->orWhere('platform', 'like', $term)
                    ->orWhere('referrer', 'like', $term)
                    ->orWhereIn('user_id', DB::table('users')->select('id')
                        ->where('email', 'like', $term)
                        ->orWhere('name', 'like', $term));
            });
        }

        return $query;
    }
}
