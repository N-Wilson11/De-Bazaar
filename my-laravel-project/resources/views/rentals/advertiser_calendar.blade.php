@extends('layouts.app')

@section('title', __('Verhuur Kalender'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mijn verhuurde producten kalender') }}</h5>
                </div>
                <div class="card-body">
                    @if($rentedOutItems->isEmpty())
                        <div class="alert alert-info">
                            {{ __('Je hebt momenteel geen verhuurde producten.') }}
                        </div>
                    @else
                        <h4 class="mt-4 mb-3">{{ __('Aankomende verhuurtransacties') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Huurder') }}</th>
                                        <th>{{ __('Afhalen') }}</th>
                                        <th>{{ __('Terugbrengen') }}</th>
                                        <th>{{ __('Duur') }}</th>
                                        <th>{{ __('Opbrengst') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Acties') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentedOutItems as $item)
                                        @php
                                            $startDate = \Carbon\Carbon::parse($item->rental_start_date);
                                            $endDate = \Carbon\Carbon::parse($item->rental_end_date);
                                            $today = \Carbon\Carbon::today();
                                            $isActive = $today->between($startDate, $endDate);
                                            $isPending = $today->lt($startDate);
                                            $isCompleted = $today->gt($endDate);
                                        @endphp
                                        <tr class="{{ $isActive ? 'table-success' : ($isPending ? 'table-info' : 'table-light') }}">
                                            <td>
                                                <strong>{{ $item->title }}</strong>
                                                @if($item->advertisement)
                                                    <div class="small">
                                                        <a href="{{ route('advertisements.show', $item->advertisement) }}">
                                                            <i class="bi bi-link-45deg"></i> Bekijk advertentie
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                {{ optional($item->order->user)->name ?? 'Onbekend' }}
                                            </td>
                                            <td>
                                                <div class="badge bg-primary">{{ $startDate->format('d-m-Y') }}</div>
                                                <div class="small text-muted">{{ $startDate->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                <div class="badge bg-danger">{{ $endDate->format('d-m-Y') }}</div>
                                                <div class="small text-muted">{{ $endDate->format('H:i') }}</div>
                                            </td>
                                            <td>{{ $startDate->diffInDays($endDate) + 1 }} {{ __('dagen') }}</td>
                                            <td>€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td>
                                                @if($isActive)
                                                    <span class="badge bg-success">{{ __('Uitgeleend') }}</span>
                                                @elseif($isPending)
                                                    <span class="badge bg-info">{{ __('Aankomend') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Verlopen') }}</span>
                                                @endif
                                                
                                                @if($item->is_returned)
                                                    <span class="badge bg-dark">{{ __('Geretourneerd') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('orders.show-sale-item', $item) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-info-circle"></i> {{ __('Details') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    <!-- Calendar View By Product -->
                    <h4 class="mt-5 mb-3">{{ __('Producten overzicht') }}</h4>
                    
                    @if($rentalsByProduct->isEmpty())
                        <div class="alert alert-info">
                            {{ __('Je hebt momenteel geen verhuurde producten.') }}
                        </div>
                    @else
                        <div class="accordion" id="productRentalsAccordion">
                            @foreach($rentalsByProduct as $advertisementId => $items)
                                @php
                                    $advertisement = $items->first()->advertisement;
                                    $productName = $advertisement->title ?? 'Product #' . $advertisementId;
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $advertisementId }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $advertisementId }}" aria-expanded="false" 
                                                aria-controls="collapse{{ $advertisementId }}">
                                            <strong>{{ $productName }}</strong>
                                            <span class="badge bg-primary ms-2">{{ $items->count() }} {{ __('verhuringen') }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $advertisementId }}" class="accordion-collapse collapse" 
                                         aria-labelledby="heading{{ $advertisementId }}" data-bs-parent="#productRentalsAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Huurder') }}</th>
                                                            <th>{{ __('Periode') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Actie') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items as $item)
                                                            @php
                                                                $startDate = \Carbon\Carbon::parse($item->rental_start_date);
                                                                $endDate = \Carbon\Carbon::parse($item->rental_end_date);
                                                                $today = \Carbon\Carbon::today();
                                                                $isActive = $today->between($startDate, $endDate);
                                                                $isPending = $today->lt($startDate);
                                                                $isCompleted = $today->gt($endDate);
                                                            @endphp
                                                            <tr>
                                                                <td>{{ optional($item->order->user)->name ?? 'Onbekend' }}</td>
                                                                <td>
                                                                    {{ $startDate->format('d-m-Y') }} tot {{ $endDate->format('d-m-Y') }}
                                                                    <div class="small text-muted">{{ $startDate->diffInDays($endDate) + 1 }} dagen</div>
                                                                </td>
                                                                <td>
                                                                    @if($isActive)
                                                                        <span class="badge bg-success">{{ __('Uitgeleend') }}</span>
                                                                    @elseif($isPending)
                                                                        <span class="badge bg-info">{{ __('Aankomend') }}</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ __('Verlopen') }}</span>
                                                                    @endif
                                                                    
                                                                    @if($item->is_returned)
                                                                        <span class="badge bg-dark">{{ __('Geretourneerd') }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('orders.show-sale-item', $item) }}" class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-info-circle"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Calendar View -->
                    <h4 class="mt-5 mb-3">{{ __('Kalender overzicht - Deze maand') }}</h4>
                    <div class="calendar-container mb-5">
                        @php
                            $currentMonth = $today->format('F Y');
                            $daysInMonth = $today->daysInMonth;
                            $firstDayOfWeek = $today->copy()->startOfMonth()->dayOfWeek;
                            // Adjust first day of week (0 = Sunday, 1 = Monday, etc.)
                            $firstDayOfWeek = $firstDayOfWeek == 0 ? 7 : $firstDayOfWeek;
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5>{{ $currentMonth }}</h5>
                        </div>
                        
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th>Ma</th>
                                    <th>Di</th>
                                    <th>Wo</th>
                                    <th>Do</th>
                                    <th>Vr</th>
                                    <th>Za</th>
                                    <th>Zo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php
                                        // Fill in empty cells before the first day of the month
                                        for ($i = 1; $i < $firstDayOfWeek; $i++) {
                                            echo '<td class="empty-day"></td>';
                                        }
                                        
                                        $dayCount = $firstDayOfWeek - 1;
                                        for ($day = 1; $day <= $daysInMonth; $day++) {
                                            $date = $today->copy()->startOfMonth()->addDays($day - 1);
                                            $dateStr = $date->format('Y-m-d');
                                            $isToday = $date->isToday();
                                            
                                            // Find rentals for this day
                                            $rentalsToday = $currentMonthRentals->filter(function($rental) use ($dateStr) {
                                                $start = $rental->rental_start_date;
                                                $end = $rental->rental_end_date;
                                                return ($dateStr >= $start && $dateStr <= $end);
                                            });
                                            
                                            $hasRentals = $rentalsToday->isNotEmpty();
                                            $dayClass = $isToday ? 'today' : '';
                                            $dayClass .= $hasRentals ? ' has-rentals' : '';
                                            
                                            // Start a new row when necessary
                                            if ($dayCount % 7 === 0 && $day > 1) {
                                                echo '</tr><tr>';
                                            }
                                            
                                            echo '<td class="' . $dayClass . '">';
                                            echo '<div class="day-number">' . $day . '</div>';
                                            
                                            if ($hasRentals) {
                                                echo '<div class="rentals-count">' . $rentalsToday->count() . ' verhuur</div>';
                                                foreach ($rentalsToday as $rental) {
                                                    $isStartDay = $dateStr == $rental->rental_start_date;
                                                    $isEndDay = $dateStr == $rental->rental_end_date;
                                                    $badgeClass = $isStartDay ? 'bg-success' : ($isEndDay ? 'bg-danger' : 'bg-primary');
                                                    $badgeText = $isStartDay ? 'Uitleen' : ($isEndDay ? 'Retour' : '');
                                                    
                                                    echo '<div class="rental-item">';
                                                    echo '<span class="badge ' . $badgeClass . '">' . $badgeText . '</span> ';
                                                    echo '<small>' . substr($rental->title, 0, 15) . (strlen($rental->title) > 15 ? '...' : '') . '</small>';
                                                    echo '</div>';
                                                }
                                            }
                                            
                                            echo '</td>';
                                            $dayCount++;
                                        }
                                        
                                        // Fill in empty cells after the last day of the month
                                        $remainingCells = 7 - ($dayCount % 7);
                                        if ($remainingCells < 7) {
                                            for ($i = 0; $i < $remainingCells; $i++) {
                                                echo '<td class="empty-day"></td>';
                                            }
                                        }
                                    @endphp
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Next Month Calendar -->
                    <h4 class="mt-5 mb-3">{{ __('Kalender overzicht - Volgende maand') }}</h4>
                    <div class="calendar-container">
                        @php
                            $nextMonth = $today->copy()->addMonth();
                            $nextMonthName = $nextMonth->format('F Y');
                            $daysInNextMonth = $nextMonth->daysInMonth;
                            $firstDayOfNextMonth = $nextMonth->copy()->startOfMonth()->dayOfWeek;
                            // Adjust first day of week
                            $firstDayOfNextMonth = $firstDayOfNextMonth == 0 ? 7 : $firstDayOfNextMonth;
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5>{{ $nextMonthName }}</h5>
                        </div>
                        
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th>Ma</th>
                                    <th>Di</th>
                                    <th>Wo</th>
                                    <th>Do</th>
                                    <th>Vr</th>
                                    <th>Za</th>
                                    <th>Zo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php
                                        // Fill in empty cells before the first day of the month
                                        for ($i = 1; $i < $firstDayOfNextMonth; $i++) {
                                            echo '<td class="empty-day"></td>';
                                        }
                                        
                                        $dayCount = $firstDayOfNextMonth - 1;
                                        for ($day = 1; $day <= $daysInNextMonth; $day++) {
                                            $date = $nextMonth->copy()->startOfMonth()->addDays($day - 1);
                                            $dateStr = $date->format('Y-m-d');
                                            
                                            // Find rentals for this day
                                            $rentalsToday = $nextMonthRentals->filter(function($rental) use ($dateStr) {
                                                $start = $rental->rental_start_date;
                                                $end = $rental->rental_end_date;
                                                return ($dateStr >= $start && $dateStr <= $end);
                                            });
                                            
                                            $hasRentals = $rentalsToday->isNotEmpty();
                                            $dayClass = $hasRentals ? 'has-rentals' : '';
                                            
                                            // Start a new row when necessary
                                            if ($dayCount % 7 === 0 && $day > 1) {
                                                echo '</tr><tr>';
                                            }
                                            
                                            echo '<td class="' . $dayClass . '">';
                                            echo '<div class="day-number">' . $day . '</div>';
                                            
                                            if ($hasRentals) {
                                                echo '<div class="rentals-count">' . $rentalsToday->count() . ' verhuur</div>';
                                                foreach ($rentalsToday as $rental) {
                                                    $isStartDay = $dateStr == $rental->rental_start_date;
                                                    $isEndDay = $dateStr == $rental->rental_end_date;
                                                    $badgeClass = $isStartDay ? 'bg-success' : ($isEndDay ? 'bg-danger' : 'bg-primary');
                                                    $badgeText = $isStartDay ? 'Uitleen' : ($isEndDay ? 'Retour' : '');
                                                    
                                                    echo '<div class="rental-item">';
                                                    echo '<span class="badge ' . $badgeClass . '">' . $badgeText . '</span> ';
                                                    echo '<small>' . substr($rental->title, 0, 15) . (strlen($rental->title) > 15 ? '...' : '') . '</small>';
                                                    echo '</div>';
                                                }
                                            }
                                            
                                            echo '</td>';
                                            $dayCount++;
                                        }
                                        
                                        // Fill in empty cells after the last day of the month
                                        $remainingCells = 7 - ($dayCount % 7);
                                        if ($remainingCells < 7) {
                                            for ($i = 0; $i < $remainingCells; $i++) {
                                                echo '<td class="empty-day"></td>';
                                            }
                                        }
                                    @endphp
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Calendar Legend -->
                    <div class="mt-4">
                        <h5>{{ __('Legenda') }}</h5>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-1">Uitleen</span>
                                <small>Eerste dag van verhuur (product uit te lenen)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-1">Retour</span>
                                <small>Laatste dag van verhuur (product terug)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-1">Verhuurd</span>
                                <small>Tussenliggende dag (product is uit)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .calendar-table {
        table-layout: fixed;
    }
    
    .calendar-table th {
        text-align: center;
        background-color: #f8f9fa;
    }
    
    .calendar-table td {
        height: 120px;
        width: calc(100% / 7);
        vertical-align: top;
        padding: 5px;
    }
    
    .empty-day {
        background-color: #f8f9fa;
    }
    
    .day-number {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .today {
        background-color: rgba(255, 248, 230, 0.5);
    }
    
    .has-rentals {
        background-color: rgba(240, 249, 255, 0.5);
    }
    
    .rentals-count {
        font-size: 0.8em;
        color: #666;
        margin-bottom: 5px;
    }
    
    .rental-item {
        margin-bottom: 3px;
        font-size: 0.8em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
