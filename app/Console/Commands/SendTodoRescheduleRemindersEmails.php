<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\Admin;
use App\Mail\AssignedTodoReminderMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTodoRescheduleRemindersEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reminders:send-reschedule';

    /**
     * The console command description.
     */
    protected $description = 'Send rescheduled reminders based on reminder_before and reminder_at fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d H:i');
        
        // Convert $now back into a Carbon object that includes seconds
        $now = Carbon::parse($now);
        
        $tasks = Todo::whereNotNull('reminder_at')
            ->whereNotIn('task_status', ['Complete', 'Achieved'])
            ->whereBetween('reminder_at', [
                $now->copy()->startOfMinute(),
                $now->copy()->endOfMinute()
            ])
            ->get();

        foreach ($tasks as $task) {
            Log::info("📧 Sending Reschedule Reminder for Task ID: {$task->id}");

            $assignees = Admin::whereIn('id', explode(",", $task->assignto_id))
                              ->where('status', 1)
                              ->get();

            foreach ($assignees as $assignee) {
                Mail::to($assignee->working_email)->send(new AssignedTodoReminderMail($task, $assignee->name));
            }

            // Mark that the reminder was sent to avoid duplicates
            $task->save();
        }

        $this->info('✅ Reschedule reminders completed.');
    }
}
