<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth_token')) {
            Log::info('No auth_token in session, redirecting to login.');
            return redirect('/login');
        }

        Log::info('auth_token found, processing request.');
        return $next($request);
    }
}

