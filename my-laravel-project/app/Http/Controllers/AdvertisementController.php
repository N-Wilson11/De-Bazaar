<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $advertisements = Advertisement::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('advertisements.index', compact('advertisements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getCategories();
        $conditions = $this->getConditions();
        
        return view('advertisements.create', compact('categories', 'conditions'));
    }

    /**
     * Show form for creating a rental advertisement
     */
    public function createRental()
    {
        $categories = $this->getCategories();
        $conditions = $this->getConditions();
        
        return view('advertisements.create_rental', compact('categories', 'conditions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateAdvertisement($request);
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('advertisements', 'public');
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
    }

    /**
     * Store a rental advertisement
     */    public function storeRental(Request $request)
    {
        $validated = $this->validateRentalAdvertisement($request);
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('advertisements', 'public');
                $images[] = $path;
            }
        }
        
        // Set rental specific fields
        $advertisement = new Advertisement($validated);
        $advertisement->user_id = Auth::id();
        $advertisement->images = $images;
        $advertisement->is_rental = true;
        $advertisement->type = 'rental';
        
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
        
        return view('advertisements.show', compact('advertisement'));
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
            foreach ($advertisement->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $advertisement->delete();
        
        return redirect()->route('advertisements.index')
            ->with('success', __('De advertentie is succesvol verwijderd!'));
    }
    
    /**
     * Display all rental advertisements
     */
    public function rentals()
    {
        $rentals = Advertisement::where('is_rental', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('advertisements.rentals', compact('rentals'));
    }
    
    /**
     * Display user's rental advertisements
     */
    public function myRentals()
    {
        $rentals = Advertisement::where('user_id', Auth::id())
            ->where('is_rental', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('advertisements.my_rentals', compact('rentals'));
    }
    
    /**
     * Handle rental calendar functionality
     */
    public function calendar(Advertisement $advertisement)
    {
        // Check if the ad is a rental
        if (!$advertisement->isRental()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Deze advertentie is geen verhuuradvertentie.'));
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
            'rental_pickup_location' => 'nullable|string|max:200',
            'rental_availability_dates' => 'nullable|string',
        ]);
    }
}
