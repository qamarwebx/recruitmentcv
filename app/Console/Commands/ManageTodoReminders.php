<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\Admin;
use App\Models\Metawhatsappapi;
use App\Models\Todoreminderresponse;
use App\Mail\AssignedTodoReminderMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ManageTodoReminders extends Command
{
    protected $signature = 'reminders:ManageTodoReminders';
    protected $description = 'Check and trigger OneTime, Recurring, and Custom reminders from the Todo list.';

    public function handle()
    {
        $nowGlobal = Carbon::now();
        Log::channel('todo_reminder_logs')->info("🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁  Reminder Check Running Start at {$nowGlobal} 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 ");

        /* ============================================================
         * 1️⃣ ONE-TIME REMINDERS
         * ============================================================ */
        $oneTimeTasks = Todo::where('reminder_type', 'OneTime')
            ->whereNotNull('scheduled_date_time')
            ->whereNotIn('task_status', ['Complete', 'Achieved'])
            ->where('scheduled_status', true)
            ->get();

        foreach ($oneTimeTasks as $task) {

            $now = Carbon::now()->format('Y-m-d H:i');
            $scheduled = Carbon::parse($task->scheduled_date_time)->format('Y-m-d H:i');
            
            if ($now === $scheduled) {
        
                Log::channel('todo_reminder_logs')
                    ->info("🟩 OneTime Reminder Triggered for Task Title: {$task->task_title}");
        
                $this->sendReminder($task);
        
                // 🔒 lock forever
                $task->scheduled_status = false;
                $task->save();
            }
        }
        

        /* ============================================================
         * 2️⃣ RECURRING REMINDERS
         * ============================================================ */
        $recurringTasks = Todo::where('reminder_type', 'Recurring')
            ->whereNotIn('task_status', ['Complete', 'Achieved'])
            ->get();

        foreach ($recurringTasks as $task) {
            $now = Carbon::now();

            switch ($task->recurring_type) {

                /* --------------------------------------------------------
                 * DAILY — MULTIPLE TIMES
                 * -------------------------------------------------------- */
                case 'Daily':
                    $times = $this->normalizeTimes($task->recurring_time);

                    if (empty($times)) {
                        Log::channel('todo_reminder_logs')->warning("⚠️ Daily reminder skipped: no valid times for Task Title {$task->task_title}");
                        break;
                    }

                    foreach ($times as $time) {

                        try {
                            $time = $this->fixTimeFormat($time);
                            [$hour, $minute] = explode(':', $time);

                            $scheduledToday = Carbon::create(
                                $now->year, $now->month, $now->day,
                                intval($hour), intval($minute)
                            );

                            if ($now->isSameMinute($scheduledToday)) {
                                Log::channel('todo_reminder_logs')->info("🟩 Daily Reminder Triggered for Task Title: {$task->task_title} at {$time}");
                                $this->sendReminder($task);
                            }
                        } catch (\Throwable $e) {
                            Log::channel('todo_reminder_logs')->error("❌ Error parsing daily time '{$time}' for Task Title {$task->task_title}: {$e->getMessage()}");
                        }
                    }
                    break;

                /* --------------------------------------------------------
                 * WEEKLY
                 * -------------------------------------------------------- */
                case 'Weekly':
                    $weekdays = $task->recurring_weekdays
                        ? json_decode($task->recurring_weekdays, true)
                        : [];

                    if (!is_array($weekdays)) {
                        $weekdays = array_filter(array_map(
                            'trim',
                            explode(',', str_replace(['[', ']', '"'], '', $task->recurring_weekdays))
                        ));
                    }

                    $times = $this->normalizeTimes($task->recurring_time);
                    $todayShort = $now->format('D');

                    if (in_array($todayShort, $weekdays)) {
                        foreach ($times as $time) {

                            try {
                                $time = $this->fixTimeFormat($time);
                                [$hour, $minute] = explode(':', $time);

                                $scheduledToday = Carbon::create(
                                    $now->year, $now->month, $now->day,
                                    intval($hour), intval($minute)
                                );

                                if ($now->isSameMinute($scheduledToday)) {
                                    Log::channel('todo_reminder_logs')->info("🟦 Weekly Reminder Triggered for Task Title: {$task->task_title} at {$time}");
                                    $this->sendReminder($task);
                                }
                            } catch (\Throwable $e) {
                                Log::channel('todo_reminder_logs')->error("❌ Error parsing weekly time '{$time}' for Task Title {$task->task_title}: {$e->getMessage()}");
                            }
                        }
                    }
                    break;

                /* --------------------------------------------------------
                 * MONTHLY
                 * -------------------------------------------------------- */
                case 'Monthly':
                    $timeStr = $this->fixTimeFormat($this->getPrimaryTimeString($task->recurring_time));

                    if ($task->recurring_month_day) {
                        if ($now->day == intval($task->recurring_month_day)) {

                            [$hour, $minute] = explode(':', $timeStr);

                            $scheduledToday = Carbon::create(
                                $now->year, $now->month, $now->day,
                                intval($hour), intval($minute)
                            );

                            if ($now->isSameMinute($scheduledToday)) {
                                Log::channel('todo_reminder_logs')->info("🟨 Monthly Reminder Triggered for Task Title: {$task->task_title} at {$timeStr}");
                                $this->sendReminder($task);
                            }
                        }
                    }
                    break;

                /* --------------------------------------------------------
                 * YEARLY
                 * -------------------------------------------------------- */
                case 'Yearly':
                    if ($task->recurring_year_month_day) {
                        [$month, $day] = explode('-', $task->recurring_year_month_day);
                        $timeStr = $this->fixTimeFormat($this->getPrimaryTimeString($task->recurring_time));

                        [$hour, $minute] = explode(':', $timeStr);

                        if ($now->month == intval($month) && $now->day == intval($day)) {

                            $scheduledToday = Carbon::create(
                                $now->year, $now->month, $now->day,
                                intval($hour), intval($minute)
                            );

                            if ($now->isSameMinute($scheduledToday)) {
                                Log::channel('todo_reminder_logs')->info("🎉 Yearly Reminder Triggered for Task Title: {$task->task_title} at {$timeStr}");
                                $this->sendReminder($task);
                            }
                        }
                    }
                    break;
            }
        }

        /* ============================================================
        * 3️⃣ CUSTOM REMINDERS
        * ============================================================ */
        $customTasks = Todo::where('reminder_type', 'Custom')
        ->whereNotNull('custom_start_date')
        ->whereNotNull('custom_end_date')
        ->whereNotNull('custom_time')
        ->whereNotIn('task_status', ['Complete', 'Achieved'])
        ->get();

        foreach ($customTasks as $task) {

            $time = $this->fixTimeFormat($task->custom_time);
            if ($this->checkCustomTrigger($task)) {
                Log::channel('todo_reminder_logs')->info("🟦 Custom Reminder TRIGGERED for Task Title: {$task->task_title}");
                $this->sendReminder($task);
            }
        }

        Log::channel('todo_reminder_logs')->info("🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁  Reminder Check Running Complete 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 🔁 ");

    }

    private function checkCustomTrigger($task)
    {
        $now = Carbon::now();
        $start = Carbon::parse($task->custom_start_date)->startOfDay();
        $end = Carbon::parse($task->custom_end_date)->endOfDay();

        // Normalize time HH:MM or HH:MM:SS
        $time = $this->fixTimeFormat($task->custom_time);

        [$hour, $minute] = explode(':', $time);

        $trigger = Carbon::today()->setHour($hour)->setMinute($minute)->setSecond(0);

        // 🔥 Allow 0-59 seconds window
        if ($now->between($start, $end) && $now->diffInSeconds($trigger) <= 59) {
            return true;
        }

        return false;
    }


    /* ===============================================================
     * HELPER FUNCTIONS
     * =============================================================== */

    private function sendReminder(Todo $task)
    {
        $types = $task->notification_type ?? [];

        $assignees = Admin::whereIn('id', explode(",", $task->assignto_id))
            ->where('status', 1)
            ->get();

        foreach ($assignees as $assignee) {

            // 📲 WhatsApp
            if (in_array('whatsapp', $types)) {
                $this->sendWhatsappReminder($task, $assignee);
            } else {
                Log::channel('todo_reminder_logs')
                    ->info("⏭️ Skipping WhatsApp for Task: {$task->task_title}");
            }

            // 📧 Email
            if (in_array('email', $types)) {
                Mail::to($assignee->working_email)
                    ->send(new AssignedTodoReminderMail($task, $assignee->name));
            } else {
                Log::channel('todo_reminder_logs')
                    ->info("⏭️ Skipping Email for Task: {$task->task_title}");
            }
        }

        // Support Team WhatsApp Reminder
        if (!empty($task->support_team_number) && in_array('whatsapp', $types)) {

            $assignee = (object) [
                'name'        => $task->support_team_name,
                'work_number' => $task->support_team_number,
                'phone'       => $task->support_team_number,
            ];

            $this->sendWhatsappReminder($task, $assignee);
        }
    }

    private function sendWhatsappReminder($task, $assignee)
    {
        $assigned_by = optional(
            Admin::find($task->admin_id)
        )->name ?? 'Admin';        

        $metaAPI = Metawhatsappapi::whereRaw(
            "FIND_IN_SET (?, api_assign_to)",
            ['todo_notification']
        )->first();

        if (!$metaAPI) {
            Log::channel('todo_reminder_logs')->error("WhatsApp API config not found.");
            return;
        }

        $endpoint_api = $metaAPI->api_base_url . '/' . $metaAPI->vendor_uid . '/contact/send-template-message';
        $token = "Authorization: Bearer " . $metaAPI->api_access_token;

        $data = [
            'template_name'     => "reminder_2",
            'template_language' => "en",
            'phone_number'      => $assignee->work_number ?? '---',
            'field_1'           => $assignee->name ?? '---',
            'field_2'           => $task->task_title ?? '---',
            'field_3' =>        $this->sanitizeWhatsappParam($task->task_description),
            'field_4'           => $task->finish_on ?? '---',
            'field_5'           => $assigned_by,
            'button_0'          => "admin/todo?open_todo_model=true&todo_model_task_id=" . $task->id,
            'contact'           => [
                'first_name'   => $assignee->name ?? '---',
                'last_name'    => "--",
                'email'        => $assignee->working_email ?? '---',
                'country'      => "India",
                'language_code'=> "en"
            ]
        ];

        Log::channel('todo_reminder_logs')->info("📨 WhatsApp Payload", $data);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', $token],
            CURLOPT_URL            => $endpoint_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($data),
        ]);

        $response  = curl_exec($curl);
        curl_close($curl);

        $responseGet = json_decode($response);

        Log::channel('todo_reminder_logs')->info("📨 responseGet", [$responseGet]);

        $log = new Todoreminderresponse();
        $log->todo_id         = $task->id;
        $log->response_for    = "Whatsapp Reminder";
        $log->name            = $assignee->name ?? null;
        $log->mobile_no       = $assignee->phone ?? null;

        if (isset($responseGet->result)) {
            $log->message_status = $responseGet->result;
            $log->message_text   = $responseGet->message;
        } else {
            $log->message_status = "Failed";
            $log->message_text   = $responseGet->message ?? "Unknown error";
            $log->error_data_field = json_encode($responseGet->errors ?? []);
        }

        $log->save();
    }

    /* Convert HH:MM:SS → HH:MM */
    private function fixTimeFormat($time)
    {
        $parts = explode(':', $time);

        if (count($parts) >= 2) {
            return sprintf('%02d:%02d', intval($parts[0]), intval($parts[1]));
        }

        return $time;
    }

    private function normalizeTimes($raw): array
    {
        $valid = [];

        if (empty($raw)) return $valid;

        if (is_array($raw)) {
            $candidate = $raw;
        }
        elseif (is_string($raw) && ($decoded = json_decode($raw, true)) && is_array($decoded)) {
            $candidate = $decoded;
        }
        else {
            $clean = str_replace(['[', ']', '"', '\\'], '', trim($raw));
            $candidate = array_filter(array_map('trim', explode(',', $clean)));
        }

        foreach ($candidate as $time) {
            if ($this->isValidTimeFormat($time)) {
                $valid[] = $this->fixTimeFormat($time);
            } else {
                Log::channel('todo_reminder_logs')->warning("⚠️ Invalid time '{$time}' ignored in normalizeTimes()");
            }
        }

        return $valid;
    }

    private function getPrimaryTimeString($raw): string
    {
        if (empty($raw)) return '00:00';

        if (is_array($raw)) {
            foreach ($raw as $candidate) {
                if ($this->isValidTimeFormat($candidate)) return $this->fixTimeFormat($candidate);
            }
            return '00:00';
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            foreach ($decoded as $candidate) {
                if ($this->isValidTimeFormat($candidate)) return $this->fixTimeFormat($candidate);
            }
        }

        if ($this->isValidTimeFormat($raw)) return $this->fixTimeFormat($raw);

        $clean = str_replace(['[', ']', '"'], '', $raw);
        foreach (explode(',', $clean) as $p) {
            $p = trim($p);
            if ($this->isValidTimeFormat($p)) return $this->fixTimeFormat($p);
        }

        return '00:00';
    }

    private function isValidTimeFormat($time): bool
    {
        return is_string($time) &&
            preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $time);
    }

    private function sanitizeWhatsappParam($text)
    {
        if (!$text) return '';

        // Remove new lines & tabs
        $text = preg_replace("/[\r\n\t]+/", " ", $text);

        // Remove more than 1 consecutive spaces
        $text = preg_replace('/\s{2,}/', ' ', $text);

        // Trim spaces
        return trim($text);
    }

}
