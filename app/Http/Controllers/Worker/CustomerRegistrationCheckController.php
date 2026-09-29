<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Support\CustomerSite;
use Illuminate\Http\Request;

/**
 * Early duplicate check for the customer Sign Up form, BEFORE an OTP is sent
 * (UX only - WhatsappOtpController::registerUser2() re-checks everything
 * server-side before creating the account). Same rules as registration:
 * App\Support\CustomerSite::registrationConflicts(). Rate-limited in routes.
 */
class CustomerRegistrationCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!CustomerSite::isPartnerSite()) {
            return response()->json(['status' => 'error'], 403);
        }

        $request->validate([
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:5',
        ]);

        $conflicts = CustomerSite::registrationConflicts($request->email, $request->country_code, $request->mobile);

        return response()->json(
            ['status' => $conflicts ? 'error' : 'ok', 'errors' => (object) $conflicts],
            $conflicts ? 422 : 200
        );
    }
}
