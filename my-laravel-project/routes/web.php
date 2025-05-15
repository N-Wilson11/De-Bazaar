<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ThemeController;
use App\Http\Middleware\CompanyThemeMiddleware;

// Apply both language and theme middleware to all routes
Route::middleware(['language', CompanyThemeMiddleware::class])->group(function () {
    
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/home', function () {
        return view('home');
    });

    // Taal route
    Route::get('/language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.switch');
    
    // Company theme switch routes (accessible to everyone)
    Route::get('/company/{companyId}', [ThemeController::class, 'switchCompany'])->name('company.switch');
    Route::post('/company/{companyId}', [ThemeController::class, 'switchCompany']);

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
        
        // Advertentie routes
        Route::get('/advertisements', [App\Http\Controllers\AdvertisementController::class, 'index'])->name('advertisements.index');
        Route::get('/advertisements/create', [App\Http\Controllers\AdvertisementController::class, 'create'])->name('advertisements.create');
        Route::post('/advertisements', [App\Http\Controllers\AdvertisementController::class, 'store'])->name('advertisements.store');
        Route::get('/advertisements/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'show'])->name('advertisements.show');
        Route::get('/advertisements/{advertisement}/edit', [App\Http\Controllers\AdvertisementController::class, 'edit'])->name('advertisements.edit');
        Route::put('/advertisements/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'update'])->name('advertisements.update');
        Route::delete('/advertisements/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'destroy'])->name('advertisements.destroy');
        
        // Speciale verhuur advertentie routes
        Route::get('/rentals', [App\Http\Controllers\AdvertisementController::class, 'rentals'])->name('rentals.index');
        Route::get('/my-rentals', [App\Http\Controllers\AdvertisementController::class, 'myRentals'])->name('rentals.my');
        Route::get('/rentals/create', [App\Http\Controllers\AdvertisementController::class, 'createRental'])->name('rentals.create');
        Route::post('/rentals', [App\Http\Controllers\AdvertisementController::class, 'storeRental'])->name('rentals.store');
        Route::get('/rentals/{advertisement}/calendar', [App\Http\Controllers\AdvertisementController::class, 'calendar'])->name('advertisements.calendar');
        Route::post('/rentals/{advertisement}/availability', [App\Http\Controllers\AdvertisementController::class, 'updateAvailability'])->name('advertisements.update-availability');
        
        // Contract management routes - alleen toegankelijk voor platformeigenaren (admin)
        Route::middleware('admin')->group(function () {
            Route::get('/contracts', [ContractsController::class, 'index'])->name('contracts.index');
            Route::get('/contracts/create', [ContractsController::class, 'create'])->name('contracts.create');
            Route::post('/contracts', [ContractsController::class, 'store'])->name('contracts.store');
            Route::get('/contracts/{contract}', [ContractsController::class, 'show'])->name('contracts.show');
            Route::get('/contracts/{contract}/download', [ContractsController::class, 'download'])->name('contracts.download');
            Route::post('/contracts/{contract}/review', [ContractsController::class, 'review'])->name('contracts.review');
            Route::delete('/contracts/{contract}', [ContractsController::class, 'destroy'])->name('contracts.destroy');
            
            // Thema-instellingen routes
            Route::get('/theme/settings', [ThemeController::class, 'index'])->name('theme.settings');
            Route::post('/theme/update', [ThemeController::class, 'update'])->name('theme.update');
            Route::get('/theme/logo', [ThemeController::class, 'changeLogo'])->name('theme.change-logo');
            Route::post('/theme/logo/update', [ThemeController::class, 'updateLogo'])->name('theme.update-logo');
            Route::post('/theme/logo/remove', [ThemeController::class, 'removeLogo'])->name('theme.remove-logo');
        });
    });
    
});
