@extends('layouts.customer')

@section('page-title', 'Track Delivery')

@section('content')
<style>
    .track-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px 0;
    }
    
    .track-header {
        background: white;
        border-radius: 12px;
        padding: 20px 25px;
        border: 1px solid #e8e8e8;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .track-header .order-id {
        font-weight: 700;
        color: #6F4E37;
        font-size: 18px;
    }
    
    .track-header .order-status {
        padding: 4px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    
    .track-header .order-status.pending { background: #fff3cd; color: #856404; }
    .track-header .order-status.preparing { background: #cce5ff; color: #004085; }
    .track-header .order-status.ready { background: #fd7e14; color: white; }
    .track-header .order-status.out_for_delivery { background: #6F4E37; color: white; }
    .track-header .order-status.completed { background: #d4edda; color: #155724; }
    .track-header .order-status.cancelled { background: #f8d7da; color: #721c24; }
    
    /* Timeline */
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 24px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #e8e8e8;
    }
    
    .timeline-item {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        position: relative;
        padding-left: 10px;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-item .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        z-index: 2;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .timeline-item .icon-wrapper.completed {
        background: #d4edda;
        color: #28a745;
    }
    
    .timeline-item .icon-wrapper.active {
        background: #6F4E37;
        color: white;
    }
    
    .timeline-item .icon-wrapper.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .timeline-item .icon-wrapper.inactive {
        background: #f5f5f5;
        color: #ccc;
    }
    
    .timeline-item .content {
        flex: 1;
        padding-top: 6px;
    }
    
    .timeline-item .content .title {
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }
    
    .timeline-item .content .time {
        font-size: 12px;
        color: #999;
        margin-top: 2px;
    }
    
    .timeline-item .content .description {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
    }
    
    .timeline-item .content .title.completed {
        color: #28a745;
    }
    
    .timeline-item .content .title.active {
        color: #6F4E37;
    }
    
    .timeline-item .content .title.pending {
        color: #856404;
    }
    
    .timeline-item .content .title.inactive {
        color: #ccc;
    }
    
    /* Order Details */
    .order-details-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
        padding: 20px 25px;
        margin-top: 20px;
    }
    
    .order-details-card .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 14px;
    }
    
    .order-details-card .detail-row:last-child {
        border-bottom: none;
    }
    
    .order-details-card .detail-row .label {
        color: #999;
    }
    
    .order-details-card .detail-row .value {
        font-weight: 500;
        color: #333;
    }
    
    .order-items-list {
        margin-top: 12px;
    }
    
    .order-items-list .item {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .order-items-list .item:last-child {
        border-bottom: none;
    }
    
    .refresh-btn {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .refresh-btn:hover {
        background: #5a3d2b;
    }
    .refresh-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .delivery-map {
        height: 200px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        margin-top: 15px;
        background: #f8f6f4;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .delivery-map .placeholder {
        color: #ccc;
        text-align: center;
    }
    
    .delivery-map .placeholder i {
        font-size: 48px;
        display: block;
        margin-bottom: 8px;
    }
    
    @media (max-width: 576px) {
        .track-header {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
        .timeline-item {
            gap: 12px;
        }
        .timeline-item .icon-wrapper {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
        .timeline::before {
            left: 18px;
        }
    }
</style>

<div class="track-container">
    <!-- Header -->
    <div class="track-header">
        <div>
            <div class="order-id">#{{ $order->id }}</div>
            <div style="font-size:13px;color:#999;margin-top:2px;">
                {{ $order->created_at->format('M d, Y h:i A') }}
            </div>
        </div>
        <div>
            <span class="order-status {{ $status }}">
                <i class="fas {{ $statuses[$status]['icon'] ?? 'fa-info-circle' }} me-1"></i>
                {{ $statuses[$status]['label'] ?? ucfirst($status) }}
            </span>
            <button class="refresh-btn ms-2" onclick="refreshStatus()" id="refreshBtn">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Timeline -->
    <div class="timeline">
        @php
            $statusFlow = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed'];
            $currentIndex = array_search($status, $statusFlow);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp
        
        @foreach($statusFlow as $index => $step)
            @php
                $isCompleted = $index < $currentIndex;
                $isActive = $index === $currentIndex;
                $isPending = $index > $currentIndex;
                $iconClass = $isCompleted ? 'completed' : ($isActive ? 'active' : 'pending');
                $titleClass = $isCompleted ? 'completed' : ($isActive ? 'active' : 'inactive');
                $statusLabel = $statuses[$step]['label'] ?? ucfirst($step);
                $icon = $statuses[$step]['icon'] ?? 'fa-circle';
                $color = $statuses[$step]['color'] ?? '#ccc';
                
                $timeText = '';
                if ($isCompleted) {
                    $timeText = 'Completed';
                } elseif ($isActive) {
                    $timeText = 'In progress';
                } else {
                    $timeText = 'Pending';
                }
            @endphp
            <div class="timeline-item">
                <div class="icon-wrapper {{ $iconClass }}" style="border-color: {{ $isActive ? '#6F4E37' : ($isCompleted ? '#28a745' : '#e8e8e8') }}; background: {{ $isActive ? '#6F4E37' : ($isCompleted ? '#d4edda' : '#f5f5f5') }};">
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div class="content">
                    <div class="title {{ $titleClass }}">{{ $statusLabel }}</div>
                    <div class="time">
                        @if($isCompleted)
                            <i class="fas fa-check-circle" style="color:#28a745;"></i>
                            {{ $order->updated_at->format('M d, Y h:i A') }}
                        @elseif($isActive)
                            <i class="fas fa-spinner fa-spin" style="color:#6F4E37;"></i>
                            {{ $timeText }}
                        @else
                            <i class="far fa-clock" style="color:#ccc;"></i>
                            {{ $timeText }}
                        @endif
                    </div>
                    @if($isActive && $step === 'out_for_delivery')
                        <div class="description">
                            <i class="fas fa-truck"></i> Your order is on its way!
                            Estimated delivery: {{ $estimatedTime->format('h:i A') }}
                        </div>
                    @endif
                    @if($isActive && $step === 'ready')
                        <div class="description">
                            <i class="fas fa-store"></i> Your order is ready for pickup
                        </div>
                    @endif
                    @if($isActive && $step === 'preparing')
                        <div class="description">
                            <i class="fas fa-utensils"></i> Our baristas are preparing your order
                        </div>
                    @endif
                    @if($isActive && $step === 'pending')
                        <div class="description">
                            <i class="fas fa-clipboard-check"></i> Order received, waiting for confirmation
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Order Details -->
    <div class="order-details-card">
        <h6 class="mb-2"><i class="fas fa-receipt me-2" style="color:#6F4E37;"></i>Order Details</h6>
        
        <div class="detail-row">
            <span class="label">Branch</span>
            <span class="value">{{ str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name ?? 'N/A') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Delivery Address</span>
            <span class="value">{{ $order->delivery_address ?? 'Pickup' }}</span>
        </div>
        @if($order->discount_rate > 0)
            <div class="detail-row">
                <span class="label">Discount</span>
                <span class="value" style="color:#28a745;">{{ $order->discount_rate }}% (₱{{ number_format($order->discount_amount, 2) }})</span>
            </div>
        @endif
        <div class="detail-row" style="font-weight:700;font-size:16px;color:#6F4E37;border-bottom:2px solid #6F4E37;">
            <span class="label">Total</span>
            <span class="value">₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
        
        <div class="order-items-list">
            <div style="font-weight:600;font-size:13px;color:#999;border-bottom:1px solid #e8e8e8;padding-bottom:4px;margin-bottom:4px;">
                Items
            </div>
            @foreach($order->orders as $item)
                <div class="item">
                    <span>{{ $item->product->name ?? 'Unknown' }} × {{ $item->quantity }}</span>
                    <span>₱{{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    let refreshInterval = null;

    function refreshStatus() {
        const btn = document.getElementById('refreshBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        
        fetch('{{ route("customer.track.status", $order->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show updated status
                    location.reload();
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync"></i> Refresh';
                alert('Error refreshing status. Please try again.');
            });
    }

    // Auto-refresh every 30 seconds
    document.addEventListener('DOMContentLoaded', function() {
        refreshInterval = setInterval(() => {
            fetch('{{ route("customer.track.status", $order->id) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status !== '{{ $status }}') {
                        // Status changed, reload
                        location.reload();
                    }
                })
                .catch(() => {});
        }, 30000);
    });
</script>
@endpush
@endsection