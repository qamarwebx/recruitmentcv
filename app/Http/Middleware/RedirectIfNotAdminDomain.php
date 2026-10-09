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
            
            // Passport + driving licence image files are served by the app on every host
            // (App\Support\PassportAccess / PassportImage - original or blurred) - not an admin page.
            $document = str_starts_with($path, 'admin/assets/images/candidate/')
                && \App\Support\PassportAccess::documentType(substr($path, strlen('admin/assets/images/candidate/'))) !== null;
            if (str_starts_with($path, 'admin') && !$document && !\App\Support\PassportImage::isPassportPath($path)) {
                return redirect('/');
            }
        }
    
        return $next($request);
    }
}