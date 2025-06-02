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
                                @csrf                                @php
                                    $availability = is_array($advertisement->rental_availability) ? $advertisement->rental_availability : [];
                                @endphp
                                <input type="hidden" id="available_dates" name="available_dates" value="{{ implode(',', $availability) }}">
                                
                                <div id="availability-calendar" class="mb-3"></div>
                                  <div class="card p-3 mb-3">
                                    <h6 class="mb-3 fw-bold">{{ __('Legenda') }}</h6>
                                    <div class="d-flex flex-column gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="calendar-color-sample me-3 bg-success"></div>
                                            <span><strong>{{ __('Beschikbaar') }}</strong> - {{ __('Deze data zijn beschikbaar voor verhuur') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="calendar-color-sample me-3 bg-danger"></div>
                                            <span><strong>{{ __('Gereserveerd') }}</strong> - {{ __('Deze data zijn al geboekt') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="calendar-color-sample me-3 bg-secondary"></div>
                                            <span><strong>{{ __('Niet beschikbaar') }}</strong> - {{ __('Deze data zijn niet geselecteerd voor verhuur') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>{{ __('Beschikbaarheid opslaan') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-light rounded">
                            <h5 class="mb-3">{{ __('Beschikbaarheid') }}</h5>                            <p>{{ __('Bekijk wanneer dit item beschikbaar is om te huren.') }}</p>
                            
                            <div id="availability-calendar" class="mb-3"></div>
                              <div class="card p-3 mb-3">
                                <h6 class="mb-3 fw-bold">{{ __('Legenda') }}</h6>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="calendar-color-sample me-3 bg-success"></div>
                                        <span><strong>{{ __('Beschikbaar') }}</strong> - {{ __('Deze data zijn beschikbaar voor verhuur') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="calendar-color-sample me-3 bg-danger"></div>
                                        <span><strong>{{ __('Gereserveerd') }}</strong> - {{ __('Deze data zijn al geboekt') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="calendar-color-sample me-3 bg-secondary"></div>
                                        <span><strong>{{ __('Niet beschikbaar') }}</strong> - {{ __('Deze data zijn niet aangeboden voor verhuur') }}</span>
                                    </div>
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
        width: 25px;
        height: 25px;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid #ddd;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    /* Flatpickr custom styles - met !important attributen om zeker te zijn dat ze toegepast worden */
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }
    
    .flatpickr-day.booked {
        background-color: #dc3545 !important;
        color: white !important;
        border-color: #c82333 !important;
        font-weight: bold !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }
    
    .flatpickr-day.flatpickr-disabled {
        color: #aaa !important;
        cursor: not-allowed !important;
        background-color: #f8f9fa !important;
    }
    
    .flatpickr-day.today {
        border: 2px solid #007bff !important;
    }
    
    /* Hover effect voor eigenaar view */
    .flatpickr-day:not(.flatpickr-disabled):not(.booked):hover {
        background-color: #007bff !important;
        border-color: #0069d9 !important;
        color: white !important;
    }
    
    /* CSS voor de legenda kleurvakjes */
    .bg-success {
        background-color: #28a745 !important;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
    }
    
    .bg-secondary {
        background-color: #6c757d !important;
    }
</style>

<script>    document.addEventListener('DOMContentLoaded', function() {
        // Data van de advertentie
        @php
            $availability = is_array($advertisement->rental_availability) ? $advertisement->rental_availability : [];
            $bookedDates = is_array($advertisement->rental_booked_dates) ? $advertisement->rental_booked_dates : [];
        @endphp
        const availableDates = @json($availability);
        const bookedDates = @json($bookedDates);
        const isOwner = {{ Auth::id() === $advertisement->user_id ? 'true' : 'false' }};
          // Flatpickr configuratie
        const calendarConfig = {
            inline: true,
            mode: "multiple",
            dateFormat: "Y-m-d",
            minDate: "today",
            defaultDate: availableDates,
            disable: bookedDates,
            onReady: function(selectedDates, dateStr, instance) {
                // Force herinladen van de styling na initialisatie
                setTimeout(() => {
                    const days = document.querySelectorAll('.flatpickr-day');
                    days.forEach(day => {
                        const dateStr = day.dateObj ? day.dateObj.toISOString().split('T')[0] : '';
                        if (bookedDates.includes(dateStr)) {
                            day.classList.add('booked');
                        } else if (availableDates.includes(dateStr)) {
                            day.classList.add('available');
                        }
                    });
                }, 100);
            },
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (bookedDates.includes(dateStr)) {
                    dayElem.className += " booked";
                    dayElem.title = "{{ __('Gereserveerd') }}";
                } else if (availableDates.includes(dateStr)) {
                    dayElem.className += " available";
                    dayElem.title = "{{ __('Beschikbaar voor verhuur') }}";
                } else if (dayElem.dateObj >= today) {
                    dayElem.title = "{{ __('Niet aangeboden voor verhuur') }}";
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
        
        // Extra functies om zeker te zijn dat de kalender goed wordt getoond
        function refreshCalendarColors() {
            const days = document.querySelectorAll('.flatpickr-day');
            days.forEach(day => {
                // Controleren of day.dateObj bestaat
                if (!day.dateObj) {
                    const dayNum = parseInt(day.textContent.trim());
                    if (isNaN(dayNum)) return;
                    
                    // Datum afleiden van de datumstructuur in de kalender
                    const monthNav = document.querySelector('.flatpickr-current-month');
                    if (!monthNav) return;
                    
                    const monthYearText = monthNav.textContent.trim();
                    const dateMatch = monthYearText.match(/([a-zA-Z]+)\s+(\d{4})/);
                    if (!dateMatch) return;
                    
                    const month = new Date(`${dateMatch[1]} 1, ${dateMatch[2]}`).getMonth();
                    const year = parseInt(dateMatch[2]);
                    
                    // Datum opbouwen
                    const dateObj = new Date(year, month, dayNum);
                    const dateStr = dateObj.toISOString().split('T')[0];
                    
                    // Klassen toepassen
                    if (bookedDates.includes(dateStr)) {
                        day.classList.add('booked');
                    } else if (availableDates.includes(dateStr)) {
                        day.classList.add('available');
                    }
                }
            });
        }
        
        // Toepassen na een kleine vertraging en bij elke maandwissel
        setTimeout(refreshCalendarColors, 100);
        document.querySelectorAll('.flatpickr-prev-month, .flatpickr-next-month').forEach(button => {
            button.addEventListener('click', () => setTimeout(refreshCalendarColors, 100));
        });
    });
</script>
@endsection
