@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1>{{ __('Biedingen op je advertentie') }}</h1>
            <h4 class="text-muted">{{ $advertisement->title }}</h4>
        </div>
    </div>

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

    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ $advertisement->getFirstImageUrl() }}" class="card-img-top mb-3" alt="{{ $advertisement->title }}">
                    <h5 class="card-title">{{ $advertisement->title }}</h5>
                    <p class="card-text">{{ __('Vraagprijs: € :price', ['price' => number_format($advertisement->price, 2)]) }}</p>
                    <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-outline-primary">{{ __('Advertentie bekijken') }}</a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">{{ __('Actieve biedingen') }}</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">{{ __('Alle biedingen') }}</button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pending">
                            @if($bids->where('status', 'pending')->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Bieder') }}</th>
                                                <th>{{ __('Bedrag') }}</th>
                                                <th>{{ __('Datum') }}</th>
                                                <th>{{ __('Bericht') }}</th>
                                                <th>{{ __('Acties') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bids as $bid)
                                                @if($bid->status === 'pending')
                                                    <tr>
                                                        <td>{{ $bid->user->name }}</td>
                                                        <td>&euro; {{ number_format($bid->amount, 2) }}</td>
                                                        <td>{{ $bid->created_at->format('d-m-Y H:i') }}</td>
                                                        <td>
                                                            @if($bid->message)
                                                                <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#messageModal{{ $bid->id }}">
                                                                    {{ __('Bekijk bericht') }}
                                                                </button>
                                                                
                                                                <!-- Message Modal -->
                                                                <div class="modal fade" id="messageModal{{ $bid->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $bid->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="messageModalLabel{{ $bid->id }}">{{ __('Bericht van :name', ['name' => $bid->user->name]) }}</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>{{ $bid->message }}</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Sluiten') }}</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">{{ __('Geen bericht') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form action="{{ route('bids.accept', $bid) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('Weet je zeker dat je dit bod wilt accepteren?') }}')">
                                                                    {{ __('Accepteren') }}
                                                                </button>
                                                            </form>
                                                            
                                                            <form action="{{ route('bids.reject', $bid) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Weet je zeker dat je dit bod wilt afwijzen?') }}')">
                                                                    {{ __('Afwijzen') }}
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    {{ __('Er zijn momenteel geen actieve biedingen op deze advertentie.') }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="all">
                            @if($bids->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Bieder') }}</th>
                                                <th>{{ __('Bedrag') }}</th>
                                                <th>{{ __('Datum') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Bericht') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bids as $bid)
                                                <tr>
                                                    <td>{{ $bid->user->name }}</td>
                                                    <td>&euro; {{ number_format($bid->amount, 2) }}</td>
                                                    <td>{{ $bid->created_at->format('d-m-Y H:i') }}</td>
                                                    <td>
                                                        @if($bid->status === 'pending')
                                                            <span class="badge bg-info">{{ __('In behandeling') }}</span>
                                                        @elseif($bid->status === 'accepted')
                                                            <span class="badge bg-success">{{ __('Geaccepteerd') }}</span>
                                                        @elseif($bid->status === 'rejected')
                                                            <span class="badge bg-danger">{{ __('Afgewezen') }}</span>
                                                        @elseif($bid->status === 'expired')
                                                            <span class="badge bg-warning">{{ __('Verlopen') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bid->message)
                                                            <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#messageModal{{ $bid->id }}">
                                                                {{ __('Bekijk bericht') }}
                                                            </button>
                                                            
                                                            <!-- Message Modal -->
                                                            <div class="modal fade" id="messageModal{{ $bid->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $bid->id }}" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="messageModalLabel{{ $bid->id }}">{{ __('Bericht van :name', ['name' => $bid->user->name]) }}</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>{{ $bid->message }}</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Sluiten') }}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">{{ __('Geen bericht') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    {{ __('Er zijn nog geen biedingen op deze advertentie.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $bids->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('click', event => {
                tabEls.forEach(el => el.classList.remove('active'));
                event.target.classList.add('active');
            });
        });
    });
</script>
@endpush
