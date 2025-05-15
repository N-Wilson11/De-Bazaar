@extends('layouts.app')

@section('title', __('Beschikbaarheidskalender - ') . $advertisement->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Beschikbaarheidskalender') }}</h5>
                    <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('Terug naar advertentie') }}
                    </a>
                </div>

                <div class="card-body">
                    <h4 class="mb-3">{{ $advertisement->title }}</h4>
                    
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="text-muted">{{ __('Prijs per dag') }}</div>
                                    <div class="h4">€ {{ number_format($advertisement->rental_price_day ?? 0, 2, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($advertisement->rental_price_week)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="text-muted">{{ __('Prijs per week') }}</div>
                                    <div class="h4">€ {{ number_format($advertisement->rental_price_week, 2, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($advertisement->rental_price_month)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="text-muted">{{ __('Prijs per maand') }}</div>
                                    <div class="h4">€ {{ number_format($advertisement->rental_price_month, 2, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if(Auth::id() === $advertisement->user_id)
                        <div class="mb-4 p-4 bg-light rounded">
                            <h5 class="mb-3">{{ __('Beschikbaarheid beheren') }}</h5>
                            <p>{{ __('Selecteer data waarop je item beschikbaar is voor verhuur.') }}</p>
                            
                            <form action="{{ route('advertisements.update-availability', $advertisement) }}" method="POST">
                                @csrf
                                <input type="hidden" id="available_dates" name="available_dates" value="{{ implode(',', $advertisement->rental_availability ?? []) }}">
                                
                                <div id="availability-calendar" class="mb-3"></div>
                                
                                <div class="d-flex gap-2 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 calendar-color-sample bg-success"></div>
                                        <span>{{ __('Beschikbaar') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 calendar-color-sample bg-danger"></div>
                                        <span>{{ __('Gereserveerd') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 calendar-color-sample bg-secondary"></div>
                                        <span>{{ __('Niet beschikbaar') }}</span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>{{ __('Beschikbaarheid opslaan') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-light rounded">
                            <h5 class="mb-3">{{ __('Beschikbaarheid') }}</h5>
                            <p>{{ __('Bekijk wanneer dit item beschikbaar is om te huren.') }}</p>
                            
                            <div id="availability-calendar" class="mb-3"></div>
                            
                            <div class="d-flex gap-2 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 calendar-color-sample bg-success"></div>
                                    <span>{{ __('Beschikbaar') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-2 calendar-color-sample bg-danger"></div>
                                    <span>{{ __('Gereserveerd') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-2 calendar-color-sample bg-secondary"></div>
                                    <span>{{ __('Niet beschikbaar') }}</span>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ __('Om dit item te huren, neem contact op met de verhuurder.') }}
                            </div>
                            
                            <a href="mailto:{{ $advertisement->user->email }}?subject={{ urlencode('Interesse in huren: ' . $advertisement->title) }}" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i>{{ __('Contact opnemen met verhuurder') }}
                            </a>
                        </div>
                    @endif
                    
                    @if($advertisement->rental_conditions)
                        <div class="mb-4">
                            <h5 class="mb-2">{{ __('Verhuurvoorwaarden') }}</h5>
                            <div class="p-3 border rounded">
                                {!! nl2br(e($advertisement->rental_conditions)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Include Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    .calendar-color-sample {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }
    
    /* Flatpickr custom styles */
    .flatpickr-day.available {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }
    
    .flatpickr-day.booked {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data van de advertentie
        const availableDates = @json($advertisement->rental_availability ?? []);
        const bookedDates = @json($advertisement->rental_booked_dates ?? []);
        const isOwner = {{ Auth::id() === $advertisement->user_id ? 'true' : 'false' }};
        
        // Flatpickr configuratie
        const calendarConfig = {
            inline: true,
            mode: "multiple",
            dateFormat: "Y-m-d",
            minDate: "today",
            defaultDate: availableDates,
            disable: bookedDates,
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                
                if (bookedDates.includes(dateStr)) {
                    dayElem.className += " booked";
                } else if (availableDates.includes(dateStr)) {
                    dayElem.className += " available";
                }
            }
        };
        
        // Voor eigenaar: selecteerbare kalender
        if (isOwner) {
            calendarConfig.onChange = function(selectedDates, dateStr, instance) {
                // Update het hidden field met de geselecteerde datums
                const selectedDatesStr = selectedDates.map(date => {
                    return date.toISOString().split('T')[0];
                }).join(',');
                
                document.getElementById('available_dates').value = selectedDatesStr;
            };
        } else {
            // Voor bezoekers: alleen-lezen kalender
            calendarConfig.clickOpens = false;
        }
        
        // Initialiseer de kalender
        const calendar = flatpickr("#availability-calendar", calendarConfig);
    });
</script>
@endsection
