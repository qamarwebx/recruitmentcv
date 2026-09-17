<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;

/**
 * Resolves the dashboard's common date-filter (today/yesterday/this_week/...)
 * into a concrete [from, to] Carbon pair. Shared by every dashboard widget so
 * the period picker behaves identically everywhere it appears.
 */
class DashboardDateRange
{
    public static function resolve(?string $range, ?string $from = null, ?string $to = null): array
    {
        $range = $range ?: 'this_month';
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'yesterday':
                $y = $now->copy()->subDay();
                return [$y->copy()->startOfDay(), $y->copy()->endOfDay()];
            case 'this_week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'this_year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            case 'custom':
                $f = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth();
                $t = $to ? Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                if ($f->gt($t)) {
                    [$f, $t] = [$t->copy()->startOfDay(), $f->copy()->endOfDay()];
                }
                return [$f, $t];
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
        }
    }
}
