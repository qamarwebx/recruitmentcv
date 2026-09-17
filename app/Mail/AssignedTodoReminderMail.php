<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Todo;

class AssignedTodoReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $assigneeName;

    /**
     * Create a new message instance.
     */
    public function __construct(Todo $task, string $assigneeName)
    {
        $this->task = $task;
        $this->assigneeName = $assigneeName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Reminder: Assigned Task - {$this->task->task_title}")
                    ->view('emails.assigned_todo_reminder')
                    ->with([
                        'taskId' => $this->task->id,
                        'taskTitle' => $this->task->task_title,
                        'taskDescription' => $this->task->task_description,
                        'assigneeName' => $this->assigneeName,

                        // NEW: Proper formatted reminder type
                        'reminderTypeLabel' => $this->getReminderTypeLabel(),

                        // NEW: Proper formatted schedule time
                        'scheduledTime' => $this->getFormattedScheduledTime(),

                        'startDate' => $this->task->start_on,
                        'endDate' => $this->task->finish_on,
                    ]);
    }

    /**
     * Format Reminder Type Label
     */
    private function getReminderTypeLabel()
    {
        if ($this->task->reminder_type === 'OneTime') {
            return "One Time";
        }

        if ($this->task->reminder_type === 'Recurring') {
            return "Recurring (" . ucfirst($this->task->recurring_type) . ")";
        }

        if ($this->task->reminder_type === 'Custom') {
            return "Custom Range";
        }

        return $this->task->reminder_type ?? 'N/A';
    }

    /**
     * Format schedule time reliably
     */
    private function getFormattedScheduledTime()
    {
        // One-time reminder → full datetime
        if ($this->task->reminder_type === 'OneTime') {
            return $this->task->scheduled_date_time
                ? date('d-m-Y H:i', strtotime($this->task->scheduled_date_time))
                : null;
        }

        // Recurring → decode JSON if needed
        if ($this->task->reminder_type === 'Recurring') {

            // Multiple times (Daily or Weekly)
            if (!empty($this->task->recurring_time)) {
                $times = is_array($this->task->recurring_time)
                            ? $this->task->recurring_time
                            : json_decode($this->task->recurring_time, true);

                if (is_array($times)) {
                    return implode(', ', $times); // e.g. "10:00, 15:30"
                }
            }
        }

        // Custom → plain time
        if ($this->task->reminder_type === 'Custom') {
            return $this->task->custom_time
                ? date('H:i', strtotime($this->task->custom_time))
                : null;
        }

        return null;
    }
}
