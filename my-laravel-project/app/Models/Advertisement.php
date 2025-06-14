<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\Result\ResultInterface;

class Advertisement extends Model
{
    use HasFactory;
    
    // Purchase status constants
    const PURCHASE_STATUS_AVAILABLE = 'available';
    const PURCHASE_STATUS_SOLD = 'sold';
    const PURCHASE_STATUS_RESERVED = 'reserved';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'condition',
        'category',
        'type',
        'status',
        'purchase_status',
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
        'rental_conditions',        'rental_requires_deposit',
        'rental_deposit_amount',
        'rental_calculate_wear_and_tear',
        'rental_wear_and_tear_settings',
        'rental_pickup_location',
        // Review velden
        'average_rating',
        'review_count',
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
     */    public function getAllImageUrls()
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
        'is_featured' => 'boolean',        'is_rental' => 'boolean',
        'rental_requires_deposit' => 'boolean',
        'rental_calculate_wear_and_tear' => 'boolean',
        'expires_at' => 'datetime',
        'images' => 'array',
        'rental_availability' => 'array',
        'rental_booked_dates' => 'array',
        'rental_wear_and_tear_settings' => 'json',
    ];

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
    }    /**
     * Calculate wear and tear costs based on rental duration and settings
     * 
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @param string $condition The condition after return, e.g. 'excellent', 'good', 'fair', 'poor'
     * @return float
     */    public function calculateWearAndTear($startDate, $endDate, $condition = 'good'): float
    {
        // Als slijtageberekening niet ingeschakeld is, return 0
        if (!$this->rental_calculate_wear_and_tear) {
            return 0;
        }
        
        // Log debugging info
        \Illuminate\Support\Facades\Log::debug('Wear and tear calculation', [
            'advertisement_id' => $this->id,
            'calculate_enabled' => $this->rental_calculate_wear_and_tear,
            'deposit_amount' => $this->rental_deposit_amount,
            'settings' => $this->rental_wear_and_tear_settings,
            'condition' => $condition,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        // Haal de instellingen op
        $settings = $this->rental_wear_and_tear_settings ?? [
            'base_percentage' => 1.0, // 1% van de prijs per dag
            'condition_multipliers' => [
                'excellent' => 0.0,   // Perfect staat, geen slijtage
                'good' => 0.5,        // Goede staat, halve slijtage
                'fair' => 1.0,        // Normale slijtage
                'poor' => 2.0,        // Slechte staat, dubbele slijtage
            ]
        ];
        
        // Bereken het aantal dagen
        $days = $startDate->diffInDays($endDate) + 1; // +1 omdat we de laatste dag ook meetellen
        
        // Bereken de slijtagekosten
        $basePercentage = $settings['base_percentage'] ?? 1.0;
        $conditionMultiplier = $settings['condition_multipliers'][$condition] ?? 1.0;
        
        // Bepaal het basisbedrag waarop de slijtage wordt berekend
        // Gebruik borg als die er is, anders de dagprijs x aantal dagen
        $baseAmount = 0;
        if ($this->rental_deposit_amount && $this->rental_deposit_amount > 0) {
            $baseAmount = $this->rental_deposit_amount;
        } elseif ($this->rental_price_day && $this->rental_price_day > 0) {
            $baseAmount = $this->rental_price_day * $days;
        } elseif ($this->price && $this->price > 0) {
            $baseAmount = $this->price;
        } else {
            return 0; // Geen basisbedrag beschikbaar
        }
        
        // Bereken de totale slijtagekosten: (basePercentage * dagen * conditionMultiplier * baseAmount) / 100
        $wearAndTearCost = ($basePercentage * $days * $conditionMultiplier * $baseAmount) / 100;
        
        // Log more debug info
        \Illuminate\Support\Facades\Log::debug('Wear and tear calculation details', [
            'base_amount' => $baseAmount,
            'base_percentage' => $basePercentage,
            'days' => $days,
            'condition_multiplier' => $conditionMultiplier,
            'calculation' => "($basePercentage * $days * $conditionMultiplier * $baseAmount) / 100",
            'wear_and_tear_cost' => $wearAndTearCost
        ]);
        
        // Zorg ervoor dat we nooit meer dan de borg rekenen als die er is
        if ($this->rental_deposit_amount && $this->rental_deposit_amount > 0) {
            $wearAndTearCost = min($wearAndTearCost, $this->rental_deposit_amount);
        }
        
        return round($wearAndTearCost, 2);
    }
      
    /**
     * Counts the number of images safely
     */
    public function countImages(): int
    {
        if (empty($this->images)) {
            return 0;
        }
        
        if (is_object($this->images) && method_exists($this->images, 'count')) {
            return $this->images->count();
        }
        
        return count($this->images);
    }
    
    /**
     * Check if the advertisement is available for purchase.
     */
    public function isAvailableForPurchase(): bool
    {
        return $this->purchase_status === self::PURCHASE_STATUS_AVAILABLE && 
               $this->status === 'active';
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
        }        // Terugvallen op de normale prijs als er geen huurprijzen zijn ingesteld
        return $days * $this->price;
    }

    /**
     * Get the reviews for this advertisement.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Check if this advertisement can be reviewed by the given user.
     * Users can only review rental advertisements they have rented.
     * 
     * @param User $user
     * @return bool
     */
    public function canBeReviewedBy(User $user): bool
    {
        if (!$this->isRental() || !$user) {
            return false;
        }
        
        // In een echte implementatie zou je hier controleren of de gebruiker dit item daadwerkelijk heeft gehuurd
        // Dit is een vereenvoudigde implementatie; je kunt dit later uitbreiden met een check tegen huurgeschiedenis
        
        // Controleer of de gebruiker niet de eigenaar is (je kunt je eigen advertenties niet beoordelen)
        return $this->user_id !== $user->id;
    }
    
    /**
     * Check if the user has already reviewed this advertisement
     * 
     * @param User $user
     * @return bool
     */
    public function hasBeenReviewedBy(User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->reviews()->where('user_id', $user->id)->exists();
    }
    
    /**
     * Get the users who favorited this advertisement.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }
    
    /**
     * Check if this advertisement is favorited by the given user.
     * 
     * @param User|null $user
     * @return bool
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }    /**
     * Generate a QR code for this advertisement
     *
     * @param int $size
     * @return string URL to QR code image or fallback
     */    public function generateQrCode($size = 200): string
    {
        $url = route('advertisements.show', $this->id);
        
        try {
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                // If GD is not available, use an external QR code service as fallback
                return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url);
            }
            
            // In version 6.0 of the Endroid QR Code library, we use the constructor parameters
            $builder = new \Endroid\QrCode\Builder\Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $url,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                size: $size,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );
            
            $result = $builder->build();
            
            return $result->getDataUri();
        } catch (\Exception $e) {
            // Use external QR code service as fallback in case of any error
            return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url);
        }
    }
}
