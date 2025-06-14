@extends('layouts.app')

@section('title', __('general.my_sales'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ __('general.my_sales') }}</h1>
                    <p>{{ __('general.sales_overview') }}</p>
                    
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
                    @if($orderItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.order_number') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                        <th>{{ __('general.product') }}</th>
                                        <th>{{ __('general.buyer') }}</th>
                                        <th>{{ __('general.price') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                        <th>{{ __('general.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderItems as $item)
                                        <tr>
                                            <td>#{{ $item->order->id }}</td>
                                            <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->advertisement && !empty($item->advertisement->images))
                                                        <img src="{{ $item->advertisement->getFirstImageUrl() }}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $item->title }}">
                                                    @else
                                                        <div class="bg-light text-center me-2" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        {{ $item->title }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->order->user->name }}</td>
                                            <td>€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td>
                                                @if($item->order->status === 'pending')
                                                    <span class="badge bg-warning text-dark">{{ __('general.order_status_pending') }}</span>
                                                @elseif($item->order->status === 'paid')
                                                    <span class="badge bg-info">{{ __('general.order_status_paid') }}</span>
                                                @elseif($item->order->status === 'processing')
                                                    <span class="badge bg-primary">{{ __('general.order_status_processing') }}</span>
                                                @elseif($item->order->status === 'completed')
                                                    <span class="badge bg-success">{{ __('general.order_status_completed') }}</span>
                                                @elseif($item->order->status === 'cancelled')
                                                    <span class="badge bg-danger">{{ __('general.order_status_cancelled') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $item->order->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('orders.show-sale-item', $item) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>{{ __('general.view') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $orderItems->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">{{ __('general.no_sales_found') }}</h5>
                            <p>{{ __('general.no_sales_description') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
