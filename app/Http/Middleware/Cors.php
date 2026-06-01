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

            // Preflight must include Access-Control-Allow-Origin or the browser will block.
            // If Origin is missing we fall back to "*" (safe for non-credentialed requests).
            if ($hasOrigin) {
                $response->header('Access-Control-Allow-Origin', $origin);

                // Credentials are only valid when the requesting origin is allowed by policy.
                if ($isAllowed) {
                    $response->header('Access-Control-Allow-Credentials', 'true');
                }
            } else {
                $response->header('Access-Control-Allow-Origin', '*');
            }

            // Reflect requested headers when possible to avoid missing/invalid values.
            $requestedHeaders = $request->header('Access-Control-Request-Headers');
            $allowHeaders = $requestedHeaders ?: 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language';

            return $response
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', $allowHeaders)
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

        // For non-preflight responses, also make sure headers/methods are explicitly listed.
        // Note: Access-Control-Allow-Headers/Methods must not contain invalid values.
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            // Use the request's requested headers if present (browser will validate exact matches)
            ->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers')
                ?: 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
            ->header('Access-Control-Expose-Headers', 'Authorization');

        return $response;
    }
}

