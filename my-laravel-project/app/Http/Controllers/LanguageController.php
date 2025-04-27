<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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
        // Log voor debugging
        Log::info('Taalwissel poging', ['locale' => $locale]);
        
        // Only allow 'en' or 'nl' locales
        if ($locale === 'en' || $locale === 'nl') {
            // Store the locale in the session
            Session::put('locale', $locale);
            
            // Force the session to be saved immediately
            Session::save();
            
            // Set the locale for the current request
            App::setLocale($locale);
            
            Log::info('Taal succesvol gewisseld', [
                'locale' => $locale, 
                'session_id' => Session::getId(),
                'has_locale_in_session' => Session::has('locale')
            ]);
        }
        
        return redirect()->back();
    }
}