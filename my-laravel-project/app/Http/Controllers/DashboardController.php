<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Advertisement;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware will be applied at the route level
    }
    
    public function index()
    {
        // Tel het aantal verkoop- en verhuuradvertenties voor de huidige gebruiker
        $user = Auth::user();
        
        $normalAdsCount = Advertisement::where('user_id', $user->id)
            ->where('is_rental', false)
            ->count();
            
        $rentalAdsCount = Advertisement::where('user_id', $user->id)
            ->where('is_rental', true)
            ->count();
              // Bepaal hoeveel advertenties nog beschikbaar zijn
        $maxNormalAds = 4;
        $maxRentalAds = 4;
        
        // Alle gebruikers hebben dezelfde limiet (4 voor elke type advertentie)
        $normalAdsRemaining = $maxNormalAds - $normalAdsCount;
        $rentalAdsRemaining = $maxRentalAds - $rentalAdsCount;
        
        return view('dashboard', compact(
            'normalAdsCount', 
            'rentalAdsCount', 
            'maxNormalAds', 
            'maxRentalAds', 
            'normalAdsRemaining', 
            'rentalAdsRemaining'
        ));
    }
}
