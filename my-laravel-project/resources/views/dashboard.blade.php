@extends('layouts.app')

@section('title', __('general.dashboard'))
@section('content')
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
                                        @elseif(Auth::user()->user_type === 'zakelijk')
                                            {{ __('general.business_user') }}
                                        @elseif(Auth::user()->user_type === 'normaal')
                                            {{ __('general.normal_user') }}
                                        @else
                                            {{ Auth::user()->user_type }}
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
                    
                    <!-- Advertentie statistieken -->
                    <div class="mt-4">
                        <h4>{{ __('general.ad_statistics') }}</h4>
                        <div class="row">
                            <!-- Verkoopadvertenties -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('general.advertisements') }}</h5>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-1">{{ __('general.current_ads') }}: <strong>{{ $normalAdsCount }}</strong></p>
                                                <p class="mb-1">{{ __('general.maximum') }}: <strong>{{ $maxNormalAds }}</strong></p>
                                                <p class="mb-1">{{ __('general.still_available') }}: <strong>{{ $normalAdsRemaining }}</strong></p>
                                            </div>
                                            <div>
                                                <a href="{{ route('advertisements.create') }}" class="btn btn-primary {{ $normalAdsRemaining === 0 && $maxNormalAds !== __('general.unlimited') ? 'disabled' : '' }}">
                                                    <i class="bi bi-plus-circle me-1"></i>{{ __('general.new_advertisement') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Verhuuradvertenties -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Verhuuradvertenties') }}</h5>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-1">{{ __('general.current_ads') }}: <strong>{{ $rentalAdsCount }}</strong></p>
                                                <p class="mb-1">{{ __('general.maximum') }}: <strong>{{ $maxRentalAds }}</strong></p>
                                                <p class="mb-1">{{ __('general.still_available') }}: <strong>{{ $rentalAdsRemaining }}</strong></p>
                                            </div>
                                            <div>
                                                <a href="{{ route('rentals.create') }}" class="btn btn-success {{ $rentalAdsRemaining === 0 && $maxRentalAds !== __('general.unlimited') ? 'disabled' : '' }}">
                                                    <i class="bi bi-calendar-plus me-1"></i>{{ __('Nieuwe verhuuradvertentie') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    @elseif(Auth::user()->user_type === 'normaal')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.normal_user_info') }}</h5>
                            <p>{{ __('general.normal_user_features') }}</p>
                            <ul>
                                <li>{{ __('general.ad_limit_info') }}</li>
                                <li>{{ __('general.standard_search') }}</li>
                            </ul>
                        </div>
                    @elseif(Auth::user()->user_type === 'particulier')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.private_user_info') }}</h5>
                            <p>{{ __('general.private_user_features') }}</p>
                            <ul>
                                <li>{{ __('general.ad_limit_info') }}</li>
                                <li>{{ __('Advertenties bekijken en zoeken') }}</li>
                                <li>{{ __('Contact opnemen met verkopers en verhuurders') }}</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection