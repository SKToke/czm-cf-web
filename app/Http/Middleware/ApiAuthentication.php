<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    use HttpResponses;
    /**
     * Check if oncoming API request has valid API-token before processing any request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $headerApiToken = $request->header('api-key');

        if (isset($headerApiToken) && $headerApiToken == env('API_KEY')) {
            return $next($request);
        }
        else {
            return $this->error('Invalid API Key');
        }
    }
}
