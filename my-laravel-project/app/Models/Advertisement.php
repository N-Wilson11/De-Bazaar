<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advertisement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'condition',
        'category',
        'type',
        'status',
        'images',
        'location',
        'is_highlighted',
        'is_featured',
        'expires_at',
        // Huurspecifieke velden
        'is_rental',
        'rental_price_day',
        'rental_price_week',
        'rental_price_month',
        'minimum_rental_days',
        'rental_availability',
        'rental_booked_dates',
        'rental_conditions',
        'rental_requires_deposit',
        'rental_deposit_amount',
        'rental_pickup_location',
    ];
    
    /**
     * Get image URL for the first image
     */
    public function getFirstImageUrl()
    {
        if (empty($this->images)) {
            return null;
        }
        
        $image = $this->images[0];
        
        // Handle both absolute and relative paths
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }
        
        // Fix path separators for Windows
        $image = str_replace('\\', '/', $image);
        
        // Add storage URL prefix if needed
        if (strpos($image, '/storage/') !== 0) {
            return asset('storage/' . $image);
        }
        
        return asset($image);
    }
    
    /**
     * Get all image URLs
     */
    public function getAllImageUrls()
    {
        if (empty($this->images)) {
            return [];
        }
        
        return collect($this->images)->map(function($image) {
            // Handle both absolute and relative paths
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }
            
            // Fix path separators for Windows
            $image = str_replace('\\', '/', $image);
            
            // Add storage URL prefix if needed
            if (strpos($image, '/storage/') !== 0) {
                return asset('storage/' . $image);
            }
            
            return asset($image);
        })->all();
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'rental_price_day' => 'decimal:2',
        'rental_price_week' => 'decimal:2',
        'rental_price_month' => 'decimal:2',
        'rental_deposit_amount' => 'decimal:2',
        'is_highlighted' => 'boolean',
        'is_featured' => 'boolean',
        'is_rental' => 'boolean',
        'rental_requires_deposit' => 'boolean',
        'expires_at' => 'datetime',
        'images' => 'array',
        'rental_availability' => 'array',
        'rental_booked_dates' => 'array',
    ];

    /**
     * Get the user that owns the advertisement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the advertisement is a rental.
     */
    public function isRental(): bool
    {
        return $this->is_rental === true;
    }

    /**
     * Check if the advertisement is available for the specified dates.
     * 
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return bool
     */
    public function isAvailableForDates(string $startDate, string $endDate): bool
    {
        if (!$this->isRental()) {
            return false;
        }

        // Controleer of de data beschikbaar zijn in de beschikbaarheidskalender
        // Dit is een eenvoudige implementatie; een geavanceerde implementatie zou kalenderfunctionaliteit gebruiken
        $availability = $this->rental_availability ?? [];
        $bookedDates = $this->rental_booked_dates ?? [];

        // Controleer of de advertentie actief is
        if ($this->status !== 'active') {
            return false;
        }

        // Als er geen specifieke beschikbaarheid is ingesteld, nemen we aan dat het beschikbaar is
        if (empty($availability)) {
            // Controleer of de data niet in de gereserveerde data zitten
            return !in_array($startDate, $bookedDates) && !in_array($endDate, $bookedDates);
        }

        // Anders controleren we of beide data in de beschikbaarheidskalender zitten
        return in_array($startDate, $availability) && in_array($endDate, $availability);
    }

    /**
     * Calculate the rental price for a period.
     * 
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return float|null
     */
    public function calculateRentalPrice(string $startDate, string $endDate): ?float
    {
        if (!$this->isRental()) {
            return null;
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1; // Inclusief de einddag

        // Controleer minimaal aantal huurdagen
        if ($this->minimum_rental_days && $days < $this->minimum_rental_days) {
            return null; // Periode te kort
        }

        // Bereken prijs op basis van langste periode
        if ($days >= 30 && $this->rental_price_month) {
            $months = ceil($days / 30);
            return $months * $this->rental_price_month;
        }

        if ($days >= 7 && $this->rental_price_week) {
            $weeks = ceil($days / 7);
            return $weeks * $this->rental_price_week;
        }

        // Dagprijs berekenen
        if ($this->rental_price_day) {
            return $days * $this->rental_price_day;
        }

        // Terugvallen op de normale prijs als er geen huurprijzen zijn ingesteld
        return $days * $this->price;
    }
}
