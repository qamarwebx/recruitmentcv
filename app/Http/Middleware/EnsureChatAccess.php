<?php

namespace App\Http\Middleware;

use App\Models\Adminpermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureChatAccess
{
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();

        if (! $this->hasChatAccess($admin)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access Chat.',
                    'data'    => null,
                ], 403);
            }

            return redirect()->route('admin.dashboard')->with('errorMsg', 'You do not have permission to access Chat.');
        }

        return $next($request);
    }

    public static function hasChatAccess($admin): bool
    {
        if (! $admin) {
            return false;
        }

        if ($admin->user_type == 1) {
            return true;
        }

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        return (bool) (optional($permission)->full_access || optional($permission)->chat);
    }
}
