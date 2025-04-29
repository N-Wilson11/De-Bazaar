@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Contracten beheren') }}</span>
                    <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm">{{ __('Contract uploaden') }}</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (count($contracts) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Contractnummer') }}</th>
                                        <th>{{ __('Gebruiker') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Datum') }}</th>
                                        <th>{{ __('Acties') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts as $contract)
                                        <tr>
                                            <td>{{ $contract->contract_number }}</td>
                                            <td>{{ $contract->user->name }} ({{ $contract->user->email }})</td>
                                            <td>
                                                @if ($contract->isPending())
                                                    <span class="badge bg-warning text-dark">{{ __('In afwachting') }}</span>
                                                @elseif ($contract->isApproved())
                                                    <span class="badge bg-success">{{ __('Goedgekeurd') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Afgekeurd') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $contract->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-info">
                                                    {{ __('Bekijken') }}
                                                </a>
                                                <a href="{{ route('contracts.download', $contract) }}" class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-download"></i> {{ __('Downloaden') }}
                                                </a>
                                                <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Weet je zeker dat je dit contract wilt verwijderen?')">
                                                        {{ __('Verwijderen') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $contracts->links() }}
                        </div>
                    @else
                        <p class="text-center">{{ __('Geen contracten gevonden.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection