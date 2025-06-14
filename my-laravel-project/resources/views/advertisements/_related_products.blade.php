@if($advertisement->relatedAdvertisements->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('Gerelateerde producten') }}</h5>
            <p class="text-muted small">{{ __('De aanbieder raadt deze producten aan bij deze advertentie:') }}</p>
            
            <div class="row row-cols-1 row-cols-md-3 g-3">
                @foreach($advertisement->relatedAdvertisements as $related)
                    <div class="col">
                        <div class="card h-100 border">
                            <div style="height: 150px; overflow: hidden;">
                                @if(!empty($related->images))
                                    <a href="{{ route('advertisements.show', $related) }}">
                                        <img src="{{ $related->getFirstImageUrl() }}" class="card-img-top" style="object-fit: cover; height: 100%; width: 100%;" alt="{{ $related->title }}">
                                    </a>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('advertisements.show', $related) }}" class="text-decoration-none text-dark">
                                        {{ Str::limit($related->title, 40) }}
                                    </a>
                                </h6>
                                
                                <p class="card-text">
                                    <span class="fw-bold">€ {{ number_format($related->price, 2, ',', '.') }}</span>
                                    
                                    @if($related->isRental())
                                        <span class="badge bg-info ms-1">{{ __('Verhuur') }}</span>
                                    @endif
                                </p>
                                
                                <div class="mt-2">
                                    @if($related->purchase_status === 'available' && auth()->check() && auth()->id() !== $related->user_id)                                        @if(!$related->isRental())
                                            <form action="{{ route('cart.add', $related) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-cart-plus"></i> {{ __('In winkelwagen') }}
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('rentals.rent', $related) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-calendar-plus"></i> {{ __('Huren') }}
                                            </a>
                                        @endif
                                    @elseif($related->purchase_status === 'reserved' || $related->purchase_status === 'sold')
                                        <span class="badge bg-secondary">
                                            {{ $related->purchase_status === 'reserved' ? __('Gereserveerd') : __('Verkocht') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
