<?php

namespace App\Services\Dashboard;

use App\Models\Allcontact;
use App\Models\Allcontactnote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Read-only aggregate queries over the Contacts module (app/Models/Allcontact.php,
 * table `allcontacts`) for the CRM dashboard — mirrors the shape of
 * LeadDashboardService/TodoDashboardService/DealDashboardService.
 *
 * There is no dedicated "calls" table anywhere in this app — a call is a
 * Allcontactnote row with conversation_type='Call' (same pattern Leads and
 * Deal Pipeline use). "Calls Made" is scoped by the note's own creator
 * (admin_id) — a call is made by whoever logged it, not necessarily the
 * contact's assigned owner (careoff_id).
 */
class ContactDashboardService
{
    protected function baseQuery(?int $scopeAdminId)
    {
        $q = Allcontact::query();
        if ($scopeAdminId) {
            $q->where('careoff_id', $scopeAdminId);
        }
        return $q;
    }

    public function kpis(?int $scopeAdminId = null): array
    {
        return [
            'total_contacts' => $this->baseQuery($scopeAdminId)->count(),
        ];
    }

    /** "New Contacts Created" — created in the period, scoped by careoff_id. */
    public function createdTrend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        return $this->baseQuery($scopeAdminId)
            ->whereBetween('created_at', [$from, $to])
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd')->toArray();
    }

    /** "Calls Made" — scoped by the note's creator, not the contact's owner. */
    public function callsMadeTrend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        return Allcontactnote::where('conversation_type', 'Call')
            ->whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('admin_id', $scopeAdminId))
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd')->toArray();
    }
}
