<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    /**
     * Status constanten
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    /**
     * De attributen die mass-assignable zijn.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'advertisement_id',
        'amount',
        'status',
        'message',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Krijg de gebruiker die het bod heeft geplaatst.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Krijg de advertentie waarop is geboden.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Controleer of het bod is verlopen.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Controleer of het bod nog in behandeling is.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && !$this->isExpired();
    }

    /**
     * Controleer of het bod is geaccepteerd.
     *
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Controleer of het bod is afgewezen.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
