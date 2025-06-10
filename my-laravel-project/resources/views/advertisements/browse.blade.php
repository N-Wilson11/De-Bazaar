@extends('layouts.app')

@section('title', __('Advertenties'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">                    <h1 class="h3 mb-3">{{ __('Advertenties') }}</h1>
                    <p>{{ __('Bekijk alle advertenties op De Bazaar.') }}</p>
                    
                    @if(Auth::check() && (Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk'))
                        <a href="{{ route('advertisements.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('Advertentie plaatsen') }}
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
                    <form action="{{ route('advertisements.browse') }}" method="GET">
                        <div class="mb-3">
                            <label for="category" class="form-label">{{ __('Categorie') }}</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">{{ __('Alle categorieën') }}</option>
                                <option value="elektronica" {{ request('category') == 'elektronica' ? 'selected' : '' }}>{{ __('Elektronica') }}</option>
                                <option value="meubels" {{ request('category') == 'meubels' ? 'selected' : '' }}>{{ __('Meubels') }}</option>
                                <option value="kleding" {{ request('category') == 'kleding' ? 'selected' : '' }}>{{ __('Kleding & Mode') }}</option>
                                <option value="auto" {{ request('category') == 'auto' ? 'selected' : '' }}>{{ __('Auto & Vervoer') }}</option>
                                <option value="sport" {{ request('category') == 'sport' ? 'selected' : '' }}>{{ __('Sport & Vrije tijd') }}</option>
                                <option value="overig" {{ request('category') == 'overig' ? 'selected' : '' }}>{{ __('Overig') }}</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price_max" class="form-label">{{ __('Maximale prijs') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" id="price_max" name="price_max" class="form-control" value="{{ request('price_max') }}" min="0" step="0.50">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="condition" class="form-label">{{ __('Conditie') }}</label>
                            <select id="condition" name="condition" class="form-select">
                                <option value="">{{ __('Alle condities') }}</option>
                                <option value="nieuw" {{ request('condition') == 'nieuw' ? 'selected' : '' }}>{{ __('Nieuw') }}</option>
                                <option value="als-nieuw" {{ request('condition') == 'als-nieuw' ? 'selected' : '' }}>{{ __('Als nieuw') }}</option>
                                <option value="goed" {{ request('condition') == 'goed' ? 'selected' : '' }}>{{ __('Goed') }}</option>
                                <option value="gebruikt" {{ request('condition') == 'gebruikt' ? 'selected' : '' }}>{{ __('Gebruikt') }}</option>
                            </select>
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
                                <option value="price|asc" {{ request('sort') == 'price|asc' ? 'selected' : '' }}>{{ __('Prijs: laag - hoog') }}</option>
                                <option value="price|desc" {{ request('sort') == 'price|desc' ? 'selected' : '' }}>{{ __('Prijs: hoog - laag') }}</option>
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
                    @if(request('category') || request('price_max') || request('condition') || request('location') || request('sort'))
                        <div class="text-end mt-2">
                            <a href="{{ route('advertisements.browse') }}" class="text-decoration-none">
                                <i class="bi bi-x-circle"></i> {{ __('Reset filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Tips voor kopers') }}</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Controleer het item voor aankoop') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Maak duidelijke afspraken') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Betaal bij voorkeur bij ophalen') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            @if(count($advertisements) > 0)
                <div class="row">
                    @foreach($advertisements as $advertisement)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">                                <div class="position-relative">
                                    @if(!empty($advertisement->images))
                                        <img src="{{ $advertisement->getFirstImageUrl() }}" class="card-img-top" alt="{{ $advertisement->title }}" style="height: 180px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-center py-5" style="height: 180px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-secondary position-absolute top-0 start-0 m-2">{{ __('Verkoop') }}</span>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate">{{ $advertisement->title }}</h5>
                                    
                                    <div class="mb-2">
                                        <span class="h5">€ {{ number_format($advertisement->price, 2, ',', '.') }}</span>
                                    </div>
                                    
                                    <p class="card-text flex-grow-1" style="height: 50px; overflow: hidden;">
                                        {{ Str::limit($advertisement->description, 100) }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $advertisement->location ?: __('Onbekend') }}
                                        </small>
                                        
                                        <small class="text-muted">
                                            {{ $advertisement->created_at->format('d-m-Y') }}
                                        </small>
                                    </div>
                                    
                                    <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-outline-primary mt-3">{{ __('Bekijken') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $advertisements->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <h4 class="alert-heading">{{ __('Geen advertenties gevonden!') }}</h4>
                    <p>{{ __('Er zijn momenteel geen advertenties die aan je zoekcriteria voldoen.') }}</p>
                    
                    <hr>
                      <p class="mb-0">
                        {{ __('Heb je zelf iets te verkopen?') }} 
                        @if(Auth::check() && (Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk'))
                            <a href="{{ route('advertisements.create') }}" class="alert-link">{{ __('Plaats nu een advertentie') }}</a>.
                        @else
                            {{ __('Alleen particuliere en zakelijke gebruikers kunnen advertenties plaatsen.') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
