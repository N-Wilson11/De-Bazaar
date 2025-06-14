<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\CompanyTheme;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share theme configuration with all views
        if (Config::get('theme.enabled', true)) {
            // Determine company ID based on authentication status
            $companyId = null;
            
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->user_type === 'zakelijk' && $user->company_id) {
                    $companyId = $user->company_id;
                }
            }
            
            // If no company ID from auth, get from session or use default
            if (!$companyId) {
                $companyId = Session::get('company_id', 'default');
            }
            
            // Check if the company_themes table exists before trying to use it
            if (Schema::hasTable('company_themes')) {
                try {
                    // Try to load company theme from database
                    $companyTheme = CompanyTheme::where('company_id', $companyId)
                        ->where('is_active', true)
                        ->first();
                    
                    // If no theme found, use default from config
                    if (!$companyTheme) {
                        $theme = Config::get('theme.default');
                    } else {
                        // Convert database theme to config format
                        $theme = $companyTheme->toThemeConfig();
                    }
                } catch (\Exception $e) {
                    // If there's any error, use default theme
                    $theme = Config::get('theme.default');
                }
            } else {
                // If table doesn't exist yet, use default theme
                $theme = Config::get('theme.default');
            }
            
            View::share('theme', $theme);
        }
    }
}
