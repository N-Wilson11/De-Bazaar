@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="container py-4">
    @if(isset($components) && $components->count() > 0)
        <!-- Component-based landing page -->
        @foreach($components as $component)
            <div class="component mb-5" id="component-{{ $component->id }}">
                @switch($component->type)
                    @case('hero')
                        <div class="hero-component">
                            <div class="row align-items-center">
                                @if(isset($component->settings['image_path']))
                                <div class="col-md-6">
                                    <img src="{{ asset($component->settings['image_path']) }}" alt="Hero image" class="img-fluid rounded">
                                </div>
                                <div class="col-md-6">
                                @else
                                <div class="col-md-12">
                                @endif
                                    <div class="py-3">
                                        {!! $component->content !!}
                                        
                                        @if(isset($component->settings['button_text']) && isset($component->settings['button_url']))
                                        <div class="mt-4">
                                            <a href="{{ $component->settings['button_url'] }}" class="btn btn-primary">
                                                {{ $component->settings['button_text'] }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break
                        
                    @case('text')
                        <div class="text-component">
                            {!! $component->content !!}
                        </div>
                        @break
                        
                    @case('image')
                        <div class="image-component text-center">
                            @if(isset($component->settings['image_path']))
                            <img src="{{ asset($component->settings['image_path']) }}" 
                                alt="{{ $component->settings['alt_text'] ?? 'Company image' }}" 
                                class="img-fluid rounded">
                            @endif
                        </div>
                        @break
                        
                    @case('featured_ads')
                        <div class="featured-ads-component">
                            <h3 class="mb-4">Uitgelichte advertenties</h3>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                                @php 
                                    $count = $component->settings['count'] ?? 4;
                                    $category = $component->settings['category'] ?? null;
                                    
                                    $featuredAds = \App\Models\Advertisement::whereHas('user', function ($query) use ($company) {
                                        $query->where('company_id', $company->id);
                                    })
                                    ->where('status', 'active');
                                    
                                    if ($category) {
                                        $featuredAds->where('category', $category);
                                    }
                                    
                                    $featuredAds = $featuredAds->orderBy('created_at', 'desc')
                                        ->take($count)
                                        ->get();
                                @endphp
                                
                                @forelse($featuredAds as $ad)
                                <div class="col">
                                    <div class="card h-100">
                                        <div style="height: 150px; overflow: hidden;">
                                            @if(!empty($ad->images))
                                                <img src="{{ $ad->getFirstImageUrl() }}" class="card-img-top" 
                                                    style="object-fit: cover; height: 100%; width: 100%;" alt="{{ $ad->title }}">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ Str::limit($ad->title, 40) }}</h5>
                                            <p class="card-text fw-bold">€ {{ number_format($ad->price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <a href="{{ route('advertisements.show', $ad) }}" class="btn btn-sm btn-outline-primary">Bekijk details</a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        Er zijn momenteel geen uitgelichte advertenties beschikbaar.
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @break
                        
                    @case('product_grid')
                        <div class="product-grid-component">
                            <h3 class="mb-4">{{ isset($component->settings['is_rental']) && $component->settings['is_rental'] ? 'Verhuur' : 'Producten' }}</h3>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                @php 
                                    $count = $component->settings['count'] ?? 8;
                                    $isRental = $component->settings['is_rental'] ?? false;
                                    
                                    $products = \App\Models\Advertisement::whereHas('user', function ($query) use ($company) {
                                        $query->where('company_id', $company->id);
                                    })
                                    ->where('status', 'active')
                                    ->where('is_rental', $isRental)
                                    ->orderBy('created_at', 'desc')
                                    ->take($count)
                                    ->get();
                                @endphp
                                
                                @forelse($products as $product)
                                <div class="col">
                                    <div class="card h-100">
                                        <div style="height: 200px; overflow: hidden;">
                                            @if(!empty($product->images))
                                                <img src="{{ $product->getFirstImageUrl() }}" class="card-img-top" 
                                                    style="object-fit: cover; height: 100%; width: 100%;" alt="{{ $product->title }}">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $product->title }}</h5>
                                            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                                            <p class="card-text fw-bold">€ {{ number_format($product->price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <a href="{{ route('advertisements.show', $product) }}" class="btn btn-outline-primary">Bekijk details</a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        Er zijn momenteel geen producten beschikbaar.
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @break
                        
                    @case('cta')
                        <div class="cta-component text-center bg-light p-5 rounded">
                            <div class="py-3">
                                {!! $component->content !!}
                                
                                @if(isset($component->settings['button_text']) && isset($component->settings['button_url']))
                                <div class="mt-4">
                                    <a href="{{ $component->settings['button_url'] }}" class="btn btn-primary btn-lg">
                                        {{ $component->settings['button_text'] }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @break
                        
                    @case('testimonials')
                        <div class="testimonials-component">
                            <h3 class="mb-4">Wat onze klanten zeggen</h3>
                            <div class="row row-cols-1 row-cols-md-3 g-4">
                                @php
                                    $count = $component->settings['count'] ?? 3;
                                    $reviews = \App\Models\Review::whereHas('advertisement.user', function ($query) use ($company) {
                                        $query->where('company_id', $company->id);
                                    })
                                    ->latest()
                                    ->take($count)
                                    ->get();
                                @endphp
                                
                                @forelse($reviews as $review)
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="mb-2">
                                                @for($i = 0; $i < $review->rating; $i++)
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                @endfor
                                                @for($i = $review->rating; $i < 5; $i++)
                                                    <i class="bi bi-star text-warning"></i>
                                                @endfor
                                            </div>
                                            <p class="card-text font-italic mb-3">"{{ Str::limit($review->comment, 150) }}"</p>
                                            <p class="card-text text-muted small">- {{ $review->user->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        Er zijn nog geen beoordelingen beschikbaar.
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @break
                        
                    @default
                        <!-- Onbekend component type -->
                @endswitch
            </div>
        @endforeach
        
    @elseif($company->landing_content)
        <!-- Custom Content - This is the user's own content -->
        <div class="custom-landing-content">
            {!! $company->landing_content !!}
        </div>
    @else
        <!-- Default Content (only shown when no custom content or components are provided) -->
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
        </div>    @endif
    
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
