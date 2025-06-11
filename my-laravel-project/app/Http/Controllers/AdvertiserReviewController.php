<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertiserReviewController extends Controller
{
    /**
     * Toon alle beoordelingen voor een adverteerder
     */
    public function index(User $user)
    {
        $reviews = $user->reviewsReceived()
            ->where('review_type', 'advertiser')
            ->with('user')
            ->latest()
            ->paginate(10);
            
        $averageRating = $user->reviewsReceived()
            ->where('review_type', 'advertiser')
            ->avg('rating');
            
        $reviewCount = $user->reviewsReceived()
            ->where('review_type', 'advertiser')
            ->count();
            
        return view('advertisers.reviews.index', compact(
            'user', 
            'reviews', 
            'averageRating',
            'reviewCount'
        ));
    }
    
    /**
     * Toon het formulier om een nieuwe beoordeling voor een adverteerder te schrijven
     */
    public function create(User $user)
    {
        // Controleer of de huidige gebruiker deze adverteerder kan beoordelen
        if (!$this->canReviewAdvertiser(Auth::user(), $user)) {
            return redirect()->route('advertisers.show', $user)
                ->with('error', __('general.cannot_review_this_advertiser'));
        }
        
        // Controleer of de gebruiker deze adverteerder al heeft beoordeeld
        if ($this->hasReviewedAdvertiser(Auth::user(), $user)) {
            return redirect()->route('advertisers.show', $user)
                ->with('error', __('general.already_reviewed_advertiser'));
        }
        
        return view('advertisers.reviews.create', compact('user'));
    }
    
    /**
     * Sla een nieuwe beoordeling voor een adverteerder op
     */
    public function store(Request $request, User $user)
    {
        // Controleer of de huidige gebruiker deze adverteerder kan beoordelen
        if (!$this->canReviewAdvertiser(Auth::user(), $user)) {
            return redirect()->route('advertisers.show', $user)
                ->with('error', __('general.cannot_review_this_advertiser'));
        }
        
        // Controleer of de gebruiker deze adverteerder al heeft beoordeeld
        if ($this->hasReviewedAdvertiser(Auth::user(), $user)) {
            return redirect()->route('advertisers.show', $user)
                ->with('error', __('general.already_reviewed_advertiser'));
        }
        
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:100'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ]);
        
        $review = new Review([
            'user_id' => Auth::id(),
            'reviewer_id' => $user->id,
            'review_type' => 'advertiser',
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
        ]);
        
        $review->save();
        
        return redirect()->route('advertisers.show', $user)
            ->with('success', __('general.review_submitted'));
    }
    
    /**
     * Controleer of een gebruiker een adverteerder kan beoordelen
     */
    private function canReviewAdvertiser($currentUser, $advertiser)
    {
        if (!$currentUser || !$advertiser) {
            return false;
        }
        
        // Gebruikers kunnen zichzelf niet beoordelen
        if ($currentUser->id === $advertiser->id) {
            return false;
        }
        
        // Controleer of de gebruiker een bestelling heeft geplaatst bij deze verkoper
        // of een advertentie heeft gehuurd van deze verhuurder
        $hasTransaction = $currentUser->orders()
            ->whereHas('items', function($query) use ($advertiser) {
                $query->where('seller_id', $advertiser->id);
            })
            ->exists();
            
        return $hasTransaction;
    }
    
    /**
     * Controleer of een gebruiker deze adverteerder al heeft beoordeeld
     */
    private function hasReviewedAdvertiser($currentUser, $advertiser)
    {
        if (!$currentUser || !$advertiser) {
            return false;
        }
        
        return Review::where('user_id', $currentUser->id)
            ->where('reviewer_id', $advertiser->id)
            ->where('review_type', 'advertiser')
            ->exists();
    }
}
