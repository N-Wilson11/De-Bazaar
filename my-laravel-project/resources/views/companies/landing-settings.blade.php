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
            <div class="card shadow-sm">                <div class="card-body">
                    <!-- URL Settings Section -->
                    <form action="{{ route('landing.update') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-4">
                            <label for="landing_url" class="form-label">{{ __('Custom URL') }} *</label>
                            <p class="text-muted mb-2"><small>Het voorvoegsel "/bedrijf/" wordt automatisch toegevoegd aan je URL.</small></p>
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
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>{{ __('Save URL') }}
                            </button>
                            
                            @if($company->landing_url)
                                <a href="{{ route('company.landing', $company->landing_url) }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('View Landing Page') }}
                                </a>
                            @endif
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    <h4 class="mb-4">{{ __('Landing Page Components') }}</h4>
                    
                    <!-- Components Section -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        {{ __('Met componenten kunt u eenvoudig een professionele landingspagina maken zonder HTML-kennis.') }}
                                    </p>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="componentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-plus-lg"></i> Component toevoegen
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="componentDropdown">
                                            @foreach(App\Models\PageComponent::getComponentTypes() as $type => $name)
                                            <li><a class="dropdown-item" href="{{ route('components.create', $type) }}">{{ $name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                  <div class="alert alert-info">
                                    <strong>Tip:</strong> Sleep de componenten om de volgorde aan te passen.
                                    <p>Op uw landingspagina worden alleen de componenten getoond die u hierboven heeft toegevoegd. Als u geen componenten heeft toegevoegd, wordt een standaard pagina weergegeven.</p>
                                    <div class="mt-2">
                                        <a href="{{ route('company.landing', $company->landing_url) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye me-1"></i> Bekijk landingspagina
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="component-list" class="component-sortable">
                                @forelse($company->pageComponents as $component)
                                <div class="card mb-3 component-item" data-id="{{ $component->id }}">
                                    <div class="card-header {{ $component->is_active ? 'bg-light' : 'bg-secondary text-white' }} d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-grip-vertical me-2 handle" style="cursor: move;"></i>
                                            <span>
                                                {{ App\Models\PageComponent::getComponentTypes()[$component->type] ?? $component->type }}
                                                @if(!$component->is_active)
                                                <span class="badge bg-warning text-dark ms-2">Inactief</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ route('components.edit', $component) }}" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bi bi-pencil"></i> Bewerken
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('{{ $component->id }}', '{{ App\Models\PageComponent::getComponentTypes()[$component->type] ?? $component->type }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $component->id }}" action="{{ route('components.destroy', $component) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="component-preview">
                                            @switch($component->type)
                                                @case('hero')
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            @if(isset($component->settings['image_path']))
                                                            <img src="{{ asset($component->settings['image_path']) }}" class="img-fluid rounded" alt="Hero afbeelding">
                                                            @else
                                                            <div class="bg-light rounded p-4 text-center">
                                                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="small">{{ Str::limit(strip_tags($component->content), 150) }}</div>
                                                            @if(isset($component->settings['button_text']))
                                                            <div class="mt-2"><span class="badge bg-primary">{{ $component->settings['button_text'] }}</span></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @break
                                                    
                                                @case('text')
                                                    <div class="small">{{ Str::limit(strip_tags($component->content), 200) }}</div>
                                                    @break
                                                    
                                                @case('image')
                                                    @if(isset($component->settings['image_path']))
                                                    <div class="text-center">
                                                        <img src="{{ asset($component->settings['image_path']) }}" class="img-fluid rounded" style="max-height: 150px;" alt="{{ $component->settings['alt_text'] ?? 'Afbeelding' }}">
                                                    </div>
                                                    @else
                                                    <div class="bg-light rounded p-4 text-center">
                                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                    </div>
                                                    @endif
                                                    @break
                                                    
                                                @case('featured_ads')
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-tags me-2" style="font-size: 1.5rem;"></i>
                                                        <div>
                                                            <div>Toont {{ $component->settings['count'] ?? 4 }} uitgelichte advertenties</div>
                                                            @if(isset($component->settings['category']) && $component->settings['category'])
                                                            <div class="small text-muted">Categorie: {{ $component->settings['category'] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @break
                                                    
                                                @case('product_grid')
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-grid-3x3-gap me-2" style="font-size: 1.5rem;"></i>
                                                        <div>
                                                            <div>Toont {{ $component->settings['count'] ?? 8 }} producten in raster</div>
                                                            <div class="small text-muted">
                                                                Type: {{ isset($component->settings['is_rental']) && $component->settings['is_rental'] ? 'Verhuur' : 'Verkoop' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @break
                                                    
                                                @case('cta')
                                                    <div class="row align-items-center">
                                                        <div class="col-md-8 small">
                                                            {{ Str::limit(strip_tags($component->content), 100) }}
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <span class="badge bg-primary">{{ $component->settings['button_text'] ?? 'Actieknop' }}</span>
                                                        </div>
                                                    </div>
                                                    @break
                                                    
                                                @case('testimonials')
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-chat-quote me-2" style="font-size: 1.5rem;"></i>
                                                        <div>Toont {{ $component->settings['count'] ?? 3 }} beoordelingen</div>
                                                    </div>
                                                    @break
                                                    
                                                @default
                                                    <div class="text-muted">Componentvoorbeeld niet beschikbaar</div>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> U heeft nog geen componenten toegevoegd. Gebruik de knop "Component toevoegen" om te beginnen.
                                </div>
                                @endforelse
                            </div>                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verwijder bevestigingsmodal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Component verwijderen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Weet u zeker dat u dit component (<span id="component-name"></span>) wilt verwijderen?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Verwijderen</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable voor componenten
    const componentList = document.getElementById('component-list');
    
    if (componentList) {
        new Sortable(componentList, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                updateOrder();
            }
        });
    }
    
    // Functie om de volgorde bij te werken
    function updateOrder() {
        const components = document.querySelectorAll('.component-item');
        const orderData = [];
        
        components.forEach(component => {
            orderData.push(component.dataset.id);
        });
        
        fetch('{{ route('components.order') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                components: orderData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Volgorde bijgewerkt');
            }
        })
        .catch(error => {
            console.error('Fout bij het bijwerken van de volgorde:', error);
        });
    }
});

// Verwijder bevestiging met verbeterde logica
let deleteModal;
let currentComponentId;

function confirmDelete(componentId, componentName) {
    // Opslaan van component ID voor later gebruik
    currentComponentId = componentId;
    
    // Component naam in de modal plaatsen
    document.getElementById('component-name').textContent = componentName;
    
    // Modal initialiseren indien nog niet gedaan
    if (!deleteModal) {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    }
    
    // Event listeners verwijderen en opnieuw toevoegen om duplicatie te voorkomen
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    
    // Event listener toevoegen aan de nieuwe knop
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        // Formulier indienen en modal sluiten
        document.getElementById('delete-form-' + currentComponentId).submit();
        deleteModal.hide();
    });
    
    // Modal tonen
    deleteModal.show();
}
</script>
@endpush

@section('styles')
<style>
.component-sortable .component-item {
    cursor: grab;
}
.component-sortable .component-item:active {
    cursor: grabbing;
}
.component-preview {
    min-height: 50px;
}
</style>
@endsection
