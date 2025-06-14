@extends('layouts.app')

@section('title', __('Bestelgegevens'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="h3 mb-1">{{ __('Bestelling') }} #{{ $order->id }}</h1>
                            <p class="mb-0 text-muted">{{ __('Geplaatst op') }}: {{ $order->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                        
                        <div class="text-end">
                            @if($order->status === 'pending')
                                <span class="badge bg-warning text-dark mb-2">{{ __('In behandeling') }}</span>
                            @elseif($order->status === 'paid')
                                <span class="badge bg-info mb-2">{{ __('Betaald') }}</span>
                            @elseif($order->status === 'processing')
                                <span class="badge bg-primary mb-2">{{ __('In verwerking') }}</span>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-success mb-2">{{ __('Voltooid') }}</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge bg-danger mb-2">{{ __('Geannuleerd') }}</span>
                            @else
                                <span class="badge bg-secondary mb-2">{{ $order->status }}</span>
                            @endif
                            
                            <div>
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left me-1"></i>{{ __('Terug naar Bestellingen') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
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
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Bestelde artikelen') }}</h5>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Verkoper') }}</th>
                                    <th>{{ __('Prijs') }}</th>
                                    <th>{{ __('Aantal') }}</th>
                                    <th>{{ __('Totaal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->advertisement && !empty($item->advertisement->images))
                                                    <img src="{{ $item->advertisement->getFirstImageUrl() }}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->title }}">
                                                @else
                                                    <div class="bg-light text-center me-2" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    {{ $item->title }}
                                                    @if($item->advertisement)
                                                        <div class="small">
                                                            <a href="{{ route('advertisements.show', $item->advertisement) }}" class="text-decoration-none">
                                                                {{ __('Bekijk advertentie') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if($item->is_rental)
                                                    <div class="mt-1">
                                                        <span class="badge bg-info">{{ __('Huurtermijn') }}: {{ \Carbon\Carbon::parse($item->rental_start_date)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($item->rental_end_date)->format('d-m-Y') }}</span>
                                                        
                                                        @if($item->is_returned)
                                                            <span class="badge bg-success ms-1">{{ __('Teruggebracht') }}</span>
                                                        @elseif(\Carbon\Carbon::now()->gt($item->rental_end_date))
                                                            <span class="badge bg-danger ms-1">{{ __('Inleveren verlopen') }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->seller)
                                                <a href="{{ route('advertisers.show', $item->seller) }}" class="text-decoration-none">
                                                    {{ $item->seller->name }}
                                                    <i class="bi bi-person-vcard text-primary small ms-1"></i>
                                                </a>
                                            @else
                                                {{ __('Onbekend') }}
                                            @endif
                                        </td>
                                        <td>€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                                                
                                                @if($item->is_rental && !$item->is_returned && \Carbon\Carbon::now()->lte(\Carbon\Carbon::parse($item->rental_end_date)->addDays(7)))
                                                    <a href="{{ route('rentals.return', $item) }}" class="btn btn-sm btn-primary ms-2">
                                                        <i class="bi bi-box-arrow-in-left me-1"></i>{{ __('Terugbrengen') }}
                                                    </a>
                                                @elseif($item->is_rental && $item->is_returned)
                                                    <a href="{{ route('rentals.return-details', $item) }}" class="btn btn-sm btn-outline-secondary ms-2">
                                                        <i class="bi bi-eye me-1"></i>{{ __('Details') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('Subtotaal') }}:</strong></td>
                                    <td>€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('Verzendkosten') }}:</strong></td>
                                    <td>€ 0,00</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('Totaal') }}:</strong></td>
                                    <td><strong>€ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($order->notes)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Opmerkingen') }}</h5>
                        <p>{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
            
            @if($order->status === 'pending')
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Acties') }}</h5>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('{{ __('Weet je zeker dat je deze bestelling wilt annuleren?') }}')">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i>{{ __('Bestelling annuleren') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            
            @if($order->status === 'completed')
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Beoordelingen') }}</h5>
                        
                        <div class="mb-4">
                            <p>{{ __('Je kunt nu de verkopers van deze producten beoordelen:') }}</p>
                            <ul class="list-group">
                                @php
                                    $sellers = $order->items->map(function($item) {
                                        return $item->seller;
                                    })->unique('id');
                                @endphp
                                
                                @foreach($sellers as $seller)
                                    @if($seller)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $seller->name }}</strong>
                                            </div>
                                            <a href="{{ route('advertiser.reviews.create', $seller) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-star me-1"></i>{{ __('general.review_advertiser') }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Bestelling details') }}</h5>
                    
                    <div class="mb-3">
                        <strong>{{ __('Bestelnummer') }}:</strong>
                        <p class="mb-0">#{{ $order->id }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('Datum') }}:</strong>
                        <p class="mb-0">{{ $order->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('Status') }}:</strong>
                        <p class="mb-0">
                            @if($order->status === 'pending')
                                <span class="badge bg-warning text-dark">{{ __('In behandeling') }}</span>
                            @elseif($order->status === 'paid')
                                <span class="badge bg-info">{{ __('Betaald') }}</span>
                            @elseif($order->status === 'processing')
                                <span class="badge bg-primary">{{ __('In verwerking') }}</span>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-success">{{ __('Voltooid') }}</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge bg-danger">{{ __('Geannuleerd') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('Betaalmethode') }}:</strong>
                        <p class="mb-0">
                            @if($order->payment_method === 'ideal')
                                <i class="bi bi-bank me-1"></i>{{ __('iDEAL') }}
                            @elseif($order->payment_method === 'creditcard')
                                <i class="bi bi-credit-card me-1"></i>{{ __('Creditcard') }}
                            @elseif($order->payment_method === 'banktransfer')
                                <i class="bi bi-cash me-1"></i>{{ __('Bankoverschrijving') }}
                            @else
                                {{ $order->payment_method }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Verzendgegevens') }}</h5>
                    
                    <div class="mb-3">
                        <strong>{{ __('Bezorgadres') }}:</strong>
                        <p class="mb-0 white-space-pre-wrap">{{ $order->shipping_address }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('Factuuradres') }}:</strong>
                        <p class="mb-0 white-space-pre-wrap">{{ $order->billing_address }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Hulp nodig?') }}</h5>
                    <p>{{ __('Als je vragen hebt over je bestelling, neem dan contact op met onze klantenservice.') }}</p>
                    <a href="mailto:support@debazaar.com" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-envelope me-1"></i>{{ __('Contact opnemen') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.white-space-pre-wrap {
    white-space: pre-wrap;
}
</style>
@endsection
