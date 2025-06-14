@extends('layouts.app')

@section('title', __('Verhuuradvertentie Bewerken'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Verhuuradvertentie Bewerken') }}</h5>
                </div>                <div class="card-body">
                    <!-- Huidige vervaldatum weergeven -->
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-calendar-event me-2"></i>
                        <strong>Vervaldatum:</strong> {{ $advertisement->expires_at ? $advertisement->expires_at->format('d-m-Y') : 'Geen vervaldatum ingesteld' }}
                        @if($advertisement->expires_at && $advertisement->expires_at->isPast())
                            <span class="badge bg-danger ms-2">Verlopen</span>
                        @elseif($advertisement->expires_at && floor($advertisement->expires_at->floatDiffInDays(now())) <= 7)
                            <span class="badge bg-warning text-dark ms-2">Verloopt binnenkort</span>
                        @endif
                    </div>
                      <!-- QR Code voor deze advertentie -->
                    <div class="alert alert-light border mb-4 text-center">
                        <h6><i class="bi bi-qr-code me-2"></i>QR-code voor deze advertentie</h6>
                        <div class="p-2">
                            <img src="{{ route('qrcode.advertisement', $advertisement) }}?size=120" alt="QR Code" class="img-fluid rounded border p-1 mb-2" style="max-width: 120px;">
                        </div>
                        <small class="text-muted d-block mb-2">Scan deze code om direct naar je advertentie te gaan</small>
                    </div>
                    
                    <form method="POST" action="{{ route('advertisements.update', $advertisement) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label fw-semibold">{{ __('Titel') }} <span class="text-danger">*</span></label>
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $advertisement->title) }}" required maxlength="100">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-semibold">{{ __('Categorie') }} <span class="text-danger">*</span></label>
                                <select id="category" class="form-select @error('category') is-invalid @enderror" name="category" required>
                                    <option value="">-- {{ __('Selecteer categorie') }} --</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}" {{ old('category', $advertisement->category) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="condition" class="form-label fw-semibold">{{ __('Conditie') }} <span class="text-danger">*</span></label>
                                <select id="condition" class="form-select @error('condition') is-invalid @enderror" name="condition" required>
                                    <option value="">-- {{ __('Selecteer conditie') }} --</option>
                                    @foreach($conditions as $value => $label)
                                        <option value="{{ $value }}" {{ old('condition', $advertisement->condition) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('condition')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">{{ __('Vervangingswaarde') }} (€) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input id="price" type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $advertisement->price) }}" required>
                                </div>
                                <div class="form-text">{{ __('De waarde van het item bij verlies of schade.') }}</div>
                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">{{ __('Locatie') }}</label>
                                <input id="location" type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location', $advertisement->location) }}" maxlength="100">
                                @error('location')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label fw-semibold">{{ __('Beschrijving') }} <span class="text-danger">*</span></label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="6" required maxlength="2000">{{ old('description', $advertisement->description) }}</textarea>
                                <div class="form-text">{{ __('Maximaal 2000 tekens.') }}</div>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <h5 class="mb-3">{{ __('Verhuurgegevens') }}</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="rental_price_day" class="form-label fw-semibold">{{ __('Prijs per dag') }} (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input id="rental_price_day" type="number" step="0.01" min="0" class="form-control @error('rental_price_day') is-invalid @enderror" name="rental_price_day" value="{{ old('rental_price_day', $advertisement->rental_price_day) }}">
                                </div>
                                @error('rental_price_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="rental_price_week" class="form-label fw-semibold">{{ __('Prijs per week') }} (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input id="rental_price_week" type="number" step="0.01" min="0" class="form-control @error('rental_price_week') is-invalid @enderror" name="rental_price_week" value="{{ old('rental_price_week', $advertisement->rental_price_week) }}">
                                </div>
                                @error('rental_price_week')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="rental_price_month" class="form-label fw-semibold">{{ __('Prijs per maand') }} (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input id="rental_price_month" type="number" step="0.01" min="0" class="form-control @error('rental_price_month') is-invalid @enderror" name="rental_price_month" value="{{ old('rental_price_month', $advertisement->rental_price_month) }}">
                                </div>
                                @error('rental_price_month')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="minimum_rental_days" class="form-label fw-semibold">{{ __('Minimale huurdagen') }}</label>
                                <input id="minimum_rental_days" type="number" min="1" class="form-control @error('minimum_rental_days') is-invalid @enderror" name="minimum_rental_days" value="{{ old('minimum_rental_days', $advertisement->minimum_rental_days) }}">
                                @error('minimum_rental_days')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="rental_pickup_location" class="form-label fw-semibold">{{ __('Ophaallocatie') }}</label>
                                <input id="rental_pickup_location" type="text" class="form-control @error('rental_pickup_location') is-invalid @enderror" name="rental_pickup_location" value="{{ old('rental_pickup_location', $advertisement->rental_pickup_location) }}">
                                @error('rental_pickup_location')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rental_requires_deposit" id="rental_requires_deposit" value="1" {{ old('rental_requires_deposit', $advertisement->rental_requires_deposit) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rental_requires_deposit">
                                        {{ __('Borg vereist') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3" id="depositAmountContainer" style="display: {{ $advertisement->rental_requires_deposit ? 'block' : 'none' }};">
                            <div class="col-md-6">
                                <label for="rental_deposit_amount" class="form-label fw-semibold">{{ __('Borg bedrag') }} (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input id="rental_deposit_amount" type="number" step="0.01" min="0" class="form-control @error('rental_deposit_amount') is-invalid @enderror" name="rental_deposit_amount" value="{{ old('rental_deposit_amount', $advertisement->rental_deposit_amount) }}">
                                </div>
                                @error('rental_deposit_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="rental_calculate_wear_and_tear" id="rental_calculate_wear_and_tear" value="1" {{ old('rental_calculate_wear_and_tear', $advertisement->rental_calculate_wear_and_tear) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rental_calculate_wear_and_tear">
                                                <strong>{{ __('Slijtageberekening inschakelen') }}</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body" id="wearAndTearSettingsContainer" style="display: {{ $advertisement->rental_calculate_wear_and_tear ? 'block' : 'none' }};">
                                        <p class="card-text text-muted">
                                            {{ __('Stel het percentage per dag in en vermenigvuldigers per conditie om slijtage te berekenen wanneer het product wordt teruggebracht.') }}
                                        </p>
                                        
                                        @php
                                            $settings = $advertisement->rental_wear_and_tear_settings ?? [
                                                'base_percentage' => 1.0,
                                                'condition_multipliers' => [
                                                    'excellent' => 0.0,
                                                    'good' => 0.5,
                                                    'fair' => 1.0,
                                                    'poor' => 2.0,
                                                ]
                                            ];
                                            $basePercentage = $settings['base_percentage'] ?? 1.0;
                                            $conditionMultipliers = $settings['condition_multipliers'] ?? [
                                                'excellent' => 0.0,
                                                'good' => 0.5,
                                                'fair' => 1.0,
                                                'poor' => 2.0,
                                            ];
                                        @endphp
                                        
                                        <div class="mb-3">
                                            <label for="base_percentage" class="form-label">{{ __('Basis percentage per dag') }}</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" min="0" max="10" class="form-control" id="base_percentage" name="base_percentage" value="{{ old('base_percentage', $basePercentage) }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="form-text">{{ __('Percentage van de borg dat per dag als slijtage wordt berekend') }}</div>
                                        </div>
                                        
                                        <p class="fw-semibold">{{ __('Vermenigvuldigers per conditie:') }}</p>
                                        <p class="text-muted small">{{ __('Deze waarden vermenigvuldigen het basisbedrag per conditie waarin het product wordt teruggebracht.') }}</p>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label for="condition_excellent" class="form-label">{{ __('Uitstekend') }}</label>
                                                <input type="number" step="0.1" min="0" max="5" class="form-control" id="condition_excellent" name="condition_excellent" value="{{ old('condition_excellent', $conditionMultipliers['excellent']) }}">
                                                <div class="form-text">{{ __('Perfect staat, geen slijtage') }}</div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="condition_good" class="form-label">{{ __('Goed') }}</label>
                                                <input type="number" step="0.1" min="0" max="5" class="form-control" id="condition_good" name="condition_good" value="{{ old('condition_good', $conditionMultipliers['good']) }}">
                                                <div class="form-text">{{ __('Goede staat, minimale slijtage') }}</div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="condition_fair" class="form-label">{{ __('Redelijk') }}</label>
                                                <input type="number" step="0.1" min="0" max="5" class="form-control" id="condition_fair" name="condition_fair" value="{{ old('condition_fair', $conditionMultipliers['fair']) }}">
                                                <div class="form-text">{{ __('Normale slijtage') }}</div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="condition_poor" class="form-label">{{ __('Slecht') }}</label>
                                                <input type="number" step="0.1" min="0" max="5" class="form-control" id="condition_poor" name="condition_poor" value="{{ old('condition_poor', $conditionMultipliers['poor']) }}">
                                                <div class="form-text">{{ __('Beschadigd/overmatige slijtage') }}</div>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info mt-3">
                                            <i class="bi bi-info-circle me-2"></i>
                                            {{ __('Voorbeeld:') }} {{ __('Bij een borg van €100, een basis percentage van 1% per dag, en een huurperiode van 10 dagen, zou een product dat in goede staat (0.5 vermenigvuldiger) wordt teruggebracht, een slijtage van €5 hebben.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                                <select id="status" class="form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="active" {{ old('status', $advertisement->status) == 'active' ? 'selected' : '' }}>{{ __('Actief') }}</option>
                                    <option value="inactive" {{ old('status', $advertisement->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactief') }}</option>
                                    <option value="rented" {{ old('status', $advertisement->status) == 'rented' ? 'selected' : '' }}>{{ __('Verhuurd') }}</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="images" class="form-label fw-semibold">{{ __('Nieuwe afbeeldingen toevoegen') }}</label>
                                <input id="images" type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" multiple accept="image/*">
                                <div class="form-text">{{ __('Je kunt maximaal 5 afbeeldingen uploaden. Ondersteunde formaten: JPG, PNG, GIF. Max 2MB per afbeelding.') }}</div>
                                @error('images')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @error('images.*')
                                    <span class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>                          @php
                            $images = is_array($advertisement->images) ? $advertisement->images : [];
                            $imageUrls = $advertisement->getAllImageUrls();
                        @endphp
                        @if(count($images) > 0)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">{{ __('Huidige afbeeldingen') }}</label>
                                    <div class="row">
                                        @foreach($images as $index => $image)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <img src="{{ $imageUrls[$index] ?? asset('images/no-image.png') }}" class="card-img-top" alt="Afbeelding {{ $index + 1 }}">
                                                    <div class="card-body text-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="remove_images[]" id="remove_image_{{ $index }}" value="{{ $image }}">
                                                            <label class="form-check-label" for="remove_image_{{ $index }}">
                                                                {{ __('Verwijderen') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-muted mb-3">
                            <small>{{ __('Velden met') }} <span class="text-danger">*</span> {{ __('zijn verplicht') }}</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('Terug') }}
                            </a>
                            <div>
                                <a href="{{ route('advertisements.calendar', $advertisement) }}" class="btn btn-info me-2">
                                    <i class="bi bi-calendar-event me-1"></i>{{ __('Beheer beschikbaarheid') }}
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('Opslaan') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const depositCheckbox = document.getElementById('rental_requires_deposit');
        const depositContainer = document.getElementById('depositAmountContainer');
        
        // Toon/verberg borg bedrag veld
        depositCheckbox.addEventListener('change', function() {
            depositContainer.style.display = this.checked ? 'block' : 'none';
        });
        
        const wearAndTearCheckbox = document.getElementById('rental_calculate_wear_and_tear');
        const wearAndTearContainer = document.getElementById('wearAndTearSettingsContainer');
        
        // Toon/verberg slijtageberekening instellingen
        wearAndTearCheckbox.addEventListener('change', function() {
            wearAndTearContainer.style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
@endsection
