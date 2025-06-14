@extends('layouts.app')

@section('title', __('Bedrijfsgegevens Bewerken'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">{{ __('Bedrijfsgegevens Bewerken') }}</h1>
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
                
                    <form method="POST" action="{{ route('companies.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h3>{{ __('Algemene Informatie') }}</h3>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Bedrijfsnaam') }} *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Zakelijk E-mailadres') }} *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('Telefoonnummer') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="website" class="form-label">{{ __('Website') }}</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website) }}" placeholder="https://www.voorbeeld.nl">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('Beschrijving') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $company->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h3>{{ __('Adresgegevens') }}</h3>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ __('Adres') }}</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $company->address) }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">{{ __('Postcode') }}</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $company->postal_code) }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="city" class="form-label">{{ __('Plaats') }}</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $company->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="country" class="form-label">{{ __('Land') }}</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $company->country ?? 'Nederland') }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <h3 class="mt-4">{{ __('Website Instellingen') }}</h3>
                                
                                <div class="mb-3">
                                    <label for="landing_url" class="form-label">{{ __('Unieke URL voor landingspagina') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ url('/company/') }}/</span>
                                        <input type="text" class="form-control @error('landing_url') is-invalid @enderror" id="landing_url" name="landing_url" value="{{ old('landing_url', $company->landing_url) }}" placeholder="mijn-bedrijf">
                                    </div>
                                    <small class="form-text text-muted">{{ __('Deze URL wordt gebruikt voor uw openbare landingspagina.') }}</small>
                                    @error('landing_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="slug" class="form-label">{{ __('SEO-vriendelijke URL') }}</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $company->slug) }}">
                                    <small class="form-text text-muted">{{ __('Laat leeg om automatisch te genereren op basis van bedrijfsnaam.') }}</small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">{{ __('Annuleren') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Opslaan') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
