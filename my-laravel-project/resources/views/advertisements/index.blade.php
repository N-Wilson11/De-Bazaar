@extends('layouts.app')

@section('title', __('general.my_advertisements'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('general.my_advertisements') }}</h5>
                    @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                        <div>
                            <a href="{{ route('advertisements.expiration-calendar') }}" class="btn btn-sm btn-secondary me-2">
                                <i class="bi bi-calendar-event"></i> {{ __('Verloop Kalender') }}
                            </a>
                            <a href="{{ route('advertisements.import') }}" class="btn btn-sm btn-info me-2">
                                <i class="bi bi-file-arrow-up"></i> {{ __('CSV importeren') }}
                            </a>
                            
                            @if($canCreateNormal)
                                <a href="{{ route('advertisements.create') }}" class="btn btn-sm btn-primary me-2">
                                    <i class="bi bi-plus-circle"></i> {{ __('general.new_advertisement') }}
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-primary me-2 disabled" title="{{ __('general.max_normal_ads') }}">
                                    <i class="bi bi-plus-circle"></i> {{ __('general.new_advertisement') }}
                                </button>
                            @endif
                            
                            @if($canCreateRental)
                                <a href="{{ route('rentals.create') }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-calendar-plus"></i> {{ __('general.new_rental') }}
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-success disabled" title="{{ __('general.max_rental_ads') }}">
                                    <i class="bi bi-calendar-plus"></i> {{ __('general.new_rental') }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div><div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                      @if((Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk') && (!$canCreateNormal || !$canCreateRental))
                        <div class="alert alert-info mb-4">
                            <h5><i class="bi bi-info-circle me-2"></i>{{ __('general.ad_limit_reached') }}</h5>
                            <p class="mb-0">
                                @if(!$canCreateNormal)
                                    <span class="d-block"><i class="bi bi-dash me-2"></i>{{ __('general.max_normal_ads') }}</span>
                                @endif
                                @if(!$canCreateRental)
                                    <span class="d-block"><i class="bi bi-dash me-2"></i>{{ __('general.max_rental_ads') }}</span>
                                @endif
                                <span class="d-block mt-2">{{ __('general.delete_to_add') }}</span>
                            </p>
                        </div>
                    @endif
                      <!-- Filter sectie -->
                    <div class="mb-4">
                        <form action="{{ route('advertisements.index') }}" method="GET" class="row g-3 align-items-end">
                            <!-- Zoekbalk -->
                            <div class="col-md-12 mb-2">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Zoek op titel...') }}" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>{{ __('Zoeken') }}
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Overige filters -->
                            <div class="col-md-4">
                                <label for="type" class="form-label">{{ __('general.advertisement_type') }}</label>
                                <select id="type" name="type" class="form-select">
                                    <option value="">{{ __('general.all_types') }}</option>
                                    <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>{{ __('general.sale_advertisements') }}</option>
                                    <option value="rental" {{ request('type') == 'rental' ? 'selected' : '' }}>{{ __('general.rental_advertisements') }}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">                                <label for="category" class="form-label">{{ __('general.category') }}</label>
                                <select id="category" name="category" class="form-select">
                                    <option value="all">{{ __('general.all_categories') }}</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="sort" class="form-label">{{ __('general.sort_by') }}</label>
                                <select id="sort" name="sort" class="form-select">
                                    <option value="created_at|desc" {{ request('sort') == 'created_at|desc' ? 'selected' : '' }}>{{ __('general.date_newest_first') }}</option>
                                    <option value="created_at|asc" {{ request('sort') == 'created_at|asc' ? 'selected' : '' }}>{{ __('general.date_oldest_first') }}</option>
                                    <option value="title|asc" {{ request('sort') == 'title|asc' ? 'selected' : '' }}>{{ __('general.title_asc') }}</option>
                                    <option value="title|desc" {{ request('sort') == 'title|desc' ? 'selected' : '' }}>{{ __('general.title_desc') }}</option>
                                    <option value="price|asc" {{ request('sort') == 'price|asc' ? 'selected' : '' }}>{{ __('general.price_low_high') }}</option>
                                    <option value="price|desc" {{ request('sort') == 'price|desc' ? 'selected' : '' }}>{{ __('general.price_high_low') }}</option>
                                    <option value="views|desc" {{ request('sort') == 'views|desc' ? 'selected' : '' }}>{{ __('general.popularity') }}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100 mt-4">
                                    <i class="bi bi-filter me-1"></i>{{ __('Filteren') }}
                                </button>
                            </div>
                        </form>
                        
                        <!-- Reset link -->
                        @if(request('search') || request('type') || request('category'))
                            <div class="text-end mt-2">
                                <a href="{{ route('advertisements.index') }}" class="text-decoration-none">
                                    <i class="bi bi-x-circle"></i> {{ __('Reset filters') }}
                                </a>
                            </div>
                        @endif
                    </div>

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
                                                    <span class="badge bg-info">{{ __('general.rentals') }}</span>
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
                                            <td class="text-center">{{ $advertisement->views }}</td>                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-info" title="{{ __('Bekijken') }}">
                                                        <i class="bi bi-eye"></i> <span class="d-none d-md-inline">{{ __('Bekijken') }}</span>
                                                    </a>
                                                    <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-sm btn-primary" title="{{ __('Bewerken') }}">
                                                        <i class="bi bi-pencil"></i> <span class="d-none d-md-inline">{{ __('Bewerken') }}</span>
                                                    </a>
                                                    @if($advertisement->isRental())
                                                        <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-sm btn-warning" title="{{ __('Kalender') }}">
                                                            <i class="bi bi-calendar-event"></i> <span class="d-none d-md-inline">{{ __('Kalender') }}</span>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('advertisements.destroy', $advertisement) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Weet je zeker dat je deze advertentie wilt verwijderen?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Verwijderen') }}">
                                                            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">{{ __('Verwijderen') }}</span>
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
                        </div>                    @else
                        <div class="text-center py-4">
                            <p class="mb-4">{{ __('Je hebt nog geen advertenties geplaatst.') }}</p>
                            <div class="d-flex justify-content-center gap-3">
                                @if(Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk')
                                    @if($canCreateNormal)
                                        <a href="{{ route('advertisements.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>{{ __('Nieuwe Advertentie') }}
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-primary disabled" title="{{ __('general.max_normal_ads') }}">
                                            <i class="bi bi-plus-circle me-2"></i>{{ __('Nieuwe Advertentie') }}
                                        </button>
                                    @endif

                                    @if($canCreateRental)
                                        <a href="{{ route('rentals.create') }}" class="btn btn-success">
                                            <i class="bi bi-calendar-plus me-2"></i>{{ __('Nieuw Verhuuraanbod') }}
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-success disabled" title="{{ __('general.max_rental_ads') }}">
                                            <i class="bi bi-calendar-plus me-2"></i>{{ __('Nieuw Verhuuraanbod') }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                              @if((Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk') && (!$canCreateNormal || !$canCreateRental))
                                <div class="alert alert-warning mt-4">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    {{ __('general.ad_limit_info') }}. {{ __('general.delete_to_add') }}.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
