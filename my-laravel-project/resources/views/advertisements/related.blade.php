@extends('layouts.app')

@section('title', __('Gerelateerde advertenties beheren'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('Gerelateerde advertenties beheren') }}</h1>
                    <p>{{ __('Koppel andere advertenties aan ') }} <strong>"{{ $advertisement->title }}"</strong> {{ __('zodat kopers aanvullende producten kunnen vinden.') }}</p>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('advertisements.index') }}">{{ __('Mijn advertenties') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('advertisements.show', $advertisement) }}">{{ $advertisement->title }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Gerelateerde advertenties') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Huidige gerelateerde advertenties') }}</h5>
                    
                    @if($relatedAdvertisements->count() > 0)
                        <div class="list-group mb-3">
                            @foreach($relatedAdvertisements as $related)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        @if(!empty($related->images))
                                            <img src="{{ $related->getFirstImageUrl() }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $related->title }}">
                                        @else
                                            <div class="bg-light me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <h6 class="mb-0">{{ $related->title }}</h6>
                                            <small class="text-muted">€ {{ number_format($related->price, 2, ',', '.') }} - {{ $related->isRental() ? __('Verhuur') : __('Verkoop') }}</small>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <a href="{{ route('advertisements.show', $related) }}" class="btn btn-sm btn-outline-secondary me-1">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('advertisements.related.destroy', [$advertisement->id, $related->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Weet je zeker dat je deze koppeling wilt verwijderen?') }}')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('Er zijn nog geen advertenties gekoppeld.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Advertentie toevoegen') }}</h5>
                    
                    @if($userAdvertisements->count() > 0)
                        <form action="{{ route('advertisements.related.store', $advertisement) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="related_advertisement_id" class="form-label">{{ __('Selecteer een advertentie') }}</label>
                                <select name="related_advertisement_id" id="related_advertisement_id" class="form-select @error('related_advertisement_id') is-invalid @enderror" required>
                                    <option value="">{{ __('-- Selecteer een advertentie --') }}</option>
                                    @foreach($userAdvertisements as $userAd)
                                        <option value="{{ $userAd->id }}">
                                            {{ $userAd->title }} 
                                            (€ {{ number_format($userAd->price, 2, ',', '.') }}) 
                                            - {{ $userAd->isRental() ? __('Verhuur') : __('Verkoop') }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                @error('related_advertisement_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i>{{ __('Koppelen') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            {{ __('Je hebt geen andere actieve advertenties die je kunt koppelen.') }}
                        </div>
                        
                        <div class="d-grid">
                            <a href="{{ route('advertisements.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('Nieuwe advertentie maken') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Tips voor gerelateerde advertenties') }}</h5>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Koppel accessoires aan hoofdproducten') }}</li>
                        <li class="list-group-item ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Voeg verbruiksartikelen toe voor verhuurproducten') }}</li>
                        <li class="list-group-item ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ __('Combineer producten die vaak samen worden gebruikt') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
