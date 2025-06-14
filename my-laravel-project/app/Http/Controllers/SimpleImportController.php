<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleImportController extends Controller
{
    /**
     * Create a new controller instance.
     */    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('business');
    }
    
    /**
     * Toon een eenvoudig formulier.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function showForm()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'De testcontroller werkt! Je bent ingelogd als: ' . Auth::user()->name
        ]);
    }
}
