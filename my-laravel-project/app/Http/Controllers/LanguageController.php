<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

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
        // Only allow 'en' or 'nl' as valid locales
        if ($locale === 'en' || $locale === 'nl') {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}