@extends('layouts.app')

@section('title', __('Teruggebracht Product Details'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Teruggebracht Product Details') }}</h5>
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
                                <p class="mb-0 text-muted">{{ __('Teruggebracht op') }}: {{ \Carbon\Carbon::parse($orderItem->returned_at)->format('d-m-Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Foto bij teruggave') }}</h6>
                        </div>
                        <div class="card-body text-center">
                            @if($orderItem->return_photo)
                                <img src="{{ Storage::url($orderItem->return_photo) }}" 
                                    class="img-fluid rounded mb-3" 
                                    alt="{{ __('Foto van teruggebracht product') }}"
                                    style="max-height: 400px;">
                                <div>
                                    <a href="{{ Storage::url($orderItem->return_photo) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="bi bi-arrows-fullscreen me-1"></i>{{ __('Foto bekijken') }}
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    {{ __('Geen foto beschikbaar') }}
                                </div>
                            @endif
                        </div>
                    </div>
                      <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Gegevens teruggave') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">{{ __('Conditie bij teruggave') }}:</div>
                                <div class="col-md-8">
                                    @php
                                        $conditionLabels = [
                                            'excellent' => __('Uitstekend'),
                                            'good' => __('Goed'),
                                            'fair' => __('Redelijk'),
                                            'poor' => __('Slecht'),
                                        ];
                                        $conditionBadges = [
                                            'excellent' => 'success',
                                            'good' => 'info',
                                            'fair' => 'warning',
                                            'poor' => 'danger',
                                        ];
                                    @endphp
                                    
                                    @if($orderItem->return_condition)
                                        <span class="badge bg-{{ $conditionBadges[$orderItem->return_condition] ?? 'secondary' }}">
                                            {{ $conditionLabels[$orderItem->return_condition] ?? $orderItem->return_condition }}
                                        </span>
                                    @else
                                        <span class="text-muted">{{ __('Niet gespecificeerd') }}</span>
                                    @endif
                                </div>
                            </div>
                              @if($orderItem->deposit_amount > 0)
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('Borg') }}:</div>
                                    <div class="col-md-8">
                                        <span>€{{ number_format($orderItem->deposit_amount, 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                @if($orderItem->advertisement && $orderItem->advertisement->rental_calculate_wear_and_tear)
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">{{ __('Berekende slijtage') }}:</div>
                                        <div class="col-md-8">
                                            @if($orderItem->wear_and_tear_amount > 0)
                                                <span class="text-danger">
                                                    €{{ number_format($orderItem->wear_and_tear_amount, 2, ',', '.') }}
                                                </span>
                                                <small class="d-block text-muted">
                                                    {{ __('Dit bedrag wordt ingehouden van de borg') }}
                                                </small>
                                            @else
                                                <span class="text-success">€0,00</span>
                                                <small class="d-block text-muted">
                                                    {{ __('Geen slijtage berekend') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">{{ __('Terugbetaling') }}:</div>
                                        <div class="col-md-8">
                                            @if($orderItem->deposit_refunded_amount >= $orderItem->deposit_amount)
                                                <span class="text-success fw-bold">
                                                    €{{ number_format($orderItem->deposit_refunded_amount, 2, ',', '.') }}
                                                </span>
                                                <small class="d-block text-muted">
                                                    {{ __('Volledige borg wordt terugbetaald') }}
                                                </small>
                                            @else
                                                <span class="fw-bold @if($orderItem->deposit_refunded_amount > 0) text-warning @else text-danger @endif">
                                                    €{{ number_format($orderItem->deposit_refunded_amount, 2, ',', '.') }}
                                                </span>
                                                <small class="d-block text-muted">
                                                    {{ __('Borg minus slijtagekosten') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">{{ __('Terugbetaling') }}:</div>
                                        <div class="col-md-8">
                                            <span class="text-success fw-bold">
                                                €{{ number_format($orderItem->deposit_amount, 2, ',', '.') }}
                                            </span>
                                            <small class="d-block text-muted">
                                                {{ __('Volledige borg wordt terugbetaald') }}
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            @elseif($orderItem->advertisement && $orderItem->advertisement->rental_calculate_wear_and_tear)
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('Berekende slijtage') }}:</div>
                                    <div class="col-md-8">
                                        @if($orderItem->wear_and_tear_amount > 0)
                                            <span class="text-danger">
                                                €{{ number_format($orderItem->wear_and_tear_amount, 2, ',', '.') }}
                                            </span>
                                            <small class="d-block text-muted">
                                                {{ __('Voor dit product was geen borg vereist') }}
                                            </small>
                                        @else
                                            <span class="text-success">€0,00</span>
                                            <small class="d-block text-muted">
                                                {{ __('Geen slijtage berekend') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($orderItem->return_notes)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Opmerkingen bij teruggave') }}</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $orderItem->return_notes }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('Terug') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
