<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews for a specific advertisement.
     */
    public function index(Advertisement $advertisement)
    {
        $reviews = $advertisement->reviews()->with('user')->latest()->paginate(10);
        
        return view('reviews.index', compact('advertisement', 'reviews'));
    }

    /**
     * Show the form for creating a new review for an advertisement.
     */
    public function create(Advertisement $advertisement)
    {
        if (!$advertisement->canBeReviewedBy(Auth::user())) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('general.cannot_review_this_ad'));
        }
        
        if ($advertisement->hasBeenReviewedBy(Auth::user())) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('general.already_reviewed'));
        }
        
        return view('reviews.create', compact('advertisement'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Advertisement $advertisement)
    {
        if (!$advertisement->canBeReviewedBy(Auth::user())) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('general.cannot_review_this_ad'));
        }
        
        if ($advertisement->hasBeenReviewedBy(Auth::user())) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('general.already_reviewed'));
        }
        
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ]);
        
        $review = new Review([
            'user_id' => Auth::id(),
            'advertisement_id' => $advertisement->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);
        
        $review->save();
        
        // Update average rating (this will be handled by an observer)
        
        return redirect()->route('advertisements.show', $advertisement)
            ->with('success', __('general.review_submitted'));
    }    /**
     * Show the form for editing a review.
     */
    public function edit(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            if ($review->isAdvertisementReview()) {
                return redirect()->route('advertisements.show', $review->advertisement_id)
                    ->with('error', __('general.cannot_edit_review'));
            } else {
                return redirect()->route('advertisers.show', $review->reviewer_id)
                    ->with('error', __('general.cannot_edit_review'));
            }
        }
        
        if ($review->isAdvertisementReview()) {
            $advertisement = $review->advertisement;
            return view('reviews.edit', compact('review', 'advertisement'));
        } else {
            $user = $review->reviewer;
            return view('advertisers.reviews.edit', compact('review', 'user'));
        }
    }

    /**
     * Update a review in storage.
     */
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            if ($review->isAdvertisementReview()) {
                return redirect()->route('advertisements.show', $review->advertisement_id)
                    ->with('error', __('general.cannot_edit_review'));
            } else {
                return redirect()->route('advertisers.show', $review->reviewer_id)
                    ->with('error', __('general.cannot_edit_review'));
            }
        }
        
        $validationRules = [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ];
        
        if ($review->isAdvertiserReview()) {
            $validationRules['title'] = ['required', 'string', 'max:100'];
        }
        
        $validated = $request->validate($validationRules);
        
        $updateData = [
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ];
        
        if ($review->isAdvertiserReview() && isset($validated['title'])) {
            $updateData['title'] = $validated['title'];
        }
        
        $review->update($updateData);
        
        // Determine redirect route based on review type
        if ($review->isAdvertisementReview()) {
            return redirect()->route('advertisements.show', $review->advertisement_id)
                ->with('success', __('general.review_updated'));
        } else {
            return redirect()->route('advertisers.show', $review->reviewer_id)
                ->with('success', __('general.review_updated'));
        }
    }
    
    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
            if ($review->isAdvertisementReview()) {
                return redirect()->route('advertisements.show', $review->advertisement_id)
                    ->with('error', __('general.cannot_delete_review'));
            } else {
                return redirect()->route('advertisers.show', $review->reviewer_id)
                    ->with('error', __('general.cannot_delete_review'));
            }
        }
        
        // Store IDs for redirect before deleting the review
        $advertisementId = $review->advertisement_id;
        $reviewerId = $review->reviewer_id;
        $isAdvertiserReview = $review->isAdvertiserReview();
        
        $review->delete();
        
        // Redirect based on review type
        if (!$isAdvertiserReview) {
            return redirect()->route('advertisements.show', $advertisementId)
                ->with('success', __('general.review_deleted'));
        } else {
            return redirect()->route('advertisers.show', $reviewerId)
                ->with('success', __('general.review_deleted'));
        }
    }
}
