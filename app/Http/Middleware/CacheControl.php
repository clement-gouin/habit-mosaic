<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CacheControl
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
