@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 18px 22px;
        border: 1px solid #e8e8e8;
        transition: all 0.2s;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        border-color: #6F4E37;
    }
    .stat-card .stat-icon {
        opacity: 0.15;
        position: absolute;
        right: 12px;
        bottom: 12px;
        font-size: 44px;
        color: #6F4E37;
    }
    .stat-card .stat-number {
        font-size: 26px;
        font-weight: 700;
        color: #2d3436;
        margin-bottom: 2px;
    }
    .stat-card .stat-label {
        font-size: 13px;
        color: #999;
        font-weight: 500;
    }
    .stat-card .stat-change {
        font-size: 12px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 12px;
        display: inline-block;
        margin-top: 4px;
    }
    .stat-change.up { background: #d4edda; color: #28a745; }
    .stat-change.down { background: #f8d7da; color: #dc3545; }
    
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e8e8e8;
        height: 100%;
    }
    .chart-bar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        height: 140px;
        gap: 6px;
        padding-top: 8px;
    }
    .chart-bar-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .chart-bar-item .bar {
        width: 100%;
        max-width: 32px;
        background: linear-gradient(180deg, #6F4E37, #8B6B4A);
        border-radius: 4px 4px 0 0;
        transition: height 0.5s ease;
        min-height: 4px;
    }
    .chart-bar-item .bar-label { font-size: 10px; color: #999; font-weight: 500; }
    .chart-bar-item .bar-value { font-size: 10px; font-weight: 600; color: #6F4E37; }
    
    .activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item .activity-icon {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .activity-item .activity-content { flex: 1; }
    .activity-item .activity-content .activity-title { font-size: 13px; font-weight: 500; }
    .activity-item .activity-content .activity-time { font-size: 11px; color: #999; }
    .activity-item .activity-amount { font-weight: 600; color: #6F4E37; font-size: 14px; }
    
    .low-stock-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .low-stock-item:last-child { border-bottom: none; }
    .low-stock-item .stock-quantity { font-weight: 600; }
    .low-stock-item .stock-quantity.critical { color: #dc3545; }
    .low-stock-item .stock-quantity.warning { color: #ffc107; }
    
    .top-product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .top-product-item:last-child { border-bottom: none; }
    .top-product-item .product-rank {
        font-weight: 700;
        color: #6F4E37;
        margin-right: 10px;
        font-size: 12px;
        width: 20px;
    }
    .top-product-item .product-name { flex: 1; font-weight: 500; }
    .top-product-item .product-sold { font-size: 12px; color: #999; }
    
    .greeting-section {
        background: linear-gradient(135deg, #6F4E37, #8B6B4A);
        border-radius: 12px;
        padding: 20px 25px;
        color: white;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .greeting-section h2 { font-weight: 700; font-size: 22px; margin-bottom: 2px; }
    .greeting-section p { opacity: 0.8; font-size: 14px; margin: 0; }
    .greeting-section .badge-role {
        background: rgba(255,255,255,0.2);
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .offline-banner {
        border-radius: 10px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        border: 1px solid;
    }
    .offline-banner.offline {
        background: #fff3cd;
        border-color: #ffc107;
    }
    .offline-banner.offline .offline-text { color: #856404; }
    .offline-banner.pending {
        background: #cce5ff;
        border-color: #0d6efd;
    }
    .offline-banner.pending .offline-text { color: #004085; }
    .offline-banner .offline-icon {
        font-size: 18px;
        margin-right: 10px;
    }
    .offline-banner .offline-text { font-weight: 500; font-size: 14px; }
    .offline-banner .offline-count {
        background: #dc3545;
        color: white;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .offline-banner .sync-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 6px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s;
        cursor: pointer;
    }
    .offline-banner .sync-btn:hover { background: #1e7e34; }
    .offline-banner .sync-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-badge.synced {
        background: #d4edda;
        color: #155724;
    }
    .status-badge.offline {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<div class="container-fluid">
    <!-- Greeting -->
    <div class="greeting-section">
        <div>
            <h2>Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name }}!</h2>
            <p>Here's what's happening with your business today</p>
        </div>
        <div>
            <span class="badge-role">
                @if($isAdmin) Administrator
                @elseif($isManager) Manager
                @else Staff
                @endif
            </span>
        </div>
    </div>

    <!-- Offline Banner -->
    <div class="offline-banner offline" id="offlineBanner" style="display: none;">
        <div>
            <span class="offline-icon">📡</span>
            <span class="offline-text">You have <strong id="offlineCount">0</strong> pending offline order(s).</span>
        </div>
        <div>
            <button class="sync-btn" id="syncBtn" onclick="syncOfflineOrders()">
                <i class="fas fa-sync me-1"></i> Sync Now
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-coins"></i></div>
                <div class="stat-number">₱{{ number_format($todaySales, 2) }}</div>
                <div class="stat-label">Today's Sales</div>
                <span class="stat-change up">{{ $todayOrders }} orders</span>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number">₱{{ number_format($monthSales, 2) }}</div>
                <div class="stat-label">This Month</div>
                <span class="stat-change up">Monthly revenue</span>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ $totalCustomers }}</div>
                <div class="stat-label">Total Customers</div>
                <span class="stat-change up">Registered</span>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">{{ $pendingOrders }}</div>
                <div class="stat-label">Pending Orders</div>
                <span class="stat-change {{ $pendingOrders > 0 ? 'up' : 'down' }}">
                    {{ $pendingOrders > 0 ? 'In queue' : 'All clear' }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0" style="font-weight:600;"><i class="fas fa-chart-bar me-2"></i>Weekly Sales</h6>
                    <small class="text-muted">Last 7 days</small>
                </div>
                <div class="chart-bar">
                    @foreach($chartData as $data)
                        @php
                            $maxSales = max(array_column($chartData, 'sales')) ?: 1;
                            $height = ($data['sales'] / $maxSales) * 130;
                            $height = max($height, 4);
                        @endphp
                        <div class="chart-bar-item">
                            <div class="bar-value">₱{{ number_format($data['sales'], 0) }}</div>
                            <div class="bar" style="height: {{ $height }}px;"></div>
                            <div class="bar-label">{{ $data['day'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h6 class="mb-3" style="font-weight:600;"><i class="fas fa-crown me-2"></i>Top Selling Products</h6>
                @if($topProducts->count() > 0)
                    @foreach($topProducts as $index => $product)
                        <div class="top-product-item">
                            <span class="product-rank">#{{ $index + 1 }}</span>
                            <span class="product-name">{{ $product->name }}</span>
                            <span class="product-sold">{{ $product->total_sold }} sold</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-3" style="font-size:14px;">No sales data yet</p>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h6 class="mb-3" style="font-weight:600;"><i class="fas fa-bolt me-2"></i>Recent Activity</h6>
                @if($recentSales->count() > 0)
                    @foreach($recentSales as $sale)
                        <div class="activity-item">
                            <div class="activity-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Order #{{ $sale->id }}</div>
                                <div class="activity-time">
                                    {{ $sale->user->name ?? 'Unknown' }} • {{ $sale->sale_date->diffForHumans() }}
                                    @if($sale->branch)
                                        • {{ str_replace('☕ Brew & Bean Co. - ', '', $sale->branch->name) }}
                                    @endif
                                    @if($sale->sync_status === 'pending')
                                        <span class="status-badge offline">Offline</span>
                                    @else
                                        <span class="status-badge synced">Synced</span>
                                    @endif
                                </div>
                            </div>
                            <div class="activity-amount">₱{{ number_format($sale->total_amount, 2) }}</div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-3" style="font-size:14px;">No recent activity</p>
                @endif
            </div>
        </div>

        <!-- Low Stock -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h6 class="mb-3" style="font-weight:600;"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts</h6>
                @if($lowStockItems->count() > 0)
                    @foreach($lowStockItems as $item)
                        <div class="low-stock-item">
                            <span>{{ $item->name }}</span>
                            <span class="stock-quantity {{ $item->stock_quantity <= 2 ? 'critical' : 'warning' }}">
                                {{ number_format($item->stock_quantity) }} / {{ $item->low_stock_threshold }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-3" style="font-size:14px;">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        All stocks are healthy!
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="stat-number" style="font-size:18px;font-weight:600;color:#6F4E37;">{{ $totalBranches }}</div>
                        <div class="stat-label" style="font-size:12px;color:#999;">Branches</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-number" style="font-size:18px;font-weight:600;color:#6F4E37;">{{ $totalProducts }}</div>
                        <div class="stat-label" style="font-size:12px;color:#999;">Products</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-number" style="font-size:18px;font-weight:600;color:#6F4E37;">{{ $todayOrders }}</div>
                        <div class="stat-label" style="font-size:12px;color:#999;">Today's Orders</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isOnline = navigator.onLine;
    let offlineOrders = @json($offlineOrders);
    let offlineCount = {{ $offlinePendingOrders }};

    function updateConnectionStatus() {
        isOnline = navigator.onLine;
        const banner = document.getElementById('offlineBanner');
        const countEl = document.getElementById('offlineCount');
        
        if (!isOnline) {
            banner.style.display = 'flex';
            banner.className = 'offline-banner offline';
            banner.querySelector('.offline-text').innerHTML = '📡 You are <strong>offline</strong>. Orders will be saved locally and synced when back online.';
            countEl.textContent = '0';
        } else if (offlineCount > 0) {
            banner.style.display = 'flex';
            banner.className = 'offline-banner pending';
            banner.querySelector('.offline-text').innerHTML = '🔄 You have <strong>' + offlineCount + '</strong> pending offline order(s). Click "Sync Now" to upload.';
            countEl.textContent = offlineCount;
        } else {
            banner.style.display = 'none';
        }
    }

    function syncOfflineOrders() {
        if (offlineCount === 0) {
            alert('No pending offline orders to sync.');
            return;
        }

        const btn = document.getElementById('syncBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Syncing...';

        fetch('/sync/now', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync me-1"></i> Sync Now';
            
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync me-1"></i> Sync Now';
            alert('Error: ' + error);
        });
    }

    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);

    document.addEventListener('DOMContentLoaded', updateConnectionStatus);
</script>
@endpush
@endsection