@extends('layouts.app')

@section('title', $advertisement->title)

@section('styles')
<style>    @media (max-width: 767px) {
        .social-share-btn {
            width: 100%;
        }
    }
    
    /* Ensure proper popover con                            @if($advertisement->isRental())
                                <a href="{{ route('rentals.rent', $advertisement) }}" class="btn btn-success">
                                    <i class="bi bi-calendar-check me-1"></i>{{ __('Nu huren') }}
                                </a>
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-outline-info mt-2">
                                    <i class="bi bi-calendar me-1"></i>{{ __('Beschikbaarheid bekijken') }}
                                </a>
                            @else
                                @if(isset($canBePurchased) && $canBePurchased)
                                    <form action="{{ route('cart.add', $advertisement) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-cart-plus me-1"></i>{{ __('In winkelwagen') }}
                                        </button>
                                    </form>
                                @endif
                            @endif/
    .popover {
        max-width: 250px;
    }
    
    .popover-body {
        padding: 15px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ $advertisement->title }}</h1>
                    
                    <div class="d-flex mb-3 gap-2 flex-wrap">
                        @if($advertisement->isRental())
                            <span class="badge bg-info text-dark">{{ __('general.rentals') }}</span>
                        @elseif($advertisement->type === 'auction')
                            <span class="badge bg-warning text-dark">{{ __('Veiling') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Verkoop') }}</span>
                        @endif
                          <span class="badge bg-light text-dark border">{{ __('general.category') }}: {{ ucfirst($advertisement->category) }}</span>
                        <span class="badge bg-light text-dark border">{{ __('general.condition') }}: {{ ucfirst($advertisement->condition) }}</span>
                    </div>                    @php
                        $images = is_array($advertisement->images) ? $advertisement->images : [];
                        $imageUrls = $advertisement->getAllImageUrls();
                    @endphp
                    @if(count($images) > 0)
                        <div class="advertisement-images mb-4">
                            <div id="advertisementCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner rounded">
                                    @foreach($images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ $imageUrls[$index] ?? asset('images/no-image.png') }}" class="d-block w-100" alt="{{ $advertisement->title }}">
                                        </div>
                                    @endforeach</div>
                                @if(count($images) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#advertisementCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">{{ __('Vorige') }}</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#advertisementCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">{{ __('Volgende') }}</span>
                                    </button>
                                @endif
                            </div>                              @if(count($images) > 1)
                                <div class="row mt-2">
                                    @foreach($images as $index => $image)
                                        <div class="col-3 mt-2">
                                            <img src="{{ $imageUrls[$index] ?? asset('images/no-image.png') }}" class="img-thumbnail" alt="{{ $advertisement->title }}"
                                                onclick="document.querySelector('#advertisementCarousel').carousel({{ $index }})">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <h5 class="mb-2">{{ __('Beschrijving') }}</h5>
                    <div class="mb-4">
                        {!! nl2br(e($advertisement->description)) !!}
                    </div>
                    
                    @if($advertisement->isRental())
                        <h5 class="mb-2">{{ __('Verhuurgegevens') }}</h5>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-2">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">{{ __('Per dag') }}</div>
                                        <div class="h4">€ {{ number_format($advertisement->rental_price_day ?? 0, 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($advertisement->rental_price_week)
                            <div class="col-md-4 mb-2">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">{{ __('Per week') }}</div>
                                        <div class="h4">€ {{ number_format($advertisement->rental_price_week, 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($advertisement->rental_price_month)
                            <div class="col-md-4 mb-2">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">{{ __('Per maand') }}</div>
                                        <div class="h4">€ {{ number_format($advertisement->rental_price_month, 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <strong>{{ __('Minimale huurtermijn') }}:</strong> 
                            {{ $advertisement->minimum_rental_days ?: 1 }} {{ $advertisement->minimum_rental_days == 1 ? __('dag') : __('dagen') }}
                        </div>
                        
                        @if($advertisement->rental_conditions)
                            <div class="mb-3">
                                <h6>{{ __('Verhuurvoorwaarden') }}</h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($advertisement->rental_conditions)) !!}
                                </div>
                            </div>
                        @endif
                        
                        @if($advertisement->rental_requires_deposit)
                            <div class="mb-3">
                                <h6>{{ __('Borg') }}</h6>
                                <div class="alert alert-info">
                                    {{ __('Voor deze verhuur is een borg vereist van') }} 
                                    <strong>€ {{ number_format($advertisement->rental_deposit_amount, 2, ',', '.') }}</strong>
                                </div>
                            </div>
                        @endif
                        
                        @if($advertisement->rental_pickup_location)
                            <div class="mb-3">
                                <h6>{{ __('Ophaallocatie') }}</h6>
                                <p>{{ $advertisement->rental_pickup_location }}</p>
                            </div>
                        @endif
                          <div class="mt-3 mb-4">
                            <div class="d-grid gap-2">
                                <a href="{{ route('rentals.rent', $advertisement) }}" class="btn btn-primary">
                                    <i class="bi bi-calendar-plus me-1"></i>{{ __('Nu huren') }}
                                </a>
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-calendar me-1"></i>{{ __('Beschikbaarheid bekijken') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            @include('advertisements._bidding_section')
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @if($advertisement->isRental())
                        <div class="mb-3 d-flex align-items-center">
                            <span class="badge bg-info text-dark me-2 px-3 py-2">{{ __('general.rentals') }}</span>
                            <span class="h4 mb-0">€ {{ number_format($advertisement->rental_price_day ?? 0, 2, ',', '.') }}/dag</span>
                        </div>
                        <p class="text-muted">{{ __('Vervangingswaarde') }}: € {{ number_format($advertisement->price, 2, ',', '.') }}</p>
                    @else
                        <div class="h3 mb-3">€ {{ number_format($advertisement->price, 2, ',', '.') }}</div>
                    @endif
                    
                    <div class="mb-3">
                        <strong><i class="bi bi-geo-alt me-1"></i>{{ __('Locatie') }}:</strong> 
                        {{ $advertisement->location ?: __('Niet gespecificeerd') }}
                    </div>
                    
                    <div class="mb-3">                        <strong><i class="bi bi-clock-history me-1"></i>{{ __('general.posted_on') }}:</strong> 
                        {{ $advertisement->created_at->format('d-m-Y') }}
                    </div>
                    
                    <div class="mb-3">                        <strong><i class="bi bi-eye me-1"></i>{{ __('general.views') }}:</strong> 
                        {{ $advertisement->views }} {{ __('general.viewed') }}
                    </div>
                    
                    @if($advertisement->user_id === Auth::id())
                        <div class="d-grid gap-2 mt-4">                            <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i>{{ __('general.edit') }}
                            </a>
                            
                            @if($advertisement->isRental())
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-outline-info">
                                    <i class="bi bi-calendar-event me-1"></i>{{ __('general.manage_availability') }}
                                </a>
                            @endif
                            
                            <form action="{{ route('advertisements.destroy', $advertisement) }}" method="POST">
                                @csrf
                                @method('DELETE')                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                    <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                                </button>
                            </form>
                        </div>
                    @else                        <div class="mb-3">
                            <h6>{{ __('general.offered_by') }}</h6>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle fs-4 me-2"></i>
                                <div>
                                    <a href="{{ route('advertisers.show', $advertisement->user) }}" class="text-decoration-none">
                                        <div>{{ $advertisement->user->name }}</div>
                                        <div class="d-flex align-items-center mt-1">
                                            @php
                                                $avgRating = $advertisement->user->getAverageRatingAttribute();
                                                $reviewCount = $advertisement->user->getReviewCountAttribute();
                                            @endphp
                                            @if($avgRating)
                                                <div class="me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($avgRating))
                                                            <i class="bi bi-star-fill text-warning small"></i>
                                                        @else
                                                            <i class="bi bi-star text-muted small"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <small>({{ number_format($avgRating, 1) }} - {{ $reviewCount }} {{ __('general.reviews') }})</small>
                                            @else
                                                <small class="text-muted">{{ __('general.no_reviews') }}</small>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ __('Lid sinds') }} {{ $advertisement->user->created_at->format('M Y') }}</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                          <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('advertisers.show', $advertisement->user) }}" class="btn btn-outline-primary">
                                <i class="bi bi-person-badge me-1"></i>{{ __('general.view_seller_profile') }}
                            </a>@if($advertisement->isRental())
                                <a href="{{ route('rentals.rent', $advertisement) }}" class="btn btn-info mb-2">
                                    <i class="bi bi-calendar-check me-1"></i>{{ __('Nu huren') }}
                                </a>
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-outline-info mb-2">
                                    <i class="bi bi-calendar me-1"></i>{{ __('Beschikbaarheid bekijken') }}
                                </a>
                            @else
                                @if(isset($canBePurchased) && $canBePurchased)
                                    <form action="{{ route('cart.add', $advertisement) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-cart-plus me-1"></i>{{ __('In winkelwagen') }}
                                        </button>
                                    </form>                                @endif
                            @endif
                            
                            @auth
                                <form action="{{ route('favorites.toggle', $advertisement) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        @if($advertisement->isFavoritedBy(Auth::user()))
                                            <i class="bi bi-heart-fill me-1"></i>{{ __('general.remove_from_favorites') }}
                                        @else
                                            <i class="bi bi-heart me-1"></i>{{ __('general.add_to_favorites') }}
                                        @endif
                                    </button>
                                </form>
                            @endauth
                            
                            @if($advertisement->purchase_status === 'sold')
                                <button class="btn btn-secondary w-100 mt-2" disabled>
                                    <i class="bi bi-bag-check me-1"></i>{{ __('Verkocht') }}
                                </button>                            @elseif($advertisement->purchase_status === 'reserved')
                                <button class="btn btn-warning w-100 mt-2" disabled>
                                    <i class="bi bi-hourglass-split me-1"></i>{{ __('Gereserveerd') }}
                                </button>
                            @elseif(Auth::check() && $advertisement->user_id === Auth::id())
                                <button class="btn btn-outline-secondary w-100 mt-2" disabled>
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ __('general.cannot_buy_own_ad') }}
                                </button>                            @endif
                        </div>
                    @endif
                </div>
            </div>
              <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Veilig handelen') }}</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Bekijk het item zorgvuldig') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Betaal nooit vooraf') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Ontmoet op een veilige plek') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Controleer reviews van de verkoper') }}</li>
                    </ul>
                </div>            </div>
            
            @if($advertisement->isRental())
                <!-- Beoordelingssectie voor verhuuradvertenties -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('general.reviews') }}</h5>
                        @auth
                            @if($advertisement->canBeReviewedBy(Auth::user()) && !$advertisement->hasBeenReviewedBy(Auth::user()))
                                <a href="{{ route('reviews.create', $advertisement) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-star me-1"></i>{{ __('general.write_review') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                    <div class="card-body">
                        @if($advertisement->reviews->count() > 0)
                            <div class="rating-summary mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="h2 mb-0 me-2">{{ number_format($advertisement->average_rating, 1) }}</div>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($advertisement->average_rating))
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-muted"></i>
                                            @endif
                                        @endfor
                                        <div class="text-muted small">
                                            {{ __('general.based_on_reviews', ['count' => $advertisement->review_count]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="reviews-list">
                                @foreach($advertisement->reviews()->with('user')->latest()->take(3)->get() as $review)
                                    <div class="review-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="review-header mb-2">
                                                <strong>{{ $review->user->name }}</strong>
                                                <div class="small text-muted">
                                                    {{ $review->created_at->format('d-m-Y') }}
                                                </div>
                                            </div>
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @else
                                                        <i class="bi bi-star text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="review-content">
                                            {{ $review->comment }}
                                        </div>
                                        @auth
                                            @if($review->user_id === Auth::id())
                                                <div class="mt-2">
                                                    <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil me-1"></i>{{ __('general.edit') }}
                                                    </a>
                                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                                            <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($advertisement->reviews->count() > 3)
                                <div class="text-center mt-3">
                                    <a href="{{ route('reviews.index', $advertisement) }}" class="btn btn-outline-primary">
                                        {{ __('Alle beoordelingen bekijken') }} ({{ $advertisement->reviews->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-star h1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">{{ __('general.no_reviews') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-3">                        <a href="{{ url()->previous() === route('advertisements.show', $advertisement) ? route('advertisements.index') : url()->previous() }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('general.back') }}
        </a>
    </div>
</div>
@endsection
