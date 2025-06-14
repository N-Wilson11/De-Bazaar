<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTheme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'background_color',
        'custom_css_path',
        'custom_js_path',
        'footer_text',
        'is_active',
    ];

    /**
     * Get the theme colors as an array.
     *
     * @return array
     */
    public function getColorsAttribute()
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color,
            'text' => $this->text_color,
            'background' => $this->background_color,
        ];
    }

    /**
     * Get the theme as a config array compatible with the theme configuration.
     *
     * @return array
     */
    public function toThemeConfig()
    {
        return [
            'name' => $this->name,
            'logo' => $this->logo_path,
            'favicon' => $this->favicon_path,
            'colors' => $this->getColorsAttribute(),
            'custom_css' => $this->custom_css_path,
            'custom_js' => $this->custom_js_path,
            'footer_text' => $this->footer_text ?: '© ' . date('Y') . ' ' . $this->name . '. All rights reserved.',
        ];
    }
}