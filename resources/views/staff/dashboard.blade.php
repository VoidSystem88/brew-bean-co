@extends('layouts.app')

@section('page-title', 'Staff Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 18px 22px;
        border: 1px solid #e8e8e8;
        transition: all 0.2s;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #6F4E37;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .stat-card .stat-number {
        font-size: 26px;
        font-weight: 700;
        color: #2d3436;
    }
    .stat-card .stat-label {
        font-size: 13px;
        color: #999;
        font-weight: 500;
    }
    
    .transfer-alert {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .transfer-alert .alert-icon {
        font-size: 24px;
        color: #856404;
        margin-right: 12px;
    }
    .transfer-alert .alert-text {
        font-weight: 500;
        color: #856404;
    }
    .transfer-alert .alert-count {
        background: #dc3545;
        color: white;
        padding: 2px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 16px;
    }
    .transfer-alert .btn-view {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 6px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
    }
    .transfer-alert .btn-view:hover {
        background: #5a3d2b;
        color: white;
    }
    .transfer-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .transfer-item:last-child { border-bottom: none; }
    .transfer-item .item-name { font-weight: 500; }
    .transfer-item .item-qty { color: #6F4E37; font-weight: 600; }
    .transfer-item .item-from { color: #999; font-size: 12px; }
    
    .activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: 0.2s;
        position: relative;
    }
    .activity-item:hover {
        background: #f8f6f4;
        margin: 0 -10px;
        padding-left: 10px;
        padding-right: 10px;
        border-radius: 6px;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .activity-item .activity-content { flex: 1; min-width: 0; }
    .activity-item .activity-content .activity-title { font-size: 14px; font-weight: 500; }
    .activity-item .activity-content .activity-time { font-size: 12px; color: #999; }
    .activity-item .activity-amount { font-weight: 600; color: #6F4E37; font-size: 14px; flex-shrink: 0; }
    
    /* Tooltip for order items */
    .order-tooltip {
        display: none;
        position: absolute;
        background: white;
        border: 1px solid #e8e8e8;
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        z-index: 100;
        min-width: 200px;
        top: 100%;
        left: 0;
        margin-top: 4px;
    }
    .activity-item:hover .order-tooltip {
        display: block;
    }
    .order-tooltip .tooltip-item {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .order-tooltip .tooltip-item:last-child { border-bottom: none; }
    .order-tooltip .tooltip-total {
        font-weight: 600;
        color: #6F4E37;
        margin-top: 6px;
        padding-top: 6px;
        border-top: 2px solid #eee;
    }
    
    .sync-badge {
        font-size: 10px;
        padding: 1px 8px;
        border-radius: 10px;
        font-weight: 600;
        display: inline-block;
    }
    .sync-badge.synced {
        background: #d4edda;
        color: #155724;
    }
    .sync-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    .sync-badge.offline {
        background: #f8d7da;
        color: #721c24;
    }
    
    .greeting-section {
        background: linear-gradient(135deg, #6F4E37, #8B6B4A);
        border-radius: 12px;
        padding: 20px 25px;
        color: white;
        margin-bottom: 20px;
    }
    .greeting-section h2 {
        font-weight: 700;
        font-size: 22px;
        margin-bottom: 2px;
    }
    .greeting-section p {
        opacity: 0.8;
        font-size: 14px;
        margin: 0;
    }
</style>

<div class="container-fluid">
    <!-- Greeting -->
    <div class="greeting-section">
        <h2>👋 Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name }}!</h2>
        <p>Your branch: <strong>{{ str_replace('☕ Brew & Bean Co. - ', '', Auth::user()->branch->name ?? 'N/A') }}</strong></p>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">₱{{ number_format($todaySales, 2) }}</div>
                <div class="stat-label">Today's Sales</div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">{{ $todayOrders }}</div>
                <div class="stat-label">Today's Orders</div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">{{ $pendingOrders }}</div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
    </div>

    <!-- Pending Transfers Alert -->
    @if($pendingTransfersCount > 0)
        <div class="transfer-alert">
            <div>
                <span class="alert-icon">📦</span>
                <span class="alert-text">
                    You have <strong>{{ $pendingTransfersCount }}</strong> pending delivery transfer(s)
                </span>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="alert-count">{{ $pendingTransfersCount }}</span>
                <a href="{{ route('delivery.index') }}" class="btn-view">
                    <i class="fas fa-eye me-1"></i> View Transfers
                </a>
            </div>
        </div>
    @endif

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Recent Activity</h6>
            <span class="text-muted" style="font-size: 12px;">Hover over order to see items</span>
        </div>
        <div class="card-body">
            @if($recentOrders->count() > 0)
                @foreach($recentOrders as $order)
                    <div class="activity-item">
                        <div class="activity-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                Order #{{ $order->id }}
                                @if($order->is_offline)
                                    <span class="sync-badge pending">📡 Offline</span>
                                @else
                                    <span class="sync-badge synced">✅ Synced</span>
                                @endif
                            </div>
                            <div class="activity-time">
                                {{ $order->user->name ?? 'Staff' }} • {{ $order->sale_date->diffForHumans() }}
                                @if($order->branch)
                                    • {{ str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name) }}
                                @endif
                            </div>
                            <!-- Tooltip for order items -->
                            <div class="order-tooltip">
                                @foreach($order->items as $item)
                                    <div class="tooltip-item">
                                        <span>{{ $item->product->name ?? 'Unknown' }}</span>
                                        <span>×{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                                <div class="tooltip-total">
                                    Total: ₱{{ number_format($order->total_amount, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="activity-amount">₱{{ number_format($order->total_amount, 2) }}</div>
                    </div>
                @endforeach
            @else
                <p class="text-muted text-center py-3" style="font-size: 14px;">No recent orders</p>
            @endif
        </div>
    </div>

    <!-- Pending Transfers List -->
    @if($pendingTransfersCount > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Pending Deliveries</h6>
            </div>
            <div class="card-body">
                @foreach($pendingTransfers->take(5) as $transfer)
                    <div class="transfer-item">
                        <div>
                            <div class="item-name">{{ $transfer->item->name ?? 'Unknown' }}</div>
                            <div class="item-from">
                                <i class="fas fa-warehouse me-1"></i>
                                From: {{ str_replace('☕ Brew & Bean Co. - ', '', $transfer->fromBranch->name ?? 'Warehouse') }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="item-qty">{{ number_format($transfer->quantity, 2) }}</div>
                            <div class="item-from">{{ $transfer->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
                @if($pendingTransfersCount > 5)
                    <div class="text-center mt-2">
                        <a href="{{ route('delivery.index') }}" class="text-muted">
                            +{{ $pendingTransfersCount - 5 }} more transfers
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>