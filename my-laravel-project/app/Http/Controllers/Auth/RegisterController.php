<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __construct()
    {
        // Use middleware through the route instead of in the controller
    }
    
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        
        $user = $this->create($request->all());
        
        event(new Registered($user));
        
        // Use the Auth facade instead of the auth() helper
        Auth::login($user);
        
        return redirect('/dashboard');
    }
      protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'user_type' => ['required', 'string', 'in:particulier,zakelijk,normaal'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        
        // Valideer company_id alleen als het gebruikerstype 'normaal' is
        if (isset($data['user_type']) && $data['user_type'] === 'normaal') {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        }
        
        return Validator::make($data, $rules);
    }
      protected function create(array $data)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'user_type' => $data['user_type'],
            'password' => Hash::make($data['password']),
        ];
        
        // Voeg company_id toe als het gebruikerstype 'normaal' is
        if ($data['user_type'] === 'normaal' && isset($data['company_id'])) {
            $userData['company_id'] = $data['company_id'];
        }
        
        // Maak een bedrijf aan als het gebruikerstype 'zakelijk' is
        if ($data['user_type'] === 'zakelijk') {
            // Maak een nieuw bedrijf
            $company = \App\Models\Company::create([
                'name' => $data['name'] . '\'s Bedrijf', // Standaard bedrijfsnaam, kan later worden gewijzigd
                'slug' => \Illuminate\Support\Str::slug($data['name'] . '-bedrijf'),
                'email' => $data['email'],
                'description' => 'Zakelijk account',
                'is_active' => true,
            ]);
            
            // Maak een standaard thema voor dit bedrijf
            \App\Models\CompanyTheme::create([
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
            
            // Koppel het bedrijf aan de gebruiker
            $userData['company_id'] = $company->id;
        }
        
        return User::create($userData);
    }
}
