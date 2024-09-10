<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssSanitization
{
    protected array $except = [

    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (mixed &$input, mixed $key) {
            if (! in_array($key, $this->except)) {
                $input = is_string($input) ? strip_tags($input) : $input;
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
