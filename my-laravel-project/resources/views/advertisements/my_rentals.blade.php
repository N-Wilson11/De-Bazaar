@extends('layouts.app')

@section('title', __('Mijn Verhuuraanbod'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mijn Verhuuraanbod') }}</h5>
                    <a href="{{ route('rentals.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle"></i> {{ __('Nieuw Verhuuraanbod') }}
                    </a>
                </div>

                <div class="card-body">
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

                    @if(count($rentals) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Titel') }}</th>
                                        <th>{{ __('Prijs per dag') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Datum') }}</th>
                                        <th class="text-center">{{ __('Weergaven') }}</th>
                                        <th class="text-end">{{ __('Acties') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentals as $rental)
                                        <tr>
                                            <td>
                                                <a href="{{ route('advertisements.show', $rental) }}" class="text-decoration-none fw-semibold">
                                                    {{ Str::limit($rental->title, 40) }}
                                                </a>
                                            </td>
                                            <td>€ {{ number_format($rental->rental_price_day, 2, ',', '.') }}</td>
                                            <td>
                                                @if($rental->status === 'active')
                                                    <span class="badge bg-success">{{ __('Actief') }}</span>
                                                @elseif($rental->status === 'inactive')
                                                    <span class="badge bg-secondary">{{ __('Inactief') }}</span>
                                                @elseif($rental->status === 'rented')
                                                    <span class="badge bg-dark">{{ __('Verhuurd') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $rental->created_at->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $rental->views }}</td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('advertisements.show', $rental) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('advertisements.edit', $rental) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="{{ route('advertisements.calendar', $rental) }}" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-calendar-event"></i>
                                                    </a>
                                                    <form action="{{ route('advertisements.destroy', $rental) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Weet je zeker dat je deze advertentie wilt verwijderen?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $rentals->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="mb-4">{{ __('Je hebt nog geen verhuuradvertenties geplaatst.') }}</p>
                            <a href="{{ route('rentals.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('Plaats een verhuuradvertentie') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Verhuur handleiding') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-calendar-plus fs-1 text-success"></i>
                                <h5 class="mt-2">{{ __('1. Stel beschikbaarheid in') }}</h5>
                                <p>{{ __('Geef duidelijk aan wanneer je item beschikbaar is voor verhuur via de kalender.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-chat-text fs-1 text-primary"></i>
                                <h5 class="mt-2">{{ __('2. Communiceer duidelijk') }}</h5>
                                <p>{{ __('Bespreek de details met de huurder en maak duidelijke afspraken over ophalen en terugbrengen.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-check-circle fs-1 text-info"></i>
                                <h5 class="mt-2">{{ __('3. Controleer bij teruggave') }}</h5>
                                <p>{{ __('Controleer het item op schade of slijtage en werk de beschikbaarheidskalender bij.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
