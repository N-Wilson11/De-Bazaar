@extends('layouts.app')

@section('title', __('Landing Page Settings'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('Landing Page Settings') }}</h1>
                    <p>{{ __('Configure your company landing page and custom URL.') }}</p>
                    
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
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">                    <form action="{{ route('landing.update') }}" method="POST">
                        @csrf
                          <div class="mb-4">
                            <label for="landing_url" class="form-label">{{ __('Custom URL') }} *</label>                            <p class="text-muted mb-2"><small>Het voorvoegsel "/bedrijf/" wordt automatisch toegevoegd aan je URL.</small></p>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/bedrijf/') }}</span>
                                <input type="text" name="landing_url" id="landing_url" 
                                    class="form-control @error('landing_url') is-invalid @enderror"
                                    value="{{ old('landing_url', $company->landing_url) }}" 
                                    required
                                    pattern="[a-z0-9-]+"
                                    title="{{ __('Only lowercase letters, numbers, and hyphens are allowed') }}">
                            </div>
                            <small class="text-muted">{{ __('This will be your custom URL. Use only lowercase letters, numbers, and hyphens.') }}</small>
                            @error('landing_url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>                          <div class="mb-4">
                            <label for="landing_content" class="form-label">{{ __('Landing Page Content') }}</label>
                            <textarea name="landing_content" id="landing_content" rows="15" 
                                class="form-control @error('landing_content') is-invalid @enderror">{{ old('landing_content', $company->landing_content) }}</textarea>
                            <div class="alert alert-info mt-2">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>{{ __('Aangepaste content:') }}</strong> {{ __('Voer hier je eigen HTML-code in voor je landingspagina. Je kunt volledige controle hebben over de weergave.') }}
                                <hr>
                                <p><strong>{{ __('Werking:') }}</strong></p>
                                <ul>
                                    <li>{{ __('Als je dit veld leeg laat, wordt de standaard bedrijfspagina met producten getoond.') }}</li>
                                    <li>{{ __('Als je dit veld invult, wordt alleen jouw eigen HTML-inhoud getoond.') }}</li>
                                    <li>{{ __('Je kunt volledige HTML gebruiken, inclusief CSS-stijlen.') }}</li>
                                </ul>
                                <p><strong>{{ __('Producten tonen:') }}</strong> {{ __('Als je ook producten wilt tonen onder je eigen content, voeg dan deze code toe aan je content:') }}<br>
                                <code>&lt;!-- SHOW_PRODUCTS --&gt;</code>
                                </p>
                            </div>                            <small class="text-muted">{{ __('HTML is supported. This content will be displayed on your landing page.') }}</small>
                            
                            <!-- Example code collapsible section -->
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#exampleCode">
                                    <i class="bi bi-code-slash me-1"></i>{{ __('Toon HTML voorbeeldcode') }}
                                </button>
                                <div class="collapse mt-2" id="exampleCode">
                                    <div class="card card-body">
                                        <h6>{{ __('Eenvoudig HTML voorbeeld:') }}</h6>
                                        <pre class="bg-light p-2" style="white-space: pre-wrap;">
&lt;div style="text-align: center; padding: 30px 0;"&gt;
  &lt;h1 style="color: #3498db;"&gt;Welkom bij [Bedrijfsnaam]&lt;/h1&gt;
  &lt;p style="font-size: 18px;"&gt;Wij zijn gespecialiseerd in [dienst/product].&lt;/p&gt;
  
  &lt;div style="margin: 30px 0;"&gt;
    &lt;img src="https://via.placeholder.com/800x400" alt="Hoofdafbeelding" style="max-width: 100%; border-radius: 10px;"&gt;
  &lt;/div&gt;
  
  &lt;h2&gt;Onze diensten:&lt;/h2&gt;
  &lt;div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; flex-wrap: wrap;"&gt;
    &lt;div style="flex: 1; min-width: 250px; padding: 20px; border: 1px solid #eee; border-radius: 10px;"&gt;
      &lt;h3&gt;Service 1&lt;/h3&gt;
      &lt;p&gt;Beschrijving van service 1.&lt;/p&gt;
    &lt;/div&gt;
    &lt;div style="flex: 1; min-width: 250px; padding: 20px; border: 1px solid #eee; border-radius: 10px;"&gt;
      &lt;h3&gt;Service 2&lt;/h3&gt;
      &lt;p&gt;Beschrijving van service 2.&lt;/p&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  
  &lt;div style="margin-top: 40px;"&gt;
    &lt;h2&gt;Onze producten:&lt;/h2&gt;
    &lt;p&gt;Bekijk hieronder onze producten.&lt;/p&gt;
  &lt;/div&gt;
  
  &lt;!-- SHOW_PRODUCTS --&gt;
&lt;/div&gt;</pre>
                                    </div>
                                </div>
                            </div>
                            
                            @error('landing_content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>{{ __('Save Settings') }}
                            </button>
                            
                            @if($company->landing_url)
                                <a href="{{ route('company.landing', $company->landing_url) }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('View Landing Page') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('QR Code for Your Landing Page') }}</h5>
                    <p>{{ __('Use this QR code to share your landing page.') }}</p>
                    
                    @if($company->landing_url)
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('company.landing', $company->landing_url)) }}" 
                                    alt="QR Code" class="img-fluid mb-3">
                                
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode(route('company.landing', $company->landing_url)) }}&download=1" 
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download me-1"></i>{{ __('Download QR Code') }}
                                </a>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Landing Page URL') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ route('company.landing', $company->landing_url) }}" readonly>
                                        <button class="btn btn-outline-secondary copy-btn" type="button" data-clipboard-text="{{ route('company.landing', $company->landing_url) }}">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <p class="mb-0">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    {{ __('Share this link or QR code with your customers to direct them to your company landing page.') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('Save your landing page settings first to generate a QR code.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize clipboard.js
    var clipboard = new ClipboardJS('.copy-btn');
    
    clipboard.on('success', function(e) {
        e.trigger.innerHTML = '<i class="bi bi-check"></i>';
        setTimeout(function() {
            e.trigger.innerHTML = '<i class="bi bi-clipboard"></i>';
        }, 2000);
        e.clearSelection();
    });
});
</script>
@endpush
@endsection
