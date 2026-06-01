<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    protected $allowedOrigins = [];

    public function __construct()
    {
        $originsString = env('ALLOWED_CORS_ORIGINS', '');
        $this->allowedOrigins = array_map('trim', array_filter(explode(',', $originsString)));
    }

    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin');
        $hasOrigin = !empty($origin);

        // If an allowlist is configured, only reflect for those origins.
        // If no allowlist is configured, reflect any incoming Origin (good default for cross-site SPAs).
        $isAllowed = $hasOrigin && (in_array($origin, $this->allowedOrigins) || empty($this->allowedOrigins));

        // Handle preflight requests FIRST (before routing)
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 204);

            // Always set Access-Control-Allow-Origin on preflight whenever Origin is present.
            // This prevents the browser from failing early due to missing headers.
            if ($hasOrigin) {
                $response->header('Access-Control-Allow-Origin', $origin);
                // Credentials are only valid when the requesting origin is allowed by policy.
                if ($isAllowed) {
                    $response->header('Access-Control-Allow-Credentials', 'true');
                }
            }


            return $response
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
                ->header('Access-Control-Max-Age', '86400');
        }

        // Process the request
        $response = $next($request);

        // Add CORS headers to all responses (when Origin is present)
        if ($hasOrigin) {
            $response->header('Access-Control-Allow-Origin', $origin);

            // Credentials are only valid when the requesting origin is allowed by policy.
            if ($isAllowed) {
                $response->header('Access-Control-Allow-Credentials', 'true');
            }
        }

        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
            ->header('Access-Control-Expose-Headers', 'Authorization');


        return $response;
    }
}

