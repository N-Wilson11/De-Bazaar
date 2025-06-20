<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * User types
     */
    const TYPE_PARTICULIER = 'particulier';
    const TYPE_ZAKELIJK = 'zakelijk';
    const TYPE_ADMIN = 'admin'; // Platformeigenaar
    const TYPE_NORMAAL = 'normaal'; // Normale gebruiker

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_type',
        'company_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin (platform owner)
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is a business user
     * 
     * @return bool
     */
    public function isBusinessUser(): bool
    {
        return $this->user_type === self::TYPE_ZAKELIJK;
    }
    
    /**
     * Check if user is a normal user
     * 
     * @return bool
     */
    public function isNormalUser(): bool
    {
        return $this->user_type === self::TYPE_NORMAAL;
    }

    /**
     * Get all bids placed by the user.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }
    
    /**
     * Get the count of active bids placed by the user.
     * 
     * @return int
     */
    public function getActiveBidsCountAttribute(): int
    {
        return $this->bids()
            ->where('status', Bid::STATUS_PENDING)
            ->count();
    }
    
    /**
     * Check if user has reached the maximum number of allowed bids (4).
     * 
     * @return bool
     */
    public function hasReachedMaxBids(): bool
    {
        return $this->active_bids_count >= 4;
    }
    
    /**
     * Get the contracts associated with the user.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the company this user belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the advertisements associated with the user.
     */
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }
    
    /**
     * Get only rental advertisements associated with the user.
     */
    public function rentalAdvertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class)->where('is_rental', true);
    }
    
    /**
     * Get the active cart for the user.
     */
    public function activeCart()
    {
        return $this->hasOne(Cart::class)->where('status', 'active')->latest();
    }
    
    /**
     * Get all carts for the user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
    
    /** 
     * Get all orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get all reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * Get all reviews received by this user (as advertiser).
     */
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
    
    /**
     * Get all reviews about this user as an advertiser.
     */
    public function advertiserReviews(): HasMany
    {
        return $this->reviewsReceived()->where('review_type', 'advertiser');
    }
    
    /**
     * Get the favorites for the user.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
    
    /**
     * Get the favorited advertisements for the user.
     */
    public function favoritedAdvertisements()
    {
        return $this->belongsToMany(Advertisement::class, 'favorites')
                    ->withTimestamps();
    }
    
    /**
     * Calculate the average rating as an advertiser.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $reviews = $this->advertiserReviews;
        if ($reviews->isEmpty()) {
            return null;
        }
        return round($reviews->avg('rating'), 1);
    }
    
    /**
     * Get the total number of reviews received as an advertiser.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->advertiserReviews()->count();
    }
}
