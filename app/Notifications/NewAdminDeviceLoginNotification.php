<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;

class NewAdminDeviceLoginNotification extends Notification
{
    use Queueable;

    protected $admin;
    protected $device;
    protected $agent;
    protected $ip;

    /**
     * Create a new notification instance.
     */
    public function __construct($admin, $device, Agent $agent, $ip)
    {
        $this->admin = $admin;
        $this->device = $device;
        $this->agent = $agent;
        $this->ip = $ip;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $approvalUrl = route('admin.device.approval.form', $this->device->id);

        return (new MailMessage)
            ->subject('New Device Login Detected (Admin Panel)')
            ->line("A new device logged in Throught: **{$this->admin->username}**")
            ->line("**User ID:** " . $this->admin->id)
            ->line("**Browser:** " . $this->agent->browser())
            ->line("**Operating System:** " . $this->agent->platform())
            ->line("**IP Address:** " . $this->ip)
            ->line('If this was you, please approve this device below:')
            ->action('Review & Approve Device', $approvalUrl)
            ->line('If this was not you, please secure your account immediately.');
    }
}
