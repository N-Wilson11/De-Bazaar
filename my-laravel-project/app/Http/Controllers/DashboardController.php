<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware will be applied at the route level
    }
    
    public function index()
    {
        return view('dashboard');
    }
}
