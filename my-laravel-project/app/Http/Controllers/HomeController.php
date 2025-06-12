<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Toon de homepage met de laatste advertenties.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Haal de laatste 6 verkoopadvertenties op
        $latestSaleAds = Advertisement::where('is_rental', false)
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();
            
        // Haal de laatste 6 verhuuradvertenties op
        $latestRentalAds = Advertisement::where('is_rental', true)
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();
            
        return view('home', compact('latestSaleAds', 'latestRentalAds'));
    }
}
