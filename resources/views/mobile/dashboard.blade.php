<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Brew & Bean Co.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#6F4E37">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%236F4E37'/><text x='50' y='72' font-size='60' text-anchor='middle' fill='%23F5EDE6'>☕</text></svg>">
    
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
            --radius: 20px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: #2D1F1A;
            padding-bottom: 90px;
            min-height: 100vh;
        }
        
        .topbar {
            background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%);
            color: white;
            padding: 18px 24px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 2px solid var(--gold);
        }
        .topbar .brand { display: flex; align-items: center; gap: 10px; }
        .topbar .brand .logo { font-size: 26px; }
        .topbar .brand h1 { font-size: 18px; font-weight: 700; margin: 0; }
        .topbar .brand small { font-size: 10px; opacity: 0.7; font-weight: 400; display: block; margin-top: -2px; }
        .topbar .status-badge {
            background: rgba(255,255,255,0.12);
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .topbar .status-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-dot 2s infinite;
        }
        .topbar .status-badge .dot.online { background: #4ade80; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        
        .greeting { padding: 20px 24px 8px; }
        .greeting h2 { font-size: 22px; font-weight: 700; color: var(--brand-dark); margin: 0; }
        .greeting h2 span { color: var(--gold); }
        .greeting p { font-size: 14px; color: #8B7355; margin: 2px 0 0; }
        
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 16px 20px 8px; }
        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 18px 16px 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.15);
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            border-radius: 0 0 4px 4px;
        }
        .stat-card.sales::after { background: linear-gradient(90deg, #4ade80, #22c55e); }
        .stat-card.stock::after { background: linear-gradient(90deg, #facc15, #f59e0b); }
        .stat-card .icon-wrap {
            width: 38px; height: 38px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 17px; margin-bottom: 8px;
        }
        .stat-card.sales .icon-wrap { background: #dcfce7; color: #16a34a; }
        .stat-card.stock .icon-wrap { background: #fef3c7; color: #d97706; }
        .stat-card .number { font-size: 28px; font-weight: 700; letter-spacing: -0.5px; line-height: 1.1; color: var(--brand-dark); }
        .stat-card .number .currency { font-size: 16px; font-weight: 600; color: #8B7355; }
        .stat-card .label { font-size: 12px; color: #8B7355; font-weight: 500; margin-top: 2px; }
        .stat-card .sub { font-size: 11px; color: #a08b7a; margin-top: 4px; }
        .stat-card .status-tag { font-size: 10px; font-weight: 600; padding: 2px 10px; border-radius: 40px; display: inline-block; margin-top: 4px; }
        .status-tag.danger { background: #fee2e2; color: #dc2626; }
        .status-tag.success { background: #dcfce7; color: #16a34a; }
        
        .section { padding: 12px 20px 4px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .section-header h3 { font-size: 15px; font-weight: 600; color: var(--brand-dark); margin: 0; display: flex; align-items: center; gap: 8px; }
        .section-header h3 i { color: var(--gold); }
        .section-header .badge-count { background: var(--brand); color: white; font-size: 11px; font-weight: 600; padding: 2px 12px; border-radius: 40px; }
        
        .branch-card {
            background: white;
            border-radius: var(--radius);
            padding: 16px 18px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.10);
        }
        .branch-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0ebe6;
        }
        .branch-item:last-child { border-bottom: none; padding-bottom: 0; }
        .branch-item .left { display: flex; align-items: center; gap: 10px; }
        .branch-item .left .b-icon {
            width: 32px; height: 32px; border-radius: 10px; background: var(--cream);
            display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--brand);
        }
        .branch-item .left .name { font-weight: 500; font-size: 14px; color: var(--brand-dark); }
        .branch-item .right .amount { font-weight: 700; font-size: 15px; color: var(--brand-dark); }
        .branch-item .right .count { font-size: 11px; color: #a08b7a; }
        
        .critical-item {
            background: white;
            border-radius: var(--radius);
            padding: 14px 18px;
            margin-bottom: 10px;
            box-shadow: var(--card-shadow);
            border-left: 4px solid #ef4444;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .critical-item .info .name { font-weight: 600; font-size: 14px; color: var(--brand-dark); }
        .critical-item .info .detail { font-size: 12px; color: #8B7355; margin-top: 1px; }
        .critical-item .stock-badge {
            background: #fee2e2; color: #dc2626; font-weight: 700; font-size: 14px;
            padding: 4px 14px; border-radius: 40px; display: flex; align-items: center; gap: 4px;
        }
        
        .empty-state {
            background: white; border-radius: var(--radius); padding: 32px 20px;
            text-align: center; box-shadow: var(--card-shadow);
            border: 1px solid rgba(201, 169, 110, 0.10);
        }
        .empty-state i { font-size: 40px; color: #4ade80; margin-bottom: 8px; }
        .empty-state h4 { font-size: 16px; font-weight: 600; color: var(--brand-dark); margin: 0; }
        .empty-state p { color: #8B7355; font-size: 13px; margin: 4px 0 0; }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(201, 169, 110, 0.20);
            display: flex;
            justify-content: space-around;
            padding: 8px 0 env(safe-area-inset-bottom, 8px);
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
            font-size: 10px;
            font-weight: 500;
            padding: 4px 16px;
            cursor: pointer;
            text-decoration: none;
            gap: 2px;
            position: relative;
        }
        .bottom-nav .nav-btn i { font-size: 20px; }
        .bottom-nav .nav-btn.active { color: var(--brand); }
        .bottom-nav .nav-btn .badge-dot {
            position: absolute;
            top: 2px;
            right: 4px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid white;
        }
        
        .install-banner {
            background: var(--brand-dark);
            color: white;
            padding: 14px 18px;
            display: none;
            align-items: center;
            justify-content: space-between;
            border-radius: 16px;
            margin: 0 20px 12px;
            box-shadow: 0 4px 20px rgba(74, 50, 40, 0.25);
        }
        .install-banner.show { display: flex; }
        .install-banner .left { display: flex; align-items: center; gap: 12px; }
        .install-banner .left .icon { font-size: 28px; }
        .install-banner .left .text { font-size: 13px; font-weight: 500; }
        .install-banner .left .text small { font-weight: 400; opacity: 0.7; display: block; font-size: 11px; }
        .install-banner .btn-install {
            background: var(--gold);
            color: var(--brand-dark);
            border: none;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
        }
        
        @media (max-width: 420px) {
            .stats-grid { gap: 8px; padding: 12px 16px 4px; }
            .stat-card { padding: 14px 12px; }
            .stat-card .number { font-size: 22px; }
            .greeting { padding: 16px 16px 4px; }
            .greeting h2 { font-size: 18px; }
            .section { padding: 8px 16px 4px; }
        }
        @media (min-width: 600px) {
            body { max-width: 480px; margin: 0 auto; background: #e8ddd4; }
            .bottom-nav { border-radius: 20px 20px 0 0; }
        }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="brand">
            <span class="logo">☕</span>
            <div>
                <h1>Brew & Bean Co.</h1>
                <small>Pulse · {{ now()->format('M d') }}</small>
            </div>
        </div>
        <div class="status-badge">
            <span class="dot online"></span>
            {{ now()->format('h:i A') }}
        </div>
    </header>

    <div class="greeting">
        <h2>Good {{ \Carbon\Carbon::now()->format('A') == 'AM' ? 'Morning' : 'Afternoon' }}, <span>{{ auth()->user()->name ?? 'Owner' }}</span></h2>
        <p><i class="fas fa-store me-1" style="color:var(--gold);"></i> {{ auth()->user()->branch->name ?? 'All Branches' }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card sales">
            <div class="icon-wrap"><i class="fas fa-coins"></i></div>
            <div class="number"><span class="currency">$</span>{{ number_format($todaySales ?? 0, 0) }}</div>
            <div class="label">Today's Sales</div>
            <div class="sub">{{ $todayCount ?? 0 }} transactions</div>
        </div>
        <div class="stat-card stock">
            <div class="icon-wrap"><i class="fas fa-boxes"></i></div>
            <div class="number">{{ $lowStockCount ?? 0 }}</div>
            <div class="label">Low Stock Items</div>
            @if($lowStockCount > 0)
                <span class="status-tag danger">⚠️ Needs action</span>
            @else
                <span class="status-tag success">✅ All good</span>
            @endif
        </div>
    </div>

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
                        <div class="amount">${{ number_format($branch->total, 2) }}</div>
                        <div class="count">{{ $branch->count }} orders</div>
                    </div>
                </div>
            @empty
                <div class="branch-item">
                    <span style="color:#a08b7a;font-size:13px;">No sales recorded today</span>
                </div>
            @endforelse
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-triangle-exclamation" style="color:#ef4444;"></i> Critical Stock</h3>
            @if($criticalItems->count() > 0)
                <span class="badge-count" style="background:#ef4444;">{{ $criticalItems->count() }}</span>
            @endif
        </div>

        @forelse($criticalItems as $item)
            <div class="critical-item">
                <div class="info">
                    <div class="name">{{ $item->item_name }}</div>
                    <div class="detail">
                        <i class="fas fa-tag"></i> {{ $item->category }}
                        <i class="fas fa-circle" style="font-size:4px;vertical-align:middle;"></i>
                        <i class="fas fa-store"></i> {{ $item->branch_name }}
                    </div>
                </div>
                <div class="stock-badge">
                    <i class="fas fa-box"></i> {{ number_format($item->stock, 0) }} {{ $item->unit }}
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h4>All Stock Levels Are Healthy</h4>
                <p>No critical items need attention right now.</p>
            </div>
        @endforelse
    </div>

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

    <nav class="bottom-nav">
        <a href="{{ url('/mobile-pulse') }}" class="nav-btn active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ url('/mobile-pulse') }}" class="nav-btn" id="alertNav">
            <i class="fas fa-bell"></i>
            <span>Alerts</span>
            @if($lowStockCount > 0)
                <span class="badge-dot"></span>
            @endif
        </a>
        <a href="#" class="nav-btn" onclick="event.preventDefault(); location.reload();">
            <i class="fas fa-rotate-right"></i>
            <span>Refresh</span>
        </a>
        <a href="#" class="nav-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </nav>

    <script>
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

        // Register Service Worker for Home Screen
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('Service Worker registered'))
                .catch(() => console.log('Service Worker registration failed'));
        }

        setTimeout(() => { location.reload(); }, 120000);
    </script>
</body>
</html>
