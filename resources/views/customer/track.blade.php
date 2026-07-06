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
    
    .timeline-item .content .title.completed {
        color: #28a745;
    }
    
    .timeline-item .content .title.active {
        color: #6F4E37;
    }
    
    .timeline-item .content .title.pending {
        color: #856404;
    }
    
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
    .refresh-btn:hover { background: #5a3d2b; }
    .refresh-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    
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
            <span class="order-status {{ $status }}" id="orderStatusBadge">
                <i class="fas {{ $statuses[$status]['icon'] ?? 'fa-info-circle' }} me-1"></i>
                {{ $statuses[$status]['label'] ?? ucfirst($status) }}
            </span>
            <button class="refresh-btn ms-2" onclick="refreshStatus()" id="refreshBtn">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Timeline -->
    <div class="timeline" id="timeline">
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
            @endphp
            <div class="timeline-item" data-index="{{ $index }}" data-status="{{ $step }}">
                <div class="icon-wrapper {{ $iconClass }}">
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div class="content">
                    <div class="title {{ $titleClass }}">{{ $statusLabel }}</div>
                    <div class="time" id="time-{{ $step }}">
                        @if($isCompleted)
                            <i class="fas fa-check-circle" style="color:#28a745;"></i>
                            Completed
                        @elseif($isActive)
                            <i class="fas fa-spinner fa-spin" style="color:#6F4E37;"></i>
                            In progress
                        @else
                            <i class="far fa-clock" style="color:#ccc;"></i>
                            Pending
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Order Details -->
    <div class="order-details-card">
        <h6 class="mb-2"><i class="fas fa-receipt me-2" style="color:#6F4E37;"></i>Order Details</h6>
        
        <div class="detail-row">
            <span class="label">Branch</span>
            <span class="value">{{ str_replace('â˜• Brew & Bean Co. - ', '', $order->branch->name ?? 'N/A') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Delivery Address</span>
            <span class="value">{{ $order->delivery_address ?? 'Pickup' }}</span>
        </div>
        @if($order->discount_rate > 0)
            <div class="detail-row">
                <span class="label">Discount</span>
                <span class="value" style="color:#28a745;">{{ $order->discount_rate }}% (â‚±{{ number_format($order->discount_amount, 2) }})</span>
            </div>
        @endif
        <div class="detail-row" style="font-weight:700;font-size:16px;color:#6F4E37;border-bottom:2px solid #6F4E37;">
            <span class="label">Total</span>
            <span class="value">â‚±{{ number_format($order->total_amount, 2) }}</span>
        </div>
        
        <div class="order-items-list">
            <div style="font-weight:600;font-size:13px;color:#999;border-bottom:1px solid #e8e8e8;padding-bottom:4px;margin-bottom:4px;">
                Items
            </div>
            @foreach($order->orders as $item)
                <div class="item" style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;border-bottom:1px solid #f5f5f5;">
                    <span>{{ $item->product->name ?? 'Unknown' }} Ã— {{ $item->quantity }}</span>
                    <span>â‚±{{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    let refreshInterval = null;
    let currentStatus = '{{ $status }}';
    const statusFlow = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed'];

    function updateTimeline(status) {
        const currentIndex = statusFlow.indexOf(status);
        const items = document.querySelectorAll('.timeline-item');
        
        items.forEach((item, index) => {
            const icon = item.querySelector('.icon-wrapper');
            const title = item.querySelector('.title');
            const time = item.querySelector('.time');
            
            // Reset classes
            icon.className = 'icon-wrapper';
            title.className = 'title';
            
            if (index < currentIndex) {
                // Completed
                icon.classList.add('completed');
                title.classList.add('completed');
                if (time) {
                    time.innerHTML = '<i class="fas fa-check-circle" style="color:#28a745;"></i> Completed';
                }
            } else if (index === currentIndex) {
                // Active - In Progress
                icon.classList.add('active');
                title.classList.add('active');
                if (time) {
                    time.innerHTML = '<i class="fas fa-spinner fa-spin" style="color:#6F4E37;"></i> In progress';
                }
            } else {
                // Pending
                icon.classList.add('pending');
                title.classList.add('inactive');
                if (time) {
                    time.innerHTML = '<i class="far fa-clock" style="color:#ccc;"></i> Pending';
                }
            }
        });
        
        // Update status badge
        const badge = document.getElementById('orderStatusBadge');
        if (badge) {
            const statusMap = {
                'pending': { class: 'pending', label: 'Order Placed', icon: 'fa-clipboard-check' },
                'preparing': { class: 'preparing', label: 'Preparing', icon: 'fa-utensils' },
                'ready': { class: 'ready', label: 'Ready for Pickup', icon: 'fa-box' },
                'out_for_delivery': { class: 'out_for_delivery', label: 'Out for Delivery', icon: 'fa-truck' },
                'completed': { class: 'completed', label: 'Delivered', icon: 'fa-check-circle' },
                'cancelled': { class: 'cancelled', label: 'Cancelled', icon: 'fa-times-circle' }
            };
            const info = statusMap[status] || statusMap['pending'];
            badge.className = 'order-status ' + info.class;
            badge.innerHTML = '<i class="fas ' + info.icon + ' me-1"></i> ' + info.label;
        }
    }

    function refreshStatus() {
        const btn = document.getElementById('refreshBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        
        fetch('{{ route("customer.track.status", $order->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status !== currentStatus) {
                    currentStatus = data.status;
                    updateTimeline(currentStatus);
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync"></i> Refresh';
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync"></i> Refresh';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initial update
        updateTimeline(currentStatus);
        
        // Auto-refresh every 30 seconds
        refreshInterval = setInterval(refreshStatus, 30000);
    });
</script>
@endpush
@endsection