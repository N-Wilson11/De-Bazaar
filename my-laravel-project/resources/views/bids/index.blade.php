@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1>{{ __('Mijn biedingen') }}</h1>
            <p class="text-muted">{{ __('Je hebt momenteel :count van de maximaal 4 actieve biedingen', ['count' => $activeBidsCount]) }}</p>
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

    @if($bids->isEmpty())
        <div class="alert alert-info">
            {{ __('Je hebt nog geen biedingen geplaatst.') }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">{{ __('Actieve biedingen') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">{{ __('Alle biedingen') }}</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="active">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Advertentie') }}</th>
                                                <th>{{ __('Bod bedrag') }}</th>
                                                <th>{{ __('Datum') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Acties') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hasActive = false; @endphp
                                            @foreach($bids as $bid)
                                                @if($bid->status === 'pending')
                                                    @php $hasActive = true; @endphp
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('advertisements.show', $bid->advertisement) }}">
                                                                {{ $bid->advertisement->title }}
                                                            </a>
                                                        </td>
                                                        <td>&euro; {{ number_format($bid->amount, 2) }}</td>
                                                        <td>{{ $bid->created_at->format('d-m-Y H:i') }}</td>
                                                        <td>
                                                            <span class="badge bg-info">{{ __('In behandeling') }}</span>
                                                        </td>
                                                        <td>
                                                            <form action="{{ route('bids.cancel', $bid) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Weet je zeker dat je dit bod wilt annuleren?') }}')">
                                                                    {{ __('Annuleren') }}
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            
                                            @if(!$hasActive)
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('Je hebt geen actieve biedingen.') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="all">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Advertentie') }}</th>
                                                <th>{{ __('Bod bedrag') }}</th>
                                                <th>{{ __('Datum') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Acties') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bids as $bid)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('advertisements.show', $bid->advertisement) }}">
                                                            {{ $bid->advertisement->title }}
                                                        </a>
                                                    </td>
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
                                                        @if($bid->status === 'pending')
                                                            <form action="{{ route('bids.cancel', $bid) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Weet je zeker dat je dit bod wilt annuleren?') }}')">
                                                                    {{ __('Annuleren') }}
                                                                </button>
                                                            </form>                                                        @elseif($bid->status === 'accepted')
                                                            <form action="{{ route('cart.add', $bid->advertisement) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    {{ __('Kopen') }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $bids->links() }}
                </div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('advertisements.browse') }}" class="btn btn-primary">{{ __('Zoek advertenties') }}</a>
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
