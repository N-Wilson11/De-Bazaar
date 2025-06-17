@extends('layouts.app')

@section('title', __('Advertenties importeren'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('Advertenties importeren') }}</h1>
                    <p>{{ __('Upload een CSV-bestand om meerdere advertenties in één keer te importeren.') }}</p>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('import_success'))
                        <div class="alert alert-success">
                            {{ session('import_success') }}
                        </div>
                    @endif
                    
                    @if(session('import_stats'))
                        <div class="alert alert-info">
                            <strong>{{ __('Import resultaten:') }}</strong><br>
                            - {{ __('Succesvol:') }} {{ session('import_stats.success') }}<br>
                            - {{ __('Mislukt:') }} {{ session('import_stats.error') }}
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <a href="{{ route('advertisements.import.template') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i>{{ __('Downloadsjabloon') }}
                        </a>
                    </div>
                      <form action="{{ route('advertisements.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf                      
                          <div class="mb-3">
                            <label for="csv_file" class="form-label">{{ __('Selecteer CSV-bestand') }}</label>
                            <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file" name="csv_file" accept=".csv, text/csv" required>
                            
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($errors->any())
                                <div class="alert alert-danger mt-2">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="form-text">
                                {{ __('Maximum bestandsgrootte: 10MB. Alleen CSV-bestanden.') }}
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i>{{ __('Importeren') }}
                            </button>
                            <a href="{{ route('advertisements.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('Terug naar advertenties') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            @if(session('import_errors'))
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-danger">{{ __('Importfouten') }}</h5>
                        
                        <div class="accordion" id="importErrorsAccordion">
                            @foreach(session('import_errors') as $row => $errors)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ Str::slug($row) }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($row) }}" aria-expanded="false" aria-controls="collapse{{ Str::slug($row) }}">
                                            {{ $row }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ Str::slug($row) }}" class="accordion-collapse collapse" aria-labelledby="heading{{ Str::slug($row) }}" data-bs-parent="#importErrorsAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled mb-0">
                                                @foreach($errors as $error)
                                                    <li><i class="bi bi-exclamation-circle text-danger me-1"></i> {{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('CSV-formaat instructies') }}</h5>
                    
                    <p>{{ __('Volg deze instructies voor het maken van je CSV-bestand:') }}</p>
                    
                    <ol>
                        <li>{{ __('Download het sjabloonbestand hierboven als startpunt') }}</li>
                        <li>{{ __('De eerste rij moet de kolomnamen bevatten') }}</li>
                        <li>{{ __('Alle datums moeten in het formaat YYYY-MM-DD staan (bijv. 2025-06-18)') }}</li>
                        <li>{{ __('Voor booleaanse waarden gebruik 1 (waar/ja) of 0 (onwaar/nee)') }}</li>
                        <li>{{ __('Afbeeldings-URL\'s moeten worden gescheiden door komma\'s (zonder spaties)') }}</li>
                        <li>{{ __('Voor verhuurinstellingen moet is_rental op 1 staan') }}</li>
                        <li>{{ __('Voor biedingen moet is_accepting_bids op 1 staan') }}</li>
                    </ol>
                    
                    <h6 class="mt-3">{{ __('Verplichte velden:') }}</h6>
                    <ul>
                        <li>title - {{ __('De titel van de advertentie (max 255 tekens)') }}</li>
                        <li>description - {{ __('Uitgebreide beschrijving van het product') }}</li>
                        <li>price - {{ __('Prijs in euro\'s (punt als decimaal scheidingsteken)') }}</li>
                        <li>condition - {{ __('Staat van het product (nieuw, gebruikt, goed, redelijk, matig)') }}</li>
                        <li>category - {{ __('Categorie van het product (max 100 tekens)') }}</li>
                    </ul>
                    
                    <h6 class="mt-3">{{ __('Optionele velden:') }}</h6>
                    <ul>
                        <li>location - {{ __('Locatie van het product (max 100 tekens)') }}</li>
                        <li>images_urls - {{ __('Lijst van URL\'s naar afbeeldingen, gescheiden door komma\'s') }}</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        {{ __('Tip: Open het sjabloonbestand in Excel of Google Spreadsheets en vul je gegevens in.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
