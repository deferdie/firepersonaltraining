<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsClient
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->is_trainer) {
            abort(403, 'Only clients can access this resource.');
        }

        return $next($request);
    }
}
