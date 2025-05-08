<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
use App\Models\CompanyTheme;

class ThemeController extends Controller
{
    /**
     * Display the theme settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get current company ID from session or use a default
        $companyId = Session::get('company_id', 'default');
        
        // Try to load the company theme from database
        $companyTheme = CompanyTheme::where('company_id', $companyId)->first();
        
        // If no theme exists yet, use the default theme from config
        if (!$companyTheme) {
            $theme = Config::get('theme.default');
        } else {
            $theme = $companyTheme->toThemeConfig();
        }
        
        return view('theme.settings', [
            'theme' => $theme,
            'companyId' => $companyId,
            'companyTheme' => $companyTheme
        ]);
    }

    /**
     * Update the theme settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'background_color' => 'required|string|max:7',
            'footer_text' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get current company ID from session or use a default
        $companyId = Session::get('company_id', 'default');

        // Handle logo upload if provided
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $logoName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images/theme/' . $companyId);
            
            // Create directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            $image->move($destinationPath, $logoName);
            $logoPath = '/images/theme/' . $companyId . '/' . $logoName;
        }

        // Find or create the company theme
        $companyTheme = CompanyTheme::firstOrNew(['company_id' => $companyId]);
        
        // Update theme attributes
        $companyTheme->name = $request->name;
        $companyTheme->primary_color = $request->primary_color;
        $companyTheme->secondary_color = $request->secondary_color;
        $companyTheme->accent_color = $request->accent_color;
        $companyTheme->text_color = $request->text_color;
        $companyTheme->background_color = $request->background_color;
        $companyTheme->footer_text = $request->footer_text;
        $companyTheme->is_active = true;
        
        // Update logo if a new one was uploaded
        if ($logoPath) {
            $companyTheme->logo_path = $logoPath;
        }
        
        // Save the theme to database
        $companyTheme->save();
        
        // Clear view cache to ensure changes are visible immediately
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Continue even if cache clearing fails
        }
        
        // Add debug output to the session
        Session::flash('debug_info', [
            'background_color' => $request->background_color,
            'company_id' => $companyId,
            'saved_to_db' => true
        ]);

        return redirect()->route('theme.settings')
            ->with('success', __('theme.settings_updated'));
    }

    /**
     * Switch to a different company theme
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $companyId
     * @return \Illuminate\Http\Response
     */
    public function switchCompany(Request $request, $companyId)
    {
        // If companyId is 'new', use the value from the form
        if ($companyId === 'new' && $request->has('new_company_id')) {
            $companyId = $request->new_company_id;
        }

        // Store the company ID in session if it's valid
        if (!empty($companyId)) {
            Session::put('company_id', $companyId);
        }
        
        return redirect()->route('theme.settings')
            ->with('success', __('Company switched to') . ': ' . $companyId);
    }

    /**
     * Display logo change form.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeLogo()
    {
        // Get current company ID from session or use a default
        $companyId = Session::get('company_id', 'default');
        
        // Load the company theme from database
        $companyTheme = CompanyTheme::where('company_id', $companyId)
                        ->where('is_active', true)
                        ->first();
        
        return view('theme.change_logo', [
            'companyTheme' => $companyTheme,
            'companyId' => $companyId
        ]);
    }

    /**
     * Update company logo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get current company ID from session or use a default
        $companyId = Session::get('company_id', 'default');

        // Find the company theme
        $companyTheme = CompanyTheme::where('company_id', $companyId)->first();
        
        if (!$companyTheme) {
            return redirect()->back()
                ->with('error', __('Company theme not found'));
        }

        // Handle logo upload
        $image = $request->file('logo');
        $logoName = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('/images/theme/' . $companyId);
        
        // Create directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        // Delete old logo file if it exists and is not a default logo
        if ($companyTheme->logo_path && File::exists(public_path($companyTheme->logo_path)) && 
            !str_starts_with($companyTheme->logo_path, '/default/')) {
            File::delete(public_path($companyTheme->logo_path));
        }
        
        // Upload and save new logo
        $image->move($destinationPath, $logoName);
        $logoPath = '/images/theme/' . $companyId . '/' . $logoName;
        
        // Update theme logo path
        $companyTheme->logo_path = $logoPath;
        $companyTheme->save();
        
        // Clear view cache to ensure changes are visible immediately
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Continue even if cache clearing fails
        }

        return redirect()->route('theme.change-logo')
            ->with('success', __('Logo updated successfully'));
    }
    
    /**
     * Remove company logo and revert to default.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeLogo()
    {
        // Get current company ID from session or use a default
        $companyId = Session::get('company_id', 'default');

        // Find the company theme
        $companyTheme = CompanyTheme::where('company_id', $companyId)->first();
        
        if (!$companyTheme) {
            return redirect()->route('theme.change-logo')
                ->with('error', __('Company theme not found'));
        }

        // Delete existing logo file if it exists and is not a default logo
        if ($companyTheme->logo_path && File::exists(public_path($companyTheme->logo_path)) && 
            !str_starts_with($companyTheme->logo_path, '/default/')) {
            File::delete(public_path($companyTheme->logo_path));
        }
        
        // Set logo path to null or default logo
        $companyTheme->logo_path = null; // Or set to a default logo path if you have one
        $companyTheme->save();
        
        // Clear view cache to ensure changes are visible immediately
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Continue even if cache clearing fails
        }

        return redirect()->route('theme.change-logo')
            ->with('success', __('Logo removed successfully'));
    }
}