@extends('layouts.app')

@section('title', __('Afrekenen'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('Afrekenen') }}</h1>
                    <p>{{ __('Vul je gegevens in om de bestelling af te ronden.') }}</p>
                    
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
                    <h5 class="card-title mb-4">{{ __('Leveringsgegevens') }}</h5>
                    
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">{{ __('Bezorgadres') }} *</label>
                            <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">{{ __('Factuuradres') }} *</label>
                            <textarea name="billing_address" id="billing_address" rows="3" class="form-control @error('billing_address') is-invalid @enderror" required>{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="same_address" checked>
                                <label class="form-check-label" for="same_address">
                                    {{ __('Factuuradres is hetzelfde als bezorgadres') }}
                                </label>
                            </div>
                        </div>
                        
                        <h5 class="card-title mt-4 mb-3">{{ __('Betaalmethode') }}</h5>
                        
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_ideal" value="ideal" checked>
                                <label class="form-check-label" for="payment_ideal">
                                    <i class="bi bi-bank me-2"></i>{{ __('iDEAL') }}
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_credit" value="creditcard">
                                <label class="form-check-label" for="payment_credit">
                                    <i class="bi bi-credit-card me-2"></i>{{ __('Creditcard') }}
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="banktransfer">
                                <label class="form-check-label" for="payment_bank">
                                    <i class="bi bi-cash me-2"></i>{{ __('Bankoverschrijving') }}
                                </label>
                            </div>
                            
                            @error('payment_method')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Opmerkingen') }}</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                            <small class="form-text text-muted">{{ __('Optioneel: voeg speciale instructies toe voor je bestelling.') }}</small>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-bag-check me-1"></i>{{ __('Bestelling plaatsen') }}
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('Terug naar winkelwagen') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Bestelgegevens') }}</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($cart->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(!empty($item->advertisement->images))
                                                    <img src="{{ $item->advertisement->getFirstImageUrl() }}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $item->advertisement->title }}">
                                                @else
                                                    <div class="bg-light text-center me-2" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div style="font-size: 0.9rem;">{{ Str::limit($item->advertisement->title, 30) }}</div>
                                                    <div class="small text-muted">{{ __('Aantal') }}: {{ $item->quantity }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            € {{ number_format($item->advertisement->price * $item->quantity, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Subtotaal') }}</span>
                        <span>€ {{ number_format($cart->getTotalPrice(), 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Verzendkosten') }}</span>
                        <span>€ 0,00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-2">
                        <strong>{{ __('Totaal') }}</strong>
                        <strong>€ {{ number_format($cart->getTotalPrice(), 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Veilig betalen') }}</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-shield-check text-success me-2"></i>{{ __('Beveiligde verbinding') }}</li>
                        <li class="mb-2"><i class="bi bi-shield-check text-success me-2"></i>{{ __('Versleutelde betaalgegevens') }}</li>
                        <li><i class="bi bi-shield-check text-success me-2"></i>{{ __('Garantie bij problemen') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sameAddressCheckbox = document.getElementById('same_address');
    const shippingAddress = document.getElementById('shipping_address');
    const billingAddress = document.getElementById('billing_address');
    
    function syncAddresses() {
        if(sameAddressCheckbox.checked) {
            billingAddress.value = shippingAddress.value;
            billingAddress.disabled = true;
        } else {
            billingAddress.disabled = false;
        }
    }
    
    // Initial sync
    syncAddresses();
    
    // Update billing address when shipping address changes
    shippingAddress.addEventListener('input', function() {
        if(sameAddressCheckbox.checked) {
            billingAddress.value = shippingAddress.value;
        }
    });
    
    // Handle checkbox change
    sameAddressCheckbox.addEventListener('change', syncAddresses);
});
</script>
@endsection
