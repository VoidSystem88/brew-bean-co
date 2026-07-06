@extends('layouts.app')

@section('page-title', 'Order Details')

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
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e8e8e8;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-receipt me-2"></i>Order #{{ $order->id }}</h2>
        <div>
            <a href="{{ route('customer.orders') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-home me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="detail-card">
                <h6 class="mb-3"><i class="fas fa-shopping-bag me-2"></i>Items</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:50px;">Image</th><th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                                                    <td>
                                    @if($item->product->image && file_exists(storage_path('app/public/products/' . $item->product->image)))
                                        <img src="{{ asset('storage/products/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div style="width:40px;height:40px;background:#f5f0eb;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc;">
                                            <i class="fas fa-coffee"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">â‚±{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">â‚±{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                <td class="text-end fw-bold text-success">â‚±{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="detail-card">
                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Order Details</h6>
                <div class="mb-2">
                    <span class="text-muted">Order Date:</span>
                    <br>
                    <strong>{{ $order->sale_date->format('M d, Y h:i A') }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Branch:</span>
                    <br>
                    <strong>{{ str_replace('â˜• Brew & Bean Co. - ', '', $order->branch->name ?? 'Unknown') }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Status:</span>
                    <br>
                    <span class="order-status {{ $order->delivery_status ?? 'pending' }}">
                        {{ ucfirst($order->delivery_status ?? 'pending') }}
                    </span>
                </div>
                @if($order->delivery_address)
                    <div class="mb-2">
                        <span class="text-muted">Delivery Address:</span>
                        <br>
                        <strong>{{ $order->delivery_address }}</strong>
                    </div>
                @endif
                @if($order->order_notes)
                    <div class="mb-2">
                        <span class="text-muted">Notes:</span>
                        <br>
                        <strong>{{ $order->order_notes }}</strong>
                    </div>
                @endif
            </div>

            <div class="detail-card">
                <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment</h6>
                <div class="mb-2">
                    <span class="text-muted">Total Amount:</span>
                    <br>
                    <strong class="text-success" style="font-size: 20px;">â‚±{{ number_format($order->total_amount, 2) }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Payment Method:</span>
                    <br>
                    <strong>Cash on Delivery / Pickup</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
