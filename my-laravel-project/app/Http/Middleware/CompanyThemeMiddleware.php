<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\CompanyTheme;
use Symfony\Component\HttpFoundation\Response;

class CompanyThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First check for company ID in request params
        if ($request->has('company_id')) {
            Session::put('company_id', $request->input('company_id'));
        }
        
        // Then check for company ID in session
        if (!Session::has('company_id') && Cookie::has('company_id')) {
            Session::put('company_id', Cookie::get('company_id'));
        }
        
        // Make sure we always have a company ID (default if none)
        $companyId = Session::get('company_id', 'default');
        
        // Store company ID in cookie for persistence across sessions
        // This ensures the theme stays consistent even after browser restart
        Cookie::queue('company_id', $companyId, 60 * 24 * 30); // 30 days
        
        return $next($request);
    }
}