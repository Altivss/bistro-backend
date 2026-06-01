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
        $isAllowed = in_array($origin, $this->allowedOrigins) || empty($this->allowedOrigins);

        // Handle preflight requests FIRST (before routing)
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 204);
            
            if ($isAllowed && $origin) {
                $response->header('Access-Control-Allow-Origin', $origin);
            }
            
            return $response
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        // Process the request
        $response = $next($request);

        // Add CORS headers to all responses
        if ($isAllowed && $origin) {
            $response->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Credentials', 'true');
        }
        
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Accept-Language')
            ->header('Access-Control-Expose-Headers', 'Authorization');

        return $response;
    }
}
