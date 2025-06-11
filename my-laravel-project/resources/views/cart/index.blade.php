@extends('layouts.app')

@section('title', __('Winkelwagen'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('Winkelwagen') }}</h1>
                    <p>{{ __('Hieronder vind je de artikelen in je winkelwagen.') }}</p>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($cart && $cart->items->count() > 0)
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Artikelen') }} ({{ $cart->items->count() }})</h5>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px">{{ __('Foto') }}</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Prijs') }}</th>
                                        <th style="width: 120px">{{ __('Aantal') }}</th>
                                        <th style="width: 120px">{{ __('Actie') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                        <tr>
                                            <td>
                                                @if(!empty($item->advertisement->images))
                                                    <img src="{{ $item->advertisement->getFirstImageUrl() }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->advertisement->title }}">
                                                @else
                                                    <div class="bg-light text-center" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('advertisements.show', $item->advertisement) }}" class="text-decoration-none">
                                                    {{ $item->advertisement->title }}
                                                </a>
                                                <div class="small text-muted">
                                                    {{ __('Verkoper') }}: {{ $item->advertisement->user->name }}
                                                </div>
                                            </td>
                                            <td>€ {{ number_format($item->advertisement->price, 2, ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('cart.update', $item) }}" method="POST">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="10" class="form-control" style="max-width: 60px;">
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-trash"></i> {{ __('Verwijderen') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Samenvatting') }}</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Subtotaal') }}</span>
                            <span>€ {{ number_format($cart->getTotalPrice(), 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Verzendkosten') }}</span>
                            <span>€ 0,00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>{{ __('Totaal') }}</strong>
                            <strong>€ {{ number_format($cart->getTotalPrice(), 2, ',', '.') }}</strong>
                        </div>
                        
                        <div class="d-grid">
                            <a href="{{ route('cart.checkout') }}" class="btn btn-primary">
                                <i class="bi bi-credit-card me-1"></i>{{ __('Afrekenen') }}
                            </a>
                        </div>
                        
                        <div class="mt-3">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100" onclick="return confirm('{{ __('Weet je zeker dat je de winkelwagen wilt leegmaken?') }}')">
                                    <i class="bi bi-trash me-1"></i>{{ __('Winkelwagen leegmaken') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Veilig handelen') }}</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Veilige betaalomgeving') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Betrouwbare verkopers') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Beveiligde gegevens') }}</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Klantenservice') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">{{ __('Je winkelwagen is leeg') }}</h5>
                        <p>{{ __('Voeg producten toe om te kunnen bestellen.') }}</p>
                        <a href="{{ route('advertisements.browse') }}" class="btn btn-primary">
                            <i class="bi bi-shop me-1"></i>{{ __('Bekijk advertenties') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
