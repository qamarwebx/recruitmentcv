<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;
use App\Models\Todo;
use App\Mail\AdminNoTodoLast24HoursDeactivatedMail;

class DeactivateAdminIfNoTodoLast24Hours extends Command
{
    protected $signature = 'admin:deactivate-no-todo-24h';
    protected $description = 'Deactivate admins who have not created any todo in the last 24 hours';

    public function handle()
    {
        // ⛔ Skip Sunday (extra safety)
        if (Carbon::now()->isSunday()) {
            Log::channel('DeactivateAdminIfNoTodoLast24Hours')
                ->info('🚫 Sunday detected — command skipped');
            return 0;
        }

        $checkFrom  = Carbon::now()->subHours(24);
        $todayDate = Carbon::now()->format('Y-m-d');
        $deactivatedAdmins = [];

        Log::channel('DeactivateAdminIfNoTodoLast24Hours')
            ->info('⏳ Admin inactivity check started', [
                'from' => $checkFrom->toDateTimeString()
            ]);

        // Only ACTIVE admins (skip super admin)
        $admins = Admin::where('login_status', 1)
            ->where('user_type', '!=', 1)
            ->get();

        foreach ($admins as $admin) {

            // Get todo IDs in last 24 hours
            $todoIds = Todo::where('admin_id', $admin->id)
                ->where('created_at', '>=', $checkFrom)
                ->pluck('id')
                ->toArray();

            // ❌ No todo found → deactivate
            if (empty($todoIds)) {

                Admin::where('id', $admin->id)
                    ->update(['login_status' => 0]);

                $deactivatedAdmins[] = $admin->id;

                // 📧 Send Mail
                if (!empty($admin->working_email)) {
                    try {
                        Mail::to($admin->working_email)->send(
                            new AdminNoTodoLast24HoursDeactivatedMail(
                                $admin,
                                $todayDate,
                                $todoIds
                            )
                        );
                
                        Log::channel('DeactivateAdminIfNoTodoLast24Hours')
                            ->info("📧 Email sent to {$admin->working_email}");
                    } catch (\Exception $e) {
                        Log::channel('DeactivateAdminIfNoTodoLast24Hours')
                            ->error("❌ Email failed for admin ID {$admin->id}", [
                                'error' => $e->getMessage()
                            ]);
                    }
                }                
            }
        }

        // 🔹 Consolidated logging
        if (!empty($deactivatedAdmins)) {
            Log::channel('DeactivateAdminIfNoTodoLast24Hours')
                ->warning('❌ Admin deactivated (no todo in 24h)', [
                    'ids' => $deactivatedAdmins
                ]);
        } else {
            Log::channel('DeactivateAdminIfNoTodoLast24Hours')
                ->info('✅ No admin deactivated');
        }

        Log::channel('DeactivateAdminIfNoTodoLast24Hours')
            ->info('🏁 Admin inactivity check completed');

        return 0;
    }
}
