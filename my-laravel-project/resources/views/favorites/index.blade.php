@extends('layouts.app')

@section('title', __('general.my_favorites'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">{{ __('general.my_favorites') }}</h1>
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

                    <p>{{ __('general.favorites_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if($favorites->count() > 0)
            @foreach($favorites as $advertisement)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            @php 
                                $imageUrl = $advertisement->getFirstImageUrl() ?? asset('images/no-image.png');
                            @endphp
                            <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $advertisement->title }}" style="height: 200px; object-fit: cover;">
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                <form action="{{ route('favorites.destroy', $advertisement) }}" method="POST">
                                    @csrf
                                    @method('DELETE')                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('general.remove_from_favorites') }}">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>
                            </div>
                              @if($advertisement->isRental())
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-info text-dark">{{ __('general.rentals') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate">{{ $advertisement->title }}</h5>
                            <p class="card-text text-primary fw-bold">€ {{ number_format($advertisement->price, 2, ',', '.') }}</p>
                            <p class="card-text flex-grow-1" style="height: 50px; overflow: hidden;">
                                {{ Str::limit($advertisement->description, 100) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-2">                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ $advertisement->location ?: __('general.unknown') }}
                                </small>
                                
                                <small class="text-muted">
                                    {{ $advertisement->created_at->format('d-m-Y') }}
                                </small>
                            </div>
                            
                            <div class="mt-2">
                                <a href="{{ route('advertisers.show', $advertisement->user) }}" class="text-decoration-none">
                                    <small><i class="bi bi-person"></i> {{ $advertisement->user->name }}</small>
                                </a>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i>{{ __('general.view') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-md-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-heart h1 d-block mb-3"></i>                    <h4>{{ __('general.no_favorites') }}</h4>
                    <p>{{ __('general.no_favorites_description') }}</p>
                    <a href="{{ route('advertisements.browse') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-search me-1"></i>{{ __('general.browse_advertisements') }}
                    </a>
                </div>
            </div>
        @endif
    </div>    <div class="d-flex justify-content-center mt-3">
        {{ $favorites->links() }}
    </div>
</div>
@endsection
