<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->updateAverageRating($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $this->updateAverageRating($review);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->updateAverageRating($review);
    }

    /**
     * Update the average rating for the advertisement.
     */
    protected function updateAverageRating(Review $review): void
    {
        $advertisement = $review->advertisement;
        
        if (!$advertisement) {
            return;
        }
        
        // Get all reviews for this advertisement
        $reviews = $advertisement->reviews;
        $reviewCount = $reviews->count();
        
        if ($reviewCount > 0) {
            // Calculate average rating
            $totalRating = $reviews->sum('rating');
            $averageRating = $totalRating / $reviewCount;
            
            // Update the advertisement with the new average rating
            $advertisement->update([
                'average_rating' => round($averageRating, 2),
                'review_count' => $reviewCount
            ]);
        } else {
            // No reviews, reset average rating
            $advertisement->update([
                'average_rating' => null,
                'review_count' => 0
            ]);
        }
    }
}
