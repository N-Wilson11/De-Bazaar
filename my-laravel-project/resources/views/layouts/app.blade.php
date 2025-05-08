<!DOCTYPE html>
@php
    // Direct de database-themakleur ophalen zonder transformaties
    $dbTheme = \App\Models\CompanyTheme::where('company_id', Session::get('company_id', 'default'))
        ->where('is_active', true)
        ->first();
    $dbBackgroundColor = $dbTheme ? $dbTheme->background_color : '#ffffff';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: {{ $dbBackgroundColor }} !important;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $theme['name'] ?? config('app.name') }} - @yield('title', 'Welcome')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ $theme['favicon'] ?? '/favicon.ico' }}" type="image/x-icon">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @if(isset($theme['custom_css']) && $theme['custom_css'])
        <link rel="stylesheet" href="{{ asset($theme['custom_css']) }}">
    @endif
    
    <!-- OVERRIDE BACKGROUND COLOR - DIRECT DB STYLING -->
    <style id="theme-colors">
        /* Forceer de achtergrondkleur direct uit de database op het hele document */
        html, body, main, .container, .container-fluid, 
        div:not(.card):not(.card-header):not(.card-body):not(.alert):not(.modal):not(.dropdown-menu):not(.navbar),
        section, article, aside {
            background-color: {{ $dbBackgroundColor }} !important;
        }
        
        /* Voor containers die direct in een main staan, laat de achtergrondkleur transparant */
        main > .container, main > .container-fluid {
            background-color: transparent !important;
        }
        
        /* Zorg ervoor dat alle andere kleuren goed worden toegepast */
        :root {
            --primary-color: {{ $theme['colors']['primary'] ?? '#4a90e2' }};
            --secondary-color: {{ $theme['colors']['secondary'] ?? '#f5a623' }};
            --accent-color: {{ $theme['colors']['accent'] ?? '#50e3c2' }};
            --text-color: {{ $theme['colors']['text'] ?? '#333333' }};
            --background-color: {{ $dbBackgroundColor }};
        }
        
        /* De body-achtergrond direct instellen met de database waarde */
        body {
            background-color: {{ $dbBackgroundColor }} !important;
            color: {{ $theme['colors']['text'] ?? '#333333' }} !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Element-specifieke kleurtoepassingen */
        .btn-primary {
            background-color: {{ $theme['colors']['primary'] ?? '#4a90e2' }} !important;
            border-color: {{ $theme['colors']['primary'] ?? '#4a90e2' }} !important;
        }
        
        .btn-secondary {
            background-color: {{ $theme['colors']['secondary'] ?? '#f5a623' }} !important;
            border-color: {{ $theme['colors']['secondary'] ?? '#f5a623' }} !important;
        }
        
        /* Navigatiebalk */
        .navbar {
            background-color: {{ $theme['colors']['primary'] ?? '#4a90e2' }} !important;
        }
        
        /* Footer styling */
        .footer {
            background-color: {{ $theme['colors']['primary'] ?? '#4a90e2' }} !important;
            color: #ffffff !important;
        }
        
        /* Achtergrondkleuren */
        .theme-background {
            background-color: {{ $dbBackgroundColor }} !important;
        }
    </style>
    
    <!-- Debug infobox -->
    <div style="position: fixed; bottom: 10px; right: 10px; background: #fff; border: 1px solid #000; padding: 10px; z-index: 9999; font-size: 12px; max-width: 400px;">
        <strong>Theme Debug Info:</strong><br>
        Theme Background: <span style="display: inline-block; width: 15px; height: 15px; background-color: {{ $theme['colors']['background'] ?? '#ffffff' }}; border: 1px solid #000;"></span> {{ $theme['colors']['background'] ?? 'Not set' }}<br>
        <strong>DB Background: <span style="display: inline-block; width: 15px; height: 15px; background-color: {{ $dbBackgroundColor }}; border: 1px solid #000;"></span> {{ $dbBackgroundColor }}</strong><br>
        Company ID: {{ Session::get('company_id', 'default') }}<br>
        Theme Name: {{ $theme['name'] ?? 'Default' }}<br>
    </div>
    
    @stack('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if(isset($theme['logo']) && $theme['logo'])
                        <img src="{{ asset($theme['logo']) }}" alt="{{ $theme['name'] ?? config('app.name') }}" height="40">
                    @else
                        {{ $theme['name'] ?? config('app.name') }}
                    @endif
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <!-- Navigation Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/dashboard') }}">{{ __('Dashboard') }}</a>
                        </li>
                        
                        <!-- Admin Menu Items -->
                        @auth
                            @if(auth()->user()->user_type === 'platform_owner')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                        {{ __('Admin') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('contracts.index') }}">{{ __('Contracts') }}</a></li>
                                        <li><a class="dropdown-item" href="{{ route('theme.settings') }}">{{ __('Theme Settings') }}</a></li>
                                    </ul>
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
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
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

    <main class="container py-4">
        @yield('content')
    </main>

    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <p>{{ $theme['footer_text'] ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' }}</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @if(isset($theme['custom_js']) && $theme['custom_js'])
        <script src="{{ asset($theme['custom_js']) }}"></script>
    @endif
    
    <!-- Forceer achtergrondkleur via JavaScript -->
    <script>
        // Force background color on page load and after any content changes
        document.addEventListener('DOMContentLoaded', function() {
            const dbBgColor = '{{ $dbBackgroundColor }}';
            document.documentElement.style.backgroundColor = dbBgColor;
            document.body.style.backgroundColor = dbBgColor;
            
            // Apply to all main containers except certain elements
            const applyBgColor = (selector) => {
                document.querySelectorAll(selector).forEach(el => {
                    // Skip elements inside cards
                    if (!el.closest('.card, .card-header, .card-body, .alert, .dropdown-menu')) {
                        el.style.backgroundColor = 'transparent';
                    }
                });
            };
            
            applyBgColor('.container, .container-fluid, main');
            
            // Add a class to body for easier targeting in custom styles
            document.body.classList.add('theme-applied');
            
            console.log('Applied DB background color: ' + dbBgColor);
        });
    </script>
    
    @stack('scripts')
</body>
</html>