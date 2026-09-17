<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;

class DeviceApprovedNotification extends Notification
{
    use Queueable;

    protected $device;
    protected $agent;

    public function __construct($device, Agent $agent)
    {
        $this->device = $device;
        $this->agent = $agent;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $loginUrl = route('admin.login');

        return (new MailMessage)
            ->subject('Device Login Approved')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your device login has been approved!")
            ->line("Device Type: ".$this->agent->device())
            ->line("Browser: ".$this->agent->browser())
            ->line("Operating System: ".$this->agent->platform())
            ->line("IP Address: ".$this->device->ip_address)
            ->line('You can now login from this device.')
            ->action('Login Now', $loginUrl)
            ->salutation('Regards, Your Team');
    }
}
