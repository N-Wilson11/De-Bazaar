<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessMiddleware
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
        if (Auth::check() && Auth::user()->user_type === 'zakelijk') {
            return $next($request);
        }
        
        return redirect()->route('dashboard')->with('error', 'Deze pagina is alleen beschikbaar voor zakelijke gebruikers.');
    }
}
