@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="container py-4">
    @if($company->landing_content)
    <!-- Custom Content - This is the user's own content -->
    <div class="custom-landing-content">
        {!! $company->landing_content !!}
    </div>
    @else
    <!-- Default Content (only shown when no custom content is provided) -->
    <div class="text-center py-4">
        <h1 class="display-4 fw-bold">{{ $company->name }}</h1>
        <p class="lead">{{ $company->description }}</p>
        
        <div class="mt-4">
            @if($company->website)
            <a href="{{ $company->website }}" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-globe me-1"></i>{{ __('Visit Website') }}
            </a>
            @endif
            
            <a href="{{ route('advertisements.browse') }}?company={{ $company->id }}" class="btn btn-primary ms-2">
                <i class="bi bi-shop me-1"></i>{{ __('Browse All Products') }}
            </a>
        </div>
    </div>
    @endif
    
    <!-- Optional contact information, only if no custom content is provided -->
    @if(!$company->landing_content && ($company->address || $company->email || $company->phone))
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-center">{{ __('Contact Information') }}</h5>
                    
                    @if($company->address)
                    <div class="mb-2 text-center">
                        <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                        {{ $company->address }}, {{ $company->city }} {{ $company->postal_code }}
                    </div>
                    @endif
                    
                    @if($company->email)
                    <div class="mb-2 text-center">
                        <i class="bi bi-envelope-fill text-primary me-1"></i>
                        <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                    </div>
                    @endif
                    
                    @if($company->phone)
                    <div class="mb-2 text-center">
                        <i class="bi bi-telephone-fill text-primary me-1"></i>
                        <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>    @endif
    
    <!-- Only show products if there is no custom landing content OR if custom content explicitly allows it -->
    @if(!$company->landing_content || strpos($company->landing_content, '<!-- SHOW_PRODUCTS -->') !== false)
        <!-- Latest Products -->
        @if($advertisements->count() > 0)
        <div class="mb-4">
            <h2 class="fw-bold mb-3">{{ __('Our Latest Products') }}</h2>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($advertisements as $advertisement)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        @if(!empty($advertisement->images))
                            <img src="{{ $advertisement->getFirstImageUrl() }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $advertisement->title }}">
                        @else
                            <div class="bg-light text-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 4rem; line-height: 200px;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $advertisement->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($advertisement->description, 80) }}</p>
                            <p class="card-text fw-bold">€ {{ number_format($advertisement->price, 2, ',', '.') }}</p>
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('advertisements.browse') }}?company={{ $company->id }}" class="btn btn-outline-primary">
                    {{ __('View All Products') }} <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        @endif
        
        <!-- Rental Products -->
        @if($rentalAds->count() > 0)
        <div class="mb-4">
            <h2 class="fw-bold mb-3">{{ __('Our Rental Products') }}</h2>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($rentalAds as $rental)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        @if(!empty($rental->images))
                            <img src="{{ $rental->getFirstImageUrl() }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $rental->title }}">
                        @else
                            <div class="bg-light text-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 4rem; line-height: 200px;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <span class="badge bg-info mb-2">{{ __('Rental') }}</span>
                            <h5 class="card-title">{{ $rental->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($rental->description, 80) }}</p>
                            <p class="card-text fw-bold">€ {{ number_format($rental->price, 2, ',', '.') }} / {{ __($rental->rental_period) }}</p>
                            <a href="{{ route('advertisements.show', $rental) }}" class="btn btn-primary">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('rentals.index') }}?company={{ $company->id }}" class="btn btn-outline-primary">
                    {{ __('View All Rentals') }} <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
