<!DOCTYPE html>
@php
    // Direct alle database-thema-elementen ophalen zonder transformaties
    $dbTheme = \App\Models\CompanyTheme::where('company_id', Session::get('company_id', 'default'))
        ->where('is_active', true)
        ->first();
    
    // Database waarden direct gebruiken of terugvallen op defaults
    $dbBackgroundColor = $dbTheme ? $dbTheme->background_color : '#ffffff';
    $dbTextColor = $dbTheme ? $dbTheme->text_color : '#333333';
    $dbPrimaryColor = $dbTheme ? $dbTheme->primary_color : '#4a90e2';
    $dbSecondaryColor = $dbTheme ? $dbTheme->secondary_color : '#f5a623';
    $dbAccentColor = $dbTheme ? $dbTheme->accent_color : '#50e3c2';
    $dbLogoPath = $dbTheme && $dbTheme->logo_path ? $dbTheme->logo_path : null;
    $dbFaviconPath = $dbTheme && $dbTheme->favicon_path ? $dbTheme->favicon_path : '/favicon.ico';
    $dbCompanyName = $dbTheme ? $dbTheme->name : config('app.name');
    $dbFooterText = $dbTheme && $dbTheme->footer_text ? $dbTheme->footer_text : '© ' . date('Y') . ' ' . $dbCompanyName . '. All rights reserved.';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: {{ $dbBackgroundColor }} !important; height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $dbCompanyName }} - @yield('title', 'Welcome')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ $dbFaviconPath }}" type="image/x-icon">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @if($dbTheme && $dbTheme->custom_css_path)
        <link rel="stylesheet" href="{{ asset($dbTheme->custom_css_path) }}">
    @endif
    
    <!-- OVERRIDE THEME COLORS - DIRECT DB STYLING -->
    <style id="theme-colors">
        /* Forceer de kleuren direct uit de database op het hele document */
        html, body, main, .container, .container-fluid, 
        div:not(.card):not(.card-header):not(.card-body):not(.alert):not(.modal):not(.dropdown-menu):not(.navbar),
        section, article, aside {
            background-color: {{ $dbBackgroundColor }} !important;
        }
        
        /* Paginering stijlen */
        .pagination {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        .page-link {
            color: {{ $dbPrimaryColor }} !important;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }
        
        .page-item.active .page-link {
            color: #fff !important;
            background-color: {{ $dbPrimaryColor }} !important;
            border-color: {{ $dbPrimaryColor }} !important;
        }
        
        .page-item.disabled .page-link {
            color: #6c757d !important;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        /* Voor containers die direct in een main staan, laat de achtergrondkleur transparant */
        main > .container, main > .container-fluid {
            background-color: transparent !important;
        }
        
        /* Aanpassing om alle containers primaire kleur te geven */
        .navbar .container, 
        .footer .container,
        .primary-container,
        div.primary-container {
            background-color: {{ $dbPrimaryColor }} !important;
            color: white !important;
        }
        
        /* SUPER SPECIFIEKE REGEL VOOR HET CONTAINER ELEMENT OP BASIS VAN AFMETINGEN */
        div.container[style*="width: 1320px"], 
        div.container[style*="width:1320px"],
        div.container:not(.py-4):not(.text-center),
        body > header .container, 
        body > footer .container,
        nav .container {
            background-color: {{ $dbPrimaryColor }} !important;
            color: white !important;
        }

        /* Style het specifieke element uit de screenshot */
        div.container {
            background-color: {{ $dbPrimaryColor }} !important;
        }
        
        /* Zorg ervoor dat alle andere kleuren goed worden toegepast */
        :root {
            --primary-color: {{ $dbPrimaryColor }};
            --secondary-color: {{ $dbSecondaryColor }};
            --accent-color: {{ $dbAccentColor }};
            --text-color: {{ $dbTextColor }};
            --background-color: {{ $dbBackgroundColor }};
        }
        
        /* De body-achtergrond en tekstkleur direct instellen met database waarden */
        body {
            background-color: {{ $dbBackgroundColor }} !important;
            color: {{ $dbTextColor }} !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
            overflow-x: hidden;
            height: 100% !important;
        }
        
        /* Zorg ervoor dat de footer altijd onderaan staat en de navigatie bovenaan */
        header {
            width: 100%;
            position: sticky !important;
            top: 0 !important;
            z-index: 1000;
            order: 0 !important; /* Zorgt ervoor dat header eerste komt */
        }
        
        main {
            flex: 1 !important;
            position: relative;
            z-index: 1;
            order: 1 !important; /* Komt na de header */
            overflow: auto;
        }
        
        footer {
            width: 100%;
            position: relative;
            z-index: 100;
            order: 2 !important; /* Komt als laatste */
            margin-top: auto;
        }
        
        /* Text kleur for normale tekst */
        p, span, h1, h2, h3, h4, h5, h6, label, input, select, textarea, a:not(.btn):not(.nav-link) {
            color: {{ $dbTextColor }} !important;
        }
        
        /* Element-specifieke kleurtoepassingen */
        .btn-primary {
            background-color: {{ $dbPrimaryColor }} !important;
            border-color: {{ $dbPrimaryColor }} !important;
        }
        
        .btn-secondary {
            background-color: {{ $dbSecondaryColor }} !important;
            border-color: {{ $dbSecondaryColor }} !important;
        }
        
        /* Accentkleuren */
        .accent, .text-accent, .accent-color {
            color: {{ $dbAccentColor }} !important;
        }
        
        .accent-bg, .bg-accent {
            background-color: {{ $dbAccentColor }} !important;
        }
        
        /* Navigatiebalk */
        .navbar {
            background-color: {{ $dbPrimaryColor }} !important;
            width: 100%;
            position: relative;
            z-index: 100;
        }
        
        /* Specifiek voor navbarNav element */
        #navbarNav, .navbar-collapse {
            background-color: {{ $dbPrimaryColor }} !important;
        }
        
        /* Header-styling - zorgt dat de header bovenaan blijft */
        header {
            width: 100%;
            position: relative;
            z-index: 1000;
        }
        
        /* Main content - vult de beschikbare ruimte tussen header en footer */
        main {
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        /* Footer styling */
        .footer {
            background-color: {{ $dbPrimaryColor }} !important;
            color: #ffffff !important;
            width: 100%;
            position: relative;
            z-index: 100;
        }
        
        /* Primaire kleur elementen */
        .primary-color, .text-primary {
            color: {{ $dbPrimaryColor }} !important;
        }
        
        .primary-bg, .bg-primary {
            background-color: {{ $dbPrimaryColor }} !important;
        }
        
        /* Secundaire kleur elementen */
        .secondary-color, .text-secondary {
            color: {{ $dbSecondaryColor }} !important;
        }
        
        .secondary-bg, .bg-secondary {
            background-color: {{ $dbSecondaryColor }} !important;
        }
        
        /* Custom klassen */
        .theme-background {
            background-color: {{ $dbBackgroundColor }} !important;
        }
        
        .theme-text {
            color: {{ $dbTextColor }} !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if($dbLogoPath)
                        <img src="{{ asset($dbLogoPath) }}" alt="{{ $dbCompanyName }}" height="40">
                    @else
                        {{ $dbCompanyName }}
                    @endif
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav" style="background-color: {{ $dbPrimaryColor }} !important;">
                    <!-- Left side navigation items -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">{{ __('general.home') }}</a>
                        </li>
                        
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a>
                            </li>
                            
                            <!-- Advertentie menu items -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="advertentiesDropdown" role="button" data-bs-toggle="dropdown">
                                    {{ __('general.advertisements') }}
                                </a>
                                <ul class="dropdown-menu">
                                    @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                                        <li><a class="dropdown-item" href="{{ route('advertisements.index') }}">{{ __('general.my_advertisements') }}</a></li>
                                        <li><a class="dropdown-item" href="{{ route('advertisements.create') }}">{{ __('general.new_advertisement') }}</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('advertisements.browse') }}">{{ __('general.all_sale_advertisements') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                                        <li><a class="dropdown-item" href="{{ route('rentals.create') }}">{{ __('general.new_rental') }}</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('rentals.index') }}">{{ __('general.all_rental_advertisements') }}</a></li>
                                </ul>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('favorites.index') }}">
                                    <i class="bi bi-heart me-1"></i>{{ __('general.favorites') }}
                                </a>
                            </li>
                            
                            <!-- Biedingen menu-item -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bids.index') }}">
                                    <i class="bi bi-cash-stack me-1"></i>{{ __('Mijn biedingen') }}
                                </a>
                            </li>
                        @endauth
                        
                        <!-- Admin Menu Items -->
                        @auth
                            @if(auth()->user()->user_type === 'admin')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                        {{ __('Extra') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ url('/theme/settings') }}">{{ __('Themes') }}</a></li>
                                        <li><a class="dropdown-item" href="{{ url('/contracts') }}">{{ __('Contracts') }}</a></li>
                                    </ul>
                                </li>
                            @endif
                            

                        @endauth
                    </ul>
                    
                    <!-- Right side navigation items -->
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <!-- Shopping Cart Link -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cart.index') }}">
                                    <i class="bi bi-cart3"></i>
                                    {{ __('general.cart') }}
                                    @php
                                        $cartItemCount = Auth::user()->activeCart ? Auth::user()->activeCart->items->count() : 0;
                                    @endphp
                                    @if($cartItemCount > 0)
                                        <span class="badge rounded-pill bg-danger">{{ $cartItemCount }}</span>
                                    @endif
                                </a>
                            </li>
                            
                            <!-- Orders Link -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    {{ __('general.my_orders') }}
                                </a>
                            </li>
                            
                            <!-- Seller Sales Link - Only visible to users with advertisements -->
                            @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.my-sales') }}">
                                    <i class="bi bi-graph-up"></i>
                                    {{ __('general.my_sales') }}
                                </a>
                            </li>
                            @endif
                            

                        @endauth
                        
                        <!-- Language Switcher -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ App::getLocale() === 'nl' ? 'Nederlands' : 'English' }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('language/en') }}">English</a></li>
                                <li><a class="dropdown-item" href="{{ url('language/nl') }}">Nederlands</a></li>
                            </ul>
                        </li>
                        
                        <!-- Auth Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('general.login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('general.register') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="bi bi-receipt me-2"></i>{{ __('general.my_orders') }}
                                        </a>
                                    </li>
                                    @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.my-sales') }}">
                                            <i class="bi bi-graph-up me-2"></i>{{ __('general.my_sales') }}
                                        </a>
                                    </li>
                                    @endif

                                    <li>
                                        <a class="dropdown-item" href="{{ route('cart.index') }}">
                                            <i class="bi bi-cart3 me-2"></i>{{ __('general.cart') }}
                                            @php
                                                $cartItemCount = Auth::user()->activeCart ? Auth::user()->activeCart->items->count() : 0;
                                            @endphp
                                            @if($cartItemCount > 0)
                                                <span class="badge rounded-pill bg-danger">{{ $cartItemCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('general.logout') }}
                                        </a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container py-4">
            @yield('content')
        </div>
    </main>

    <footer class="footer mt-auto py-3">
        <div class="container text-center" style="background-color: {{ $dbPrimaryColor }} !important; color: white !important;">
            <p class="mb-0">{{ $dbFooterText }}</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @if($dbTheme && $dbTheme->custom_js_path)
        <script src="{{ asset($dbTheme->custom_js_path) }}"></script>
    @endif
    
    <!-- Forceer thema-kleuren via JavaScript -->
    <script>
        // Force theme colors on page load and after any content changes
        document.addEventListener('DOMContentLoaded', function() {
            // Direct database kleuren toepassen
            const bgColor = '{{ $dbBackgroundColor }}';
            const textColor = '{{ $dbTextColor }}';
            const primaryColor = '{{ $dbPrimaryColor }}';
            const secondaryColor = '{{ $dbSecondaryColor }}';
            const accentColor = '{{ $dbAccentColor }}';
            
            // Root elementen instellen
            document.documentElement.style.backgroundColor = bgColor;
            document.documentElement.style.color = textColor;
            document.body.style.backgroundColor = bgColor;
            document.body.style.color = textColor;
            
            // Fix voor navbar-positie - zorg dat de header altijd bovenaan blijft
            document.querySelector('header').style.position = 'sticky';
            document.querySelector('header').style.top = '0';
            document.querySelector('header').style.zIndex = '1000';
            document.querySelector('footer').style.position = 'relative';
            document.querySelector('footer').style.bottom = '0';
            
            // Forceer de juiste volgorde
            document.body.prepend(document.querySelector('header'));
            document.body.appendChild(document.querySelector('footer'));
            
            // Apply to all main containers except certain elements
            const applyBgColor = (selector) => {
                document.querySelectorAll(selector).forEach(el => {
                    // Skip elements inside cards or with manual styling
                    if (!el.closest('.card, .card-header, .card-body, .alert, .dropdown-menu') && 
                        !el.hasAttribute('style') && 
                        !el.classList.contains('primary-container') && 
                        !el.classList.contains('primary-bg')) {
                        el.style.backgroundColor = 'transparent';
                    }
                });
            };
            
            applyBgColor('.container, .container-fluid, main');
            
            // Add a class to body for easier targeting in custom styles
            document.body.classList.add('theme-applied');
            
            console.log('Applied DB theme colors');
        });
    </script>
    
    <!-- Zorg ervoor dat de navbar bovenaan staat en niet onderaan -->
    <script>
        // Direct korrigeren van de DOM structuur op pageload
        window.onload = function() {
            // Check huidige DOM structuur
            const header = document.querySelector('header');
            const footer = document.querySelector('footer');
            const body = document.body;
            
            // Zorg ervoor dat header altijd het eerste element is
            if (header && body.firstChild !== header) {
                body.prepend(header);
            }
            
            // Zorg ervoor dat footer altijd het laatste element is
            if (footer && body.lastChild !== footer) {
                body.appendChild(footer);
            }
            
            // Forceer de juiste stijlen
            header.style.position = 'sticky';
            header.style.top = '0';
            header.style.zIndex = '9999';
            header.style.width = '100%';
            header.style.order = '-1'; // Negatieve waarde zorgt dat het bovenaan komt
            
            // Forceer de juiste stijlen voor de main
            const main = document.querySelector('main');
            if (main) {
                main.style.order = '0';
                main.style.flex = '1';
            }
            
            // Forceer de juiste stijlen voor de footer
            if (footer) {
                footer.style.order = '1';
                footer.style.width = '100%';
                footer.style.marginTop = 'auto';
            }
            
            console.log('DOM structuur gecorrigeerd: header bovenaan, footer onderaan');
        };
    </script>
    
    @stack('scripts')
</body>
</html>