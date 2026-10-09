<?php

namespace App\Jobs;

use App\Support\NotificationCenter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * One RecruitmentCV notification event (App\Support\NotificationCenter::
 * notify()): resolves the recipients server-side and sends the email and
 * WhatsApp the Notification Settings allow, logging each message.
 * Dispatched from either app, run by the CRM queue worker - twin file in
 * both apps. Not retried, so a message is never sent twice.
 */
class SendRecruitmentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public function __construct(public string $event, public ?int $partnerId, public array $data)
    {
    }

    public function handle()
    {
        NotificationCenter::deliver($this->event, $this->partnerId, $this->data);
    }
}
