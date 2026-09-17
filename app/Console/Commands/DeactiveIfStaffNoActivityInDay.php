<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Todo;
use App\Models\TodoNote;
use App\Models\Admin;
use App\Mail\StaffNoActivityDeactivatedMail;

class DeactiveIfStaffNoActivityInDay extends Command
{
    protected $signature = 'staff:deactive-if-no-activity-today';
    protected $description = 'Deactivate staff if they have no todo activity today';

    public function handle()
    {
        // TODAY’s Date
        $today = Carbon::today();

        Log::channel('staff_inactivity')->info("⏳ Running at: {$today->toDateTimeString()}");

        // ⛔ RULE 1: If today is Sunday → SKIP
        if ($today->isSunday()) {
            Log::channel('staff_inactivity')->info("🚫 Today ({$today->toDateString()}) is Sunday — Skipping check.");
            return 0;
        }

        // RULE: For Monday – Saturday (10 PM)
        Log::channel('staff_inactivity')->info("📌 Checking staff activity for {$today->toDateString()}");

        // 1️⃣ Get today's reminder todos
        $todayTodos = Todo::filterTodayReminderTask(1)->get();

        if ($todayTodos->isEmpty()) {
            Log::channel('staff_inactivity')->info("ℹ️ No reminder todos for today.");
            return 0;
        }

        // 2️⃣ Allow processing ONLY between 10 PM and 11 PM
        $now = Carbon::now(); // uses app timezone

        $startTime = $today->copy()->setTime(22, 0, 0); // 10:00 PM
        $endTime   = $today->copy()->setTime(23, 0, 0); // 11:00 PM

        if ($now->lt($startTime) || $now->gte($endTime)) {
            Log::channel('staff_inactivity')->info(
                "⏱️ Current time {$now->format('H:i:s')} is outside 22:00–23:00 window — Skipping check."
            );
            return 0;
        }

        // 2️⃣ Build staff → todo list mapping
        $staffTodos = [];

        foreach ($todayTodos as $todo) {
            if (!$todo->assignto_id) continue;

            $assigned = explode(',', $todo->assignto_id);

            foreach ($assigned as $sid) {
                $sid = trim($sid);
                if (!is_numeric($sid)) continue;

                $staffTodos[$sid][] = $todo->id;
            }
        }

        if (empty($staffTodos)) {
            Log::channel('staff_inactivity')->info("ℹ️ No staff assigned today.");
            return 0;
        }

        $deactivated = [];
        $active = [];

        // 3️⃣ Check activity for each staff
        foreach ($staffTodos as $staffId => $todoIds) {

            $user = Admin::find($staffId);

            if (!$user) continue;

            if ($user->user_type == 1) {
                Log::channel('staff_inactivity')->info("⛔ Super Admin skipped: {$staffId}");
                continue;
            }

            // 4️⃣ Activity check for TODAY
            $hasActivity = TodoNote::whereIn('todo_id', $todoIds)
                ->whereDate('created_at', $today)
                ->exists();

            if ($hasActivity) {
                $active[] = $staffId;
                Log::channel('staff_inactivity')->info("✔ User {$staffId} HAS activity today.");
                continue;
            }

            // 5️⃣ Deactivate Staff
            $user->login_status = 0;
            $user->save();

            $deactivated[] = $staffId;

            Log::channel('staff_inactivity')->info("❌ User {$staffId} has NO activity → DEACTIVATED.");

            // 6️⃣ Send Mail
            if ($user->working_email) {
                Mail::to($user->working_email)->send(
                    new StaffNoActivityDeactivatedMail($user, $today->format('Y-m-d'), $todoIds)
                );

                Log::channel('staff_inactivity')->info("📧 Email sent to {$user->working_email}");
            }
        }

        Log::channel('staff_inactivity')->info("🟥 Deactivated Users: " . implode(',', $deactivated));
        Log::channel('staff_inactivity')->info("🟩 Active Users: " . implode(',', $active));
        Log::channel('staff_inactivity')->info("=== Staff Inactivity Check Completed ===");

        return 0;
    }
}
