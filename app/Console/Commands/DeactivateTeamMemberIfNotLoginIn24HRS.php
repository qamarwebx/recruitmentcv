<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeactivateTeamMemberIfNotLoginIn24HRS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'team:deactivate-if-not-login-24hrs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate login access for team members who have not logged in within the last 24 hours.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();


        // Sunday skip
        if ($now->isSunday()) {
            $this->info('Skipped on Sunday.');
            Log::channel('deactivate_if_not_login_24hrs')->info('Team deactivation skipped because today is Sunday.');

            return self::SUCCESS;
        }

        // Monday = Saturday se compare (48 hrs), baaki days = 24 hrs
        $cutoff = $now->isMonday()
        ? $now->copy()->subDays(2)->setTime(7, 0, 0) // Saturday 01:00:00
        : $now->copy()->subDay()->setTime(7, 0, 0);  // Yesterday 01:00:00

        $admins = Admin::where('login_status', 1)
            ->where('user_type', '!=', 1)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_login_at')
                    ->orWhere('last_login_at', '<', $cutoff);
            })
            ->get(['id', 'name', 'working_email', 'last_login_at']);

        if ($admins->isEmpty()) {
            $this->info('No team members found for deactivation.');
            return self::SUCCESS;
        }

        // Log every account
        foreach ($admins as $admin) {
            Log::channel('deactivate_if_not_login_24hrs')->info('Deactivating inactive team member', [
                'id'            => $admin->id,
                'name'          => $admin->name,
                'email'         => $admin->working_email,
                'last_login_at' => $admin->last_login_at,
                'cutoff'        => $cutoff->toDateTimeString(),
            ]);
        }

        $count = Admin::whereIn('id', $admins->pluck('id'))
            ->update([
                'login_status' => 0,
                'updated_at'   => now(),
            ]);

        Log::channel('deactivate_if_not_login_24hrs')->info('Inactive team members deactivated successfully.', [
            'count' => $count,
            'ids'   => $admins->pluck('id')->toArray(),
        ]);

        $this->info("{$count} team member(s) deactivated successfully.");

        return self::SUCCESS;

    }
}