@extends('layouts.app')

@section('title', $advertisement->title)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ $advertisement->title }}</h1>
                    
                    <div class="d-flex mb-3 gap-2 flex-wrap">
                        @if($advertisement->isRental())
                            <span class="badge bg-info text-dark">{{ __('Verhuur') }}</span>
                        @elseif($advertisement->type === 'auction')
                            <span class="badge bg-warning text-dark">{{ __('Veiling') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Verkoop') }}</span>
                        @endif
                        
                        <span class="badge bg-light text-dark border">{{ __('Categorie') }}: {{ ucfirst($advertisement->category) }}</span>
                        <span class="badge bg-light text-dark border">{{ __('Conditie') }}: {{ ucfirst($advertisement->condition) }}</span>
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
                            <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-primary">
                                <i class="bi bi-calendar-check me-1"></i>{{ __('Beschikbaarheid bekijken') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @if($advertisement->isRental())
                        <div class="mb-3 d-flex align-items-center">
                            <span class="badge bg-info text-dark me-2 px-3 py-2">{{ __('Verhuur') }}</span>
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
                    
                    <div class="mb-3">
                        <strong><i class="bi bi-clock-history me-1"></i>{{ __('Geplaatst') }}:</strong> 
                        {{ $advertisement->created_at->format('d-m-Y') }}
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="bi bi-eye me-1"></i>{{ __('Bekeken') }}:</strong> 
                        {{ $advertisement->views }} {{ __('keer') }}
                    </div>
                    
                    @if($advertisement->user_id === Auth::id())
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i>{{ __('Bewerken') }}
                            </a>
                            
                            @if($advertisement->isRental())
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-outline-info">
                                    <i class="bi bi-calendar-event me-1"></i>{{ __('Beheer beschikbaarheid') }}
                                </a>
                            @endif
                            
                            <form action="{{ route('advertisements.destroy', $advertisement) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('{{ __('Weet je zeker dat je deze advertentie wilt verwijderen?') }}')">
                                    <i class="bi bi-trash me-1"></i>{{ __('Verwijderen') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mb-3">
                            <h6>{{ __('Aangeboden door') }}</h6>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle fs-4 me-2"></i>
                                <div>
                                    <div>{{ $advertisement->user->name }}</div>
                                    <small class="text-muted">{{ __('Lid sinds') }} {{ $advertisement->user->created_at->format('M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="mailto:{{ $advertisement->user->email }}?subject={{ urlencode('Interesse in: ' . $advertisement->title) }}" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i>{{ __('Contact opnemen') }}
                            </a>
                              @if($advertisement->isRental())
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-info">
                                    <i class="bi bi-calendar-check me-1"></i>{{ __('Beschikbaarheid bekijken') }}
                                </a>
                            @else
                                @if(isset($canBePurchased) && $canBePurchased)
                                    <form action="{{ route('cart.add', $advertisement) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-cart-plus me-1"></i>{{ __('In winkelwagen') }}
                                        </button>
                                    </form>
                                @elseif($advertisement->purchase_status === 'sold')
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="bi bi-bag-check me-1"></i>{{ __('Verkocht') }}
                                    </button>
                                @elseif(Auth::check() && $advertisement->user_id === Auth::id())
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ __('general.cannot_buy_own_ad') }}
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        <i class="bi bi-x-circle me-1"></i>{{ __('general.not_available_for_purchase') }}
                                    </button>
                                @elseif($advertisement->purchase_status === 'reserved')
                                    <button class="btn btn-warning w-100" disabled>
                                        <i class="bi bi-hourglass-split me-1"></i>{{ __('Gereserveerd') }}
                                    </button>
                                @endif
                            @endif
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
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ url()->previous() === route('advertisements.show', $advertisement) ? route('advertisements.index') : url()->previous() }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Terug') }}
        </a>
    </div>
</div>
@endsection
