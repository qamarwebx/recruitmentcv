<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Customer (`web` guard) pages of the RecruitmentCV site (/account/*).
 * Guests go back to the same host's homepage with the customer Login modal
 * opened, and return to the page they asked for after logging in. The
 * shared `auth` middleware can't be used: it redirects to the legacy
 * qamarhire.com-style /login page.
 */
class EnsureWorkerCustomerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('web')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('worker.home')
                ->with('customer_auth_open', true)
                ->with('customer_auth_intended', $request->getRequestUri());
        }

        return $next($request);
    }
}
