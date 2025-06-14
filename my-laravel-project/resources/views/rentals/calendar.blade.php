@extends('layouts.app')

@section('title', __('Huurkalender'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mijn huurkalender') }}</h5>
                </div>
                <div class="card-body">
                    @if($upcomingRentals->isEmpty())
                        <div class="alert alert-info">
                            {{ __('Je hebt momenteel geen huurtransacties.') }}
                        </div>
                    @else
                        <h4 class="mt-4 mb-3">{{ __('Aankomende huurtransacties') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Ophalen') }}</th>
                                        <th>{{ __('Terugbrengen') }}</th>
                                        <th>{{ __('Duur') }}</th>
                                        <th>{{ __('Kosten') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Acties') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingRentals as $item)
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
                                                <div class="small text-muted">{{ __('Verkoper') }}: {{ optional($item->advertisement)->user->name ?? 'Onbekend' }}</div>
                                            </td>
                                            <td>
                                                <div class="badge bg-primary">{{ $startDate->format('d-m-Y') }}</div>
                                            </td>
                                            <td>
                                                <div class="badge bg-danger">{{ $endDate->format('d-m-Y') }}</div>
                                            </td>
                                            <td>{{ $startDate->diffInDays($endDate) + 1 }} {{ __('dagen') }}</td>
                                            <td>€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td>
                                                @if($isActive)
                                                    <span class="badge bg-success">{{ __('Actief') }}</span>
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
                                                @if($isActive && !$item->is_returned)
                                                    <a href="{{ route('rentals.return', $item) }}" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-box-arrow-in-left"></i> {{ __('Inleveren') }}
                                                    </a>
                                                @elseif($item->is_returned)
                                                    <a href="{{ route('rentals.return-details', $item) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-receipt"></i> {{ __('Details') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('orders.show', $item->order_id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-info-circle"></i> {{ __('Details') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                        
                        <h5 class="text-center">{{ $currentMonth }}</h5>
                        
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Ma') }}</th>
                                    <th>{{ __('Di') }}</th>
                                    <th>{{ __('Wo') }}</th>
                                    <th>{{ __('Do') }}</th>
                                    <th>{{ __('Vr') }}</th>
                                    <th>{{ __('Za') }}</th>
                                    <th>{{ __('Zo') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $day = 1;
                                    $dayCount = 1;
                                @endphp
                                
                                <tr>
                                    @for($i = 1; $i <= 7; $i++)
                                        @if($i < $firstDayOfWeek)
                                            <td class="calendar-day empty"></td>
                                        @else
                                            <td class="calendar-day {{ $today->day == $day ? 'today' : '' }}">
                                                <div class="day-number">{{ $day }}</div>
                                                <div class="day-events">
                                                    @foreach($currentMonthRentals as $item)
                                                        @php
                                                            $startDate = \Carbon\Carbon::parse($item->rental_start_date);
                                                            $endDate = \Carbon\Carbon::parse($item->rental_end_date);
                                                            $currentDate = $today->copy()->startOfMonth()->addDays($day - 1);
                                                        @endphp
                                                        
                                                        @if($currentDate->between($startDate, $endDate))
                                                            <div class="event {{ $currentDate->isSameDay($startDate) ? 'start' : ($currentDate->isSameDay($endDate) ? 'end' : 'middle') }}">
                                                                @if($currentDate->isSameDay($startDate))
                                                                    <i class="bi bi-box-arrow-right text-success"></i> 
                                                                @elseif($currentDate->isSameDay($endDate))
                                                                    <i class="bi bi-box-arrow-in-left text-danger"></i> 
                                                                @endif
                                                                <small>{{ Str::limit($item->title, 15) }}</small>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                            @php $day++; @endphp
                                        @endif
                                    @endfor
                                </tr>
                                
                                @for($j = 0; $j < 5; $j++)
                                    <tr>
                                        @for($i = 0; $i < 7; $i++)
                                            @if($day <= $daysInMonth)
                                                <td class="calendar-day {{ $today->day == $day ? 'today' : '' }}">
                                                    <div class="day-number">{{ $day }}</div>
                                                    <div class="day-events">
                                                        @foreach($currentMonthRentals as $item)
                                                            @php
                                                                $startDate = \Carbon\Carbon::parse($item->rental_start_date);
                                                                $endDate = \Carbon\Carbon::parse($item->rental_end_date);
                                                                $currentDate = $today->copy()->startOfMonth()->addDays($day - 1);
                                                            @endphp
                                                            
                                                            @if($currentDate->between($startDate, $endDate))
                                                                <div class="event {{ $currentDate->isSameDay($startDate) ? 'start' : ($currentDate->isSameDay($endDate) ? 'end' : 'middle') }}">
                                                                    @if($currentDate->isSameDay($startDate))
                                                                        <i class="bi bi-box-arrow-right text-success"></i> 
                                                                    @elseif($currentDate->isSameDay($endDate))
                                                                        <i class="bi bi-box-arrow-in-left text-danger"></i> 
                                                                    @endif
                                                                    <small>{{ Str::limit($item->title, 15) }}</small>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                @php $day++; @endphp
                                            @else
                                                <td class="calendar-day empty"></td>
                                            @endif
                                        @endfor
                                    </tr>
                                    
                                    @if($day > $daysInMonth)
                                        @break
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Next Month Calendar -->
                    <h4 class="mt-5 mb-3">{{ __('Kalender overzicht - Volgende maand') }}</h4>
                    <div class="calendar-container mb-4">
                        @php
                            $nextMonth = $today->copy()->addMonth();
                            $nextMonthName = $nextMonth->format('F Y');
                            $daysInNextMonth = $nextMonth->daysInMonth;
                            $firstDayOfNextMonth = $nextMonth->copy()->startOfMonth()->dayOfWeek;
                            // Adjust first day of week (0 = Sunday, 1 = Monday, etc.)
                            $firstDayOfNextMonth = $firstDayOfNextMonth == 0 ? 7 : $firstDayOfNextMonth;
                        @endphp
                        
                        <h5 class="text-center">{{ $nextMonthName }}</h5>
                        
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Ma') }}</th>
                                    <th>{{ __('Di') }}</th>
                                    <th>{{ __('Wo') }}</th>
                                    <th>{{ __('Do') }}</th>
                                    <th>{{ __('Vr') }}</th>
                                    <th>{{ __('Za') }}</th>
                                    <th>{{ __('Zo') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $day = 1;
                                @endphp
                                
                                <tr>
                                    @for($i = 1; $i <= 7; $i++)
                                        @if($i < $firstDayOfNextMonth)
                                            <td class="calendar-day empty"></td>
                                        @else
                                            <td class="calendar-day">
                                                <div class="day-number">{{ $day }}</div>
                                                <div class="day-events">
                                                    @foreach($nextMonthRentals as $item)
                                                        @php
                                                            $startDate = \Carbon\Carbon::parse($item->rental_start_date);
                                                            $endDate = \Carbon\Carbon::parse($item->rental_end_date);
                                                            $currentDate = $nextMonth->copy()->startOfMonth()->addDays($day - 1);
                                                        @endphp
                                                        
                                                        @if($currentDate->between($startDate, $endDate))
                                                            <div class="event {{ $currentDate->isSameDay($startDate) ? 'start' : ($currentDate->isSameDay($endDate) ? 'end' : 'middle') }}">
                                                                @if($currentDate->isSameDay($startDate))
                                                                    <i class="bi bi-box-arrow-right text-success"></i> 
                                                                @elseif($currentDate->isSameDay($endDate))
                                                                    <i class="bi bi-box-arrow-in-left text-danger"></i> 
                                                                @endif
                                                                <small>{{ Str::limit($item->title, 15) }}</small>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                            @php $day++; @endphp
                                        @endif
                                    @endfor
                                </tr>
                                
                                @for($j = 0; $j < 5; $j++)
                                    <tr>
                                        @for($i = 0; $i < 7; $i++)
                                            @if($day <= $daysInNextMonth)
                                                <td class="calendar-day">
                                                    <div class="day-number">{{ $day }}</div>
                                                    <div class="day-events">
                                                        @foreach($nextMonthRentals as $item)
                                                            @php
                                                                $startDate = \Carbon\Carbon::parse($item->rental_start_date);
                                                                $endDate = \Carbon\Carbon::parse($item->rental_end_date);
                                                                $currentDate = $nextMonth->copy()->startOfMonth()->addDays($day - 1);
                                                            @endphp
                                                            
                                                            @if($currentDate->between($startDate, $endDate))
                                                                <div class="event {{ $currentDate->isSameDay($startDate) ? 'start' : ($currentDate->isSameDay($endDate) ? 'end' : 'middle') }}">
                                                                    @if($currentDate->isSameDay($startDate))
                                                                        <i class="bi bi-box-arrow-right text-success"></i> 
                                                                    @elseif($currentDate->isSameDay($endDate))
                                                                        <i class="bi bi-box-arrow-in-left text-danger"></i> 
                                                                    @endif
                                                                    <small>{{ Str::limit($item->title, 15) }}</small>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                @php $day++; @endphp
                                            @else
                                                <td class="calendar-day empty"></td>
                                            @endif
                                        @endfor
                                    </tr>
                                    
                                    @if($day > $daysInNextMonth)
                                        @break
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Legenda') }}</h5>
                                <div class="d-flex flex-wrap">
                                    <div class="me-4 mb-2">
                                        <i class="bi bi-box-arrow-right text-success"></i> {{ __('Ophalen') }}
                                    </div>
                                    <div class="me-4 mb-2">
                                        <i class="bi bi-box-arrow-in-left text-danger"></i> {{ __('Terugbrengen') }}
                                    </div>
                                    <div class="me-4 mb-2">
                                        <span class="badge bg-info">{{ __('Aankomend') }}</span>
                                    </div>
                                    <div class="me-4 mb-2">
                                        <span class="badge bg-success">{{ __('Actief') }}</span>
                                    </div>
                                    <div class="me-4 mb-2">
                                        <span class="badge bg-secondary">{{ __('Verlopen') }}</span>
                                    </div>
                                </div>
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
    
    .calendar-day {
        height: 100px;
        vertical-align: top;
        position: relative;
        padding: 5px;
    }
    
    .calendar-day.today {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
    
    .calendar-day.empty {
        background-color: #f9f9f9;
    }
    
    .day-number {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .day-events {
        overflow-y: auto;
        max-height: 70px;
    }
    
    .event {
        font-size: 0.8rem;
        margin-bottom: 2px;
        padding: 2px 4px;
        border-radius: 2px;
        background-color: rgba(var(--bs-info-rgb), 0.2);
        border-left: 3px solid var(--bs-info);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .event.start {
        border-left: 3px solid var(--bs-success);
        background-color: rgba(var(--bs-success-rgb), 0.2);
    }
    
    .event.end {
        border-left: 3px solid var(--bs-danger);
        background-color: rgba(var(--bs-danger-rgb), 0.2);
    }
    
    .event.middle {
        border-left: 3px solid var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.2);
    }
</style>
@endsection
