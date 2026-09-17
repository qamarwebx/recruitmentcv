<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('auto:whatsappcampaign')->everyMinute();
        $schedule->command('auto:bulkcontactsend')->everyMinute();
        $schedule->command('auto:bulkallcontactsend')->everyMinute();
        $schedule->command('auto:deletereset')->everyMinute();
        $schedule->command('queue:work --queue=orderSend,orderCancel,default --stop-when-empty')->everyMinute();
       
        // lead assign trigger
        $schedule->command('auto:autoupdateleadassignstatus')->daily('09:00');
        
        $schedule->command('leads:assign-continuously')
            ->everyMinute()
            ->between('09:00', '19:00')
            ->withoutOverlapping();

        $schedule->command('leads:assign-pending-night')
            ->dailyAt('10:30')
            ->withoutOverlapping();
        // $schedule->command('auto:autoleadassignstaff')->everyThirtyMinutes();
        // $schedule->command('leads:reassign-unfollowed')->dailyAt('14:30');

        // TOdo Reminder trigger
        $schedule->command('auto:todoreminderscheduled')->everyMinute();
        $schedule->command('reminders:ManageTodoReminders')->everyMinute();
        // $schedule->command('auto:todoreminderurgent')->everyThirtyMinutes()->between('10:00','20:00');
        // $schedule->command('auto:todoreminderhigh')->hourly()->between('10:00','20:00');
        // $schedule->command('auto:todoremindermedium')->everyThreeHours()->between('10:00','20:00');
        // $schedule->command('auto:todoreminderlow')->everyFourHours()->between('10:00','20:00');
        // $schedule->command('auto:todoreminderdue')->dailyAt('10:30');
        // $schedule->command('auto:tododuenotifyreminder')->dailyAt('16:30');
        // $schedule->command('admin:deactivate-no-todo-24h')->dailyAt('22:00')->withoutOverlapping()->skip(function () { return now()->isSunday(); });  // at 10pm deactivate user if there is not created todo found today

        // Staff deactive trigger
        // $schedule->command('team:deactivate-if-not-login-24hrs')->daily();
        // $schedule->command('staff:deactive-if-no-activity-today')->dailyAt('22:00'); // at 10pm deactivate user if there is not activity in todo notes found today

        // sync daily allcontact to lead
        $schedule->command('sync:allcontact-to-lead')->daily();

        // DealPipeline Reminders
        $schedule->command('reminders:ManageDealPipelineReminders')->everyMinute();
        $schedule->command('deals:notify-not-updated')->daily('09:00');
        $schedule->command('reminders:send-reschedule')->everyMinute();

        // campign and automation releted
        $schedule->command('sms:process-scheduled')->everyMinute();
        $schedule->command('email:process-scheduled')->everyMinute();
        $schedule->command('automation:process-scheduled')->everyMinute(); // jb koi whatsup automation schedule krta h to wo yha se process hoga module link-> [https://qamarhire.com/admin/meta-automation/list]
        $schedule->command('automation:process-email-scheduled')->everyMinute(); // jb koi email automation schedule krta h to wo yha se process hoga module link-> [https://qamarhire.com/admin/email-automation/list]
        $schedule->command('meta:run-scheduled-leads')->everyMinute();
        $schedule->command('auto:whatsappcampaignmeta')->everyMinute();

        // Storage Usage dashboard stats (Settings > Storage Usage)
        $schedule->command('storage-usage:recalculate')->everyTenMinutes()->withoutOverlapping();

        // DB Backup (Settings > Backup > DB Backup) - one lightweight tick
        // drives both the Daily and Interval schedules; see
        // App\Console\Commands\RunBackupScheduler for the due-checks.
        $schedule->command('backup:run-scheduler')->everyMinute()->withoutOverlapping();


    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
