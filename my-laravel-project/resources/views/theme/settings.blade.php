@extends('layouts.app')

@section('title', __('theme.settings'))

@section('content')
<div class="container">
    @php
        $user = auth()->user();
        $isBusinessUser = $user && $user->user_type === 'zakelijk';
        $companyName = $isBusinessUser && $user->company ? $user->company->name : '';
    @endphp
    
    <h1>
        @if($isBusinessUser)
            {{ __('theme.company_theme_settings') }}: {{ $companyName }}
        @else
            {{ __('theme.company_theme_settings') }}
        @endif
    </h1>
    <p class="lead">{{ __('theme.customize_info') }}</p>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Company switcher removed -->

    <!-- Available themes list removed -->

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('theme.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h3>{{ __('theme.company_information') }}</h3>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('theme.company_name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $theme['name']) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Logo upload functionality removed -->
                        </div>
                        
                        <div class="mb-3">
                            <label for="footer_text" class="form-label">{{ __('theme.footer_text') }}</label>
                            <input type="text" class="form-control @error('footer_text') is-invalid @enderror" 
                                id="footer_text" name="footer_text" value="{{ old('footer_text', $theme['footer_text']) }}">
                            @error('footer_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h3>{{ __('theme.color_scheme') }}</h3>
                        
                        <div class="mb-3">
                            <label for="primary_color" class="form-label">{{ __('theme.primary_color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                    id="primary_color_picker" value="{{ old('primary_color', $theme['colors']['primary']) }}" 
                                    onchange="document.getElementById('primary_color').value = this.value;">
                                <input type="text" class="form-control @error('primary_color') is-invalid @enderror" 
                                    id="primary_color" name="primary_color" value="{{ old('primary_color', $theme['colors']['primary']) }}">
                            </div>
                            @error('primary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="secondary_color" class="form-label">{{ __('theme.secondary_color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                    id="secondary_color_picker" value="{{ old('secondary_color', $theme['colors']['secondary']) }}" 
                                    onchange="document.getElementById('secondary_color').value = this.value;">
                                <input type="text" class="form-control @error('secondary_color') is-invalid @enderror" 
                                    id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $theme['colors']['secondary']) }}">
                            </div>
                            @error('secondary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="accent_color" class="form-label">{{ __('theme.accent_color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                    id="accent_color_picker" value="{{ old('accent_color', $theme['colors']['accent']) }}" 
                                    onchange="document.getElementById('accent_color').value = this.value;">
                                <input type="text" class="form-control @error('accent_color') is-invalid @enderror" 
                                    id="accent_color" name="accent_color" value="{{ old('accent_color', $theme['colors']['accent']) }}">
                            </div>
                            @error('accent_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="text_color" class="form-label">{{ __('theme.text_color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                    id="text_color_picker" value="{{ old('text_color', $theme['colors']['text']) }}" 
                                    onchange="document.getElementById('text_color').value = this.value;">
                                <input type="text" class="form-control @error('text_color') is-invalid @enderror" 
                                    id="text_color" name="text_color" value="{{ old('text_color', $theme['colors']['text']) }}">
                            </div>
                            @error('text_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="background_color" class="form-label">{{ __('theme.background_color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                    id="background_color_picker" value="{{ old('background_color', $theme['colors']['background']) }}" 
                                    onchange="document.getElementById('background_color').value = this.value;">
                                <input type="text" class="form-control @error('background_color') is-invalid @enderror" 
                                    id="background_color" name="background_color" value="{{ old('background_color', $theme['colors']['background']) }}">
                            </div>
                            @error('background_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h3>{{ __('theme.preview') }}</h3>
                    <div class="border p-4 mb-3" id="theme-preview">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 style="color: var(--primary-color);">{{ __('theme.primary_color_heading') }}</h4>
                                <p>{{ __('theme.text_appearance') }}</p>
                                <button class="btn btn-primary">{{ __('theme.primary_button') }}</button>
                                <button class="btn btn-secondary">{{ __('theme.secondary_button') }}</button>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header" style="background-color: var(--primary-color); color: white;">
                                        {{ __('theme.card_header') }}
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title accent">{{ __('theme.card_title') }}</h5>
                                        <p class="card-text">{{ __('theme.card_sample') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">{{ __('theme.save_theme_settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update the preview when any color field changes
    document.querySelectorAll('input[type="color"]').forEach(input => {
        input.addEventListener('input', updatePreview);
    });
    
    document.querySelectorAll('input[name$="_color"]').forEach(input => {
        input.addEventListener('input', function() {
            const pickerID = this.id + '_picker';
            if (document.getElementById(pickerID)) {
                document.getElementById(pickerID).value = this.value;
            }
            updatePreview();
        });
    });

    function updatePreview() {
        const preview = document.getElementById('theme-preview');
        preview.style.setProperty('--primary-color', document.getElementById('primary_color').value);
        preview.style.setProperty('--secondary-color', document.getElementById('secondary_color').value);
        preview.style.setProperty('--accent-color', document.getElementById('accent_color').value);
        preview.style.setProperty('--text-color', document.getElementById('text_color').value);
        preview.style.setProperty('--background-color', document.getElementById('background_color').value);
    }
    
    // Initialize preview
    updatePreview();
</script>
@endpush
@endsection
