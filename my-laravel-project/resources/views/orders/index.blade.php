@extends('layouts.app')

@section('title', __('Mijn bestellingen'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('Mijn bestellingen') }}</h1>
                    <p>{{ __('Hier zie je een overzicht van al je bestellingen.') }}</p>
                    
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
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Bestelnummer') }}</th>
                                        <th>{{ __('Datum') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Totaalbedrag') }}</th>
                                        <th>{{ __('Actie') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                @if($order->status === 'pending')
                                                    <span class="badge bg-warning text-dark">{{ __('In behandeling') }}</span>
                                                @elseif($order->status === 'paid')
                                                    <span class="badge bg-info">{{ __('Betaald') }}</span>
                                                @elseif($order->status === 'processing')
                                                    <span class="badge bg-primary">{{ __('In verwerking') }}</span>
                                                @elseif($order->status === 'completed')
                                                    <span class="badge bg-success">{{ __('Voltooid') }}</span>
                                                @elseif($order->status === 'cancelled')
                                                    <span class="badge bg-danger">{{ __('Geannuleerd') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td>€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                            <td>
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>{{ __('Bekijken') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">{{ __('Geen bestellingen gevonden') }}</h5>
                            <p>{{ __('Je hebt nog geen bestellingen geplaatst.') }}</p>
                            <a href="{{ route('advertisements.browse') }}" class="btn btn-primary">
                                <i class="bi bi-shop me-1"></i>{{ __('Bekijk advertenties') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
