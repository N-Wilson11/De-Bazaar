<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
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
        // Voor zakelijke gebruikers, gebruik hun eigen bedrijfs-ID
        if (Auth::check() && Auth::user()->user_type === 'zakelijk' && Auth::user()->company_id) {
            $companyId = Auth::user()->company_id;
        }
        // Voor normale gebruikers die specifiek een bedrijf bezoeken
        else if ($request->has('company_id')) {
            $companyId = $request->input('company_id');
        }
        // Gebruik opgeslagen bedrijfs-ID uit sessie of cookie
        else if (Session::has('company_id')) {
            $companyId = Session::get('company_id');
        }
        else if (Cookie::has('company_id')) {
            $companyId = Cookie::get('company_id');
        }
        // Standaardwaarde als er geen bedrijf is ingesteld
        else {
            $companyId = 'default';
        }
        
        // Sla het bedrijfs-ID op in de sessie
        Session::put('company_id', $companyId);
        
        // Bewaar het bedrijfs-ID in een cookie voor consistentie tussen sessies
        // 30 dagen geldig
        Cookie::queue('company_id', $companyId, 60 * 24 * 30);
        
        return $next($request);
    }
}