<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOrBusinessMiddleware
{
    /**
     * Handle an incoming request.
     * Allow access for both admin and business users
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || (Auth::user()->user_type !== 'admin' && Auth::user()->user_type !== 'zakelijk')) {
            return redirect()->route('dashboard')
                ->with('error', __('Je hebt geen toegang tot deze pagina.'));
        }

        return $next($request);
    }
}
