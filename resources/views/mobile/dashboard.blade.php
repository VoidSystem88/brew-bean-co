<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Brew & Bean Co.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#6F4E37">
    <link rel="manifest" href="/manifest.json">
    
    <title>Brew & Bean Co. · Pulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand: #6F4E37;
            --brand-light: #8B6B55;
            --brand-dark: #4A3228;
            --gold: #C9A96E;
            --cream: #FDF8F3;
            --bg: #F5EDE6;
            --card-shadow: 0 8px 30px rgba(74, 50, 40, 0.08);
            --radius: 16px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: #2D1F1A;
            padding-bottom: 80px;
            min-height: 100vh;
        }
        
        .topbar {
            background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%);
            color: white;
            padding: 16px 20px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 2px solid var(--gold);
        }
        .topbar .brand { display: flex; align-items: center; gap: 10px; }
        .topbar .brand .logo { font-size: 24px; }
        .topbar .brand h1 { font-size: 16px; font-weight: 700; margin: 0; }
        .topbar .brand small { font-size: 9px; opacity: 0.7; font-weight: 400; display: block; margin-top: -2px; }
        .topbar .time-display {
            background: rgba(255,255,255,0.12);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .greeting { padding: 16px 20px 6px; }
        .greeting h2 { font-size: 20px; font-weight: 700; color: var(--brand-dark); margin: 0; }
        .greeting h2 span { color: var(--gold); }
        .greeting p { font-size: 13px; color: #8B7355; margin: 2px 0 0; }
        
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 12px 16px 8px; }
        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 14px 14px 12px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.12);
            position: relative;
            overflow: hidden;
            transition: 0.2s;
        }
        .stat-card:active { transform: scale(0.97); }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            border-radius: 0 0 4px 4px;
        }
        .stat-card.sales::after { background: linear-gradient(90deg, #4ade80, #22c55e); }
        .stat-card.orders::after { background: linear-gradient(90deg, #60a5fa, #3b82f6); }
        .stat-card.stock::after { background: linear-gradient(90deg, #facc15, #f59e0b); }
        .stat-card.customers::after { background: linear-gradient(90deg, #a78bfa, #8b5cf6); }
        .stat-card .icon-wrap {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 14px; margin-bottom: 6px;
        }
        .stat-card.sales .icon-wrap { background: #dcfce7; color: #16a34a; }
        .stat-card.orders .icon-wrap { background: #dbeafe; color: #2563eb; }
        .stat-card.stock .icon-wrap { background: #fef3c7; color: #d97706; }
        .stat-card.customers .icon-wrap { background: #ede9fe; color: #7c3aed; }
        .stat-card .number { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; line-height: 1.1; color: var(--brand-dark); }
        .stat-card .number .currency { font-size: 14px; font-weight: 600; color: #8B7355; }
        .stat-card .label { font-size: 11px; color: #8B7355; font-weight: 500; margin-top: 2px; }
        .stat-card .sub { font-size: 10px; color: #a08b7a; margin-top: 2px; }
        .stat-card .trend {
            font-size: 10px;
            font-weight: 600;
            padding: 1px 8px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 2px;
        }
        .trend.up { background: #dcfce7; color: #16a34a; }
        .trend.down { background: #fee2e2; color: #dc2626; }
        
        .section { padding: 8px 16px 4px; }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .section-header h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--brand-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .section-header h3 i { color: var(--gold); }
        .section-header .badge-count {
            background: var(--brand);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 10px;
            border-radius: 12px;
        }
        
        .branch-card {
            background: white;
            border-radius: var(--radius);
            padding: 14px 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.08);
        }
        .branch-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0ebe6;
        }
        .branch-item:last-child { border-bottom: none; padding-bottom: 0; }
        .branch-item .left { display: flex; align-items: center; gap: 8px; }
        .branch-item .left .b-icon {
            width: 28px; height: 28px; border-radius: 8px; background: var(--cream);
            display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--brand);
        }
        .branch-item .left .name { font-weight: 500; font-size: 13px; color: var(--brand-dark); }
        .branch-item .right .amount { font-weight: 700; font-size: 14px; color: var(--brand-dark); }
        .branch-item .right .count { font-size: 10px; color: #a08b7a; }
        
        .critical-item {
            background: white;
            border-radius: var(--radius);
            padding: 12px 16px;
            margin-bottom: 8px;
            box-shadow: var(--card-shadow);
            border-left: 4px solid #ef4444;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
        }
        .critical-item:active { transform: scale(0.98); }
        .critical-item .info .name { font-weight: 600; font-size: 13px; color: var(--brand-dark); }
        .critical-item .info .detail { font-size: 11px; color: #8B7355; margin-top: 1px; }
        .critical-item .stock-badge {
            background: #fee2e2; color: #dc2626; font-weight: 700; font-size: 13px;
            padding: 3px 12px; border-radius: 20px; display: flex; align-items: center; gap: 4px;
        }
        
        .order-item {
            background: white;
            border-radius: var(--radius);
            padding: 10px 14px;
            margin-bottom: 6px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-item .order-info .order-id { font-weight: 600; font-size: 13px; color: var(--brand-dark); }
        .order-item .order-info .order-meta { font-size: 10px; color: #a08b7a; }
        .order-item .order-total { font-weight: 700; font-size: 14px; color: var(--brand); }
        .order-status-badge {
            font-size: 9px;
            padding: 1px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        .order-status-badge.pending { background: #fff3cd; color: #856404; }
        .order-status-badge.preparing { background: #dbeafe; color: #2563eb; }
        .order-status-badge.ready { background: #d4edda; color: #155724; }
        .order-status-badge.completed { background: #d1ecf1; color: #0c5460; }
        
        .product-rank-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
        }
        .product-rank-item:last-child { border-bottom: none; }
        .product-rank-item .rank {
            font-weight: 700;
            color: var(--brand);
            font-size: 12px;
            width: 24px;
        }
        .product-rank-item .name { flex: 1; font-weight: 500; color: #333; }
        .product-rank-item .sold { font-size: 11px; color: #999; }
        
        .alert-item {
            background: white;
            border-radius: var(--radius);
            padding: 10px 14px;
            margin-bottom: 6px;
            box-shadow: var(--card-shadow);
            border-left: 4px solid var(--brand);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-item .alert-icon {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;
        }
        .alert-item .alert-content { flex: 1; }
        .alert-item .alert-content .alert-title { font-weight: 600; font-size: 13px; color: #333; }
        .alert-item .alert-content .alert-message { font-size: 11px; color: #8B7355; }
        .alert-item .alert-time { font-size: 10px; color: #a08b7a; flex-shrink: 0; }
        
        .empty-state {
            background: white; border-radius: var(--radius); padding: 24px 16px;
            text-align: center; box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.06);
        }
        .empty-state i { font-size: 32px; color: #4ade80; margin-bottom: 6px; display: block; }
        .empty-state h5 { font-size: 14px; font-weight: 600; color: var(--brand-dark); margin: 0; }
        .empty-state p { color: #8B7355; font-size: 12px; margin: 2px 0 0; }
        
        .install-banner {
            background: var(--brand-dark);
            color: white;
            padding: 10px 16px;
            display: none;
            align-items: center;
            justify-content: space-between;
            border-radius: var(--radius);
            margin: 8px 16px 4px;
            box-shadow: 0 4px 20px rgba(74, 50, 40, 0.2);
        }
        .install-banner.show { display: flex; }
        .install-banner .left { display: flex; align-items: center; gap: 10px; }
        .install-banner .left .icon { font-size: 24px; }
        .install-banner .left .text { font-size: 12px; font-weight: 500; }
        .install-banner .left .text small { font-weight: 400; opacity: 0.7; display: block; font-size: 10px; }
        .install-banner .btn-install {
            background: var(--gold);
            color: var(--brand-dark);
            border: none;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(201, 169, 110, 0.15);
            display: flex;
            justify-content: space-around;
            padding: 6px 0 env(safe-area-inset-bottom, 6px);
            z-index: 100;
            max-width: 480px;
            margin: 0 auto;
        }
        .bottom-nav .nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: none;
            border: none;
            color: #b0a094;
            font-size: 9px;
            font-weight: 500;
            padding: 2px 12px;
            cursor: pointer;
            text-decoration: none;
            gap: 1px;
            position: relative;
        }
        .bottom-nav .nav-btn i { font-size: 18px; }
        .bottom-nav .nav-btn.active { color: var(--brand); }
        .bottom-nav .nav-btn .badge-dot {
            position: absolute;
            top: 0;
            right: 2px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid white;
        }
        
        @media (max-width: 420px) {
            .stats-grid { gap: 8px; padding: 8px 12px 4px; }
            .stat-card { padding: 10px 10px 8px; }
            .stat-card .number { font-size: 18px; }
            .greeting { padding: 12px 14px 4px; }
            .greeting h2 { font-size: 16px; }
            .section { padding: 4px 12px 2px; }
            .stat-card .icon-wrap { width: 26px; height: 26px; font-size: 12px; }
        }
        @media (min-width: 600px) {
            body { max-width: 480px; margin: 0 auto; background: #e8ddd4; }
            .bottom-nav { border-radius: 20px 20px 0 0; }
        }
        
        .pulse-refresh {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: var(--brand);
            color: white;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 4px 16px rgba(74, 50, 40, 0.25);
            cursor: pointer;
            transition: 0.2s;
            z-index: 50;
        }
        .pulse-refresh:active { transform: scale(0.9); }
        .pulse-refresh i { font-size: 18px; }
        .pulse-refresh.spinning i { animation: spin 1s linear; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <header class="topbar">
        <div class="brand">
            <span class="logo">☕</span>
            <div>
                <h1>Brew & Bean Co.</h1>
                <small>Pulse · {{ now()->format('M d, Y') }}</small>
            </div>
        </div>
        <div class="time-display" id="liveTime">{{ now()->format('h:i A') }}</div>
    </header>

    <!-- Greeting -->
    <div class="greeting">
        <h2>Good {{ \Carbon\Carbon::now()->format('A') == 'AM' ? 'Morning' : 'Afternoon' }}, <span>{{ auth()->user()->name ?? 'Owner' }}</span></h2>
        <p><i class="fas fa-store me-1" style="color:var(--gold);"></i> {{ auth()->user()->branch->name ?? 'All Branches' }}</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card sales">
            <div class="icon-wrap"><i class="fas fa-coins"></i></div>
            <div class="number"><span class="currency">₱</span>{{ number_format($todaySales ?? 0, 0) }}</div>
            <div class="label">Today's Sales</div>
            <div class="sub">{{ $todayCount ?? 0 }} transactions</div>
        </div>
        <div class="stat-card orders">
            <div class="icon-wrap"><i class="fas fa-clock"></i></div>
            <div class="number">{{ $pendingOrders ?? 0 }}</div>
            <div class="label">Pending Orders</div>
            <div class="sub">Waiting for preparation</div>
        </div>
        <div class="stat-card stock">
            <div class="icon-wrap"><i class="fas fa-boxes"></i></div>
            <div class="number">{{ $lowStockCount ?? 0 }}</div>
            <div class="label">Low Stock Items</div>
            @if(($lowStockCount ?? 0) > 0)
                <span class="trend down">⚠️ Needs action</span>
            @else
                <span class="trend up">✅ All good</span>
            @endif
        </div>
        <div class="stat-card customers">
            <div class="icon-wrap"><i class="fas fa-users"></i></div>
            <div class="number">{{ $customerCount ?? 0 }}</div>
            <div class="label">Total Customers</div>
            <div class="sub">+{{ $newCustomers ?? 0 }} this month</div>
        </div>
    </div>

    <!-- Alerts -->
    @if(isset($alerts) && count($alerts) > 0)
        <div class="section">
            <div class="section-header">
                <h3><i class="fas fa-bell" style="color:#f59e0b;"></i> Alerts</h3>
                <span class="badge-count">{{ count($alerts) }}</span>
            </div>
            @foreach($alerts as $alert)
                <div class="alert-item" style="border-left-color: {{ $alert['color'] }};">
                    <div class="alert-icon" style="background: {{ $alert['color'] }}20; color: {{ $alert['color'] }};">
                        <i class="fas {{ $alert['icon'] }}"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">{{ $alert['title'] }}</div>
                        <div class="alert-message">{{ $alert['message'] }}</div>
                    </div>
                    <div class="alert-time">{{ $alert['time'] }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Sales by Branch -->
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-store-alt"></i> Sales by Branch</h3>
            <span class="badge-count">Today</span>
        </div>
        <div class="branch-card">
            @forelse($branchSales ?? [] as $branch)
                <div class="branch-item">
                    <div class="left">
                        <div class="b-icon"><i class="fas fa-store"></i></div>
                        <span class="name">{{ $branch->branch->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="right">
                        <div class="amount">₱{{ number_format($branch->total, 2) }}</div>
                        <div class="count">{{ $branch->count }} orders</div>
                    </div>
                </div>
            @empty
                <div class="branch-item">
                    <span style="color:#a08b7a;font-size:12px;">No sales recorded today</span>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Top Products -->
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-fire" style="color:#f97316;"></i> Top Products Today</h3>
        </div>
        <div class="branch-card">
            @forelse($topProducts ?? [] as $product)
                <div class="product-rank-item">
                    <span class="rank">#{{ $loop->iteration }}</span>
                    <span class="name">{{ $product->name }}</span>
                    <span class="sold">{{ $product->total_sold }} sold</span>
                </div>
            @empty
                <div class="text-center text-muted py-2" style="font-size:12px;">No sales yet today</div>
            @endforelse
        </div>
    </div>

    <!-- Critical Stock -->
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-triangle-exclamation" style="color:#ef4444;"></i> Critical Stock</h3>
            @if(isset($criticalItems) && $criticalItems->count() > 0)
                <span class="badge-count" style="background:#ef4444;">{{ $criticalItems->count() }}</span>
            @endif
        </div>
        @forelse($criticalItems ?? [] as $item)
            <div class="critical-item">
                <div class="info">
                    <div class="name">{{ $item->item_name }}</div>
                    <div class="detail">
                        <i class="fas fa-tag"></i> {{ $item->category }}
                        <i class="fas fa-circle" style="font-size:3px;vertical-align:middle;margin:0 4px;"></i>
                        <i class="fas fa-store"></i> {{ $item->branch_name }}
                    </div>
                </div>
                <div class="stock-badge">
                    <i class="fas fa-box"></i> {{ number_format($item->stock, 0) }} {{ $item->unit }}
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-check-circle" style="color:#4ade80;"></i>
                <h5>All Stock Levels Are Healthy</h5>
                <p>No critical items need attention right now.</p>
            </div>
        @endforelse
    </div>

    <!-- Recent Orders -->
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-clock"></i> Recent Orders</h3>
            <span class="badge-count">{{ isset($recentOrders) ? $recentOrders->count() : 0 }}</span>
        </div>
        @forelse($recentOrders ?? [] as $order)
            <div class="order-item">
                <div class="order-info">
                    <div class="order-id">#{{ $order->id }}</div>
                    <div class="order-meta">
                        {{ $order->customer->name ?? 'Walk-in' }}
                        <span class="order-status-badge {{ $order->order_status ?? 'pending' }}">
                            {{ ucfirst($order->order_status ?? 'pending') }}
                        </span>
                        <i class="fas fa-circle" style="font-size:3px;vertical-align:middle;margin:0 4px;color:#ddd;"></i>
                        {{ $order->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="order-total">₱{{ number_format($order->total_amount, 2) }}</div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox" style="color:#ddd;"></i>
                <h5>No Recent Orders</h5>
                <p>Orders will appear here as they come in.</p>
            </div>
        @endforelse
    </div>

    <!-- Install Banner -->
    <div class="install-banner" id="installBanner">
        <div class="left">
            <span class="icon">☕</span>
            <div class="text">
                Brew & Bean Co.
                <small>Add to home screen for quick access</small>
            </div>
        </div>
        <button class="btn-install" id="installBtn">Install</button>
    </div>

    <!-- Refresh Button -->
    <button class="pulse-refresh" id="refreshBtn" onclick="refreshPage()">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Bottom Nav -->
    <nav class="bottom-nav">
        <a href="{{ url('/mobile-pulse') }}" class="nav-btn active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="nav-btn" onclick="event.preventDefault(); refreshPage();">
            <i class="fas fa-rotate-right"></i>
            <span>Refresh</span>
        </a>
        <a href="{{ route('mobile.low-stock') }}" class="nav-btn" id="alertNav">
            <i class="fas fa-bell"></i>
            <span>Alerts</span>
            @if(($lowStockCount ?? 0) > 0)
                <span class="badge-dot"></span>
            @endif
        </a>
        <a href="{{ route('mobile.sales-summary') }}" class="nav-btn">
            <i class="fas fa-chart-bar"></i>
            <span>Summary</span>
        </a>
        <a href="{{ route('mobile.profile') }}" class="nav-btn">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <script>
        // PWA Install
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('installBanner').classList.add('show');
        });
        
        document.getElementById('installBtn').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const result = await deferredPrompt.userChoice;
                if (result.outcome === 'accepted') {
                    document.getElementById('installBanner').classList.remove('show');
                }
                deferredPrompt = null;
            }
        });

        // Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('Service Worker registered'))
                .catch(() => console.log('Service Worker registration failed'));
        }

        // Live Time
        function updateTime() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
            document.getElementById('liveTime').textContent = time;
        }
        setInterval(updateTime, 1000);

        // Refresh
        function refreshPage() {
            const btn = document.getElementById('refreshBtn');
            btn.classList.add('spinning');
            location.reload();
        }

        // Auto refresh every 60 seconds
        setTimeout(() => { location.reload(); }, 60000);

        // Online/Offline detection
        window.addEventListener('online', () => {
            document.querySelector('.topbar .time-display').style.borderColor = '#4ade80';
        });
        window.addEventListener('offline', () => {
            document.querySelector('.topbar .time-display').style.borderColor = '#ef4444';
        });

        // Check online status on load
        if (!navigator.onLine) {
            document.querySelector('.topbar .time-display').style.borderColor = '#ef4444';
        }
    </script>
</body>
</html>