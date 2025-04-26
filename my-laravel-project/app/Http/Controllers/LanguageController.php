<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change the application language.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLanguage($locale)
    {
        // Only allow 'en' or 'nl' locales
        if ($locale === 'en' || $locale === 'nl') {
            // Store the locale in the session
            Session::put('locale', $locale);
            
            // Set the locale for the current request
            App::setLocale($locale);
        }
        
        return redirect()->back();
    }
}