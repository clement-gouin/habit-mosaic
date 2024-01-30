<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class VersionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        try {
            $response->headers->set('X-App-Version', strval(Config::get('app.version')));
        } catch (Exception) {
            // ignore
        }

        return $response;
    }
}
