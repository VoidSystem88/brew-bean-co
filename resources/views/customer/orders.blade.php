@extends('layouts.customer')

@section('page-title', 'My Orders')

@section('content')
<style>
    .order-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
        padding: 16px 20px;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .order-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    
    .order-status {
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .order-status.pending { background: #fff3cd; color: #856404; }
    .order-status.preparing { background: #cce5ff; color: #004085; }
    .order-status.ready { background: #fd7e14; color: white; }
    .order-status.out_for_delivery { background: #6F4E37; color: white; }
    .order-status.completed { background: #d4edda; color: #155724; }
    .order-status.cancelled { background: #f8d7da; color: #721c24; }
    
    .btn-track {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 16px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-track:hover {
        background: #5a3d2b;
        color: white;
    }
</style>

<div class="container-fluid">
    <h2 class="mb-3"><i class="fas fa-box me-2" style="color:#6F4E37;"></i>My Orders</h2>
    
    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="order-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <div style="font-weight:700;font-size:16px;color:#6F4E37;">
                            #{{ $order->id }}
                        </div>
                        <div style="font-size:13px;color:#999;">
                            {{ $order->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div style="font-size:13px;color:#666;margin-top:4px;">
                            {{ str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name ?? 'N/A') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-weight:700;font-size:18px;color:#6F4E37;">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </div>
                        <div class="mt-1">
                            <span class="order-status {{ $order->delivery_status ?? 'pending' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->delivery_status ?? 'pending')) }}
                            </span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('customer.track', $order->id) }}" class="btn-track">
                                <i class="fas fa-truck me-1"></i> Track
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-inbox" style="font-size:48px;color:#ddd;"></i>
            <p class="mt-3 text-muted">You haven't placed any orders yet.</p>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-brown">
                <i class="fas fa-shopping-bag me-2"></i> Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection