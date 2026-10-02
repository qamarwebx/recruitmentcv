<?php

namespace App\Http\Controllers\PartnerAuth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Partner;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('partner.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Legacy email+password sign-up created a Partner with credentials
        // and logged it in with no mobile OTP and no admin approval. Partner
        // registration is the mobile-OTP flow (Partner Login page ->
        // Register as Partner -> PartnerAuthController), which only saves
        // credentials after the OTP is verified - send the visitor there.
        return redirect()->route('worker.partner.login.page', ['mode' => 'register']);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Partner::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Partner::create([
            'name' => $request->name,
            'user_type' => '1',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            
        ]);

        event(new Registered($user));

        Auth::guard('partner')->login($user);

        return redirect(RouteServiceProvider::PARTNER_HOME);
    }
}
