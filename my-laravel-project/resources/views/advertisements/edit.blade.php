@extends('layouts.app')

@section('title', __('Advertentie Bewerken'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Advertentie Bewerken') }}</h5>
                </div>

                <div class="card-body">
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
                                <label for="price" class="form-label fw-semibold">{{ __('Prijs') }} (€) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input id="price" type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $advertisement->price) }}" required>
                                </div>
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

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                                <select id="status" class="form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="active" {{ old('status', $advertisement->status) == 'active' ? 'selected' : '' }}>{{ __('Actief') }}</option>
                                    <option value="inactive" {{ old('status', $advertisement->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactief') }}</option>
                                    <option value="sold" {{ old('status', $advertisement->status) == 'sold' ? 'selected' : '' }}>{{ __('Verkocht') }}</option>
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
                                @enderror                        </div>
                        </div>
                          @php
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

                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary bg-opacity-10">
                                <h5 class="mb-0 text-primary">{{ __('Biedingen') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_accepting_bids" name="is_accepting_bids" value="1" {{ old('is_accepting_bids', $advertisement->is_accepting_bids) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_accepting_bids">{{ __('Biedingen accepteren') }}</label>
                                </div>
                                
                                <div class="row mb-3" id="bid_settings" style="{{ old('is_accepting_bids', $advertisement->is_accepting_bids) ? '' : 'display: none;' }}">
                                    <div class="col-md-6">
                                        <label for="min_bid_amount" class="form-label fw-semibold">{{ __('Minimum bod bedrag') }} (€)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">€</span>
                                            <input id="min_bid_amount" type="number" step="0.01" min="0" class="form-control @error('min_bid_amount') is-invalid @enderror" name="min_bid_amount" value="{{ old('min_bid_amount', $advertisement->min_bid_amount ?? $advertisement->price) }}">
                                        </div>
                                        <div class="form-text">{{ __('Laat leeg om de advertentieprijs als minimum bod te gebruiken.') }}</div>
                                        @error('min_bid_amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <i class="bi bi-info-circle fs-5 me-2"></i>
                                        <div>
                                            <strong>{{ __('Over biedingen') }}</strong>
                                            <ul class="mb-0 mt-1">
                                                <li>{{ __('Gebruikers kunnen bieden op je advertentie.') }}</li>
                                                <li>{{ __('Je kunt biedingen accepteren of afwijzen.') }}</li>
                                                <li>{{ __('Als je een bod accepteert, wordt de advertentie automatisch gereserveerd.') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-muted mb-3">
                            <small>{{ __('Velden met') }} <span class="text-danger">*</span> {{ __('zijn verplicht') }}</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('Terug') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Opslaan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isAcceptingBidsToggle = document.getElementById('is_accepting_bids');
        const bidSettingsDiv = document.getElementById('bid_settings');
        
        isAcceptingBidsToggle.addEventListener('change', function() {
            bidSettingsDiv.style.display = isAcceptingBidsToggle.checked ? 'block' : 'none';
        });
        
        // Voorbeeld functionaliteit voor afbeelding verwijderen
        const removeImgBtns = document.querySelectorAll('.remove-img-btn');
        removeImgBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const imgPath = this.getAttribute('data-img-path');
                document.getElementById('remove_images').value += imgPath + ',';
                this.closest('.image-preview-container').remove();
            });
        });
        
        // Image preview voor nieuwe uploads
        const imagesInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview');
        
        if (imagesInput) {
            imagesInput.addEventListener('change', function() {
                previewContainer.innerHTML = '';
                
                const files = this.files;
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.match('image.*')) {
                        continue;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'col-md-3 mb-2';
                        previewItem.innerHTML = `
                            <div class="card">
                                <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2 text-center">
                                    <small class="text-muted">Nieuwe foto</small>
                                </div>
                            </div>
                        `;
                        previewContainer.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
