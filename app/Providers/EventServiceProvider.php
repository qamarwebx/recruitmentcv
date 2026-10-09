<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Partner Portal "Account Details" prompt: every successful partner
        // login (any login path) starts a new once-per-login cycle.
        Event::listen(Login::class, [\App\Support\PartnerAccountPrompt::class, 'startCycle']);
        // Pending Partner session limit: every partner login starts a new session clock.
        Event::listen(Login::class, [\App\Support\PendingPartnerSession::class, 'stamp']);
        // CRM -> Website -> Live Partners: login-session history (start / end).
        Event::listen(Login::class, [\App\Support\PartnerLoginTracker::class, 'start']);
        Event::listen(\Illuminate\Auth\Events\Logout::class, [\App\Support\PartnerLoginTracker::class, 'onLogout']);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
