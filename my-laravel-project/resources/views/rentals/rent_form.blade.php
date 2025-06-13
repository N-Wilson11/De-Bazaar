@extends('layouts.app')

@section('title', __('Product huren') . ' - ' . $advertisement->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Product huren') }}</h5>
                    <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Terug naar advertentie') }}
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                @if(!empty($advertisement->images))
                                    <img src="{{ $advertisement->getFirstImageUrl() }}" class="img-fluid rounded" alt="{{ $advertisement->title }}">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center p-4">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h3 class="mb-2">{{ $advertisement->title }}</h3>
                            <div class="mb-3">
                                <span class="badge bg-info text-dark mb-2">{{ __('Verhuur') }}</span>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <div class="price-badge">
                                        <small class="d-block text-muted">{{ __('Per dag') }}</small>
                                        <strong>€ {{ number_format($advertisement->rental_price_day, 2, ',', '.') }}</strong>
                                    </div>
                                    
                                    @if($advertisement->rental_price_week)
                                    <div class="price-badge">
                                        <small class="d-block text-muted">{{ __('Per week') }}</small>
                                        <strong>€ {{ number_format($advertisement->rental_price_week, 2, ',', '.') }}</strong>
                                    </div>
                                    @endif
                                    
                                    @if($advertisement->rental_price_month)
                                    <div class="price-badge">
                                        <small class="d-block text-muted">{{ __('Per maand') }}</small>
                                        <strong>€ {{ number_format($advertisement->rental_price_month, 2, ',', '.') }}</strong>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong>{{ __('Eigenaar') }}:</strong> {{ $advertisement->user->name }}
                            </div>
                            
                            <div class="mb-3">
                                <strong>{{ __('Minimale huurtermijn') }}:</strong> 
                                {{ $advertisement->minimum_rental_days ?: 1 }} {{ $advertisement->minimum_rental_days == 1 ? __('dag') : __('dagen') }}
                            </div>
                            
                            @if($advertisement->rental_requires_deposit)
                                <div class="alert alert-warning mb-3">
                                    <strong>{{ __('Borg vereist') }}:</strong> € {{ number_format($advertisement->rental_deposit_amount, 2, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                      @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <strong>Debuginfo:</strong> Dit formulier is correct geladen. Als je deze melding ziet, dan is het template correct gevonden.
                    </div>
                    
                    <form action="{{ route('rentals.process', $advertisement) }}" method="POST">
                        @csrf
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Selecteer verhuurperiode') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">{{ __('Begindatum') }}</label>
                                            <input type="text" id="start_date" name="start_date" class="form-control" placeholder="Selecteer begindatum" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">{{ __('Einddatum') }}</label>
                                            <input type="text" id="end_date" name="end_date" class="form-control" placeholder="Selecteer einddatum" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div id="rental-calendar"></div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">                                        <h6 class="mb-3 fw-bold">{{ __('Legenda') }}</h6>
                                        <div class="d-flex flex-column gap-3">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-color bg-success me-2"></span>
                                                <span><strong>{{ __('Beschikbaar') }}</strong> - {{ __('Deze data zijn beschikbaar voor verhuur') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="legend-color bg-danger me-2"></span>
                                                <span><strong>{{ __('Gereserveerd') }}</strong> - {{ __('Deze data zijn al geboekt') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="legend-color bg-secondary me-2"></span>
                                                <span><strong>{{ __('Niet beschikbaar') }}</strong> - {{ __('Deze data zijn niet aangeboden voor verhuur') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="price-calculation" class="card mb-4" style="display: none;">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Prijsberekening') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div>{{ __('Huurtermijn') }}: <span id="rental-days">0</span> dagen</div>
                                        <div class="h4 mt-2">{{ __('Totale huurprijs') }}: <span id="total-price">€ 0,00</span></div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary" id="recalculate-btn">
                                            <i class="bi bi-arrow-repeat me-1"></i>{{ __('Herberekenen') }}
                                        </button>
                                    </div>
                                </div>
                                
                                @if($advertisement->rental_requires_deposit)
                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    {{ __('Naast de huurprijs wordt er een borg van') }} <strong>€ {{ number_format($advertisement->rental_deposit_amount, 2, ',', '.') }}</strong> {{ __('gevraagd, die je terugkrijgt na het correct retourneren van het product.') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Huurvoorwaarden') }}</h5>
                            </div>
                            <div class="card-body">
                                @if($advertisement->rental_conditions)
                                    <div class="p-3 bg-light rounded mb-3">
                                        {!! nl2br(e($advertisement->rental_conditions)) !!}
                                    </div>
                                @else
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        {{ __('Er zijn geen specifieke huurvoorwaarden opgegeven door de verhuurder.') }}
                                    </div>
                                @endif
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                    <label class="form-check-label" for="agree_terms">
                                        {{ __('Ik ga akkoord met de huurvoorwaarden en beloof het product in goede staat te retourneren.') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Reservering plaatsen') }}
                            </button>
                            <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-calendar me-1"></i>{{ __('Alleen beschikbaarheid bekijken') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Include Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>    .calendar-color-sample {
        width: 25px;
        height: 25px;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid #ddd;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        display: inline-block;
        border-radius: 2px;
        border: 1px solid rgba(0,0,0,0.2);
    }
    
    .price-badge {
        padding: 8px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }
    
    /* Flatpickr custom styles */
    .flatpickr-calendar {
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15) !important;
    }
    
    .flatpickr-day.available {
        background-color: #28a745 !important;
        color: white !important;
        border-color: #218838 !important;
        font-weight: bold !important;
    }
    
    .flatpickr-day.booked {
        background-color: #dc3545 !important;
        color: white !important;
        border-color: #c82333 !important;
        font-weight: bold !important;
    }
    
    .flatpickr-day.selected {
        background-color: #007bff !important;
        border-color: #0069d9 !important;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5) !important;
    }
      .flatpickr-day.flatpickr-disabled {
        color: #aaa !important;
        cursor: not-allowed !important;
        background-color: #f8f9fa !important;
    }
    
    .flatpickr-day.today {
        border: 2px solid #007bff !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Beschikbare en geboekte data van de advertentie
            const availableDates = @json($availability);
            const bookedDates = @json($bookedDates);
            
            // Prijzen
            const pricePerDay = {{ $advertisement->rental_price_day ?? 0 }};
            
            // Eenvoudige datepicker zonder complexe logica
            flatpickr("#start_date", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            
            flatpickr("#end_date", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
              // Inline kalender met beschikbaarheidsmarkering
            flatpickr("#rental-calendar", {
                inline: true,
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: bookedDates,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                    
                    if (bookedDates.includes(dateStr)) {
                        dayElem.className += " booked";
                        dayElem.title = "{{ __('Gereserveerd') }}";
                    } else if (availableDates.includes(dateStr)) {
                        dayElem.className += " available";
                        dayElem.title = "{{ __('Beschikbaar voor verhuur') }}";
                    } else {
                        dayElem.title = "{{ __('Niet aangeboden voor verhuur') }}";
                    }
                }
            });
            
            document.getElementById('recalculate-btn').addEventListener('click', function() {
                document.getElementById('rental-days').textContent = "7";
                document.getElementById('total-price').textContent = '€ ' + (7 * pricePerDay).toFixed(2).replace('.', ',');
                document.getElementById('price-calculation').style.display = 'block';
            });
        } catch (e) {
            console.error("Er is een fout opgetreden in de JavaScript code:", e);
            // Toon een foutmelding op de pagina
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.innerHTML = '<strong>JavaScript Fout:</strong> ' + e.message;
            document.querySelector('.card-body').prepend(errorDiv);
        }
    });
</script>
@endsection
