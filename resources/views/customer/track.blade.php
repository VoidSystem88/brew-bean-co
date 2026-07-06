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
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .track-header .order-status.pending { background: #fff3cd; color: #856404; }
    .track-header .order-status.preparing { background: #cce5ff; color: #004085; }
    .track-header .order-status.ready { background: #fd7e14; color: white; }
    .track-header .order-status.out_for_delivery { background: #6F4E37; color: white; }
    .track-header .order-status.completed { background: #d4edda; color: #155724; }
    .track-header .order-status.cancelled { background: #f8d7da; color: #721c24; }
    .track-header .order-status.failed { background: #dc3545; color: white; }
    
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
        transition: all 0.3s ease;
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
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .timeline-item .icon-wrapper.completed {
        background: #d4edda;
        color: #28a745;
        transform: scale(1.05);
    }
    
    .timeline-item .icon-wrapper.active {
        background: #6F4E37;
        color: white;
        animation: pulse-icon 1.5s infinite;
    }
    
    @keyframes pulse-icon {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(111, 78, 55, 0.4); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(111, 78, 55, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(111, 78, 55, 0); }
    }
    
    .timeline-item .icon-wrapper.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .timeline-item .icon-wrapper.inactive {
        background: #f5f5f5;
        color: #ccc;
    }
    
    .timeline-item .icon-wrapper.failed {
        background: #f8d7da;
        color: #dc3545;
    }
    
    .timeline-item .content {
        flex: 1;
        padding-top: 6px;
    }
    
    .timeline-item .content .title {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        transition: color 0.3s ease;
    }
    
    .timeline-item .content .time {
        font-size: 12px;
        color: #999;
        margin-top: 2px;
    }
    
    .timeline-item .content .time .timestamp {
        color: #6F4E37;
        font-weight: 500;
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
    
    .timeline-item .content .title.failed {
        color: #dc3545;
    }
    
    .timeline-item .content .title.inactive {
        color: #ccc;
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
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .refresh-btn:hover { background: #5a3d2b; }
    .refresh-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    
    .status-banner {
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    
    .status-banner.completed {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .status-banner.cancelled {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .status-banner.failed {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .status-banner .banner-icon {
        font-size: 24px;
        flex-shrink: 0;
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
        .order-details-card {
            padding: 14px 16px;
        }
        .order-details-card .detail-row {
            font-size: 13px;
            flex-wrap: wrap;
            gap: 4px;
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

    <!-- ===== SUCCESS BANNER FOR COMPLETED ORDERS ===== -->
    @if($status === 'completed')
        <div class="status-banner completed">
            <span class="banner-icon">
                <i class="fas fa-check-circle"></i>
            </span>
            <span>
                 <strong>Order Delivered!</strong>
                @if($order->delivery_completed_at)
                    <br><small>Completed at: {{ \Carbon\Carbon::parse($order->delivery_completed_at)->format('M d, Y h:i A') }}</small>
                @endif
            </span>
        </div>
    @endif

    <!-- ===== CANCELLED/FAILED BANNER ===== -->
    @if(in_array($status, ['cancelled', 'failed']))
        <div class="status-banner {{ $status }}">
            <span class="banner-icon">
                <i class="fas {{ $status === 'cancelled' ? 'fa-times-circle' : 'fa-exclamation-triangle' }}"></i>
            </span>
            <span>
                @if($status === 'cancelled')
                    ❌ This order has been <strong>cancelled</strong>.
                    @if($order->cancellation_reason)
                        <br><small>Reason: {{ $order->cancellation_reason }}</small>
                    @endif
                @else
                    ⚠️ Delivery <strong>failed</strong>.
                    @if($order->delivery_notes)
                        <br><small>Reason: {{ $order->delivery_notes }}</small>
                    @endif
                @endif
            </span>
        </div>
    @endif

    <!-- ===== TIMELINE - FIXED COMPLETED STATUS ===== -->
    <div class="timeline" id="timeline">
        @php
            // ===== FIX: Use 'completed' as the final status =====
            $statusFlow = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed'];
            $currentIndex = array_search($status, $statusFlow);
            if ($currentIndex === false) $currentIndex = 0;
            
            $isCompleted = $status === 'completed';
            $isCancelled = $status === 'cancelled';
            $isFailed = $status === 'failed';
        @endphp
        
        @foreach($statusFlow as $index => $step)
            @php
                // Determine the state of this step
                $isCompletedStep = $index < $currentIndex;
                $isActive = $index === $currentIndex && !$isCancelled && !$isFailed;
                $isPending = $index > $currentIndex || $isCancelled || $isFailed;
                
                // Override for completed orders - mark all steps as completed
                if ($isCompleted) {
                    $isCompletedStep = true;
                    $isActive = false;
                    $isPending = false;
                }
                
                // For cancelled/failed, mark all after current as inactive
                if ($isCancelled || $isFailed) {
                    if ($index <= $currentIndex) {
                        $isCompletedStep = true;
                        $isActive = false;
                        $isPending = false;
                    } else {
                        $isCompletedStep = false;
                        $isActive = false;
                        $isPending = true;
                    }
                }
                
                $iconClass = $isCompletedStep ? 'completed' : ($isActive ? 'active' : ($isCancelled || $isFailed ? 'failed' : 'pending'));
                $titleClass = $isCompletedStep ? 'completed' : ($isActive ? 'active' : ($isCancelled || $isFailed ? 'failed' : 'inactive'));
                $statusLabel = $statuses[$step]['label'] ?? ucfirst($step);
                $icon = $statuses[$step]['icon'] ?? 'fa-circle';
                
                // Determine the time display
                $timeText = '';
                if ($isCompletedStep) {
                    $timeText = '<i class="fas fa-check-circle" style="color:#28a745;"></i> Completed';
                } elseif ($isActive) {
                    $timeText = '<i class="fas fa-spinner fa-spin" style="color:#6F4E37;"></i> In progress';
                } elseif ($isCancelled || $isFailed) {
                    $timeText = '<i class="fas fa-times-circle" style="color:#dc3545;"></i> ' . ($isCancelled ? 'Cancelled' : 'Failed');
                } else {
                    $timeText = '<i class="far fa-clock" style="color:#ccc;"></i> Pending';
                }
            @endphp
            <div class="timeline-item" data-index="{{ $index }}" data-status="{{ $step }}" id="step-{{ $step }}">
                <div class="icon-wrapper {{ $iconClass }}">
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div class="content">
                    <div class="title {{ $titleClass }}">{{ $statusLabel }}</div>
                    <div class="time" id="time-{{ $step }}">
                        {!! $timeText !!}
                        @if($isCompletedStep && $step === 'completed' && $order->delivery_completed_at)
                            <span class="timestamp">
                                at {{ \Carbon\Carbon::parse($order->delivery_completed_at)->format('h:i A') }}
                            </span>
                        @endif
                        @if($isActive && $step === 'ready')
                            <span class="timestamp">(Waiting for pickup)</span>
                        @endif
                        @if($isActive && $step === 'out_for_delivery')
                            <span class="timestamp">(On the way!)</span>
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
            <span class="value">{{ str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name ?? 'N/A') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Delivery Address</span>
            <span class="value">{{ $order->delivery_address ?? 'Pickup' }}</span>
        </div>
        @if($order->delivery_person_id)
            <div class="detail-row">
                <span class="label">Delivery Rider</span>
                <span class="value">
                    <i class="fas fa-user-circle" style="color:#6F4E37;"></i>
                    {{ $order->deliveryPerson->name ?? 'Unknown Rider' }}
                </span>
            </div>
        @endif
        @if($order->discount_rate > 0)
            <div class="detail-row">
                <span class="label">Discount</span>
                <span class="value" style="color:#28a745;">{{ $order->discount_rate }}% (₱{{ number_format($order->discount_amount, 2) }})</span>
            </div>
        @endif
        @if($order->delivery_fee > 0)
            <div class="detail-row">
                <span class="label">Delivery Fee</span>
                <span class="value">₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
        @endif
        <div class="detail-row" style="font-weight:700;font-size:16px;color:#6F4E37;border-bottom:2px solid #6F4E37;">
            <span class="label">Total</span>
            <span class="value">₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
        
        <div class="order-items-list mt-2">
            <div style="font-weight:600;font-size:13px;color:#999;border-bottom:1px solid #e8e8e8;padding-bottom:4px;margin-bottom:4px;">
                Items
            </div>
            @foreach($order->orders as $item)
                <div class="item" style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;border-bottom:1px solid #f5f5f5;">
                    <span>{{ $item->product->name ?? 'Unknown' }} × {{ $item->quantity }}</span>
                    <span>₱{{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>
        
        @if($order->order_notes)
            <div class="mt-2" style="font-size:13px;color:#666;background:#f8f6f4;padding:8px 12px;border-radius:6px;">
                <i class="fas fa-comment me-1" style="color:#6F4E37;"></i>
                <strong>Notes:</strong> {{ $order->order_notes }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    let refreshInterval = null;
    let currentStatus = '{{ $status }}';
    const statusFlow = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed'];
    let isUpdating = false;

    // ============================================================
    // UPDATE TIMELINE - FIXED FOR COMPLETED STATUS
    // ============================================================
    function updateTimeline(status) {
        if (isUpdating) return;
        isUpdating = true;
        
        const currentIndex = statusFlow.indexOf(status);
        const items = document.querySelectorAll('.timeline-item');
        const isCompleted = status === 'completed';
        const isCancelled = status === 'cancelled';
        const isFailed = status === 'failed';
        
        items.forEach((item, index) => {
            const icon = item.querySelector('.icon-wrapper');
            const title = item.querySelector('.title');
            const time = item.querySelector('.time');
            
            // Reset classes
            icon.className = 'icon-wrapper';
            title.className = 'title';
            
            // Determine state
            let isCompletedStep = index < currentIndex;
            let isActive = index === currentIndex && !isCancelled && !isFailed;
            let isPending = index > currentIndex || isCancelled || isFailed;
            
            // If order is completed, mark ALL steps as completed
            if (isCompleted) {
                isCompletedStep = true;
                isActive = false;
                isPending = false;
            }
            
            // For cancelled/failed
            if (isCancelled || isFailed) {
                if (index <= currentIndex) {
                    isCompletedStep = true;
                    isActive = false;
                    isPending = false;
                } else {
                    isCompletedStep = false;
                    isActive = false;
                    isPending = true;
                }
            }
            
            // Apply classes
            if (isCompletedStep) {
                icon.classList.add('completed');
                title.classList.add('completed');
                if (time) {
                    time.innerHTML = '<i class="fas fa-check-circle" style="color:#28a745;"></i> Completed';
                }
            } else if (isActive) {
                icon.classList.add('active');
                title.classList.add('active');
                if (time) {
                    time.innerHTML = '<i class="fas fa-spinner fa-spin" style="color:#6F4E37;"></i> In progress';
                    const step = item.dataset.status;
                    if (step === 'pending') {
                        time.innerHTML += ' <span class="timestamp">(Order placed)</span>';
                    } else if (step === 'preparing') {
                        time.innerHTML += ' <span class="timestamp">(Being prepared)</span>';
                    } else if (step === 'ready') {
                        time.innerHTML += ' <span class="timestamp">(Waiting for pickup)</span>';
                    } else if (step === 'out_for_delivery') {
                        time.innerHTML += ' <span class="timestamp">(On the way!)</span>';
                    }
                }
            } else if (isCancelled) {
                icon.classList.add('failed');
                title.classList.add('failed');
                if (time) {
                    time.innerHTML = '<i class="fas fa-times-circle" style="color:#dc3545;"></i> Cancelled';
                }
            } else if (isFailed) {
                icon.classList.add('failed');
                title.classList.add('failed');
                if (time) {
                    time.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#dc3545;"></i> Failed';
                }
            } else {
                icon.classList.add('pending');
                title.classList.add('inactive');
                if (time) {
                    time.innerHTML = '<i class="far fa-clock" style="color:#ccc;"></i> Pending';
                }
            }
            
            // Animation
            item.classList.add('status-update');
            setTimeout(() => {
                item.classList.remove('status-update');
            }, 600);
        });
        
        // Update status badge
        const badge = document.getElementById('orderStatusBadge');
        if (badge) {
            const statusMap = {
                'pending': { class: 'pending', label: 'Order Placed', icon: 'fa-clipboard-check' },
                'preparing': { class: 'preparing', label: 'Preparing', icon: 'fa-utensils' },
                'ready': { class: 'ready', label: 'Ready for Pickup', icon: 'fa-box' },
                'out_for_delivery': { class: 'out_for_delivery', label: 'Out for Delivery', icon: 'fa-truck' },
                'completed': { class: 'completed', label: 'Delivered ', icon: 'fa-check-circle' },
                'cancelled': { class: 'cancelled', label: 'Cancelled', icon: 'fa-times-circle' },
                'failed': { class: 'failed', label: 'Delivery Failed', icon: 'fa-exclamation-triangle' }
            };
            const info = statusMap[status] || statusMap['pending'];
            badge.className = 'order-status ' + info.class;
            badge.innerHTML = '<i class="fas ' + info.icon + ' me-1"></i> ' + info.label;
        }
        
        isUpdating = false;
    }

    // ============================================================
    // REFRESH STATUS
    // ============================================================
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
                    showStatusChangeNotification(data.status);
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync"></i> Refresh';
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync"></i> Refresh';
            });
    }

    // ============================================================
    // STATUS CHANGE NOTIFICATION
    // ============================================================
    function showStatusChangeNotification(status) {
        const messages = {
            'pending': 'Your order has been placed and is waiting for confirmation.',
            'preparing': 'Your order is now being prepared!',
            'ready': 'Your order is ready for pickup!',
            'out_for_delivery': 'Your order is out for delivery! 🚚',
            'completed': ' Your order has been delivered! Thank you for ordering!',
            'cancelled': 'Your order has been cancelled. ❌',
            'failed': 'Delivery failed. Please contact support.'
        };
        
        const icons = {
            'pending': '📋',
            'preparing': '👨‍🍳',
            'ready': '',
            'out_for_delivery': '🚚',
            'completed': '🎉',
            'cancelled': '❌',
            'failed': '⚠️'
        };
        
        const msg = messages[status] || 'Order status updated.';
        const icon = icons[status] || '📦';
        
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 14px 24px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            max-width: 90%;
            border-left: 4px solid ${status === 'completed' ? '#28a745' : '#6F4E37'};
            animation: slideUp 0.4s ease;
            font-family: inherit;
        `;
        
        notification.innerHTML = `
            <span style="font-size: 24px;">${icon}</span>
            <span style="flex:1;color:#333;">${msg}</span>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#999;cursor:pointer;font-size:18px;">✕</button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) notification.remove();
                }, 300);
            }
        }, 5000);
    }

    // ============================================================
    // INIT
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        updateTimeline(currentStatus);
        refreshInterval = setInterval(refreshStatus, 20000);
        
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshStatus();
            }
        });
    });
    
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    });
</script>
@endpush
@endsection