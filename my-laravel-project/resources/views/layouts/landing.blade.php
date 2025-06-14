<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $company->name ?? config('app.name') }} - @yield('title', 'Welkom')</title>
    
    <!-- Favicon -->
    @if($theme && $theme->favicon_path)
    <link rel="icon" href="{{ asset($theme->favicon_path) }}" type="image/x-icon">
    @else
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @if($theme && $theme->custom_css_path)
        <link rel="stylesheet" href="{{ asset($theme->custom_css_path) }}">
    @endif
    
    <style>
        :root {
            --primary-color: {{ $theme->primary_color ?? '#4a90e2' }};
            --secondary-color: {{ $theme->secondary_color ?? '#f5a623' }};
            --accent-color: {{ $theme->accent_color ?? '#50e3c2' }};
            --text-color: {{ $theme->text_color ?? '#333333' }};
            --background-color: {{ $theme->background_color ?? '#ffffff' }};
        }
        
        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .accent-color {
            color: var(--accent-color);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .landing-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 0;
        }
        
        .landing-footer {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            margin-top: auto;
        }
        
        .main-content {
            flex: 1;
            padding: 20px 0;
        }

        /* Custom user styles */
        @yield('custom-styles')
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="landing-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if($theme && $theme->logo_path)
                        <a href="{{ url('/') }}">
                            <img src="{{ asset($theme->logo_path) }}" alt="{{ $company->name ?? config('app.name') }}" height="40">
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="text-white text-decoration-none">
                            {{ $company->name ?? config('app.name') }}
                        </a>
                    @endif
                </div>
                <div>
                    <a href="{{ route('advertisements.browse') }}?company={{ $company->id ?? 'all' }}" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-shop me-1"></i>{{ __('Shop') }}
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-light ms-2">
                        <i class="bi bi-house me-1"></i>{{ __('Home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <div class="landing-footer">
        <div class="container text-center">
            <p class="mb-0">
                &copy; {{ date('Y') }} {{ $company->name ?? config('app.name') }}. {{ __('All rights reserved.') }}
            </p>
            <small>
                <a href="{{ url('/') }}" class="text-white text-decoration-none">{{ __('Back to main site') }}</a>
            </small>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @if($theme && $theme->custom_js_path)
        <script src="{{ asset($theme->custom_js_path) }}"></script>
    @endif
    
    @stack('scripts')
</body>
</html>
