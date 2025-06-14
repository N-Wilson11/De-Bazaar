<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Toon de homepage met de laatste advertenties.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check of gebruiker is ingelogd
        if (Auth::check()) {
            $user = Auth::user();
            
            // Als zakelijke gebruiker, check voor landingpage
            if ($user->user_type === 'zakelijk' && $user->company) {
                $company = $user->company;
                
                // Als de company een landing_url heeft, redirect naar die pagina
                if ($company && $company->landing_url) {
                    return redirect()->route('company.landing', $company->landing_url);
                }
            }
            
            // Als gebruiker bij een bedrijf hoort met een landingpagina
            if ($user->company_id) {
                $company = Company::find($user->company_id);
                if ($company && $company->landing_url) {
                    return redirect()->route('company.landing', $company->landing_url);
                }
            }
            
            // Naar dashboard als de gebruiker is ingelogd
            return redirect()->route('dashboard');
        }
        
        // Haal de meest recente verkoopadvertenties op
        $latestSaleAds = Advertisement::where('is_rental', false)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        // Haal de meest recente verhuuradvertenties op
        $latestRentalAds = Advertisement::where('is_rental', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        return view('home', compact('latestSaleAds', 'latestRentalAds'));
    }
}
