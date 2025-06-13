<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'advertisement_id',
        'quantity',
        'price',
        'title',
        'seller_id',
        'is_rental',
        'rental_start_date',
        'rental_end_date',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the advertisement that the order item refers to.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }
    
    /**
     * Get the seller (user) of the item.
     */    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
    /**
     * Get the calculated rental period in days.
     */
    public function getRentalDurationInDays(): int
    {
        if (!$this->is_rental || !$this->rental_start_date || !$this->rental_end_date) {
            return 0;
        }
        
        $start = new \DateTime($this->rental_start_date);
        $end = new \DateTime($this->rental_end_date);
        $interval = $start->diff($end);
        
        // Add 1 to include the last day
        return $interval->days + 1;
    }
}
