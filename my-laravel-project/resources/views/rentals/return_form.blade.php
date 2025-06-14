@extends('layouts.app')

@section('title', __('Product terugbrengen'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Product terugbrengen') }}</h5>
                </div>
                
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">                                @if($orderItem->advertisement && $orderItem->advertisement->countImages() > 0)
                                    <img src="{{ $orderItem->advertisement->getFirstImageUrl() }}" 
                                        class="img-thumbnail" 
                                        alt="{{ $orderItem->title }}" 
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                        style="width: 100px; height: 100px;">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $orderItem->title }}</h6>
                                <p class="mb-0 text-muted">
                                    {{ __('Gehuurd van') }}: {{ \Carbon\Carbon::parse($orderItem->rental_start_date)->format('d-m-Y') }} 
                                    {{ __('tot') }} {{ \Carbon\Carbon::parse($orderItem->rental_end_date)->format('d-m-Y') }}
                                </p>
                                <p class="mb-0">{{ __('Huurprijs') }}: €{{ number_format($orderItem->price, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                      <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Om dit product terug te brengen, upload een foto van de huidige staat van het product en voeg eventuele opmerkingen toe.') }}
                    </div>
                    
                    @if($orderItem->advertisement && $orderItem->advertisement->rental_calculate_wear_and_tear)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('Let op: Voor dit product wordt een slijtageberekening uitgevoerd op basis van de huurperiode en de conditie waarin het product wordt teruggebracht. De kosten hiervan kunnen worden ingehouden van de borg.') }}
                        
                        @if($orderItem->advertisement->rental_deposit_amount)
                            <p class="mb-0 mt-2">
                                <strong>{{ __('Maximale borg') }}:</strong> €{{ number_format($orderItem->advertisement->rental_deposit_amount, 2, ',', '.') }}
                            </p>
                        @endif
                    </div>
                    @endif
                    
                    <form method="POST" action="{{ route('rentals.process-return', $orderItem) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="return_photo" class="form-label">{{ __('Foto van het product') }} <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('return_photo') is-invalid @enderror" 
                                id="return_photo" name="return_photo" accept="image/*" required>
                            <small class="form-text text-muted">{{ __('Upload een duidelijke foto van het product in de huidige staat.') }}</small>
                            @error('return_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                          <div class="mb-3">
                            <label for="return_condition" class="form-label">{{ __('Conditie van het product') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('return_condition') is-invalid @enderror" 
                                id="return_condition" name="return_condition" required>
                                <option value="" selected disabled>{{ __('Selecteer de huidige conditie van het product...') }}</option>
                                <option value="excellent">{{ __('Uitstekend') }} - {{ __('Zoals nieuw, geen zichtbare gebreken') }}</option>
                                <option value="good">{{ __('Goed') }} - {{ __('Lichte gebruikssporen, maar in goede staat') }}</option>
                                <option value="fair">{{ __('Redelijk') }} - {{ __('Duidelijke gebruikssporen, maar nog functioneel') }}</option>
                                <option value="poor">{{ __('Slecht') }} - {{ __('Beschadigd of niet volledig functioneel') }}</option>
                            </select>
                            @error('return_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="return_notes" class="form-label">{{ __('Opmerkingen') }}</label>
                            <textarea class="form-control @error('return_notes') is-invalid @enderror" 
                                id="return_notes" name="return_notes" rows="3"
                                placeholder="{{ __('Voeg hier eventuele opmerkingen over de staat van het product toe...') }}"></textarea>
                            @error('return_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $orderItem->order_id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('Terug') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i>{{ __('Product terugbrengen') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
