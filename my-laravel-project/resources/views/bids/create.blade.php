@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1>{{ __('Bod plaatsen') }}</h1>
            <p class="text-muted">{{ __('Je hebt momenteel :count van de maximaal 4 actieve biedingen', ['count' => $activeBidsCount]) }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Advertentie: :title', ['title' => $advertisement->title]) }}</div>

                <div class="card-body">
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 me-3">
                            <img src="{{ $advertisement->getFirstImageUrl() }}" alt="{{ $advertisement->title }}" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                        <div>
                            <h5>{{ $advertisement->title }}</h5>
                            <p><strong>{{ __('Vraagprijs:') }}</strong> &euro; {{ number_format($advertisement->price, 2) }}</p>
                            @if($highestBid)
                                <p><strong>{{ __('Hoogste bod:') }}</strong> &euro; {{ number_format($highestBid->amount, 2) }}</p>
                            @endif
                            <p><strong>{{ __('Minimumbod:') }}</strong> &euro; {{ number_format($minBidAmount, 2) }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('bids.store', $advertisement) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('Jouw bod (in euro)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">&euro;</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount', $minBidAmount) }}" 
                                       step="0.01" min="{{ $minBidAmount }}" required>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                {{ __('Je minimumbod moet ten minste :amount euro zijn.', ['amount' => number_format($minBidAmount, 2)]) }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">{{ __('Bericht aan verkoper (optioneel)') }}</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="3">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('Je kunt hier optioneel een bericht voor de verkoper achterlaten.') }}
                            </small>
                        </div>

                        <div class="form-group mb-4">
                            <div class="alert alert-info">
                                <h5>{{ __('Voorwaarden voor biedingen') }}</h5>
                                <ul class="mb-0">
                                    <li>{{ __('Je kunt maximaal 4 actieve biedingen tegelijk hebben.') }}</li>
                                    <li>{{ __('Een bod kan niet worden ingetrokken nadat het door de verkoper is geaccepteerd.') }}</li>
                                    <li>{{ __('Biedingen zijn 7 dagen geldig, tenzij ze eerder worden geaccepteerd of afgewezen.') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Plaats bod') }}
                            </button>
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-outline-secondary">
                                {{ __('Annuleren') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Jouw biedingen') }}</div>
                <div class="card-body">
                    <p>{{ __('Je hebt momenteel :count van de maximaal 4 actieve biedingen', ['count' => $activeBidsCount]) }}</p>
                    <a href="{{ route('bids.index') }}" class="btn btn-outline-primary">
                        {{ __('Bekijk al je biedingen') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
