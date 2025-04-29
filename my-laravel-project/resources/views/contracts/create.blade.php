@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Contract uploaden') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('contracts.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label">{{ __('Selecteer zakelijke gebruiker') }}</label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Selecteer gebruiker --</option>
                                @foreach($businessUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contract_file" class="form-label">{{ __('Contract bestand (PDF)') }}</label>
                            <input id="contract_file" type="file" class="form-control @error('contract_file') is-invalid @enderror" name="contract_file" required>
                            <div class="form-text">{{ __('Alleen PDF bestanden tot 5MB zijn toegestaan.') }}</div>
                            @error('contract_file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comments" class="form-label">{{ __('Opmerkingen') }}</label>
                            <textarea id="comments" class="form-control @error('comments') is-invalid @enderror" name="comments" rows="3">{{ old('comments') }}</textarea>
                            @error('comments')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">{{ __('Annuleren') }}</a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Contract uploaden') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection