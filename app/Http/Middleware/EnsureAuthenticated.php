<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     if (!session()->has('auth_token')) {
    //         Log::info('No auth_token in session, redirecting to login.');
    //         return redirect('/login');
    //     }

    //     Log::info('auth_token found, processing request.');
    //     return $next($request);
    // }


    public function handle(Request $request, Closure $next)
    {
        if ($request->path() === 'login' || $request->path() === 'otp-verification' || $request->path() === 'api/proxy/verify-otp' || $request->path() === 'store-token' || $request->path() === 'test-token' || $request->path() === 'forgot-password' || $request->path() === 'api/proxy/forgot-password' || $request->path() === 'api/proxy/verify-otp' || $request->path() === 'api/proxy/change-password') {
            return $next($request);
        }

        if (!session()->has('auth_token')) {
            Log::info('No auth_token in session, redirecting to login.');
            return redirect('/login');
        }

        Log::info('auth_token found, processing request.');
        return $next($request);
    }

}

