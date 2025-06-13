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
    
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/home', function () {
        return redirect()->route('home');
    });

    // QR code routes (accessible to everyone)
    Route::get('/qrcode/advertisement/{advertisement}', [App\Http\Controllers\QrCodeController::class, 'show'])->name('qrcode.advertisement');

    // Public advertisement routes
    Route::get('/browse', [App\Http\Controllers\AdvertisementController::class, 'browse'])->name('advertisements.browse');
    Route::get('/rentals', [App\Http\Controllers\AdvertisementController::class, 'rentals'])->name('rentals.index');

    // Taal route
    Route::get('/language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.switch');
    
    // Company theme switch routes (accessible to everyone)
    Route::get('/company/{companyId}', [ThemeController::class, 'switchCompany'])->name('company.switch');
    Route::post('/company/{companyId}', [ThemeController::class, 'switchCompany']);
    
    // Company landing page routes (accessible to everyone)
    Route::get('/bedrijf/{landingUrl}', [App\Http\Controllers\CompanyLandingController::class, 'show'])->name('company.landing');

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
        
        // Alle advertentie routes
        Route::get('/advertisements', [App\Http\Controllers\AdvertisementController::class, 'index'])->name('advertisements.index');
        Route::get('/advertisements/create', [App\Http\Controllers\AdvertisementController::class, 'create'])->name('advertisements.create');
        Route::post('/advertisements', [App\Http\Controllers\AdvertisementController::class, 'store'])->name('advertisements.store');
        Route::get('/advertisements/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'show'])->name('advertisements.show');
        Route::get('/advertisements/{advertisement}/edit', [App\Http\Controllers\AdvertisementController::class, 'edit'])->name('advertisements.edit');
        Route::put('/advertisements/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'update'])->name('advertisements.update');
        Route::delete('/advertisements/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'destroy'])->name('advertisements.destroy');
        
        // Speciale verhuur advertentie routes
        Route::get('/rentals/create', [App\Http\Controllers\AdvertisementController::class, 'createRental'])->name('rentals.create');
        Route::post('/rentals', [App\Http\Controllers\AdvertisementController::class, 'storeRental'])->name('rentals.store');
        Route::get('/rentals/{advertisement}/calendar', [App\Http\Controllers\AdvertisementController::class, 'calendar'])->name('advertisements.calendar');
        Route::post('/rentals/{advertisement}/availability', [App\Http\Controllers\AdvertisementController::class, 'updateAvailability'])->name('advertisements.update-availability');
        
        // Cart routes
        Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{advertisement}', [App\Http\Controllers\CartController::class, 'addItem'])->name('cart.add');
        Route::post('/cart/update/{cartItem}', [App\Http\Controllers\CartController::class, 'updateQuantity'])->name('cart.update');
        Route::delete('/cart/remove/{cartItem}', [App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.remove');
        Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'clearCart'])->name('cart.clear');
        Route::get('/cart/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');
        
        // Order routes
        Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/confirmation', [App\Http\Controllers\OrderController::class, 'confirmation'])->name('orders.confirmation');
        Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
        
        // Advertisement Review routes
        Route::get('/advertisements/{advertisement}/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/advertisements/{advertisement}/reviews/create', [App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/advertisements/{advertisement}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/reviews/{review}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
        
        // Advertiser Profile and Review routes
        Route::get('/advertisers/{user}', [App\Http\Controllers\AdvertiserController::class, 'show'])->name('advertisers.show');
        Route::get('/advertisers/{user}/reviews', [App\Http\Controllers\AdvertiserReviewController::class, 'index'])->name('advertiser.reviews.index');
        Route::get('/advertisers/{user}/reviews/create', [App\Http\Controllers\AdvertiserReviewController::class, 'create'])->name('advertiser.reviews.create');
        Route::post('/advertisers/{user}/reviews', [App\Http\Controllers\AdvertiserReviewController::class, 'store'])->name('advertiser.reviews.store');
        
        // Favorite routes
        Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/{advertisement}', [App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::delete('/favorites/{advertisement}', [App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorites.destroy');
        
        // Seller Order Management routes
        Route::get('/my-sales', [App\Http\Controllers\OrderController::class, 'mySales'])->name('orders.my-sales');
        Route::get('/my-sales/{orderItem}', [App\Http\Controllers\OrderController::class, 'showSaleItem'])->name('orders.show-sale-item');
        Route::post('/my-sales/{orderItem}/complete', [App\Http\Controllers\OrderController::class, 'completeSaleItem'])->name('orders.complete-sale-item');
        
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
        
        // Landing page settings routes - toegankelijk voor zakelijke gebruikers
        Route::middleware(\App\Http\Middleware\Business::class)->group(function () {
            Route::get('/landing/settings', [App\Http\Controllers\CompanyLandingController::class, 'settings'])->name('landing.settings');
            Route::post('/landing/update', [App\Http\Controllers\CompanyLandingController::class, 'update'])->name('landing.update');
        });
    });
    
});
