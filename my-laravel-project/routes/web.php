<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\LanguageController;

// Pas de Language middleware toe op alle routes
Route::middleware('language')->group(function () {
    
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/home', function () {
        return view('home');
    });

    // Taal route
    Route::get('/language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.switch');

    // Authenticatie Routes
    Route::middleware('guest')->group(function () {
        // Registratie
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
        
        // Login
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });

    // Authenticatie vereiste routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Contract download route
        Route::get('/contract/{userId}', [ContractController::class, 'generateBusinessContract'])->name('contract.generate');
        
        // Contract management routes - alleen toegankelijk voor platformeigenaren (admin)
        Route::middleware('admin')->group(function () {
            Route::get('/contracts', [ContractsController::class, 'index'])->name('contracts.index');
            Route::get('/contracts/create', [ContractsController::class, 'create'])->name('contracts.create');
            Route::post('/contracts', [ContractsController::class, 'store'])->name('contracts.store');
            Route::get('/contracts/{contract}', [ContractsController::class, 'show'])->name('contracts.show');
            Route::get('/contracts/{contract}/download', [ContractsController::class, 'download'])->name('contracts.download');
            Route::post('/contracts/{contract}/review', [ContractsController::class, 'review'])->name('contracts.review');
            Route::delete('/contracts/{contract}', [ContractsController::class, 'destroy'])->name('contracts.destroy');
        });
    });
    
});
