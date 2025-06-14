<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RelatedAdvertisementController extends Controller
{    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Auth wordt al toegepast via routes
    }
    
    /**
     * Toon het beheer van gerelateerde advertenties.
     */
    public function index(Advertisement $advertisement)
    {
        // Check of de gebruiker de advertentie mag beheren
        if (Gate::denies('update', $advertisement)) {
            return redirect()->back()->with('error', __('Je hebt geen toegang tot deze functie.'));
        }        // Haal alle advertenties van de gebruiker op die niet de huidige advertentie zijn
        $userAdvertisements = Advertisement::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('id', '!=', $advertisement->id)
            ->where('status', 'active')
            ->get();
            
        // Haal de huidige gerelateerde advertenties op
        $relatedAdvertisements = $advertisement->relatedAdvertisements;
        
        return view('advertisements.related', compact('advertisement', 'userAdvertisements', 'relatedAdvertisements'));
    }
    
    /**
     * Koppel een advertentie aan een andere.
     */
    public function store(Request $request, Advertisement $advertisement)
    {
        // Check of de gebruiker de advertentie mag beheren
        if (Gate::denies('update', $advertisement)) {
            return redirect()->back()->with('error', __('Je hebt geen toegang tot deze functie.'));
        }
        
        $request->validate([
            'related_advertisement_id' => 'required|exists:advertisements,id'
        ]);
        
        $relatedAdvertisement = Advertisement::find($request->related_advertisement_id);
          // Check of de gebruiker toegang heeft tot de gerelateerde advertentie
        if ($relatedAdvertisement->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return redirect()->back()->with('error', __('Je kunt alleen je eigen advertenties koppelen.'));
        }
        
        // Voorkom het koppelen van dezelfde advertentie
        if ($relatedAdvertisement->id === $advertisement->id) {
            return redirect()->back()->with('error', __('Je kunt een advertentie niet aan zichzelf koppelen.'));
        }
        
        // Voeg relatie toe als deze nog niet bestaat
        if (!$advertisement->hasRelatedAdvertisement($relatedAdvertisement)) {
            $advertisement->relatedAdvertisements()->attach($relatedAdvertisement->id);
            return redirect()->route('advertisements.related.index', $advertisement)
                ->with('success', __('Advertentie succesvol gekoppeld!'));
        }
        
        return redirect()->route('advertisements.related.index', $advertisement)
            ->with('info', __('Deze advertenties zijn al gekoppeld.'));
    }
    
    /**
     * Verwijder een koppeling tussen advertenties.
     */
    public function destroy(Advertisement $advertisement, $relatedId)
    {
        // Check of de gebruiker de advertentie mag beheren
        if (Gate::denies('update', $advertisement)) {
            return redirect()->back()->with('error', __('Je hebt geen toegang tot deze functie.'));
        }
        
        $advertisement->relatedAdvertisements()->detach($relatedId);
        
        return redirect()->route('advertisements.related.index', $advertisement)
            ->with('success', __('Koppeling verwijderd!'));
    }
}
