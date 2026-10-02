<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    // public function create(): View
    public function create()
    {
        // return view('auth.login');
        return redirect('/');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect($request['current_page']);

        // return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Customer logout only. The session is shared by every
        // *.recruitmentcv.com site (SESSION_DOMAIN) and also carries the
        // Partner login (`partner` guard), so it is NOT invalidated/flushed:
        // only the customer login (the web guard's own session key and
        // remember cookie) and the customer-only flow data are removed, then
        // the session id is regenerated (fixation-safe). The CSRF token is
        // kept so other open tabs keep working.
        Auth::guard('web')->logout();

        $request->session()->forget([
            'EmailOtp', 'emaiAddr',                                  // email change
            'PendingMobileCountryId', 'PendingMobileCountryCode',   // mobile change
            'new_user', 'customer_google_site', 'loginStatus2525', 'url.intended',
        ]);

        // New session id, old one destroyed; data (incl. the CSRF token) kept
        // - regenerate() would also rotate the token.
        $request->session()->migrate(true);

        // Back to the page the customer logged out from - only ever a path
        // on this same site (never an absolute/protocol-relative URL, so no
        // open redirect); anything else, or nothing, goes to the site root.
        $page = (string) $request->input('current_page');
        $isLocalPath = $page !== ''
            && str_starts_with($page, '/')
            && !str_starts_with($page, '//')
            && !preg_match('#[\\\\\r\n]#', $page);

        if ($page === '/ar/my-order' || $page === '/ar/my-profile') {
            return redirect('/ar');
        } elseif ($page === '/my-order' || $page === '/my-profile') {
            return redirect('/');
        }

        return redirect($isLocalPath ? $page : '/');
    }
}
