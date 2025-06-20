<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/';
    
    public function __construct()
    {
        // Middleware will be applied at the route level
    }
    
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user belongs to a company with a landing page
            if ($user->company_id) {
                $company = \App\Models\Company::find($user->company_id);
                if ($company && $company->landing_url) {
                    return redirect()->route('company.landing', $company->landing_url);
                }
            }
            
            // Als zakelijke gebruiker, check voor landingpage
            if ($user->user_type === 'zakelijk' && $user->company) {
                $company = $user->company;
                
                // Als de company een landing_url heeft, redirect naar die pagina
                if ($company->landing_url) {
                    return redirect()->route('company.landing', $company->landing_url);
                }
            }
            
            // Anders naar dashboard
            return redirect()->intended(route('dashboard'));
        }
        
        // Vriendelijke foutmelding in plaats van exception
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => __('auth.failed'),
            ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
