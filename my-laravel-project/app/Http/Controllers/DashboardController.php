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
        
        // Initialiseer variabelen met standaardwaarden
        $normalAdsCount = 0;
        $rentalAdsCount = 0;
        $maxNormalAds = 4;
        $maxRentalAds = 4;
        $normalAdsRemaining = 0;
        $rentalAdsRemaining = 0;
        
        // Tel alleen advertenties en bereken limieten voor particuliere en zakelijke gebruikers
        if($user->user_type === 'particulier' || $user->user_type === 'zakelijk') {
            $normalAdsCount = Advertisement::where('user_id', $user->id)
                ->where('is_rental', false)
                ->count();
                
            $rentalAdsCount = Advertisement::where('user_id', $user->id)
                ->where('is_rental', true)
                ->count();
                  
            // Alle gebruikers hebben dezelfde limiet (4 voor elke type advertentie)
            $normalAdsRemaining = $maxNormalAds - $normalAdsCount;
            $rentalAdsRemaining = $maxRentalAds - $rentalAdsCount;
        }
        
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
