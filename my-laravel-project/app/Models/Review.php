<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */    protected $fillable = [
        'user_id',
        'advertisement_id',
        'reviewer_id',
        'review_type',
        'rating',
        'title',
        'comment',
    ];

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the advertisement that was reviewed.
     */    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
    
    /**
     * Get the advertiser/reviewer that is being reviewed.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    
    /**
     * Check if this review is for an advertiser
     */
    public function isAdvertiserReview(): bool
    {
        return $this->review_type === 'advertiser';
    }
    
    /**
     * Check if this review is for an advertisement
     */
    public function isAdvertisementReview(): bool
    {
        return $this->review_type === 'advertisement';
    }
}
