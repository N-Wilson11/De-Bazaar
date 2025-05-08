<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Theming Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the company's custom theming
    | including colors, logo paths, and other branding elements.
    |
    */

    // Default theme settings
    'default' => [
        'name' => env('COMPANY_NAME', 'De-Bazaar'),
        'logo' => env('COMPANY_LOGO', '/images/default-logo.png'),
        'favicon' => env('COMPANY_FAVICON', '/favicon.ico'),
        
        // Primary colors
        'colors' => [
            'primary' => env('THEME_PRIMARY_COLOR', '#4a90e2'),
            'secondary' => env('THEME_SECONDARY_COLOR', '#f5a623'),
            'accent' => env('THEME_ACCENT_COLOR', '#50e3c2'),
            'text' => env('THEME_TEXT_COLOR', '#333333'),
            'background' => env('THEME_BACKGROUND_COLOR', '#ffffff'),
        ],
        
        // Custom CSS/JS paths
        'custom_css' => env('THEME_CUSTOM_CSS', null),
        'custom_js' => env('THEME_CUSTOM_JS', null),
        
        // Footer content
        'footer_text' => env('COMPANY_FOOTER_TEXT', '© ' . date('Y') . ' De-Bazaar. All rights reserved.'),
    ],
    
    // Enable theming system
    'enabled' => env('THEME_ENABLED', true),
];