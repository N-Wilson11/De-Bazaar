@extends('layouts.app')

@section('title', __('Mijn Advertenties'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mijn Advertenties') }}</h5>
                    <div>
                        <a href="{{ route('advertisements.create') }}" class="btn btn-sm btn-primary me-2">
                            <i class="bi bi-plus-circle"></i> {{ __('Nieuwe Advertentie') }}
                        </a>
                        <a href="{{ route('rentals.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-calendar-plus"></i> {{ __('Nieuw Verhuuraanbod') }}
                        </a>
                    </div>
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

                    @if(count($advertisements) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Titel') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Prijs') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Datum') }}</th>
                                        <th class="text-center">{{ __('Weergaven') }}</th>
                                        <th class="text-end">{{ __('Acties') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($advertisements as $advertisement)
                                        <tr>
                                            <td>
                                                <a href="{{ route('advertisements.show', $advertisement) }}" class="text-decoration-none fw-semibold">
                                                    {{ Str::limit($advertisement->title, 40) }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($advertisement->isRental())
                                                    <span class="badge bg-info">{{ __('Verhuur') }}</span>
                                                @elseif($advertisement->type === 'auction')
                                                    <span class="badge bg-warning">{{ __('Veiling') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Verkoop') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($advertisement->isRental())
                                                    <span>€ {{ number_format($advertisement->rental_price_day, 2, ',', '.') }}/dag</span>
                                                @else
                                                    <span>€ {{ number_format($advertisement->price, 2, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($advertisement->status === 'active')
                                                    <span class="badge bg-success">{{ __('Actief') }}</span>
                                                @elseif($advertisement->status === 'inactive')
                                                    <span class="badge bg-secondary">{{ __('Inactief') }}</span>
                                                @elseif($advertisement->status === 'sold')
                                                    <span class="badge bg-dark">{{ __('Verkocht') }}</span>
                                                @elseif($advertisement->status === 'rented')
                                                    <span class="badge bg-dark">{{ __('Verhuurd') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $advertisement->created_at->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $advertisement->views }}</td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($advertisement->isRental())
                                                        <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-calendar-event"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('advertisements.destroy', $advertisement) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Weet je zeker dat je deze advertentie wilt verwijderen?') }}')">
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
                            {{ $advertisements->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="mb-4">{{ __('Je hebt nog geen advertenties geplaatst.') }}</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('advertisements.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>{{ __('Nieuwe Advertentie') }}
                                </a>
                                <a href="{{ route('rentals.create') }}" class="btn btn-success">
                                    <i class="bi bi-calendar-plus me-2"></i>{{ __('Nieuw Verhuuraanbod') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
