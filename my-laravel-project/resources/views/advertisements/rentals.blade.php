@extends('layouts.app')

@section('title', __('general.rentals'))

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">                    <h1 class="h3 mb-3">{{ __('general.rentals') }}</h1>
                    <p>{{ __('general.browse_rentals_text') }}</p>
                      @if(Auth::check() && (Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk'))
                        <a href="{{ route('rentals.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('general.rent_your_item') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">                <div class="card-header">
                    <h5 class="mb-0">{{ __('general.filters') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rentals.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="category" class="form-label">{{ __('general.category') }}</label>
                            <select id="category" name="category" class="form-select">                                <option value="">{{ __('general.all_categories') }}</option>
                                <option value="elektronica" {{ request('category') == 'elektronica' ? 'selected' : '' }}>{{ __('general.category_electronics') }}</option>
                                <option value="meubels" {{ request('category') == 'meubels' ? 'selected' : '' }}>{{ __('general.category_furniture') }}</option>
                                <option value="gereedschap" {{ request('category') == 'gereedschap' ? 'selected' : '' }}>{{ __('general.category_tools') }}</option>
                                <option value="auto" {{ request('category') == 'auto' ? 'selected' : '' }}>{{ __('general.category_auto') }}</option>
                                <option value="sport" {{ request('category') == 'sport' ? 'selected' : '' }}>{{ __('general.category_sports') }}</option>
                                <option value="overig" {{ request('category') == 'overig' ? 'selected' : '' }}>{{ __('general.category_other') }}</option>
                            </select>
                        </div>
                          <div class="mb-3">
                            <label for="price_max" class="form-label">{{ __('general.max_price_per_day') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" id="price_max" name="price_max" class="form-control" value="{{ request('price_max') }}" min="0" step="0.50">
                            </div>
                        </div>
                          <div class="mb-3">
                            <label for="location" class="form-label">{{ __('general.location') }}</label>
                            <input type="text" id="location" name="location" class="form-control" value="{{ request('location') }}">
                        </div>
                          <div class="mb-3">
                            <label for="sort" class="form-label">{{ __('general.sort_by') }}</label>
                            <select id="sort" name="sort" class="form-select">
                                <option value="created_at|desc" {{ request('sort') == 'created_at|desc' ? 'selected' : '' }}>{{ __('general.newest_first') }}</option>
                                <option value="created_at|asc" {{ request('sort') == 'created_at|asc' ? 'selected' : '' }}>{{ __('general.oldest_first') }}</option>
                                <option value="title|asc" {{ request('sort') == 'title|asc' ? 'selected' : '' }}>{{ __('general.title_asc') }}</option>
                                <option value="title|desc" {{ request('sort') == 'title|desc' ? 'selected' : '' }}>{{ __('general.title_desc') }}</option>
                                <option value="rental_price_day|asc" {{ request('sort') == 'rental_price_day|asc' ? 'selected' : '' }}>{{ __('general.day_price_low_high') }}</option>
                                <option value="rental_price_day|desc" {{ request('sort') == 'rental_price_day|desc' ? 'selected' : '' }}>{{ __('general.day_price_high_low') }}</option>
                                <option value="views|desc" {{ request('sort') == 'views|desc' ? 'selected' : '' }}>{{ __('general.most_viewed') }}</option>
                            </select>
                        </div>                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i>{{ __('general.apply_filters') }}
                            </button>
                        </div>
                    </form>
                    
                    <!-- Reset link -->
                    @if(request('category') || request('price_max') || request('location') || request('sort'))
                        <div class="text-end mt-2">
                            <a href="{{ route('rentals.index') }}" class="text-decoration-none">
                                <i class="bi bi-x-circle"></i> {{ __('general.reset_filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
              <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('general.safe_rental_tips') }}</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('general.check_item_before_use') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('general.make_clear_agreements') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('general.pay_only_when_collecting') }}</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('general.ask_about_deposit_terms') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            @if(count($rentals) > 0)
                <div class="row">
                    @foreach($rentals as $rental)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">                                <div class="position-relative">
                                    @if(!empty($rental->images))
                                        <img src="{{ $rental->getFirstImageUrl() }}" class="card-img-top" alt="{{ $rental->title }}" style="height: 180px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-center py-5" style="height: 180px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-info position-absolute top-0 start-0 m-2">{{ __('general.rentals') }}</span>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate">{{ $rental->title }}</h5>
                                      <div class="mb-2">
                                        <span class="h5">€ {{ number_format($rental->rental_price_day, 2, ',', '.') }}</span>
                                        <span class="text-muted">/{{ __('general.day') }}</span>
                                    </div>
                                    
                                    <p class="card-text flex-grow-1" style="height: 50px; overflow: hidden;">
                                        {{ Str::limit($rental->description, 100) }}
                                    </p>
                                      <div class="d-flex justify-content-between align-items-center mt-2">                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $rental->location ?: __('general.unknown') }}
                                        </small>
                                        
                                        <small class="text-muted">
                                            {{ $rental->created_at->format('d-m-Y') }}
                                        </small>
                                    </div>
                                      <div class="d-flex justify-content-between align-items-center mt-2">
                                        <a href="{{ route('advertisers.show', $rental->user) }}" class="text-decoration-none">
                                            <small><i class="bi bi-person"></i> {{ $rental->user->name }}</small>
                                        </a>
                                          @auth
                                            <form action="{{ route('favorites.toggle', $rental) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $rental->isFavoritedBy(Auth::user()) ? 'btn-danger' : 'btn-outline-danger' }}"
                                                    title="{{ $rental->isFavoritedBy(Auth::user()) ? __('general.remove_from_favorites') : __('general.add_to_favorites') }}">
                                                    <i class="bi {{ $rental->isFavoritedBy(Auth::user()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                    
                                    <a href="{{ route('advertisements.show', $rental) }}" class="btn btn-outline-primary mt-3">{{ __('general.view') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $rentals->links() }}
                </div>            @else
                <div class="alert alert-info">
                    <h4 class="alert-heading">{{ __('general.no_rentals_found') }}</h4>
                    <p>{{ __('general.no_rentals_matching_criteria') }}</p>
                    
                    <hr>
                      <p class="mb-0">
                        {{ __('general.have_something_to_rent') }} 
                        @if(Auth::check() && (Auth::user()->user_type === 'particulier' || Auth::user()->user_type === 'zakelijk'))
                            <a href="{{ route('rentals.create') }}" class="alert-link">{{ __('general.create_rental_now') }}</a>.
                        @else
                            {{ __('general.only_private_business_can_post') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
