<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\Business::class);
    }
    
    /**
     * Show the form for editing the company information.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Check if user has a company, if not, create a default one
        if (!$user->company) {
            $company = new Company([
                'name' => $user->name . '\'s Bedrijf',
                'slug' => Str::slug($user->name . '-bedrijf'),
                'email' => $user->email,
                'description' => 'Zakelijk account',
                'is_active' => true,
            ]);
            
            $company->save();            // Associate the company with the user
            \App\Models\User::where('id', $user->id)
                ->update(['company_id' => $company->id]);
            // Create a default theme for the company
            CompanyTheme::create([
                'company_id' => $company->id,
                'name' => $company->name,
                'primary_color' => '#4a90e2',
                'secondary_color' => '#f5a623',
                'accent_color' => '#50e3c2',
                'text_color' => '#333333',
                'background_color' => '#ffffff',
                'footer_text' => '© ' . date('Y') . ' ' . $company->name . '. Alle rechten voorbehouden.',
                'is_active' => true,
            ]);
        } else {
            $company = $user->company;
        }
        
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the company information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->route('companies.edit')
                ->with('error', __('Bedrijf niet gevonden. Er is een fout opgetreden.'));
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:companies,slug,' . $company->id,
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'landing_url' => 'nullable|string|max:100|unique:companies,landing_url,' . $company->id,
        ]);
        
        // Als geen slug opgegeven, maak er één op basis van de naam
        if (empty($request->slug) && $request->name) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }
        
        $company->fill($request->all());
        $company->save();
        
        return redirect()->route('companies.edit')
            ->with('success', __('Bedrijfsgegevens zijn succesvol bijgewerkt.'));
    }
}
