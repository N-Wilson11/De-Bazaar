<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Business
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this resource.');
        }

        if (Auth::user()->user_type !== 'zakelijk') {
            return redirect('/')->with('error', 'This feature is only available for business users.');
        }

        return $next($request);
    }
}
