@extends('layouts.app')

@section('page-title', 'My Orders')

@section('content')
<style>
    .order-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .order-status.pending { background: #fff3cd; color: #856404; }
    .order-status.processing { background: #cce5ff; color: #004085; }
    .order-status.completed { background: #d4edda; color: #155724; }
    .order-status.cancelled { background: #f8d7da; color: #721c24; }
    .order-card {
        transition: all 0.2s;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
    }
    .order-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-list me-2"></i>My Orders</h2>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="order-card">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong>Order #{{ $order->id }}</strong>
                        <br>
                        <small class="text-muted">{{ $order->sale_date->format('M d, Y h:i A') }}</small>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted">Branch:</span>
                        <br>
                        <strong>{{ str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name ?? 'Unknown') }}</strong>
                    </div>
                    <div class="col-md-2">
                        <span class="text-muted">Total:</span>
                        <br>
                        <strong class="text-success">₱{{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                    <div class="col-md-2">
                        <span class="text-muted">Status:</span>
                        <br>
                        <span class="order-status {{ $order->delivery_status ?? 'pending' }}">
                            {{ ucfirst($order->delivery_status ?? 'pending') }}
                        </span>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('customer.order-details', $order->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>
                @if($order->delivery_address)
                    <div class="mt-2 text-muted" style="font-size: 13px;">
                        <i class="fas fa-truck me-1"></i> 
                        Delivery: {{ $order->delivery_address }}
                    </div>
                @endif
                @if($order->order_notes)
                    <div class="mt-1 text-muted" style="font-size: 13px;">
                        <i class="fas fa-comment me-1"></i> 
                        Note: {{ $order->order_notes }}
                    </div>
                @endif
            </div>
        @endforeach

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
            <h5>No Orders Yet</h5>
            <p class="text-muted">Start ordering from our menu!</p>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-coffee me-2"></i> Browse Menu
            </a>
        </div>
    @endif
</div>
@endsection