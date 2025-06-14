@extends('layouts.app')

@section('title', __('Change Company Logo'))

@section('content')
<div class="container">
    <h1>{{ __('Change Company Logo') }}</h1>
    <p class="lead">{{ __('Upload a new logo for your company') }}</p>

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

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Active Company') }}: <strong>{{ $companyId }}</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>{{ __('Current Logo') }}</h4>
                    @if($companyTheme && $companyTheme->logo_path)
                        <div class="current-logo mb-3">
                            <img src="{{ asset($companyTheme->logo_path) }}" alt="Company Logo" class="img-fluid" style="max-height: 100px;">
                        </div>
                        
                        <form action="{{ route('theme.remove-logo') }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to remove the logo?') }}')">
                                {{ __('Remove Logo') }}
                            </button>
                        </form>
                    @else
                        <p class="text-muted">{{ __('No logo currently set') }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4>{{ __('Upload New Logo') }}</h4>
                    <form method="POST" action="{{ route('theme.update-logo') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="logo" class="form-label">{{ __('Choose Logo File') }}</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                id="logo" name="logo" accept="image/*" required>
                            <div class="form-text">{{ __('Recommended: PNG or JPG file, 300x100 pixels or similar ratio.') }}</div>
                            @error('logo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('theme.settings') }}" class="btn btn-secondary me-md-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Upload Logo') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection