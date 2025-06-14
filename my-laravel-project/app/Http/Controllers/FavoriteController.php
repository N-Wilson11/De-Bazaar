<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }    /**
     * Display a listing of the user's favorites.
     */    public function index(Request $request)
    {
        // Get the authenticated user with explicit class reference
        $user = Auth::user();
        
        // Ensure we're working with a User instance
        if (!$user instanceof User) {
            return redirect()->route('login')->with('error', 'You must be logged in to view favorites.');
        }
        
        // Use the pivot relationship to get favorited advertisements
        $favorites = $user->favoritedAdvertisements()
            ->with('user')  // Include user relation for each advertisement
            ->latest()
            ->paginate(6);
            
        // Behoud zoekopdracht-parameters bij paginering
        $favorites->appends($request->except('page'));

        return view('favorites.index', compact('favorites'));
    }/**
     * Toggle favorite status for an advertisement.
     */    public function toggle(Advertisement $advertisement)
    {
        // Get the authenticated user with explicit class reference
        $user = Auth::user();
        
        // Ensure we're working with a User instance
        if (!$user instanceof User) {
            return back()->with('error', 'User not authenticated properly.');
        }
        
        $isFavorite = $advertisement->isFavoritedBy($user);
        
        if ($isFavorite) {
            // Remove from favorites - use the pivot table directly
            $advertisement->favoritedBy()->detach($user->id);
            $message = __('general.favorite_removed');
        } else {
            // Add to favorites - use the pivot table directly
            $advertisement->favoritedBy()->attach($user->id);
            $message = __('general.favorite_added');
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'isFavorite' => !$isFavorite,
                'message' => $message
            ]);
        }
        
        return back()->with('success', $message);
    }
      /**
     * Remove an advertisement from favorites.
     */    public function destroy(Advertisement $advertisement)
    {
        // Get the authenticated user with explicit class reference
        $user = Auth::user();
        
        // Ensure we're working with a User instance
        if (!$user instanceof User) {
            return back()->with('error', 'User not authenticated properly.');
        }
        
        // Use the pivot relationship to remove the favorite
        $advertisement->favoritedBy()->detach($user->id);
        
        return back()->with('success', __('general.favorite_removed'));
    }
}
