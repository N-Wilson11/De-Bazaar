@extends('layouts.app')

@section('title', __('general.sale_details'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="h3 mb-1">{{ __('general.sale_from_order') }} #{{ $orderItem->order->id }}</h1>
                            <p class="mb-0 text-muted">{{ __('general.order_date') }}: {{ $orderItem->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                        
                        <div class="text-end">
                            @if($orderItem->order->status === 'pending')
                                <span class="badge bg-warning text-dark mb-2">{{ __('general.order_status_pending') }}</span>
                            @elseif($orderItem->order->status === 'paid')
                                <span class="badge bg-info mb-2">{{ __('general.order_status_paid') }}</span>
                            @elseif($orderItem->order->status === 'processing')
                                <span class="badge bg-primary mb-2">{{ __('general.order_status_processing') }}</span>
                            @elseif($orderItem->order->status === 'completed')
                                <span class="badge bg-success mb-2">{{ __('general.order_status_completed') }}</span>
                            @elseif($orderItem->order->status === 'cancelled')
                                <span class="badge bg-danger mb-2">{{ __('general.order_status_cancelled') }}</span>
                            @else
                                <span class="badge bg-secondary mb-2">{{ $orderItem->order->status }}</span>
                            @endif
                            
                            <div>
                                <a href="{{ route('orders.my-sales') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left me-1"></i>{{ __('general.back_to_sales') }}
                                </a>
                            </div>
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
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('general.product_details') }}</h5>
                    
                    <div class="d-flex mb-4">
                        @if($orderItem->advertisement && !empty($orderItem->advertisement->images))
                            <div class="me-3">
                                <img src="{{ $orderItem->advertisement->getFirstImageUrl() }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;" alt="{{ $orderItem->title }}">
                            </div>
                        @else
                            <div class="bg-light text-center me-3" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        
                        <div>
                            <h4>{{ $orderItem->title }}</h4>
                            <p class="mb-1">{{ __('general.quantity') }}: {{ $orderItem->quantity }}</p>
                            <p class="mb-1">{{ __('general.price') }}: € {{ number_format($orderItem->price, 2, ',', '.') }}</p>
                            <p class="mb-0">{{ __('general.total') }}: € {{ number_format($orderItem->price * $orderItem->quantity, 2, ',', '.') }}</p>
                            
                            @if($orderItem->advertisement)
                                <div class="mt-2">
                                    <a href="{{ route('advertisements.show', $orderItem->advertisement) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-box me-1"></i>{{ __('general.view_advertisement') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('general.buyer_details') }}</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>{{ __('general.name') }}:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $orderItem->order->user->name }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>{{ __('general.shipping_address') }}:</strong>
                        </div>
                        <div class="col-md-9 white-space-pre-wrap">
                            {{ $orderItem->order->shipping_address }}
                        </div>
                    </div>
                    
                    @if($orderItem->order->notes)
                        <div class="row mb-0">
                            <div class="col-md-3">
                                <strong>{{ __('general.order_notes') }}:</strong>
                            </div>
                            <div class="col-md-9">
                                {{ $orderItem->order->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($orderItem->order->status !== 'completed' && $orderItem->order->status !== 'cancelled')
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('general.actions') }}</h5>
                        <form action="{{ route('orders.complete-sale-item', $orderItem) }}" method="POST" onsubmit="return confirm('{{ __('general.confirm_complete_sale') }}')">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>{{ __('general.mark_as_completed') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('general.order_details') }}</h5>
                    
                    <div class="mb-3">
                        <strong>{{ __('general.order_number') }}:</strong>
                        <p class="mb-0">#{{ $orderItem->order->id }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('general.date') }}:</strong>
                        <p class="mb-0">{{ $orderItem->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('general.status') }}:</strong>
                        <p class="mb-0">
                            @if($orderItem->order->status === 'pending')
                                <span class="badge bg-warning text-dark">{{ __('general.order_status_pending') }}</span>
                            @elseif($orderItem->order->status === 'paid')
                                <span class="badge bg-info">{{ __('general.order_status_paid') }}</span>
                            @elseif($orderItem->order->status === 'processing')
                                <span class="badge bg-primary">{{ __('general.order_status_processing') }}</span>
                            @elseif($orderItem->order->status === 'completed')
                                <span class="badge bg-success">{{ __('general.order_status_completed') }}</span>
                            @elseif($orderItem->order->status === 'cancelled')
                                <span class="badge bg-danger">{{ __('general.order_status_cancelled') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $orderItem->order->status }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{ __('general.payment_method') }}:</strong>
                        <p class="mb-0">
                            @if($orderItem->order->payment_method === 'ideal')
                                <i class="bi bi-bank me-1"></i>{{ __('general.payment_ideal') }}
                            @elseif($orderItem->order->payment_method === 'creditcard')
                                <i class="bi bi-credit-card me-1"></i>{{ __('general.payment_creditcard') }}
                            @elseif($orderItem->order->payment_method === 'banktransfer')
                                <i class="bi bi-cash me-1"></i>{{ __('general.payment_banktransfer') }}
                            @else
                                {{ $orderItem->order->payment_method }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('general.seller_instructions') }}</h5>
                    <ol class="ps-3">
                        <li class="mb-2">{{ __('general.seller_instruction_1') }}</li>
                        <li class="mb-2">{{ __('general.seller_instruction_2') }}</li>
                        <li class="mb-2">{{ __('general.seller_instruction_3') }}</li>
                        <li class="mb-2">{{ __('general.seller_instruction_4') }}</li>
                        <li>{{ __('general.seller_instruction_5') }}</li>
                    </ol>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        {{ __('general.seller_help_text') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.white-space-pre-wrap {
    white-space: pre-wrap;
}
</style>
@endsection
