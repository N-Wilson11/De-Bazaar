@extends('layouts.app')

@section('title', $user->name . ' - ' . __('general.advertiser_profile'))

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Linker kolom: Profiel informatie -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h2 class="h5 mb-0">{{ $user->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="h4 mb-0 me-2">{{ number_format($averageRating ?? 0, 1) }}</div>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($averageRating && $i <= round($averageRating))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-muted"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="small text-muted">
                            @if($reviewCount)
                                {{ __('general.based_on_reviews', ['count' => $reviewCount]) }}
                            @else
                                {{ __('general.no_reviews') }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">{{ __('general.advertiser_since') }}</div>
                        <div>{{ $user->created_at->format('d-m-Y') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">{{ __('general.advertiser_type') }}</div>
                        <div>
                            @if($user->user_type === 'zakelijk')
                                {{ __('general.business_user') }}
                                @if($user->company)
                                    - {{ $user->company->name }}
                                @endif
                            @elseif($user->user_type === 'particulier')
                                {{ __('general.private_user') }}
                            @else
                                {{ __('general.normal_user') }}
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $user->email }}?subject=Vraag over uw advertentie" class="btn btn-outline-primary">
                            <i class="bi bi-envelope me-1"></i>{{ __('general.contact_advertiser') }}
                        </a>
                        
                        @auth
                            @if(Auth::id() !== $user->id)
                                <a href="{{ route('advertiser.reviews.create', $user) }}" class="btn btn-outline-success">
                                    <i class="bi bi-star me-1"></i>{{ __('general.review_advertiser') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rechter kolom: Advertenties en beoordelingen -->
        <div class="col-md-8">
            <!-- Beoordelingen -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="h5 mb-0">{{ __('general.advertiser_reviews') }}</h3>
                    <a href="{{ route('advertiser.reviews.index', $user) }}" class="btn btn-sm btn-outline-primary">
                        {{ __('general.view_all_reviews') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($reviews->count() > 0)
                        <div class="reviews-list">
                            @foreach($reviews as $review)
                                <div class="review-item {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                                    <div class="d-flex justify-content-between">
                                        <div class="review-header mb-2">
                                            <strong>{{ $review->user->name }}</strong>
                                            <div class="small text-muted">
                                                {{ $review->created_at->format('d-m-Y') }}
                                            </div>
                                        </div>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                @else
                                                    <i class="bi bi-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->title)
                                        <div class="review-title fw-bold mb-1">{{ $review->title }}</div>
                                    @endif
                                    <div class="review-content">
                                        {{ $review->comment }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-star h2 d-block mb-3 text-muted"></i>
                            <p class="text-muted">{{ __('general.no_reviews') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Advertenties -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">{{ __('general.other_ads_by') }} {{ $user->name }}</h3>
                </div>
                <div class="card-body">
                    @if($advertisements->count() > 0)
                        <div class="row">
                            @foreach($advertisements as $advertisement)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <img src="{{ $advertisement->getFirstImageUrl() }}" class="card-img-top" alt="{{ $advertisement->title }}" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $advertisement->title }}</h5>
                                            <p class="card-text">{{ Str::limit($advertisement->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">€{{ number_format($advertisement->price, 2, ',', '.') }}</span>
                                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-primary">{{ __('general.view') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $advertisements->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-shop h2 d-block mb-3 text-muted"></i>
                            <p class="text-muted">{{ __('general.no_advertisements') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('general.back') }}
        </a>
    </div>
</div>
@endsection
