<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

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
        
        return User::create($userData);
    }
}
