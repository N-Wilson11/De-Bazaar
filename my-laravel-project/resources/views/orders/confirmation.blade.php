@extends('layouts.app')

@section('title', __('Bestelling geplaatst'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <div class="display-1 text-success mb-4">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    
                    <h1 class="h3 mb-3">{{ __('Bedankt voor je bestelling!') }}</h1>
                    <p class="mb-4">{{ __('Je bestelling is succesvol geplaatst en wordt nu verwerkt.') }}</p>
                    
                    <div class="alert alert-info mb-4">
                        <h5>{{ __('Bestelnummer') }}: #{{ $order->id }}</h5>
                        <p class="mb-0">{{ __('We hebben een bevestiging gestuurd naar je e-mailadres.') }}</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">
                            <i class="bi bi-receipt me-1"></i>{{ __('Bekijk bestelling') }}
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-1"></i>{{ __('Alle bestellingen') }}
                        </a>
                        <a href="{{ route('advertisements.browse') }}" class="btn btn-outline-primary">
                            <i class="bi bi-shop me-1"></i>{{ __('Verder winkelen') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Wat gebeurt er nu?') }}</h5>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3 text-primary">
                            <i class="bi bi-1-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6>{{ __('Bestelling verwerkt') }}</h6>
                            <p>{{ __('De verkoper wordt geïnformeerd over je aankoop en verwerkt je bestelling.') }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3 text-primary">
                            <i class="bi bi-2-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6>{{ __('Contact met de verkoper') }}</h6>
                            <p>{{ __('De verkoper neemt contact met je op om de levering en betaling te regelen.') }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3 text-primary">
                            <i class="bi bi-3-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6>{{ __('Ontvang je artikel') }}</h6>
                            <p>{{ __('Je ontvangt je artikel volgens de afspraken met de verkoper.') }}</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        {{ __('Let op: Betaal pas bij levering of via een veilige betaalmethode.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
