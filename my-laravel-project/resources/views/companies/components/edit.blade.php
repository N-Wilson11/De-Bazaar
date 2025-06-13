@extends('layouts.app')

@section('title', 'Component bewerken')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $componentTypes[$component->type] ?? 'Component' }} bewerken</h1>
        <a href="{{ route('components.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Terug naar componenten
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-light">Component gegevens</div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('components.update', $component) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" {{ $component->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Component actief</label>
                </div>
                
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sorteervolgorde</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $component->sort_order) }}">
                    <div class="form-text">Componenten met een lagere waarde worden eerder weergegeven.</div>
                </div>

                @switch($component->type)
                    @case('hero')
                        <div class="mb-3">
                            <label for="content" class="form-label">Hero content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $component->content) }}</textarea>
                        </div>
                        
                        @if(isset($component->settings['image_path']))
                        <div class="mb-3">
                            <label class="form-label">Huidige afbeelding</label>
                            <div>
                                <img src="{{ asset($component->settings['image_path']) }}" class="img-fluid rounded" style="max-height: 200px;" alt="Hero afbeelding">
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Hero afbeelding {{ isset($component->settings['image_path']) ? '(laat leeg om huidige te behouden)' : '' }}</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        
                        <div class="mb-3">
                            <label for="settings_button_text" class="form-label">Knop tekst</label>
                            <input type="text" class="form-control" id="settings_button_text" name="settings[button_text]" value="{{ old('settings.button_text', $component->settings['button_text'] ?? '') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="settings_button_url" class="form-label">Knop URL</label>
                            <input type="text" class="form-control" id="settings_button_url" name="settings[button_url]" value="{{ old('settings.button_url', $component->settings['button_url'] ?? '') }}">
                        </div>
                        @break

                    @case('text')
                        <div class="mb-3">
                            <label for="content" class="form-label">Tekst inhoud</label>
                            <textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content', $component->content) }}</textarea>
                            <div class="form-text">U kunt HTML gebruiken voor opmaak.</div>
                        </div>
                        @break

                    @case('image')
                        @if(isset($component->settings['image_path']))
                        <div class="mb-3">
                            <label class="form-label">Huidige afbeelding</label>
                            <div>
                                <img src="{{ asset($component->settings['image_path']) }}" class="img-fluid rounded" style="max-height: 200px;" alt="{{ $component->settings['alt_text'] ?? 'Afbeelding' }}">
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Afbeelding {{ isset($component->settings['image_path']) ? '(laat leeg om huidige te behouden)' : '' }}</label>
                            <input type="file" class="form-control" id="image" name="image" {{ !isset($component->settings['image_path']) ? 'required' : '' }}>
                        </div>

                        <div class="mb-3">
                            <label for="settings_alt_text" class="form-label">Alt tekst</label>
                            <input type="text" class="form-control" id="settings_alt_text" name="settings[alt_text]" value="{{ old('settings.alt_text', $component->settings['alt_text'] ?? '') }}">
                            <div class="form-text">Beschrijving van de afbeelding voor toegankelijkheid.</div>
                        </div>
                        @break

                    @case('featured_ads')
                        <div class="mb-3">
                            <label for="settings_count" class="form-label">Aantal advertenties</label>
                            <input type="number" class="form-control" id="settings_count" name="settings[count]" value="{{ old('settings.count', $component->settings['count'] ?? 4) }}" min="1" max="8">
                        </div>
                        
                        <div class="mb-3">
                            <label for="settings_category" class="form-label">Categorie filter (optioneel)</label>
                            <input type="text" class="form-control" id="settings_category" name="settings[category]" value="{{ old('settings.category', $component->settings['category'] ?? '') }}">
                            <div class="form-text">Laat leeg om advertenties uit alle categorieën te tonen.</div>
                        </div>
                        @break

                    @case('product_grid')
                        <div class="mb-3">
                            <label for="settings_count" class="form-label">Aantal producten</label>
                            <input type="number" class="form-control" id="settings_count" name="settings[count]" value="{{ old('settings.count', $component->settings['count'] ?? 8) }}" min="1" max="12">
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="settings_is_rental" name="settings[is_rental]" value="1" {{ old('settings.is_rental', $component->settings['is_rental'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="settings_is_rental">Toon alleen verhuur</label>
                        </div>
                        @break

                    @case('cta')
                        <div class="mb-3">
                            <label for="content" class="form-label">CTA tekst</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required>{{ old('content', $component->content) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="settings_button_text" class="form-label">Knop tekst</label>
                            <input type="text" class="form-control" id="settings_button_text" name="settings[button_text]" value="{{ old('settings.button_text', $component->settings['button_text'] ?? 'Meer informatie') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="settings_button_url" class="form-label">Knop URL</label>
                            <input type="text" class="form-control" id="settings_button_url" name="settings[button_url]" value="{{ old('settings.button_url', $component->settings['button_url'] ?? '') }}" required>
                        </div>
                        @break

                    @case('testimonials')
                        <div class="mb-3">
                            <label for="settings_count" class="form-label">Aantal beoordelingen</label>
                            <input type="number" class="form-control" id="settings_count" name="settings[count]" value="{{ old('settings.count', $component->settings['count'] ?? 3) }}" min="1" max="5">
                        </div>
                        @break

                    @default
                        <div class="alert alert-warning">
                            Geen instellingen beschikbaar voor dit componenttype.
                        </div>
                @endswitch

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Component bijwerken</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check of er een content veld is dat rich text editing nodig heeft
        const contentField = document.getElementById('content');
        if (contentField && ['hero', 'text', 'cta'].includes('{{ $component->type }}')) {
            // Als CKEditor beschikbaar is, gebruik het
            if (typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(contentField)
                    .catch(error => {
                        console.error(error);
                    });
            }
        }
    });
</script>
@endsection
