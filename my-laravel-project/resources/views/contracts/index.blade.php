@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('general.manage_contracts') }}</span>
                    <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm">{{ __('general.upload_contract') }}</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (count($contracts) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">                                <thead>
                                    <tr>
                                        <th>{{ __('general.contract_number') }}</th>
                                        <th>{{ __('general.user') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                        <th>{{ __('general.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts as $contract)
                                        <tr>
                                            <td>{{ $contract->contract_number }}</td>
                                            <td>{{ $contract->user->name }} ({{ $contract->user->email }})</td>
                                            <td>                                                @if ($contract->isPending())
                                                    <span class="badge bg-warning text-dark">{{ __('general.contract_pending') }}</span>
                                                @elseif ($contract->isApproved())
                                                    <span class="badge bg-success">{{ __('general.contract_approved') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('general.contract_rejected') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $contract->created_at->format('d-m-Y H:i') }}</td>
                                            <td>                                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-info">
                                                    {{ __('general.view') }}
                                                </a>
                                                <a href="{{ route('contracts.download', $contract) }}" class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-download"></i> {{ __('general.download') }}
                                                </a>
                                                <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('general.confirm_delete_contract') }}')">
                                                        {{ __('general.delete') }}
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
                        <p class="text-center">{{ __('general.no_contracts_found') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection