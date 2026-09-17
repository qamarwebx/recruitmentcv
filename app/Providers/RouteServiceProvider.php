<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';
    public const ADMIN_HOME = '/admin/dashboard';
    public const PARTNER_HOME = '/partner/booking';


    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // On the source (qamarhire.com) install this group is domain-locked
            // to worker.qamarhire.com so it doesn't compete with the CRM/admin
            // panel living on the same codebase. This install (recruitmentcv.com)
            // has no separate CRM domain to disambiguate from - the whole site
            // *is* the worker portal, on the apex and on every valid partner
            // subdomain - so the group is registered unrestricted (no ->domain())
            // instead.
            //
            // That means it MUST be declared after web.php, not before: Laravel's
            // RouteCollection dedupes by a (method, domain, uri) key, and a later
            // registration for an identical key silently overwrites the earlier
            // one in that lookup - registration order only decides who wins a
            // collision, it does not add a second candidate route to try. Since
            // web.php also defines a domain-less GET / ('welcome', FrontEndController)
            // that would otherwise collide with worker.php's GET / ('worker.home'),
            // worker.php must be registered LAST so its identical-key routes are
            // the ones that survive. This is the only URI the two files share;
            // everything else either file defines is unaffected by the order.
            Route::middleware('web')
                ->group(base_path('routes/worker.php'));

            Route::middleware('web')
            ->group(base_path('routes/docs.php')); // <-- Add this
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
