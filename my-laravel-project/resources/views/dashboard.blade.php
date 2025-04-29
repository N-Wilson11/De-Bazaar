<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('general.dashboard') }} - De Bazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">De Bazaar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('dashboard') }}">{{ __('general.dashboard') }}</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-3 d-flex align-items-center">
                        @include('shared.language_selector')
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('general.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('general.dashboard') }}</span>
                        @if(Auth::user()->user_type === 'zakelijk')
                            <a href="{{ route('contract.generate', Auth::user()->id) }}" class="btn btn-primary btn-sm">
                                {{ __('general.download_contract') }}
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <h3>{{ __('general.welcome') }}, {{ Auth::user()->name }}</h3>
                        <p>{{ __('general.successfulllogin') }}</p>
                        
                        <div class="mt-4">
                            <h4>{{ __('general.account_details') }}</h4>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>{{ __('general.name') }}</th>
                                        <td>{{ Auth::user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.email') }}</th>
                                        <td>{{ Auth::user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.user_type') }}</th>
                                        <td>
                                            @if(Auth::user()->user_type === 'particulier')
                                                {{ __('general.private_user') }}
                                            @else
                                                {{ __('general.business_user') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('general.registered_on') }}</th>
                                        <td>{{ Auth::user()->created_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        @if(Auth::user()->user_type === 'zakelijk')
                            <div class="mt-4 p-3 bg-light rounded">
                                <h5>{{ __('general.business_info') }}</h5>
                                <p>{{ __('general.business_features') }}</p>
                                <ul>
                                    <li>{{ __('general.unlimited_ads') }}</li>
                                    <li>{{ __('general.stats') }}</li>
                                    <li>{{ __('general.priority_search') }}</li>
                                </ul>
                                <p>{{ __('general.download_contract_info') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>