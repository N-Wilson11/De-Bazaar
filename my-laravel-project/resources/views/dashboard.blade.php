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
                    
                    <!-- Rental Calendar Link -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-calendar-event me-2"></i>
                                <strong>{{ __('Nieuw!') }}</strong> {{ __('Je kunt nu een agenda overzicht zien van je gehuurde producten.') }}
                            </div>
                            <a href="{{ route('rentals.calendar') }}" class="btn btn-sm btn-primary">
                                {{ __('Bekijk Huurkalender') }} <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
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
                    @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
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
                                                @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                                                    <a href="{{ route('advertisements.create') }}" class="btn btn-primary {{ $normalAdsRemaining === 0 ? 'disabled' : '' }}">
                                                        <i class="bi bi-plus-circle me-1"></i>{{ __('general.new_advertisement') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Verhuuradvertenties -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('general.rental_advertisements') }}</h5>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-1">{{ __('general.current_ads') }}: <strong>{{ $rentalAdsCount }}</strong></p>
                                                <p class="mb-1">{{ __('general.maximum') }}: <strong>{{ $maxRentalAds }}</strong></p>
                                                <p class="mb-1">{{ __('general.still_available') }}: <strong>{{ $rentalAdsRemaining }}</strong></p>
                                            </div>
                                            <div>
                                                @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                                                    <a href="{{ route('rentals.create') }}" class="btn btn-success {{ $rentalAdsRemaining === 0 ? 'disabled' : '' }}">
                                                        <i class="bi bi-calendar-plus me-1"></i>{{ __('general.new_rental') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(Auth::user()->user_type === 'zakelijk')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.business_info') }}</h5>
                            <p>{{ __('general.business_features') }}</p>
                            <ul>
                                <li>{{ __('general.ad_limit_info') }}</li>
                                <li>{{ __('general.stats') }}</li>
                                <li>{{ __('general.priority_search') }}</li>
                            </ul>
                            <p>{{ __('general.download_contract_info') }}</p>
                        </div>
                        
                        <!-- Landing Page Settings for Business Users -->
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-shop me-2"></i>{{ __('Landing Pagina Instellingen') }}
                                </div>
                                <div class="card-body">
                                    <p>{{ __('Als zakelijk gebruiker kunt u uw eigen landingspagina instellen met een unieke URL.') }}</p>
                                    <p>{{ __('Deel deze pagina met uw klanten om uw producten te presenteren.') }}</p>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('landing.settings') }}" class="btn btn-primary">
                                            <i class="bi bi-gear me-2"></i>{{ __('Landingspagina Instellen') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Rental Calendar for Business Users -->
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-calendar-week me-2"></i>{{ __('Verhuurkalender') }}
                                </div>
                                <div class="card-body">
                                    <p>{{ __('Bekijk al uw verhuurde producten in een overzichtelijke agenda.') }}</p>
                                    <p>{{ __('Zo weet u precies wanneer u producten moet uitlenen en wanneer u ze terugkrijgt.') }}</p>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('rentals.advertiser-calendar') }}" class="btn btn-primary">
                                            <i class="bi bi-calendar3 me-2"></i>{{ __('Verhuuragenda Bekijken') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(Auth::user()->user_type === 'normaal')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.normal_user_info') }}</h5>
                            <p>{{ __('general.normal_user_features') }}</p>
                            <ul>
                                <li>{{ __('general.browse_advertisements') }}</li>
                                <li>{{ __('general.contact_advertiser') }}</li>
                                <li>{{ __('general.standard_search') }}</li>
                            </ul>
                        </div>
                    @elseif(Auth::user()->user_type === 'particulier')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.private_user_info') }}</h5>
                            <p>{{ __('general.private_user_features') }}</p>
                            <ul>
                                <li>{{ __('general.ad_limit_info') }}</li>
                                <li>{{ __('general.browse_advertisements') }}</li>
                                <li>{{ __('general.contact_advertiser') }}</li>
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Laatste advertenties sectie -->
                    <div class="mt-5">
                        <h4>{{ __('general.latest_advertisements') }}</h4>
                        
                        <!-- Laatste verkoopadvertenties -->
                        <div class="mb-4">
                            <h5 class="mb-3">{{ __('general.sale_advertisements') }}</h5>
                            <div class="row">
                                @if($latestSaleAds->count() > 0)
                                    @foreach($latestSaleAds as $advertisement)
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm">
                                                <div class="position-relative">
                                                    @php 
                                                        $imageUrl = $advertisement->getFirstImageUrl() ?? asset('images/no-image.png');
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $advertisement->title }}" style="height: 150px; object-fit: cover;">
                                                    <div class="position-absolute top-0 start-0 m-2">
                                                        <span class="badge bg-primary">{{ __('general.for_sale') }}</span>
                                                    </div>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <h5 class="card-title text-truncate">{{ $advertisement->title }}</h5>
                                                    <p class="card-text text-primary fw-bold">€ {{ number_format($advertisement->price, 2, ',', '.') }}</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar"></i> {{ $advertisement->created_at->format('d-m-Y') }}
                                                        </small>
                                                        <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('general.view') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <p>{{ __('general.no_advertisements') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <a href="{{ route('advertisements.browse') }}" class="btn btn-outline-primary">
                                    {{ __('general.view_all_sale_advertisements') }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Laatste verhuuradvertenties -->
                        <div>
                            <h5 class="mb-3">{{ __('general.rental_advertisements') }}</h5>
                            <div class="row">
                                @if($latestRentalAds->count() > 0)
                                    @foreach($latestRentalAds as $advertisement)
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm">
                                                <div class="position-relative">
                                                    @php 
                                                        $imageUrl = $advertisement->getFirstImageUrl() ?? asset('images/no-image.png');
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $advertisement->title }}" style="height: 150px; object-fit: cover;">
                                                    <div class="position-absolute top-0 start-0 m-2">
                                                        <span class="badge bg-info text-dark">{{ __('general.rentals') }}</span>
                                                    </div>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <h5 class="card-title text-truncate">{{ $advertisement->title }}</h5>
                                                    <p class="card-text text-primary fw-bold">€ {{ number_format($advertisement->price, 2, ',', '.') }} / {{ __('general.day') }}</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar"></i> {{ $advertisement->created_at->format('d-m-Y') }}
                                                        </small>
                                                        <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('general.view') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <p>{{ __('general.no_rentals') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <a href="{{ route('rentals.index') }}" class="btn btn-outline-primary">
                                    {{ __('general.view_all_rental_advertisements') }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Teruggebrachte huuropties sectie -->
                @if(!empty($returnedRentals) && $returnedRentals->count() > 0)
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-box-arrow-in-left me-2"></i>{{ __('Teruggebrachte Huurproducten') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Huurperiode') }}</th>
                                        <th>{{ __('Teruggebracht op') }}</th>
                                        <th>{{ __('Acties') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returnedRentals as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->advertisement && $item->advertisement->countImages() > 0)
                                                        <img src="{{ $item->advertisement->getFirstImageUrl() }}" 
                                                            class="img-thumbnail me-2" 
                                                            alt="{{ $item->title }}" 
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light text-center me-2" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <span>{{ $item->title }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($item->rental_start_date)->format('d-m-Y') }} -
                                                {{ \Carbon\Carbon::parse($item->rental_end_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($item->returned_at)->format('d-m-Y H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.return-details', $item) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye me-1"></i>{{ __('Details') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection