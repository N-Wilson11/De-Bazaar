@extends('layouts.app')

@section('title', 'Welkom bij De-Bazaar')

@section('content')
<div class="container py-4">
    <!-- Hero Sectie -->
    <div class="jumbotron bg-light p-5 mb-4 rounded">
        <h1 class="display-4">Welkom bij De-Bazaar</h1>
        <p class="lead">Ontdek de beste producten van onze verkopers en verhuurders.</p>
        <hr class="my-4">
        <p>Registreer nu om jouw producten aan te bieden of om te zoeken naar wat je nodig hebt.</p>
        <div class="mt-4">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2">Registreren</a>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Inloggen</a>
        </div>
    </div>

    <!-- Laatste Verkoopadvertenties -->
    @if(isset($latestSaleAds) && $latestSaleAds->count() > 0)
    <h2 class="mb-4 mt-5">Nieuwste Producten</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($latestSaleAds as $advertisement)
        <div class="col">
            <div class="card h-100">
                @if(!empty($advertisement->images))
                    <img src="{{ $advertisement->getFirstImageUrl() }}" class="card-img-top" 
                         style="height: 200px; object-fit: cover;" alt="{{ $advertisement->title }}">
                @else
                    <div class="bg-light text-center" style="height: 200px;">
                        <i class="bi bi-image text-muted" style="font-size: 4rem; line-height: 200px;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $advertisement->title }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($advertisement->description, 100) }}</p>
                    <p class="card-text fw-bold">€ {{ number_format($advertisement->price, 2, ',', '.') }}</p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    @if($advertisement->id)
                        <a href="{{ route('advertisements.show', $advertisement->id) }}" class="btn btn-primary">Bekijk Details</a>
                    @else
                        <button class="btn btn-secondary" disabled>Advertentie niet beschikbaar</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('advertisements.browse') }}" class="btn btn-outline-primary">Bekijk Alle Producten</a>
    </div>
    @endif

    <!-- Laatste Verhuuradvertenties -->
    @if(isset($latestRentalAds) && $latestRentalAds->count() > 0)
    <h2 class="mb-4 mt-5">Verhuur Aanbiedingen</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($latestRentalAds as $rental)
        <div class="col">
            <div class="card h-100">
                @if(!empty($rental->images))
                    <img src="{{ $rental->getFirstImageUrl() }}" class="card-img-top" 
                         style="height: 200px; object-fit: cover;" alt="{{ $rental->title }}">
                @else
                    <div class="bg-light text-center" style="height: 200px;">
                        <i class="bi bi-image text-muted" style="font-size: 4rem; line-height: 200px;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <span class="badge bg-info mb-2">Verhuur</span>
                    <h5 class="card-title">{{ $rental->title }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($rental->description, 100) }}</p>
                    <p class="card-text fw-bold">€ {{ number_format($rental->price, 2, ',', '.') }} / {{ __($rental->rental_period ?? 'dag') }}</p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    @if($rental->id)
                        <a href="{{ route('advertisements.show', $rental->id) }}" class="btn btn-primary">Bekijk Details</a>
                    @else
                        <button class="btn btn-secondary" disabled>Advertentie niet beschikbaar</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('rentals.index') }}" class="btn btn-outline-primary">Bekijk Alle Verhuur</a>
    </div>
    @endif
</div>
@endsection