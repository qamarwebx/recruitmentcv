<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotAdminDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $path = $request->path();

        // ✅ CRM DOMAIN → root open kare to login pe bhejo
        if ($host === 'crm.qamarhire.com' || $host === 'qamarhire.test') {
    
            if ($path === '' || $path === '/') {
                return redirect('/admin/login');
            }
        }else{
            
            if (str_starts_with($path, 'admin')) {
                return redirect('/');
            }
        }
    
        return $next($request);
    }
}