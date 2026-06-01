<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    protected $allowedOrigins;

    public function __construct()
    {
        $originsString = env('ALLOWED_CORS_ORIGINS', '');
        $this->allowedOrigins = array_filter(explode(',', $originsString));
    }

    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin');
        $isAllowed = in_array($origin, $this->allowedOrigins);

        // Handle preflight requests FIRST (before routing)
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)
                ->when($isAllowed, function($response) use ($origin) {
                    return $response->header('Access-Control-Allow-Origin', $origin);
                })
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        // Process the request
        $response = $next($request);

        // Add CORS headers to all responses
        if ($isAllowed) {
            $response->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Credentials', 'true');
        }
        
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
            ->header('Access-Control-Expose-Headers', 'Authorization');

        return $response;
    }
}
