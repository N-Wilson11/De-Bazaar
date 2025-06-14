<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['browse', 'rentals', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start query voor de advertenties van de ingelogde gebruiker
        $query = Advertisement::where('user_id', Auth::id());
        
        // Filter op type (verhuur of normaal)
        if ($request->filled('type')) {
            if ($request->type === 'rental') {
                $query->where('is_rental', true);
            } elseif ($request->type === 'normal') {
                $query->where('is_rental', false);
            }
        }
        
        // Filter op categorie
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        // Zoeken op titel
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }
        
        // Bepaal de sorteervolgorde
        $sortField = 'created_at'; // standaard sorteerveld
        $sortOrder = 'desc';       // standaard sorteervolgorde
        
        if ($request->filled('sort')) {
            // Sorteer op basis van het geselecteerde veld
            $sortParts = explode('|', $request->sort);
            if (count($sortParts) == 2) {
                $sortField = $sortParts[0];
                $sortOrder = $sortParts[1];
            }
        }
        
        // Haal de gefilterde advertenties op
        $advertisements = $query->orderBy($sortField, $sortOrder)
            ->paginate(6);
        
        // Behoud de zoekparameter bij paginering
        $advertisements->appends($request->except('page'));
        
        // Haal de categorieën op voor het filterformulier    
        $categories = $this->getCategories();
        
        // Tel het aantal normale en verhuur advertenties voor de gebruiker
        $user = Auth::user();
        $normalAdsCount = Advertisement::where('user_id', $user->id)
            ->where('is_rental', false)
            ->count();
            
        $rentalAdsCount = Advertisement::where('user_id', $user->id)
            ->where('is_rental', true)
            ->count();
            
        // Bepaal de limieten (4 advertenties per categorie voor alle gebruikers)
        $maxNormalAds = 4;
        $maxRentalAds = 4;
        
        // Controleer of de gebruiker onder de limiet zit en of het juiste gebruikerstype heeft
        $canCreateNormal = ($normalAdsCount < $maxNormalAds) && 
                          ($user->user_type === 'particulier' || $user->user_type === 'zakelijk');
        $canCreateRental = ($rentalAdsCount < $maxRentalAds) && 
                          ($user->user_type === 'particulier' || $user->user_type === 'zakelijk');
        
        return view('advertisements.index', compact(
            'advertisements', 
            'categories',
            'normalAdsCount',
            'rentalAdsCount',
            'canCreateNormal',
            'canCreateRental'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Alleen particuliere en zakelijke gebruikers mogen advertenties plaatsen
        if ($user->user_type !== 'particulier' && $user->user_type !== 'zakelijk') {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen rechten om advertenties te plaatsen. Alleen particuliere en zakelijke gebruikers kunnen advertenties plaatsen.'));
        }
        
        $categories = $this->getCategories();
        $conditions = $this->getConditions();
        
        return view('advertisements.create', compact('categories', 'conditions'));
    }

    /**
     * Show form for creating a rental advertisement
     */
    public function createRental()
    {
        $user = Auth::user();
        
        // Alleen particuliere en zakelijke gebruikers mogen advertenties plaatsen
        if ($user->user_type !== 'particulier' && $user->user_type !== 'zakelijk') {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen rechten om verhuuradvertenties te plaatsen. Alleen particuliere en zakelijke gebruikers kunnen advertenties plaatsen.'));
        }
        
        $categories = $this->getCategories();
        $conditions = $this->getConditions();
        
        return view('advertisements.create_rental', compact('categories', 'conditions'));
    }    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Controleer of gebruiker geautoriseerd is om advertenties te plaatsen
        $user = Auth::user();
        
        // Alleen particuliere en zakelijke gebruikers mogen advertenties plaatsen
        if ($user->user_type !== 'particulier' && $user->user_type !== 'zakelijk') {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen rechten om advertenties te plaatsen. Alleen particuliere en zakelijke gebruikers kunnen advertenties plaatsen.'));
        }
        
        // Controleer maximaal aantal normale advertenties (max 4)
        $normalAdsCount = Advertisement::where('user_id', Auth::id())
            ->where('is_rental', false)
            ->count();
            
        if ($normalAdsCount >= 4) {
            return redirect()->route('advertisements.index')
                ->with('error', __('general.max_normal_ads') . '. ' . __('general.delete_to_add') . '.');
        }
            
        $validated = $this->validateAdvertisement($request);
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('advertisements', 'public');
                // Fix path separators for Windows
                $path = str_replace('\\', '/', $path);
                $images[] = $path;
            }
        }
        
        // Create advertisement
        $advertisement = new Advertisement($validated);
        $advertisement->user_id = Auth::id();
        $advertisement->images = $images;
        $advertisement->save();
        
        return redirect()->route('advertisements.show', $advertisement)
            ->with('success', __('De advertentie is succesvol aangemaakt!'));
    }    /**
     * Store a rental advertisement
     */    public function storeRental(Request $request)
    {
        // Controleer of gebruiker geautoriseerd is om advertenties te plaatsen
        $user = Auth::user();
        
        // Alleen particuliere en zakelijke gebruikers mogen advertenties plaatsen
        if ($user->user_type !== 'particulier' && $user->user_type !== 'zakelijk') {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen rechten om verhuuradvertenties te plaatsen. Alleen particuliere en zakelijke gebruikers kunnen advertenties plaatsen.'));
        }
        
        // Controleer maximaal aantal verhuur advertenties (max 4)
        $rentalAdsCount = Advertisement::where('user_id', Auth::id())
            ->where('is_rental', true)
            ->count();
            
        if ($rentalAdsCount >= 4) {
            return redirect()->route('advertisements.index')
                ->with('error', __('general.max_rental_ads') . '. ' . __('general.delete_to_add') . '.');
        }
        
        $validated = $this->validateRentalAdvertisement($request);
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('advertisements', 'public');
                // Fix path separators for Windows
                $path = str_replace('\\', '/', $path);
                $images[] = $path;
            }
        }
        
        // Set rental specific fields
        $advertisement = new Advertisement($validated);
        $advertisement->user_id = Auth::id();
        $advertisement->images = $images;
        $advertisement->is_rental = true;
        $advertisement->type = 'rental';
        
        // Verwerk slijtage-instellingen indien ingeschakeld
        if ($request->has('rental_calculate_wear_and_tear') && $request->rental_calculate_wear_and_tear) {
            $wearAndTearSettings = [
                'base_percentage' => floatval($request->base_percentage ?? 1.0),
                'condition_multipliers' => [
                    'excellent' => floatval($request->condition_excellent ?? 0.0),
                    'good' => floatval($request->condition_good ?? 0.5),
                    'fair' => floatval($request->condition_fair ?? 1.0),
                    'poor' => floatval($request->condition_poor ?? 2.0),
                ]
            ];
            
            $advertisement->rental_wear_and_tear_settings = $wearAndTearSettings;
        }
        
        // Convert availability dates to JSON
        if ($request->filled('rental_availability_dates')) {
            $dates = explode(',', $request->rental_availability_dates);
            $advertisement->rental_availability = $dates;
        }
        
        $advertisement->save();
        
        return redirect()->route('advertisements.show', $advertisement)
            ->with('success', __('De verhuuradvertentie is succesvol aangemaakt!'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Advertisement $advertisement)
    {
        // Increment view count
        $advertisement->increment('views');
        
        // Check if the advertisement is available for purchase
        $canBePurchased = $advertisement->isAvailableForPurchase();
        
        // Users shouldn't be able to purchase their own advertisements
        if (Auth::check() && $advertisement->user_id === Auth::id()) {
            $canBePurchased = false;
        }
        
        // Get bidding information
        $highestBid = null;
        $userBid = null;
        $canPlaceBid = false;
        $activeBidsCount = 0;
        
        if (Auth::check() && $advertisement->isAcceptingBids()) {
            $user = Auth::user();
            
            // Get the highest bid
            $highestBid = $advertisement->bids()
                ->where('status', 'pending')
                ->orderBy('amount', 'desc')
                ->first();
                
            // Get the user's bid
            $userBid = $advertisement->bids()
                ->where('user_id', $user->id)
                ->first();
                
            // Check if the user can place a bid
            $activeBidsCount = \App\Models\Bid::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();
                
            $canPlaceBid = !$userBid && $activeBidsCount < 4 && $advertisement->user_id !== $user->id;
        }
        
        return view('advertisements.show', compact(
            'advertisement', 
            'canBePurchased',
            'highestBid',
            'userBid',
            'canPlaceBid',
            'activeBidsCount'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Advertisement $advertisement)
    {
        // Check if the user owns this advertisement
        if ($advertisement->user_id !== Auth::id()) {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen toegang tot deze advertentie.'));
        }
        
        $categories = $this->getCategories();
        $conditions = $this->getConditions();
        
        if ($advertisement->isRental()) {
            return view('advertisements.edit_rental', compact('advertisement', 'categories', 'conditions'));
        }
        
        return view('advertisements.edit', compact('advertisement', 'categories', 'conditions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        // Check if the user owns this advertisement
        if ($advertisement->user_id !== Auth::id()) {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen toegang tot deze advertentie.'));
        }
        
        if ($advertisement->isRental()) {
            $validated = $this->validateRentalAdvertisement($request);
            
            // Convert availability dates to JSON
            if ($request->filled('rental_availability_dates')) {
                $dates = explode(',', $request->rental_availability_dates);
                $validated['rental_availability'] = $dates;
            }
        } else {
            $validated = $this->validateAdvertisement($request);
        }
          // Handle image uploads
        $images = $advertisement->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('advertisements', 'public');
                // Fix path separators for Windows
                $path = str_replace('\\', '/', $path);
                $images[] = $path;
            }
            $validated['images'] = $images;
        }
        
        // Handle image removals
        if ($request->has('remove_images')) {
            $remainingImages = [];
            foreach ($images as $image) {
                if (!in_array($image, $request->remove_images)) {
                    $remainingImages[] = $image;
                } else {
                    // Delete the image from storage
                    Storage::disk('public')->delete($image);
                }
            }
            $validated['images'] = $remainingImages;
        }
        
        // Verwerk slijtage-instellingen voor verhuuradvertenties
        if ($advertisement->isRental() && $request->has('rental_calculate_wear_and_tear')) {
            $wearAndTearSettings = [
                'base_percentage' => floatval($request->base_percentage ?? 1.0),
                'condition_multipliers' => [
                    'excellent' => floatval($request->condition_excellent ?? 0.0),
                    'good' => floatval($request->condition_good ?? 0.5),
                    'fair' => floatval($request->condition_fair ?? 1.0),
                    'poor' => floatval($request->condition_poor ?? 2.0),
                ]
            ];
            
            $validated['rental_wear_and_tear_settings'] = $wearAndTearSettings;
        }
        
        $advertisement->update($validated);
        
        return redirect()->route('advertisements.show', $advertisement)
            ->with('success', __('De advertentie is succesvol bijgewerkt!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Advertisement $advertisement)
    {
        // Check if the user owns this advertisement
        if ($advertisement->user_id !== Auth::id()) {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen toegang tot deze advertentie.'));
        }
        
        // Delete associated images
        if (!empty($advertisement->images)) {
            // Controleer of images een array is of converteer het naar een array
            $images = is_array($advertisement->images) ? $advertisement->images : 
                      (is_string($advertisement->images) && !empty($advertisement->images) ? [$advertisement->images] : []);
            
            foreach ($images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $advertisement->delete();
        
        $successMessage = $advertisement->is_rental 
            ? __('De verhuuradvertentie is succesvol verwijderd!') 
            : __('De advertentie is succesvol verwijderd!');
            
        return redirect()->route('advertisements.index')
            ->with('success', $successMessage);
    }
    
    /**
     * Display all rental advertisements
     */
    public function rentals(Request $request)
    {
        $query = Advertisement::where('is_rental', true)
            ->where('status', 'active');
            
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('price_max')) {
            $query->where('rental_price_day', '<=', $request->price_max);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
        
        // Sorteerlogica toepassen
        $sortField = 'created_at'; // standaard sorteerveld
        $sortOrder = 'desc'; // standaard sorteervolgorde
        
        if ($request->filled('sort')) {
            // Sorteer op basis van het geselecteerde veld
            $sortParts = explode('|', $request->sort);
            if (count($sortParts) == 2) {
                $sortField = $sortParts[0];
                $sortOrder = $sortParts[1];
            }
        }
        
        $rentals = $query->orderBy($sortField, $sortOrder)
            ->paginate(6);
            
        // Behoud zoekopdracht-parameters bij paginering
        $rentals->appends($request->except('page'));
            
        return view('advertisements.rentals', compact('rentals'));
    }
    
    // myRentals methode is verwijderd - alle advertenties staan nu in de index
    
    /**
     * Handle rental calendar functionality
     */
    public function calendar(Advertisement $advertisement)
    {
        // Check if the ad is a rental
        if (!$advertisement->isRental()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('general.not_a_rental_advertisement'));
        }
        
        return view('advertisements.calendar', compact('advertisement'));
    }
    
    /**
     * Update rental availability
     */
    public function updateAvailability(Request $request, Advertisement $advertisement)
    {
        // Check if the user owns this advertisement
        if ($advertisement->user_id !== Auth::id()) {
            return redirect()->route('advertisements.index')
                ->with('error', __('Je hebt geen toegang tot deze advertentie.'));
        }
        
        // Check if the ad is a rental
        if (!$advertisement->isRental()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Deze advertentie is geen verhuuradvertentie.'));
        }
        
        $request->validate([
            'available_dates' => 'nullable|string',
        ]);
        
        // Update availability dates
        if ($request->filled('available_dates')) {
            $dates = explode(',', $request->available_dates);
            $advertisement->rental_availability = $dates;
            $advertisement->save();
        }
        
        return redirect()->route('advertisements.calendar', $advertisement)
            ->with('success', __('Beschikbaarheid is succesvol bijgewerkt!'));
    }

    /**
     * Get standard categories
     */
    private function getCategories()
    {
        return [
            'elektronica' => 'Elektronica',
            'meubels' => 'Meubels',
            'kleding' => 'Kleding',
            'boeken' => 'Boeken',
            'sport' => 'Sport & Vrije tijd',
            'tuin' => 'Tuin & Terras',
            'gereedschap' => 'Gereedschap',
            'auto' => 'Auto & Vervoer',
            'verzamelingen' => 'Verzamelingen',
            'kunst' => 'Kunst & Antiek',
            'huishoudelijk' => 'Huishoudelijk',
            'overig' => 'Overig',
        ];
    }
    
    /**
     * Get standard conditions
     */
    private function getConditions()
    {
        return [
            'nieuw' => 'Nieuw',
            'als_nieuw' => 'Als nieuw',
            'goed' => 'Goed',
            'gebruikt' => 'Gebruikt',
            'beschadigd' => 'Beschadigd',
        ];
    }
    
    /**
     * Validate advertisement data
     */
    private function validateAdvertisement(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'category' => 'required|string',
            'location' => 'nullable|string|max:100',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|string|in:active,inactive',
            // Bidding fields
            'is_accepting_bids' => 'sometimes|boolean',
            'min_bid_amount' => 'nullable|numeric|min:0',
        ]);
    }
    
    /**
     * Validate rental advertisement data
     */
    private function validateRentalAdvertisement(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'category' => 'required|string',
            'location' => 'nullable|string|max:100',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|string|in:active,inactive',
            // Rental specific validation
            'rental_price_day' => 'nullable|numeric|min:0',
            'rental_price_week' => 'nullable|numeric|min:0',
            'rental_price_month' => 'nullable|numeric|min:0',
            'minimum_rental_days' => 'nullable|integer|min:1',
            'rental_conditions' => 'nullable|string|max:1000',
            'rental_requires_deposit' => 'sometimes|boolean',
            'rental_deposit_amount' => 'nullable|numeric|min:0',
            'rental_calculate_wear_and_tear' => 'sometimes|boolean',
            'base_percentage' => 'nullable|numeric|min:0|max:10',
            'condition_excellent' => 'nullable|numeric|min:0|max:5',
            'condition_good' => 'nullable|numeric|min:0|max:5',
            'condition_fair' => 'nullable|numeric|min:0|max:5',
            'condition_poor' => 'nullable|numeric|min:0|max:5',
            'rental_pickup_location' => 'nullable|string|max:200',
            'rental_availability_dates' => 'nullable|string',
        ]);
    }

    /**
     * Display all regular (non-rental) advertisements
     */
    public function browse(Request $request)
    {
        $query = Advertisement::where('is_rental', false)
            ->where('status', 'active');
            
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
        
        // Sorteerlogica toepassen
        $sortField = 'created_at'; // standaard sorteerveld
        $sortOrder = 'desc'; // standaard sorteervolgorde
        
        if ($request->filled('sort')) {
            // Sorteer op basis van het geselecteerde veld
            $sortParts = explode('|', $request->sort);
            if (count($sortParts) == 2) {
                $sortField = $sortParts[0];
                $sortOrder = $sortParts[1];
            }
        }
        
        $advertisements = $query->orderBy($sortField, $sortOrder)
            ->paginate(6);
            
        // Behoud zoekopdracht-parameters bij paginering
        $advertisements->appends($request->except('page'));
            
        return view('advertisements.browse', compact('advertisements'));
    }
}
