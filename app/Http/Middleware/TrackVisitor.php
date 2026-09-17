<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GeoIP;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the request is for an asset or an API call and skip logging for those
        if ($request->is('assets/*') || $request->is('api/*')) {
            return $next($request);
        }

        // Collect visitor information
        $ipAddress = $request->ip();
        $visitTime = Carbon::now(); // Current timestamp
        $userAgent = $request->header('User-Agent');
        $referrer = $request->headers->get('referer') ?? 'Direct';
        
        // Get geo location using GeoIP
        $location = GeoIP::getLocation($ipAddress);
        
        // Extract location data safely
        $country = $location['country'] ?? 'Unknown'; // Optional
        $region = $location['state'] ?? 'Unknown'; // Optional
        $city = $location['city'] ?? 'Unknown'; // Optional
        $latitude = $location['lat'] ?? 0; // Optional
        $longitude = $location['lon'] ?? 0; // Optional
        
        $sessionId = session()->getId();
        $pageUrl = $request->fullUrl();
        $responseStatus = 200; // Assuming a successful visit
        $duration = 0; // This can be calculated later by comparing timestamps or using JavaScript
        $referralSource = $request->server('HTTP_REFERER') ?? 'Direct';
        $cookiesEnabled = $request->hasCookie('laravel_session') ? 1 : 0;
        $trafficSource = $this->getTrafficSource($request);

        // Insert data into the 'iptrackers' table
        DB::table('iptrackers')->insert([
            'ip_address' => $ipAddress,
            'visit_time' => $visitTime,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'country' => $country,
            'region' => $region,
            'city' => $city,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'session_id' => $sessionId,
            'page_url' => $pageUrl,
            'response_status' => $responseStatus,
            'duration' => $duration,
            'referral_source' => $referralSource,
            'cookies_enabled' => $cookiesEnabled,
            'traffic_source' => $trafficSource,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $next($request);
    }

    /**
     * This is a custom method to determine the traffic source
     */
    protected function getTrafficSource(Request $request)
    {
        $referrer = $request->headers->get('referer');
        if (!$referrer) {
            return 'Direct';
        }

        // You can add logic to categorize traffic sources (e.g., Search Engines, Social Media, etc.)
        return 'Referral';
    }
}


