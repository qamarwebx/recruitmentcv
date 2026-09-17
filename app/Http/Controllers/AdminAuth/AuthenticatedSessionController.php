<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAuth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\AdminDevice;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();


        return redirect()->intended(RouteServiceProvider::ADMIN_HOME);

    }

    /**
     * Destroy an authenticated session.
     */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     Auth::guard('admin')->logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();

    //     return redirect('/admin/login');

        
    // }

    public function destroy(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
    
        if ($admin) {
            // 🧠 Only update login_status if device record exists
            if (session()->has('device_id')) {
                AdminDevice::where('admin_id', $admin->id)
                    ->where('device_id', session('device_id'))
                    ->update(['login_status' => 'Inactive']);
            }
    
            // ✅ Clear session device ID (important cleanup)
            session()->forget('device_id');
        }
    
        // 🔐 Logout and invalidate session
        Auth::guard('admin')->logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->route('admin.login');
    }
    


}
