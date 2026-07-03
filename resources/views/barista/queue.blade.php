@extends('layouts.app')

@section('page-title', 'Order Queue')

@section('content')
<style>
    .queue-container {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
        padding: 15px 0;
    }
    .panel-header {
        padding: 10px 16px;
        border-radius: 8px 8px 0 0;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f1f1f1;
        color: #333;
        border-bottom: 2px solid #ddd;
    }
    .panel-header .badge-count {
        background: rgba(0,0,0,0.08);
        padding: 1px 10px;
        border-radius: 12px;
        font-size: 12px;
        color: #666;
    }
    .panel-body {
        padding: 12px;
        max-height: 580px;
        overflow-y: auto;
        min-height: 200px;
        background: white;
        border: 1px solid #e8e8e8;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }
    .panel-body::-webkit-scrollbar {
        width: 4px;
    }
    .panel-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    
    .panel-queue .panel-header { border-bottom-color: #6F4E37; }
    .panel-preparing .panel-header { border-bottom-color: #e6a800; }
    .panel-serve .panel-header { border-bottom-color: #28a745; }
    
    .order-item {
        background: #fafafa;
        border-radius: 6px;
        padding: 10px 12px;
        margin-bottom: 8px;
        border-left: 3px solid #ddd;
        transition: all 0.2s;
    }
    .order-item:hover {
        background: #f5f5f5;
    }
    .order-item .order-number {
        font-size: 15px;
        font-weight: 600;
        color: #333;
    }
    .order-item .order-time {
        font-size: 11px;
        color: #999;
    }
    .order-item .customer-name {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    .order-item .customer-type {
        font-size: 10px;
        padding: 1px 8px;
        border-radius: 10px;
        font-weight: 500;
        display: inline-block;
        background: #e9ecef;
        color: #666;
    }
    .order-item .customer-type.member {
        background: #e3f0ff;
        color: #0d6efd;
    }
    .order-item .order-items {
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px solid #eee;
    }
    .order-item .order-items .item-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        padding: 2px 0;
        color: #555;
    }
    .order-item .order-actions {
        margin-top: 8px;
        display: flex;
        gap: 6px;
    }
    .order-item .order-actions .btn {
        font-size: 12px;
        padding: 4px 14px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }
    .order-item .order-actions .btn-start {
        background: #6F4E37;
        color: white;
    }
    .order-item .order-actions .btn-start:hover {
        background: #5a3d2b;
    }
    .order-item .order-actions .btn-ready {
        background: #28a745;
        color: white;
    }
    .order-item .order-actions .btn-ready:hover {
        background: #1e7e34;
    }
    .order-item .order-actions .btn-serve {
        background: #17a2b8;
        color: white;
    }
    .order-item .order-actions .btn-serve:hover {
        background: #138496;
    }
    .order-item .order-actions .btn-cancel {
        background: #dc3545;
        color: white;
    }
    .order-item .order-actions .btn-cancel:hover {
        background: #bd2130;
    }
    .order-item .order-notes {
        font-size: 12px;
        color: #856404;
        background: #fff8e1;
        padding: 3px 8px;
        border-radius: 4px;
        margin-top: 5px;
        display: inline-block;
    }
    .order-item.pending { border-left-color: #6F4E37; }
    .order-item.preparing { border-left-color: #e6a800; }
    .order-item.ready { border-left-color: #17a2b8; }
    
    .badge-status {
        font-size: 10px;
        padding: 1px 10px;
        border-radius: 12px;
        font-weight: 500;
        display: inline-block;
        background: #e9ecef;
        color: #666;
    }
    .badge-status.pending { background: #e8e0da; color: #6F4E37; }
    .badge-status.preparing { background: #fff3cd; color: #856404; }
    .badge-status.ready { background: #d4edda; color: #155724; }
    
    .panel-empty {
        text-align: center;
        padding: 30px 20px;
        color: #999;
    }
    .panel-empty i {
        font-size: 32px;
        display: block;
        margin-bottom: 8px;
        opacity: 0.4;
    }
    .panel-empty p {
        margin: 0;
        font-size: 13px;
    }
    
    .queue-stats {
        display: flex;
        gap: 12px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .queue-stats .stat-item {
        background: white;
        padding: 6px 16px;
        border-radius: 6px;
        border: 1px solid #e8e8e8;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }
    .queue-stats .stat-item .stat-number {
        font-weight: 600;
        font-size: 16px;
    }
    .stat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .stat-dot.pending { background: #6F4E37; }
    .stat-dot.preparing { background: #e6a800; }
    .stat-dot.ready { background: #17a2b8; }
</style>

<div class="container-fluid queue-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Order Queue</h5>
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync"></i>
            </button>
        </div>

        <!-- Stats -->
        <div class="queue-stats">
            <div class="stat-item">
                <span class="stat-dot pending"></span>
                <span>Queue:</span>
                <span class="stat-number" style="color:#6F4E37;">{{ $orders->where('status', 'pending')->count() }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-dot preparing"></span>
                <span>Preparing:</span>
                <span class="stat-number" style="color:#e6a800;">{{ $orders->where('status', 'preparing')->count() }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-dot ready"></span>
                <span>Ready:</span>
                <span class="stat-number" style="color:#17a2b8;">{{ $orders->where('status', 'ready')->count() }}</span>
            </div>
        </div>

        <!-- Panels -->
        <div class="row">
            <!-- QUEUE Panel -->
            <div class="col-md-4">
                <div class="panel-queue">
                    <div class="panel-header">
                        <span><i class="fas fa-list me-2"></i>Queue</span>
                        <span class="badge-count">{{ $orders->where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="panel-body">
                        @php $pending = $orders->where('status', 'pending'); @endphp
                        @if($pending->count() > 0)
                            @foreach($pending as $order)
                                <div class="order-item pending">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="order-number">#{{ $order->id }}</span>
                                            <span class="badge-status pending ms-1">Queue</span>
                                        </div>
                                        <span class="order-time">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="customer-name">
                                        <i class="fas fa-user me-1" style="font-size:12px;"></i>
                                        {{ $order->customer_name ?? 'Walk-in' }}
                                        <span class="customer-type {{ $order->customer_type ?? 'walkin' }} ms-1">
                                            {{ ucfirst($order->customer_type ?? 'walkin') }}
                                        </span>
                                    </div>
                                    <div class="order-items">
                                        @php $sale = $order->sale; $items = $sale ? $sale->items : collect(); @endphp
                                        @if($items->count() > 0)
                                            @foreach($items as $item)
                                                <div class="item-row">
                                                    <span>{{ $item->product->name ?? 'Unknown' }}</span>
                                                    <span>×{{ $item->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="item-row">
                                                <span>{{ $order->product->name ?? 'Unknown' }}</span>
                                                <span>×{{ $order->quantity }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($order->notes)
                                        <div class="order-notes"><i class="fas fa-comment me-1"></i>{{ $order->notes }}</div>
                                    @endif
                                    <div class="order-actions">
                                        <button class="btn btn-start" onclick="updateStatus({{ $order->id }}, 'preparing')">
                                            <i class="fas fa-play me-1"></i> Start
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="panel-empty">
                                <i class="fas fa-check-circle"></i>
                                <p>No orders in queue</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PREPARING Panel -->
            <div class="col-md-4">
                <div class="panel-preparing">
                    <div class="panel-header">
                        <span><i class="fas fa-spinner me-2"></i>Preparing</span>
                        <span class="badge-count">{{ $orders->where('status', 'preparing')->count() }}</span>
                    </div>
                    <div class="panel-body">
                        @php $preparing = $orders->where('status', 'preparing'); @endphp
                        @if($preparing->count() > 0)
                            @foreach($preparing as $order)
                                <div class="order-item preparing">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="order-number">#{{ $order->id }}</span>
                                            <span class="badge-status preparing ms-1">Preparing</span>
                                        </div>
                                        <span class="order-time">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="customer-name">
                                        <i class="fas fa-user me-1" style="font-size:12px;"></i>
                                        {{ $order->customer_name ?? 'Walk-in' }}
                                        <span class="customer-type {{ $order->customer_type ?? 'walkin' }} ms-1">
                                            {{ ucfirst($order->customer_type ?? 'walkin') }}
                                        </span>
                                    </div>
                                    <div class="order-items">
                                        @php $sale = $order->sale; $items = $sale ? $sale->items : collect(); @endphp
                                        @if($items->count() > 0)
                                            @foreach($items as $item)
                                                <div class="item-row">
                                                    <span>{{ $item->product->name ?? 'Unknown' }}</span>
                                                    <span>×{{ $item->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="item-row">
                                                <span>{{ $order->product->name ?? 'Unknown' }}</span>
                                                <span>×{{ $order->quantity }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($order->notes)
                                        <div class="order-notes"><i class="fas fa-comment me-1"></i>{{ $order->notes }}</div>
                                    @endif
                                    <div class="order-actions">
                                        <button class="btn btn-ready" onclick="updateStatus({{ $order->id }}, 'ready')">
                                            <i class="fas fa-check me-1"></i> Ready
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="panel-empty">
                                <i class="fas fa-spinner"></i>
                                <p>No orders being prepared</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SERVE Panel -->
            <div class="col-md-4">
                <div class="panel-serve">
                    <div class="panel-header">
                        <span><i class="fas fa-check-circle me-2"></i>Ready to Serve</span>
                        <span class="badge-count">{{ $orders->where('status', 'ready')->count() }}</span>
                    </div>
                    <div class="panel-body">
                        @php $ready = $orders->where('status', 'ready'); @endphp
                        @if($ready->count() > 0)
                            @foreach($ready as $order)
                                <div class="order-item ready">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="order-number">#{{ $order->id }}</span>
                                            <span class="badge-status ready ms-1">Ready</span>
                                        </div>
                                        <span class="order-time">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="customer-name">
                                        <i class="fas fa-user me-1" style="font-size:12px;"></i>
                                        {{ $order->customer_name ?? 'Walk-in' }}
                                        <span class="customer-type {{ $order->customer_type ?? 'walkin' }} ms-1">
                                            {{ ucfirst($order->customer_type ?? 'walkin') }}
                                        </span>
                                    </div>
                                    <div class="order-items">
                                        @php $sale = $order->sale; $items = $sale ? $sale->items : collect(); @endphp
                                        @if($items->count() > 0)
                                            @foreach($items as $item)
                                                <div class="item-row">
                                                    <span>{{ $item->product->name ?? 'Unknown' }}</span>
                                                    <span>×{{ $item->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="item-row">
                                                <span>{{ $order->product->name ?? 'Unknown' }}</span>
                                                <span>×{{ $order->quantity }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($order->notes)
                                        <div class="order-notes"><i class="fas fa-comment me-1"></i>{{ $order->notes }}</div>
                                    @endif
                                    <div class="order-actions">
                                        <button class="btn btn-serve" onclick="updateStatus({{ $order->id }}, 'served')">
                                            <i class="fas fa-check-double me-1"></i> Serve
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="panel-empty">
                                <i class="fas fa-check-circle"></i>
                                <p>No orders ready to serve</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateStatus(orderId, status) {
        fetch(`/barista/order/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Auto-refresh every 15 seconds
    let refreshInterval = setInterval(() => location.reload(), 15000);
    
    document.addEventListener('click', () => {
        clearInterval(refreshInterval);
        setTimeout(() => {
            refreshInterval = setInterval(() => location.reload(), 15000);
        }, 30000);
    });
</script>
@endpush
@endsection
