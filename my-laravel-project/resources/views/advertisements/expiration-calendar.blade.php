@extends('layouts.app')

@section('title', __('Advertentie Verloop Kalender'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mijn Advertentie Verloop Kalender') }}</h5>
                    <a href="{{ route('advertisements.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('Terug naar advertenties') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($advertisements->isEmpty())
                        <div class="alert alert-info">
                            {{ __('Je hebt momenteel geen advertenties met een vervaldatum.') }}
                        </div>
                    @else
                        <form action="{{ route('advertisements.extend-multiple') }}" method="POST" id="bulk-extend-form">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">{{ __('Overzicht vervallende advertenties') }}</h4>
                                <button type="submit" class="btn btn-primary" id="bulk-extend-btn" disabled>
                                    <i class="bi bi-arrow-clockwise me-1"></i>{{ __('Verleng geselecteerde advertenties met 1 maand') }}
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="select-all">
                                                    <label class="form-check-label" for="select-all">{{ __('Alle') }}</label>
                                                </div>
                                            </th>
                                            <th>{{ __('Advertentie') }}</th>
                                            <th>{{ __('Verloopt op') }}</th>
                                            <th>{{ __('Dagen resterend') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Acties') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($advertisements as $advertisement)                                            @php
                                                $expiresAt = \Carbon\Carbon::parse($advertisement->expires_at);
                                                // Gebruik floor() om af te ronden naar beneden naar hele dagen
                                                $daysRemaining = floor($today->floatDiffInDays($expiresAt, false));
                                                
                                                // Status bepalen
                                                $isExpired = $daysRemaining < 0;
                                                $isCloseToExpiry = $daysRemaining >= 0 && $daysRemaining <= 7;
                                            @endphp
                                            <tr class="{{ $isExpired ? 'table-danger' : ($isCloseToExpiry ? 'table-warning' : 'table-light') }}">
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input ad-checkbox" type="checkbox" name="advertisement_ids[]" value="{{ $advertisement->id }}" id="ad-{{ $advertisement->id }}">
                                                        <label class="form-check-label" for="ad-{{ $advertisement->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>{{ $advertisement->title }}</strong>
                                                    <div class="small text-muted">{{ __('Categorie') }}: {{ $advertisement->category }}</div>
                                                </td>
                                                <td>
                                                    <div class="badge {{ $isExpired ? 'bg-danger' : ($isCloseToExpiry ? 'bg-warning text-dark' : 'bg-primary') }}">
                                                        {{ $expiresAt->format('d-m-Y') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($isExpired)
                                                        <span class="text-danger fw-bold">{{ __('Verlopen') }}</span>
                                                    @else
                                                        {{ $daysRemaining }} {{ __('dagen') }}
                                                    @endif
                                                </td>
                                                <td>{{ $advertisement->status }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    @endif

                    <div class="my-5">
                        <h3>{{ __('Deze maand vervallend') }}</h3>
                        @if($expiringThisMonth->isEmpty())
                            <div class="alert alert-success">
                                {{ __('Geen advertenties die deze maand vervallen.') }}
                            </div>
                        @else                            <div class="calendar-container">
                                @php
                                    $monthCalendar = '';
                                    $firstDay = new \Carbon\Carbon($today->year . '-' . $today->month . '-01');
                                    $lastDay = $firstDay->copy()->endOfMonth();
                                    
                                    // Calendar table
                                    $monthCalendar .= '<table class="table table-bordered">';
                                    
                                    // Calendar header
                                    $monthCalendar .= '<thead><tr>';
                                    $weekdays = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];
                                    foreach($weekdays as $weekday) {
                                        $monthCalendar .= '<th>' . $weekday . '</th>';
                                    }
                                    $monthCalendar .= '</tr></thead>';
                                    
                                    // Calendar body
                                    $monthCalendar .= '<tbody><tr>';
                                    
                                    // Add empty cells for days before the first of the month
                                    $firstDayOfWeek = $firstDay->dayOfWeek;
                                    if ($firstDayOfWeek == 0) $firstDayOfWeek = 7; // Convert Sunday from 0 to 7
                                    for ($i = 1; $i < $firstDayOfWeek; $i++) {
                                        $monthCalendar .= '<td class="calendar-day">&nbsp;</td>';
                                    }
                                    
                                    // Days of the month
                                    $currentDay = 1;
                                    $daysInMonth = $lastDay->day;
                                    
                                    while ($currentDay <= $daysInMonth) {
                                        $date = \Carbon\Carbon::create($today->year, $today->month, $currentDay);
                                        $dateKey = $date->format('Y-m-d');
                                        $isToday = $date->isSameDay($today);
                                        
                                        $monthCalendar .= '<td class="calendar-day ' . ($isToday ? 'calendar-today' : '') . '">';
                                        $monthCalendar .= '<div class="calendar-day-number">' . $currentDay . '</div>';
                                        
                                        if (isset($groupedByDate[$dateKey])) {
                                            $ads = $groupedByDate[$dateKey];
                                            $monthCalendar .= '<ul class="calendar-events">';
                                            foreach ($ads as $ad) {
                                                $monthCalendar .= '<li class="calendar-event calendar-future">';
                                                $monthCalendar .= '<a href="' . route('advertisements.show', $ad) . '">' . $ad->title . '</a>';
                                                $monthCalendar .= '</li>';
                                            }
                                            $monthCalendar .= '</ul>';
                                        }
                                        
                                        $monthCalendar .= '</td>';
                                        
                                        // End the row and start a new one if we reach Sunday
                                        if ($date->dayOfWeek == 0) {
                                            $monthCalendar .= '</tr><tr>';
                                        }
                                        
                                        $currentDay++;
                                    }
                                    
                                    // Add empty cells for days after the last day of the month
                                    $lastDayOfWeek = $lastDay->dayOfWeek;
                                    if ($lastDayOfWeek != 0) { // Not Sunday
                                        for ($i = $lastDayOfWeek; $i < 7; $i++) {
                                            $monthCalendar .= '<td class="calendar-day">&nbsp;</td>';
                                        }
                                    }
                                    
                                    $monthCalendar .= '</tr></tbody></table>';
                                @endphp
                                
                                {!! $monthCalendar !!}
                            </div>
                        @endif
                    </div>

                    <div class="my-5">
                        <h3>{{ __('Volgende maand vervallend') }}</h3>
                        @if($expiringNextMonth->isEmpty())
                            <div class="alert alert-success">
                                {{ __('Geen advertenties die volgende maand vervallen.') }}
                            </div>
                        @else                            <div class="calendar-container">
                                @php
                                    $nextMonth = $today->copy()->addMonth();
                                    $monthCalendar = '';
                                    $firstDay = new \Carbon\Carbon($nextMonth->year . '-' . $nextMonth->month . '-01');
                                    $lastDay = $firstDay->copy()->endOfMonth();
                                    
                                    // Calendar table
                                    $monthCalendar .= '<table class="table table-bordered">';
                                    
                                    // Calendar header
                                    $monthCalendar .= '<thead><tr>';
                                    $weekdays = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];
                                    foreach($weekdays as $weekday) {
                                        $monthCalendar .= '<th>' . $weekday . '</th>';
                                    }
                                    $monthCalendar .= '</tr></thead>';
                                    
                                    // Calendar body
                                    $monthCalendar .= '<tbody><tr>';
                                    
                                    // Add empty cells for days before the first of the month
                                    $firstDayOfWeek = $firstDay->dayOfWeek;
                                    if ($firstDayOfWeek == 0) $firstDayOfWeek = 7; // Convert Sunday from 0 to 7
                                    for ($i = 1; $i < $firstDayOfWeek; $i++) {
                                        $monthCalendar .= '<td class="calendar-day">&nbsp;</td>';
                                    }
                                    
                                    // Days of the month
                                    $currentDay = 1;
                                    $daysInMonth = $lastDay->day;
                                    
                                    while ($currentDay <= $daysInMonth) {
                                        $date = \Carbon\Carbon::create($nextMonth->year, $nextMonth->month, $currentDay);
                                        $dateKey = $date->format('Y-m-d');
                                        
                                        $monthCalendar .= '<td class="calendar-day">';
                                        $monthCalendar .= '<div class="calendar-day-number">' . $currentDay . '</div>';
                                        
                                        if (isset($groupedByDate[$dateKey])) {
                                            $ads = $groupedByDate[$dateKey];
                                            $monthCalendar .= '<ul class="calendar-events">';
                                            foreach ($ads as $ad) {
                                                $monthCalendar .= '<li class="calendar-event calendar-future">';
                                                $monthCalendar .= '<a href="' . route('advertisements.show', $ad) . '">' . $ad->title . '</a>';
                                                $monthCalendar .= '</li>';
                                            }
                                            $monthCalendar .= '</ul>';
                                        }
                                        
                                        $monthCalendar .= '</td>';
                                        
                                        // End the row and start a new one if we reach Sunday
                                        if ($date->dayOfWeek == 0) {
                                            $monthCalendar .= '</tr><tr>';
                                        }
                                        
                                        $currentDay++;
                                    }
                                    
                                    // Add empty cells for days after the last day of the month
                                    $lastDayOfWeek = $lastDay->dayOfWeek;
                                    if ($lastDayOfWeek != 0) { // Not Sunday
                                        for ($i = $lastDayOfWeek; $i < 7; $i++) {
                                            $monthCalendar .= '<td class="calendar-day">&nbsp;</td>';
                                        }
                                    }
                                    
                                    $monthCalendar .= '</tr></tbody></table>';
                                @endphp
                                
                                {!! $monthCalendar !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .calendar-container {
        margin-top: 20px;
    }
    
    .calendar-weekday-row th {
        background-color: #f8f9fa;
        text-align: center;
        padding: 10px;
    }
    
    .calendar-day {
        height: 100px;
        border: 1px solid #dee2e6;
        padding: 5px;
        vertical-align: top;
        width: 14.28%;
        position: relative;
    }
    
    .calendar-today {
        background-color: #e8f4ff;
    }
    
    .calendar-day-number {
        font-weight: bold;
        font-size: 1.1em;
        position: absolute;
        top: 5px;
        right: 8px;
    }
    
    .calendar-events {
        list-style: none;
        padding-left: 0;
        padding-top: 20px;
        font-size: 0.8em;
    }
    
    .calendar-event {
        margin-bottom: 3px;
        padding: 2px 4px;
        border-radius: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 90%;
    }
    
    .calendar-future {
        background-color: #ffeeba;
    }
    
    .calendar-past {
        background-color: #d6d8db;
        text-decoration: line-through;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecteer alle checkboxes
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.ad-checkbox');
        const bulkExtendBtn = document.getElementById('bulk-extend-btn');
        
        // Functie om de status van de bulk-extend knop bij te werken
        function updateBulkExtendButton() {
            const checkedCount = document.querySelectorAll('.ad-checkbox:checked').length;
            bulkExtendBtn.disabled = checkedCount === 0;
        }
        
        // Voeg event listeners toe aan alle checkboxes
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateBulkExtendButton();
                
                // Controleer of alle checkboxes zijn aangevinkt
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
                
                // Controleer of geen enkele checkbox is aangevinkt
                const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);
                if (noneChecked) {
                    selectAll.checked = false;
                }
            });
        });
        
        // "Selecteer alle" checkbox handler
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const isChecked = selectAll.checked;
                
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
                
                updateBulkExtendButton();
            });
        }
        
        // Update de status van de bulk-extend knop bij het laden van de pagina
        updateBulkExtendButton();
    });
</script>
@endpush
