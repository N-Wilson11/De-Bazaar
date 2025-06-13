@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="container py-4">
    @if(isset($components) && $components->count() > 0)
        <!-- Component-based landing page - Only show components -->
        @foreach($components as $component)
            <div class="component mb-5" id="component-{{ $component->id }}">                @switch($component->type)
                        
                    @case('text')
                        <div class="text-component">
                            {!! $component->content !!}
                        </div>
                        @break
                        
                    @case('image')
                        <div class="image-component text-center">
                            @if(isset($component->settings['image_path']))
                            <img src="{{ asset($component->settings['image_path']) }}" 
                                alt="{{ $component->settings['alt_text'] ?? 'Company image' }}" 
                                class="img-fluid rounded">
                            @endif
                        </div>
                        @break
                        
                    @case('featured_ads')
                        <div class="featured-ads-component">
                            <h3 class="mb-4">Uitgelichte advertenties</h3>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                                @php 
                                    $count = $component->settings['count'] ?? 4;
                                    $category = $component->settings['category'] ?? null;
                                    
                                    $featuredAds = \App\Models\Advertisement::whereHas('user', function ($query) use ($company) {
                                        $query->where('company_id', $company->id);
                                    })
                                    ->where('status', 'active');
                                    
                                    if ($category) {
                                        $featuredAds->where('category', $category);
                                    }
                                    
                                    $featuredAds = $featuredAds->orderBy('created_at', 'desc')
                                        ->take($count)
                                        ->get();
                                @endphp
                                
                                @forelse($featuredAds as $ad)
                                <div class="col">
                                    <div class="card h-100">
                                        <div style="height: 150px; overflow: hidden;">
                                            @if(!empty($ad->images))
                                                <img src="{{ $ad->getFirstImageUrl() }}" class="card-img-top" 
                                                    style="object-fit: cover; height: 100%; width: 100%;" alt="{{ $ad->title }}">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ Str::limit($ad->title, 40) }}</h5>
                                            <p class="card-text fw-bold">€ {{ number_format($ad->price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <a href="{{ route('advertisements.show', $ad) }}" class="btn btn-sm btn-outline-primary">Bekijk details</a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        Er zijn momenteel geen uitgelichte advertenties beschikbaar.
                                    </div>
                                </div>
                                @endforelse
                            </div>                        </div>                        @break
                        
                    @default
                        <!-- Onbekend component type -->
                @endswitch
            </div>
        @endforeach
        
    @else
        <!-- Default Content (only shown when no components are added) -->
        <div class="text-center py-4">
            <h1 class="display-4 fw-bold">{{ $company->name }}</h1>
            <p class="lead">{{ $company->description }}</p>
            
            <div class="mt-4">
                @if($company->website)
                <a href="{{ $company->website }}" class="btn btn-outline-primary" target="_blank">
                    <i class="bi bi-globe me-1"></i>{{ __('Visit Website') }}
                </a>
                @endif
                
                <a href="{{ route('advertisements.browse') }}?company={{ $company->id }}" class="btn btn-primary ms-2">
                    <i class="bi bi-shop me-1"></i>{{ __('Browse All Products') }}
                </a>
            </div>
            
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                {{ __('Voeg componenten toe aan je landingspagina om deze aan te passen. Ga naar de landingspagina-instellingen om te beginnen.') }}
            </div>
        </div>
    @endif
</div>
@endsection
