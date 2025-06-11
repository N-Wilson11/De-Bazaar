@extends('layouts.app')

@section('title', __('general.reviews') . ' - ' . $advertisement->title)

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">{{ __('general.reviews') }} - {{ $advertisement->title }}</h1>
            @auth
                @if($advertisement->canBeReviewedBy(Auth::user()) && !$advertisement->hasBeenReviewedBy(Auth::user()))
                    <a href="{{ route('reviews.create', $advertisement) }}" class="btn btn-primary">
                        <i class="bi bi-star me-1"></i>{{ __('general.write_review') }}
                    </a>
                @endif
            @endauth
        </div>
        <div class="card-body">
            @if($reviews->count() > 0)
                <div class="rating-summary mb-4">
                    <div class="d-flex align-items-center">
                        <div class="h1 mb-0 me-3">{{ number_format($advertisement->average_rating, 1) }}</div>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($advertisement->average_rating))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-muted"></i>
                                @endif
                            @endfor
                            <div class="text-muted">
                                {{ __('general.based_on_reviews', ['count' => $reviews->total()]) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="reviews-list">
                    @foreach($reviews as $review)
                        <div class="review-item border-bottom pb-4 mb-4">
                            <div class="d-flex justify-content-between">
                                <div class="review-header mb-2">
                                    <strong>{{ $review->user->name }}</strong>
                                    <div class="small text-muted">
                                        {{ $review->created_at->format('d-m-Y H:i') }}
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
                            <div class="review-content">
                                {{ $review->comment }}
                            </div>
                            @auth
                                @if($review->user_id === Auth::id())
                                    <div class="mt-3">
                                        <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>{{ __('general.edit') }}
                                        </a>
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                                <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-star h1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">{{ __('general.no_reviews') }}</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Terug naar advertentie') }}
        </a>
    </div>
</div>
@endsection
