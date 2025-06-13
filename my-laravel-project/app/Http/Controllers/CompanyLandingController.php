<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class CompanyLandingController extends Controller
{
    /**
     * Display the landing page for a company.
     *
     * @param  string  $landingUrl
     * @return \Illuminate\Http\Response
     */    public function show($landingUrl)
    {
        // Find the company by landing URL
        $company = Company::where('landing_url', $landingUrl)->first();
        
        if (!$company || !$company->is_active) {
            abort(404, 'Company landing page not found');
        }
        
        // Store the company ID in the session
        Session::put('company_id', $company->id);
        
        // Get the company theme
        $theme = $company->theme;
          // Load active page components
        $components = $company->activePageComponents;
        
        // Alleen de componenten worden weergegeven op de landingspagina
        return view('companies.landing', compact('company', 'theme', 'components'));
    }
      /**
     * Display the landing page settings for the authenticated company.
     *
     * @return \Illuminate\Http\Response
     */    public function settings()
    {
        $user = Auth::user();
        
        // Only business users or admins can access this
        if ($user->user_type !== 'zakelijk' && $user->user_type !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', __('You do not have permission to access this page.'));
        }
        
        $company = $user->company;
        
        // If the user doesn't have a company, create one
        if (!$company && $user->user_type === 'zakelijk') {
            // Create a new company for this business user
            $company = new \App\Models\Company([
                'name' => $user->name . '\'s Company',
                'slug' => \Illuminate\Support\Str::slug($user->name . '-company-' . $user->id),
                'email' => $user->email,
                'description' => 'My business page',
                'is_active' => true
            ]);
            
            $company->save();            // Associate the company with the user
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['company_id' => $company->id]);
            
            // Create a default theme for the company
            $theme = new \App\Models\CompanyTheme([
                'company_id' => $company->id,
                'name' => $company->name,
                'background_color' => '#ffffff',
                'text_color' => '#333333',
                'primary_color' => '#4a90e2',
                'secondary_color' => '#f5a623',
                'accent_color' => '#50e3c2',
                'is_active' => true
            ]);
            
            $theme->save();
        }
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', __('There was a problem creating your company profile. Please contact support.'));
        }
        
        // Laad de pageComponents voor de component tab
        $pageComponents = $company->pageComponents;
        
        return view('companies.landing-settings', compact('company', 'pageComponents'));
    }
      /**
     * Update the landing page settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Only business users or admins can access this
        if ($user->user_type !== 'zakelijk' && $user->user_type !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', __('You do not have permission to access this page.'));
        }
        
        // Redirect to settings which will create a company if needed
        if (!$user->company) {
            return redirect()->route('landing.settings');
        }
        
        $company = $user->company;
        
        // Validate the request
        $request->validate([
            'landing_url' => 'required|alpha_dash|unique:companies,landing_url,' . $company->id,
            'landing_content' => 'nullable|string',
        ]);        
        // Update the company
        $company->landing_url = $request->landing_url;
        $company->landing_content = $request->landing_content;
        $company->save();
        
        return redirect()->route('landing.settings')
            ->with('success', __('Landing page settings updated successfully.'));
    }
}
