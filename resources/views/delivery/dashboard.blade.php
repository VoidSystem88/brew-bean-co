@extends('layouts.app')

@section('page-title', 'My Deliveries')

@section('content')
<style>
    * { box-sizing: border-box; }
    
    .delivery-dashboard {
        max-width: 480px;
        margin: 0 auto;
        padding: 0 8px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid #e8e8e8;
        text-align: center;
        transition: 0.2s;
    }
    .stat-card .number {
        font-size: 22px;
        font-weight: 700;
        color: #6F4E37;
        line-height: 1.2;
    }
    .stat-card .number .currency {
        font-size: 14px;
    }
    .stat-card .label {
        font-size: 11px;
        color: #999;
        margin-top: 2px;
        font-weight: 500;
    }
    .stat-card .icon {
        font-size: 18px;
        display: block;
        margin-bottom: 4px;
        color: #6F4E37;
        opacity: 0.5;
    }
    .stat-card.earnings {
        background: linear-gradient(135deg, #6F4E37, #8B6B4A);
        border-color: #6F4E37;
    }
    .stat-card.earnings .number,
    .stat-card.earnings .label,
    .stat-card.earnings .icon {
        color: white;
        opacity: 1;
    }
    .stat-card .badge-commission {
        font-size: 9px;
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 1px 8px;
        border-radius: 10px;
        display: inline-block;
        margin-top: 2px;
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 12px 0 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .section-title .badge-count {
        font-size: 10px;
        background: #6F4E37;
        color: white;
        padding: 0 10px;
        border-radius: 10px;
        font-weight: 600;
        margin-left: auto;
    }
    
    .delivery-card {
        background: white;
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid #e8e8e8;
        margin-bottom: 8px;
        transition: 0.2s;
    }
    .delivery-card:active { transform: scale(0.98); }
    .delivery-card .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }
    .delivery-card .order-id {
        font-weight: 700;
        font-size: 14px;
        color: #6F4E37;
    }
    .delivery-card .order-customer {
        font-size: 13px;
        font-weight: 500;
        color: #333;
    }
    .delivery-card .order-address {
        font-size: 12px;
        color: #666;
        margin: 4px 0;
        display: flex;
        align-items: flex-start;
        gap: 4px;
    }
    .delivery-card .order-address i {
        color: #6F4E37;
        margin-top: 2px;
        flex-shrink: 0;
    }
    .delivery-card .order-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .status-badge {
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .status-badge.assigned { background: #cce5ff; color: #004085; }
    .status-badge.picked_up { background: #d1ecf1; color: #0c5460; }
    .status-badge.in_transit { background: #d4edda; color: #155724; }
    .status-badge.completed { background: #28a745; color: white; }
    .status-badge.failed { background: #dc3545; color: white; }
    
    .btn-group-actions {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    .btn-status {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: 0.2s;
        flex: 1;
        min-width: 60px;
        text-align: center;
    }
    .btn-status:active { transform: scale(0.95); }
    .btn-status.pickup { background: #17a2b8; color: white; }
    .btn-status.transit { background: #ffc107; color: #333; }
    .btn-status.deliver { background: #28a745; color: white; }
    .btn-status.fail { background: #dc3545; color: white; }
    .btn-status:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    .btn-status.navigate {
        background: #6F4E37;
        color: white;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        flex: 0 1 auto;
    }
    .btn-status.navigate:hover { background: #5a3d2b; color: white; }
    
    .empty-state {
        text-align: center;
        padding: 30px 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
    }
    .empty-state i {
        font-size: 40px;
        color: #ddd;
        display: block;
        margin-bottom: 8px;
    }
    .empty-state h5 {
        font-size: 16px;
        color: #333;
        margin: 0;
    }
    .empty-state p {
        font-size: 13px;
        color: #999;
        margin: 4px 0 0;
    }
    
    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .history-item:last-child { border-bottom: none; }
    .history-item .history-info .history-order {
        font-weight: 500;
        color: #333;
    }
    .history-item .history-info .history-date {
        font-size: 11px;
        color: #999;
    }
    .history-item .history-amount {
        font-weight: 600;
        color: #28a745;
    }
    .history-item .history-earnings {
        font-size: 11px;
        color: #6F4E37;
        font-weight: 500;
    }
    
    @media (max-width: 400px) {
        .stats-grid { gap: 6px; }
        .stat-card .number { font-size: 18px; }
        .stat-card { padding: 10px 8px; }
        .delivery-card { padding: 10px 12px; }
        .btn-status { font-size: 10px; padding: 3px 8px; min-width: 50px; }
    }
</style>

<div class="delivery-dashboard">

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card earnings">
            <span class="icon"><i class="fas fa-wallet"></i></span>
            <div class="number">
                <span class="currency">â‚±</span>{{ number_format($completedMonthEarnings, 2) }}
            </div>
            <div class="label">This Month</div>
            <span class="badge-commission">{{ $commissionRate }}% commission</span>
        </div>
        <div class="stat-card">
            <span class="icon"><i class="fas fa-box"></i></span>
            <div class="number">{{ $assignedOrders->count() }}</div>
            <div class="label">Assigned</div>
        </div>
        <div class="stat-card">
            <span class="icon"><i class="fas fa-check-circle"></i></span>
            <div class="number">{{ $completedTodayCount }}</div>
            <div class="label">Today</div>
        </div>
        <div class="stat-card">
            <span class="icon"><i class="fas fa-coins"></i></span>
            <div class="number"><span class="currency">â‚±</span>{{ number_format($completedTodayEarnings, 2) }}</div>
            <div class="label">Today's Earnings</div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:4px;margin-bottom:12px;font-size:11px;background:white;border-radius:12px;padding:8px 10px;border:1px solid #e8e8e8;">
        <div style="text-align:center;">
            <div style="font-weight:700;color:#6F4E37;">{{ $completedWeekCount }}</div>
            <div style="color:#999;">This Week</div>
            <div style="font-size:10px;color:#28a745;">â‚±{{ number_format($completedWeekEarnings, 2) }}</div>
        </div>
        <div style="text-align:center;border-left:1px solid #eee;border-right:1px solid #eee;">
            <div style="font-weight:700;color:#6F4E37;">{{ $completedMonthCount }}</div>
            <div style="color:#999;">This Month</div>
            <div style="font-size:10px;color:#28a745;">â‚±{{ number_format($completedMonthEarnings, 2) }}</div>
        </div>
        <div style="text-align:center;">
            <div style="font-weight:700;color:#6F4E37;">{{ $totalDeliveries }}</div>
            <div style="color:#999;">Total</div>
            <div style="font-size:10px;color:#28a745;">â‚±{{ number_format($totalEarnings, 2) }}</div>
        </div>
    </div>

    <!-- Assigned Orders -->
    <div class="section-title">
        <i class="fas fa-truck" style="color:#6F4E37;"></i> Assigned Orders
        <span class="badge-count">{{ $assignedOrders->count() }}</span>
    </div>

    @if($assignedOrders->count() > 0)
        @foreach($assignedOrders as $order)
            <div class="delivery-card">
                <div class="order-header">
                    <span class="order-id">#{{ $order->id }}</span>
                    <span class="status-badge {{ $order->delivery_status }}">
                        {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                    </span>
                </div>
                <div class="order-customer">
                    <i class="fas fa-user" style="color:#6F4E37;width:16px;"></i>
                    {{ $order->customer->name ?? 'Unknown' }}
                </div>
                <div class="order-address">
                    <i class="fas fa-map-pin"></i>
                    <span>{{ Str::limit($order->delivery_address, 50) }}</span>
                </div>
                <div class="order-meta">
                    <span style="font-size:12px;color:#999;">
                        <i class="fas fa-clock"></i> {{ $order->created_at->diffForHumans() }}
                    </span>
                    <span style="font-weight:600;color:#6F4E37;font-size:14px;">
                        â‚±{{ number_format($order->total_amount, 2) }}
                    </span>
                </div>
                <div class="order-meta" style="margin-top:6px;border-top:1px solid #f5f5f5;padding-top:6px;">
                    <div class="btn-group-actions" style="width:100%;">
                        @if($order->delivery_status == 'assigned')
                            <button class="btn-status pickup" onclick="updateStatus({{ $order->id }}, 'picked_up')">
                                <i class="fas fa-box"></i> Pick Up
                            </button>
                        @endif
                        @if($order->delivery_status == 'picked_up')
                            <button class="btn-status transit" onclick="updateStatus({{ $order->id }}, 'in_transit')">
                                <i class="fas fa-truck"></i> Transit
                            </button>
                        @endif
                        @if($order->delivery_status == 'in_transit')
                            <button class="btn-status deliver" onclick="updateStatus({{ $order->id }}, 'completed')">
                                <i class="fas fa-check"></i> Delivered
                            </button>
                            <button class="btn-status fail" onclick="updateStatus({{ $order->id }}, 'failed')">
                                <i class="fas fa-times"></i> Failed
                            </button>
                        @endif
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($order->delivery_address) }}" 
                           target="_blank" 
                           class="btn-status navigate">
                            <i class="fas fa-directions"></i> Navigate
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-check-circle" style="color:#28a745;"></i>
            <h5>No Assigned Orders</h5>
            <p>You'll be notified when orders are assigned to you.</p>
        </div>
    @endif

    <!-- Recent Completed -->
    @if($recentCompleted->count() > 0)
        <div class="section-title" style="margin-top:16px;">
            <i class="fas fa-history" style="color:#6F4E37;"></i> Recent Deliveries
            <span class="badge-count">{{ $recentCompleted->count() }}</span>
        </div>
        @foreach($recentCompleted as $order)
            <div class="history-item">
                <div class="history-info">
                    <div class="history-order">#{{ $order->id }} - {{ $order->customer->name ?? 'Unknown' }}</div>
                    <div class="history-date">{{ $order->delivery_completed_at ? \Carbon\Carbon::parse($order->delivery_completed_at)->format('M d, h:i A') : 'N/A' }}</div>
                </div>
                <div style="text-align:right;">
                    <div class="history-amount">â‚±{{ number_format($order->total_amount, 2) }}</div>
                    <div class="history-earnings">+â‚±{{ number_format($order->total_amount * 0.10, 2) }}</div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Commission Info -->
    <div style="margin-top:16px;text-align:center;font-size:11px;color:#999;border-top:1px solid #e8e8e8;padding-top:12px;">
        <i class="fas fa-info-circle"></i>
        You earn <strong>{{ $commissionRate }}%</strong> commission per successful delivery
    </div>

</div>

@push('scripts')
<script>
function updateStatus(saleId, status) {
    const labels = {
        'picked_up': 'Pick up this order?',
        'in_transit': 'Mark as in transit?',
        'completed': 'Confirm delivery?',
        'failed': 'Mark as failed?'
    };
    
    let msg = labels[status] || 'Update this order?';
    let reason = null;
    
    if (status === 'failed') {
        reason = prompt('Reason for delivery failure:');
        if (reason === null) return;
    }
    
    if (!confirm(msg)) return;

    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch('/delivery-person/update-status/' + saleId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: status,
            notes: reason || null
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        
        if (data.success) {
            alert('âœ… ' + data.message);
            location.reload();
        } else {
            alert('âŒ ' + data.message);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert('Error: ' + error);
    });
}
</script>
@endpush
@endsection