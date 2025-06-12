<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

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
     */
    public function index(Request $request)
    {
        $favorites = auth()->user()->favoritedAdvertisements()
            ->with('user')
            ->latest()
            ->paginate(6);
            
        // Behoud zoekopdracht-parameters bij paginering
        $favorites->appends($request->except('page'));

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite status for an advertisement.
     */
    public function toggle(Advertisement $advertisement)
    {
        $user = auth()->user();
        $isFavorite = $advertisement->isFavoritedBy($user);
          if ($isFavorite) {
            // Remove from favorites
            $user->favoritedAdvertisements()->detach($advertisement->id);
            $message = __('general.favorite_removed');
        } else {
            // Add to favorites
            $user->favoritedAdvertisements()->attach($advertisement->id);
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
     */
    public function destroy(Advertisement $advertisement)
    {
        $user = auth()->user();
        $user->favoritedAdvertisements()->detach($advertisement->id);
        
        return back()->with('success', __('general.favorite_removed'));
    }
}
