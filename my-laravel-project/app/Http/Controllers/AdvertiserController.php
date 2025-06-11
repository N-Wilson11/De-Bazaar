<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertiserController extends Controller
{
    /**
     * Toon het profiel van een adverteerder
     */
    public function show(User $user)
    {
        // Laad de advertenties van deze gebruiker
        $advertisements = $user->advertisements()
            ->where('status', 'active')
            ->latest()
            ->paginate(8);
            
        // Laad de beoordelingen van deze adverteerder
        $reviews = $user->reviewsReceived()
            ->with('user')
            ->where('review_type', 'advertiser')
            ->latest()
            ->paginate(5);
            
        // Bereken gemiddelde beoordeling
        $averageRating = $user->reviewsReceived()
            ->where('review_type', 'advertiser')
            ->avg('rating');
            
        $reviewCount = $user->reviewsReceived()
            ->where('review_type', 'advertiser')
            ->count();
            
        return view('advertisers.show', compact(
            'user', 
            'advertisements', 
            'reviews', 
            'averageRating',
            'reviewCount'
        ));
    }
}
