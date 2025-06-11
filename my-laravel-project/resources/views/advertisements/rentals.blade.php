@extends('layouts.app')

@section('title', __('Verhuuraanbod'))

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">                    <h1 class="h3 mb-3">{{ __('Verhuuraanbod') }}</h1>
                    <p>{{ __('Bekijk items die je kunt huren op De Bazaar.') }}</p>
                    
                    @if(Auth::check() && (Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk'))
                        <a href="{{ route('rentals.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('Eigen item verhuren') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rentals.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="category" class="form-label">{{ __('Categorie') }}</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">{{ __('Alle categorieën') }}</option>
                                <option value="elektronica" {{ request('category') == 'elektronica' ? 'selected' : '' }}>{{ __('Elektronica') }}</option>
                                <option value="meubels" {{ request('category') == 'meubels' ? 'selected' : '' }}>{{ __('Meubels') }}</option>
                                <option value="gereedschap" {{ request('category') == 'gereedschap' ? 'selected' : '' }}>{{ __('Gereedschap') }}</option>
                                <option value="auto" {{ request('category') == 'auto' ? 'selected' : '' }}>{{ __('Auto & Vervoer') }}</option>
                                <option value="sport" {{ request('category') == 'sport' ? 'selected' : '' }}>{{ __('Sport & Vrije tijd') }}</option>
                                <option value="overig" {{ request('category') == 'overig' ? 'selected' : '' }}>{{ __('Overig') }}</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price_max" class="form-label">{{ __('Maximale prijs per dag') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" id="price_max" name="price_max" class="form-control" value="{{ request('price_max') }}" min="0" step="0.50">
                            </div>
                        </div>
                          <div class="mb-3">
                            <label for="location" class="form-label">{{ __('Locatie') }}</label>
                            <input type="text" id="location" name="location" class="form-control" value="{{ request('location') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort" class="form-label">{{ __('Sorteren op') }}</label>
                            <select id="sort" name="sort" class="form-select">
                                <option value="created_at|desc" {{ request('sort') == 'created_at|desc' ? 'selected' : '' }}>{{ __('Nieuwste eerst') }}</option>
                                <option value="created_at|asc" {{ request('sort') == 'created_at|asc' ? 'selected' : '' }}>{{ __('Oudste eerst') }}</option>
                                <option value="title|asc" {{ request('sort') == 'title|asc' ? 'selected' : '' }}>{{ __('Titel: A-Z') }}</option>
                                <option value="title|desc" {{ request('sort') == 'title|desc' ? 'selected' : '' }}>{{ __('Titel: Z-A') }}</option>
                                <option value="rental_price_day|asc" {{ request('sort') == 'rental_price_day|asc' ? 'selected' : '' }}>{{ __('Dagprijs: laag - hoog') }}</option>
                                <option value="rental_price_day|desc" {{ request('sort') == 'rental_price_day|desc' ? 'selected' : '' }}>{{ __('Dagprijs: hoog - laag') }}</option>
                                <option value="views|desc" {{ request('sort') == 'views|desc' ? 'selected' : '' }}>{{ __('Meest bekeken') }}</option>
                            </select>
                        </div>
                          <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i>{{ __('Filteren') }}
                            </button>
                        </div>
                    </form>
                    
                    <!-- Reset link -->
                    @if(request('category') || request('price_max') || request('location') || request('sort'))
                        <div class="text-end mt-2">
                            <a href="{{ route('rentals.index') }}" class="text-decoration-none">
                                <i class="bi bi-x-circle"></i> {{ __('Reset filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Veilig huren tips') }}</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Controleer het item voor gebruik') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Maak duidelijke afspraken') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Betaal alleen bij ophalen') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Vraag naar borg voorwaarden') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            @if(count($rentals) > 0)
                <div class="row">
                    @foreach($rentals as $rental)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">                                <div class="position-relative">
                                    @if(!empty($rental->images))
                                        <img src="{{ $rental->getFirstImageUrl() }}" class="card-img-top" alt="{{ $rental->title }}" style="height: 180px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-center py-5" style="height: 180px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-info position-absolute top-0 start-0 m-2">{{ __('Verhuur') }}</span>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate">{{ $rental->title }}</h5>
                                    
                                    <div class="mb-2">
                                        <span class="h5">€ {{ number_format($rental->rental_price_day, 2, ',', '.') }}</span>
                                        <span class="text-muted">/dag</span>
                                    </div>
                                    
                                    <p class="card-text flex-grow-1" style="height: 50px; overflow: hidden;">
                                        {{ Str::limit($rental->description, 100) }}
                                    </p>
                                      <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $rental->location ?: __('Onbekend') }}
                                        </small>
                                        
                                        <small class="text-muted">
                                            {{ $rental->created_at->format('d-m-Y') }}
                                        </small>
                                    </div>
                                      <div class="d-flex justify-content-between align-items-center mt-2">
                                        <a href="{{ route('advertisers.show', $rental->user) }}" class="text-decoration-none">
                                            <small><i class="bi bi-person"></i> {{ $rental->user->name }}</small>
                                        </a>
                                        
                                        @auth
                                            <form action="{{ route('favorites.toggle', $rental) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $rental->isFavoritedBy(Auth::user()) ? 'btn-danger' : 'btn-outline-danger' }}">
                                                    <i class="bi {{ $rental->isFavoritedBy(Auth::user()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                    
                                    <a href="{{ route('advertisements.show', $rental) }}" class="btn btn-outline-primary mt-3">{{ __('Bekijken') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $rentals->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <h4 class="alert-heading">{{ __('Geen verhuuraanbod gevonden!') }}</h4>
                    <p>{{ __('Er zijn momenteel geen verhuuradvertenties die aan je zoekcriteria voldoen.') }}</p>
                    
                    <hr>
                      <p class="mb-0">
                        {{ __('Heb je zelf iets te verhuren?') }} 
                        @if(Auth::check() && (Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk'))
                            <a href="{{ route('rentals.create') }}" class="alert-link">{{ __('Plaats nu een verhuuradvertentie') }}</a>.
                        @else
                            {{ __('Alleen particuliere en zakelijke gebruikers kunnen verhuuradvertenties plaatsen.') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
