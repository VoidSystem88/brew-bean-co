<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Brew & Bean Co. Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        
        /* Top Navbar with Hamburger */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: #1a1a1a;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 16px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .top-nav .hamburger {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            padding: 8px 12px 8px 4px;
            cursor: pointer;
        }
        .top-nav .brand {
            font-size: 18px;
            font-weight: 700;
            flex: 1;
            text-align: center;
        }
        .top-nav .brand small { color: #f5c842; font-size: 11px; font-weight: 400; }
        .top-nav .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #6F4E37;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: white;
        }
        
        /* Overlay */
        .overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 998;
            display: none;
        }
        .overlay.show { display: block; }
        
        /* Sidebar - slides in from left */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #1a1a1a;
            color: white;
            z-index: 999;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
            padding: 16px 0;
        }
        .sidebar.open { transform: translateX(0); }
        
        .sidebar .brand {
            padding: 0 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 8px;
        }
        .sidebar .brand h2 { font-size: 22px; margin: 0; }
        .sidebar .brand small { color: #f5c842; font-size: 11px; letter-spacing: 1px; }
        
        .sidebar .nav-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            gap: 14px;
            font-size: 15px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar .nav-item:hover { background: rgba(255,255,255,0.05); color: white; }
        .sidebar .nav-item.active { color: #f5c842; border-left-color: #f5c842; background: rgba(255,255,255,0.05); }
        .sidebar .nav-item i { width: 22px; text-align: center; font-size: 18px; }
        .sidebar .nav-item .badge-count {
            margin-left: auto;
            background: #dc3545;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .sidebar .nav-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 8px 20px; }
        .sidebar .nav-label { padding: 12px 20px 6px; font-size: 10px; text-transform: uppercase; color: rgba(255,255,255,0.3); letter-spacing: 1px; }
        .sidebar .nav-bottom { margin-top: 20px; }
        
        /* Main Content */
        .main-content {
            padding-top: 68px;
            padding-bottom: 20px;
            padding-left: 16px;
            padding-right: 16px;
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .mobile-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid #e8e8e8;
        }
        .mobile-card .card-title { font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }
        .stat-number { font-size: 24px; font-weight: 700; }
        .stat-label { font-size: 12px; color: #999; }
        
        /* Responsive */
        @media (max-width: 480px) {
            .main-content { padding: 60px 12px 20px; }
        }
        
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Nav -->
    <div class="top-nav">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="brand">
            ☕ Brew & Bean Co. <small>Pro</small>
        </div>
        <div class="avatar">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="mobileSidebar">
        <div class="brand">
            <h2>☕ Brew & Bean Co.</h2>
            <small>Pro</small>
        </div>

        <div class="nav-label">Main</div>
        <a href="{{ route('mobile.dashboard') }}" class="nav-item {{ request()->routeIs('mobile.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('mobile.low-stock') }}" class="nav-item {{ request()->routeIs('mobile.low-stock') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Low Stock Alerts</span>
            <span class="badge-count" id="mobileLowStockCount">0</span>
        </a>

        <div class="nav-divider"></div>

        <div class="nav-label">Sales</div>
        <a href="{{ route('mobile.sales-summary') }}" class="nav-item {{ request()->routeIs('mobile.sales-summary') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Quick Sales Summary</span>
        </a>

        <div class="nav-divider"></div>

        <div class="nav-label">Account</div>
        <a href="{{ route('mobile.profile') }}" class="nav-item {{ request()->routeIs('mobile.profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>
        <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="font-size:13px;padding:10px 14px;border-radius:10px;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:10px;"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" style="font-size:13px;padding:10px 14px;border-radius:10px;">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:10px;"></button>
            </div>
        @endif

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h5 style="margin:0;font-weight:600;">@yield('page-title', 'Dashboard')</h5>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        function updateLowStockCount() {
            $.ajax({
                url: '{{ route("inventory.low-stock") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#mobileLowStockCount').text(response.data.length);
                    }
                }
            });
        }
        
        $(document).ready(function() {
            updateLowStockCount();
            setInterval(updateLowStockCount, 30000);
        });

        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('mobileSidebar');
                if (sidebar.classList.contains('open')) {
                    toggleSidebar();
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
