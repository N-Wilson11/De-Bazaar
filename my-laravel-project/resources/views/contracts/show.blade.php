@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Contract details') }}</span>
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary btn-sm">{{ __('Terug naar overzicht') }}</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table">
                        <tbody>
                            <tr>
                                <th>{{ __('Contractnummer') }}</th>
                                <td>{{ $contract->contract_number }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Gebruiker') }}</th>
                                <td>{{ $contract->user->name }} ({{ $contract->user->email }})</td>
                            </tr>
                            <tr>
                                <th>{{ __('Geüpload op') }}</th>
                                <td>{{ $contract->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}</th>
                                <td>
                                    @if ($contract->isPending())
                                        <span class="badge bg-warning text-dark">{{ __('In afwachting') }}</span>
                                    @elseif ($contract->isApproved())
                                        <span class="badge bg-success">{{ __('Goedgekeurd op') }} {{ $contract->approved_at->format('d-m-Y H:i') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Afgekeurd') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($contract->isApproved() && $contract->approver)
                                <tr>
                                    <th>{{ __('Goedgekeurd door') }}</th>
                                    <td>{{ $contract->approver->name }}</td>
                                </tr>
                            @endif
                            @if ($contract->comments)
                                <tr>
                                    <th>{{ __('Opmerkingen') }}</th>
                                    <td>{{ $contract->comments }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>{{ __('Contract bestand') }}</h5>
                            <a href="{{ route('contracts.download', $contract) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-download"></i> {{ __('Download PDF') }}
                            </a>
                        </div>
                        
                        <div class="border p-3 bg-light text-center">
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                                <p class="mb-2"><strong>{{ __('Bestandsnaam') }}:</strong> {{ basename($contract->file_path) }}</p>
                            </div>
                            <a href="{{ route('contracts.download', $contract) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i> {{ __('Contract downloaden') }}
                            </a>
                        </div>
                    </div>

                    @if ($contract->isPending())
                        <div class="mt-4">
                            <h5>{{ __('Contract beoordelen') }}</h5>
                            <form method="POST" action="{{ route('contracts.review', $contract) }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ __('Status wijzigen') }}</label>
                                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="">-- Selecteer status --</option>
                                        <option value="approved">{{ __('Goedkeuren') }}</option>
                                        <option value="rejected">{{ __('Afkeuren') }}</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="comments" class="form-label">{{ __('Opmerkingen') }}</label>
                                    <textarea id="comments" name="comments" class="form-control @error('comments') is-invalid @enderror" rows="3">{{ old('comments', $contract->comments) }}</textarea>
                                    @error('comments')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Beoordeling opslaan') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection