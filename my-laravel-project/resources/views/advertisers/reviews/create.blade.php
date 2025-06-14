@extends('layouts.app')

@section('title', __('general.review_advertiser') . ' - ' . $user->name)

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h1 class="h3 mb-0">{{ __('general.review_advertiser') }} - {{ $user->name }}</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('advertiser.reviews.store', $user) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="title" class="form-label fw-bold">{{ __('general.review_title') }}</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    <div class="form-text">{{ __('Geef een korte titel aan je beoordeling') }}</div>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="rating" class="form-label fw-bold">{{ __('general.rating') }}</label>
                    <div class="rating-input">
                        <div class="btn-group" role="group">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" class="btn-check" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : ($i == 5 ? 'checked' : '') }}>
                                <label class="btn btn-outline-warning" for="rating{{ $i }}">
                                    {{ $i }} <i class="bi bi-star-fill ms-1"></i>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="comment" class="form-label fw-bold">{{ __('general.review_experience') }}</label>
                    <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="5" required>{{ old('comment') }}</textarea>
                    <div class="form-text">{{ __('Minimaal 10 karakters, maximaal 1000 karakters') }}</div>
                    @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                    <a href="{{ route('advertisers.show', $user) }}" class="btn btn-outline-secondary">
                        {{ __('general.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-star me-1"></i>{{ __('general.submit_review') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script om de sterren te highlighten
        const ratingLabels = document.querySelectorAll('.rating-input label');
        const ratingInputs = document.querySelectorAll('.rating-input input');
        
        function updateStars() {
            ratingInputs.forEach((input, index) => {
                if (input.checked) {
                    for (let i = 0; i <= index; i++) {
                        ratingLabels[i].classList.add('active');
                    }
                    for (let i = index + 1; i < ratingLabels.length; i++) {
                        ratingLabels[i].classList.remove('active');
                    }
                }
            });
        }
        
        ratingLabels.forEach((label) => {
            label.addEventListener('click', updateStars);
        });
        
        // Initiële update
        updateStars();
    });
</script>
@endsection
