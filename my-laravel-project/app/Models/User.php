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
}
