<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'description',
        'website',
        'landing_url',
        'landing_content',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users associated with the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the company theme.
     */
    public function theme(): HasOne
    {
        return $this->hasOne(CompanyTheme::class);
    }

    /**
     * Get the contracts associated with the company through its users.
     */
    public function contracts(): HasManyThrough
    {
        return $this->hasManyThrough(Contract::class, User::class);
    }

    /**
     * Get the page components for the company landing page.
     */
    public function pageComponents(): HasMany
    {
        return $this->hasMany(PageComponent::class)->orderBy('sort_order');
    }

    /**
     * Get active page components.
     */
    public function activePageComponents(): HasMany
    {
        return $this->hasMany(PageComponent::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
