<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        try {
            $response->headers->set('X-App-Version', config('app.version'));
        } catch (Exception) {
            // ignore
        }

        return $response;
    }
}
