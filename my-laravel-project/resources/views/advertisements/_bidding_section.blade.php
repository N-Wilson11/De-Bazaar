@if($advertisement->isAcceptingBids())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Biedingen') }}</h5>
        </div>
        <div class="card-body">
            @if(Auth::check() && $advertisement->user_id === Auth::id())
                <!-- Voor de verkoper -->
                <div class="mb-3">
                    <h6>{{ __('Biedingen beheren') }}</h6>
                    <p>{{ __('Je kunt biedingen op je advertentie bekijken en beheren.') }}</p>
                    <a href="{{ route('bids.for-advertisement', $advertisement) }}" class="btn btn-primary">
                        <i class="bi bi-list-ul me-1"></i>{{ __('Biedingen bekijken') }}
                    </a>
                </div>
            @else
                <!-- Voor potentiële kopers -->
                <div class="mb-3">
                    @if($highestBid)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>{{ __('Hoogste bod:') }}</span>
                            <span class="h5 mb-0 text-success">€ {{ number_format($highestBid->amount, 2, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>{{ __('Startbod:') }}</span>
                            <span class="h5 mb-0">€ {{ number_format($advertisement->min_bid_amount ?? $advertisement->price, 2, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($userBid)
                        <div class="alert alert-info">
                            <h6>{{ __('Je hebt een bod geplaatst') }}</h6>
                            <p>{{ __('Jouw bod:') }} <strong>€ {{ number_format($userBid->amount, 2, ',', '.') }}</strong></p>
                            <p class="mb-0 small">{{ __('Status:') }} 
                                @if($userBid->status === 'pending')
                                    <span class="badge bg-info">{{ __('In behandeling') }}</span>
                                @elseif($userBid->status === 'accepted')
                                    <span class="badge bg-success">{{ __('Geaccepteerd') }}</span>
                                @elseif($userBid->status === 'rejected')
                                    <span class="badge bg-danger">{{ __('Afgewezen') }}</span>
                                @endif
                            </p>
                            
                            @if($userBid->status === 'pending')
                                <div class="mt-2">
                                    <form action="{{ route('bids.cancel', $userBid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Weet je zeker dat je dit bod wilt annuleren?') }}')">
                                            {{ __('Bod annuleren') }}
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('bids.index') }}" class="btn btn-sm btn-outline-primary ms-1">
                                        {{ __('Al mijn biedingen') }}
                                    </a>
                                </div>
                            @elseif($userBid->status === 'accepted')
                                <div class="mt-2">
                                    <div class="alert alert-success mb-2">
                                        {{ __('Gefeliciteerd! Je bod is geaccepteerd.') }}
                                    </div>
                                    <form action="{{ route('cart.add', $advertisement) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-cart-plus me-1"></i>{{ __('Nu kopen voor € :price', ['price' => number_format($userBid->amount, 2, ',', '.')]) }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @elseif($canPlaceBid)
                        <div class="d-grid gap-2">
                            <a href="{{ route('bids.create', $advertisement) }}" class="btn btn-primary">
                                <i class="bi bi-cash-stack me-1"></i>{{ __('Bod plaatsen') }}
                            </a>
                            <div class="text-center mt-1">
                                <small class="text-muted">{{ __('Je hebt momenteel :count van de maximaal 4 actieve biedingen.', ['count' => $activeBidsCount]) }}</small>
                            </div>
                        </div>
                    @elseif(Auth::check() && $activeBidsCount >= 4)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            {{ __('Je hebt het maximum van 4 actieve biedingen bereikt. Je kunt pas een nieuw bod plaatsen als een van je huidige biedingen is verlopen, geaccepteerd of afgewezen.') }}
                            <div class="mt-2">
                                <a href="{{ route('bids.index') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Mijn biedingen beheren') }}
                                </a>
                            </div>
                        </div>
                    @elseif(!Auth::check())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ __('Je moet ingelogd zijn om te bieden.') }}
                            <div class="mt-2">
                                <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                                    {{ __('Inloggen') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="mb-0">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        {{ __('Door een bod te plaatsen ga je akkoord met de voorwaarden. Een geaccepteerd bod betekent dat je het product voor de geboden prijs koopt.') }}
                    </small>
                </div>
            @endif
        </div>
    </div>
@endif
