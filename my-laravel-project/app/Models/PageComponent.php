<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageComponent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'type',
        'content',
        'sort_order',
        'settings',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the component.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
      /**
     * Get available component types
     * 
     * @return array
     */
    public static function getComponentTypes(): array
    {
        return [
            'title' => 'Titel',
            'text' => 'Tekstblok',
            'featured_ads' => 'Uitgelichte advertenties',
            'image' => 'Afbeelding',
        ];
    }
}
