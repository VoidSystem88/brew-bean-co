<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Brew & Bean Co.')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --primary-brown: #6F4E37;
            --online-color: #28a745;
            --offline-color: #dc3545;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f2ef;
            overflow-x: hidden;
        }
        
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background: #2d1f14;
            color: #e8e0d8;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            transition: width 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #2d1f14;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #5a3d2b;
            border-radius: 10px;
        }
        
        .sidebar .sidebar-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 70px;
        }
        
        .sidebar .sidebar-header .logo-container {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background: #f8f6f4;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar .sidebar-header .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .sidebar .sidebar-header .logo-container .placeholder {
            color: #999;
            font-size: 20px;
        }
        
        .sidebar .sidebar-header .brand-text {
            font-size: 16px;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar .sidebar-header .brand-text small {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: #999;
        }
        
        .sidebar .nav-section {
            padding: 12px 20px 6px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            font-weight: 600;
        }
        
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: #b8b0a8;
            text-decoration: none;
            transition: 0.2s;
            font-size: 14px;
            border-left: 3px solid transparent;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: rgba(111, 78, 55, 0.3);
            color: white;
            border-left-color: #6F4E37;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .sidebar .nav-link .badge {
            margin-left: auto;
            background: #6F4E37;
            color: white;
            font-size: 10px;
            padding: 1px 8px;
            border-radius: 10px;
        }
        
        /* Refund Badge */
        .sidebar .nav-link .badge-refund {
            margin-left: auto;
            background: #ffc107;
            color: #333;
            font-size: 10px;
            padding: 1px 8px;
            border-radius: 10px;
            animation: pulse-refund 2s infinite;
        }
        
        @keyframes pulse-refund {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        .sidebar .sidebar-footer {
            margin-top: auto;
            padding: 12px 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        
        .sidebar .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar .sidebar-footer .user-info .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #6F4E37;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        
        .sidebar .sidebar-footer .user-info .user-name {
            font-size: 13px;
            color: #e8e0d8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar .sidebar-footer .user-info .user-role {
            font-size: 11px;
            color: #888;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content .top-bar {
            background: white;
            padding: 8px 20px;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .main-content .top-bar .left-section {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 200px;
        }
        
        .main-content .top-bar .toggle-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #666;
            cursor: pointer;
            padding: 4px 8px;
        }
        
        .main-content .top-bar .toggle-btn:hover {
            color: #6F4E37;
        }
        
        .main-content .top-bar .search-container {
            flex: 1;
            max-width: 500px;
            min-width: 150px;
            position: relative;
        }
        
        .main-content .top-bar .search-container .search-input {
            width: 100%;
            padding: 6px 16px 6px 38px;
            border: 1px solid #e8e8e8;
            border-radius: 20px;
            font-size: 13px;
            transition: 0.3s;
            background: #f8f6f4;
        }
        
        .main-content .top-bar .search-container .search-input:focus {
            border-color: #6F4E37;
            outline: none;
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
            background: white;
        }
        
        .main-content .top-bar .search-container .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }
        
        .main-content .top-bar .search-container .search-clear {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            cursor: pointer;
            display: none;
            font-size: 14px;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 9999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            margin-top: 4px;
        }
        
        .search-results.show {
            display: block;
        }
        
        .search-results .result-group {
            padding: 6px 14px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            font-weight: 600;
            background: #f8f6f4;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .search-results .result-item {
            padding: 8px 14px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .search-results .result-item:hover {
            background: #f8f6f4;
        }
        
        .search-results .result-item .result-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f0ebe6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6F4E37;
            font-size: 12px;
            flex-shrink: 0;
        }
        
        .search-results .result-item .result-info {
            flex: 1;
        }
        
        .search-results .result-item .result-info .result-title {
            font-size: 13px;
            font-weight: 500;
            color: #333;
        }
        
        .search-results .result-item .result-info .result-sub {
            font-size: 11px;
            color: #999;
        }
        
        .search-results .result-item .result-badge {
            font-size: 10px;
            padding: 1px 10px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .search-results .result-item .result-badge.product { background: #d4edda; color: #155724; }
        .search-results .result-item .result-badge.customer { background: #cce5ff; color: #004085; }
        .search-results .result-item .result-badge.order { background: #fff3cd; color: #856404; }
        .search-results .result-item .result-badge.inventory { background: #f8d7da; color: #721c24; }
        .search-results .result-item .result-badge.staff { background: #d1ecf1; color: #0c5460; }
        .search-results .result-item .result-badge.branch { background: #e8d5b7; color: #6F4E37; }
        .search-results .result-item .result-badge.refund { background: #fff3cd; color: #856404; }
        
        .search-results .no-results {
            padding: 20px;
            text-align: center;
            color: #999;
            font-size: 13px;
        }
        
        .search-results .no-results i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
            color: #ddd;
        }
        
        .main-content .top-bar .connection-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #e8e8e8;
            background: white;
            white-space: nowrap;
        }
        
        .main-content .top-bar .connection-status .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .main-content .top-bar .connection-status .dot.online {
            background: var(--online-color);
        }
        
        .main-content .top-bar .connection-status .dot.offline {
            background: var(--offline-color);
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.3; }
            100% { opacity: 1; }
        }
        
        .main-content .top-bar .connection-status .status-text.online {
            color: var(--online-color);
        }
        
        .main-content .top-bar .connection-status .status-text.offline {
            color: var(--offline-color);
        }
        
        .main-content .top-bar .right-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .main-content .top-bar .right-section .time-display {
            font-size: 13px;
            color: #999;
            white-space: nowrap;
        }
        
        .main-content .content {
            padding: 20px 24px;
        }
        
        @media (max-width: 992px) {
            .main-content .top-bar .search-container { max-width: 300px; }
        }
        
        @media (max-width: 768px) {
            .sidebar { width: var(--sidebar-collapsed); transform: translateX(0); }
            .sidebar .brand-text, .sidebar .nav-link span, .sidebar .nav-section,
            .sidebar .sidebar-footer .user-info .user-name,
            .sidebar .sidebar-footer .user-info .user-role { display: none; }
            .sidebar .sidebar-header { justify-content: center; padding: 12px; }
            .sidebar .nav-link { justify-content: center; padding: 12px; }
            .sidebar .sidebar-footer .user-info { justify-content: center; }
            .main-content { margin-left: var(--sidebar-collapsed); }
            .main-content .top-bar .search-container { max-width: 200px; }
            .main-content .top-bar .connection-status .status-text { display: inline; }
            .main-content .top-bar .right-section .time-display { display: inline; }
            .main-content .top-bar .connection-status { padding: 2px 8px; font-size: 11px; }
            .main-content .top-bar .right-section .time-display { font-size: 11px; }
        }
        
        @media (max-width: 576px) {
            .sidebar { width: 0; transform: translateX(-100%); }
            .sidebar.open { width: var(--sidebar-width); transform: translateX(0); }
            .sidebar.open .brand-text, .sidebar.open .nav-link span,
            .sidebar.open .nav-section,
            .sidebar.open .sidebar-footer .user-info .user-name,
            .sidebar.open .sidebar-footer .user-info .user-role { display: inline; }
            .sidebar.open .sidebar-header { justify-content: flex-start; padding: 16px 20px; }
            .sidebar.open .nav-link { justify-content: flex-start; padding: 10px 20px; }
            .sidebar.open .sidebar-footer .user-info { justify-content: flex-start; }
            .main-content { margin-left: 0; }
            .main-content .top-bar { padding: 6px 10px; flex-wrap: wrap; gap: 4px; }
            .main-content .top-bar .search-container { max-width: 100%; flex: 1 1 100%; order: 3; margin-top: 4px; }
            .main-content .top-bar .left-section { flex: 0 1 auto; min-width: auto; }
            .main-content .top-bar .connection-status .status-text { display: inline; font-size: 10px; }
            .main-content .top-bar .right-section .time-display { display: inline; font-size: 10px; }
            .main-content .top-bar .connection-status { padding: 2px 6px; font-size: 10px; }
            .main-content .top-bar .connection-status .dot { width: 6px; height: 6px; }
            .main-content .top-bar .right-section { gap: 6px; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.4); z-index: 999; }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    @php
                        $logoPath = Storage::disk('public')->exists('settings/logo.png') 
                            ? asset('storage/settings/logo.png') 
                            : null;
                        $brandName = Storage::disk('public')->exists('settings/brand_name.txt') 
                            ? Storage::disk('public')->get('settings/brand_name.txt') 
                            : 'Brew & Bean Co.';
                        $brandTagline = Storage::disk('public')->exists('settings/brand_tagline.txt') 
                            ? Storage::disk('public')->get('settings/brand_tagline.txt') 
                            : 'Management System';
                    @endphp
                    @if($logoPath)
                        <img src="{{ $logoPath }}" alt="Logo" id="sidebarLogo">
                    @else
                        <div class="placeholder" id="sidebarLogoPlaceholder">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                </div>
                <div class="brand-text">
                    {{ $brandName }}
                    <small>{{ $brandTagline }}</small>
                </div>
            </div>

            @php
                $user = Auth::user();
                $isAdmin = $user && $user->role === 'admin';
                $isManager = $user && $user->role === 'manager';
                $isStaff = $user && $user->role === 'staff';
                $isDelivery = $user && $user->role === 'delivery';
            @endphp

            @if($isDelivery)
                <!-- DELIVERY - Only My Deliveries -->
                <div class="nav-section">Delivery</div>
                <a href="{{ route('delivery.dashboard') }}" class="nav-link {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>My Deliveries</span>
                </a>
            @else
                <!-- MAIN -->
                <div class="nav-section">Main</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>

                <!-- OPERATIONS -->
                @if($isStaff || $isAdmin || $isManager)
                    @if($isStaff)
                        <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register"></i>
                            <span>Point of Sale</span>
                        </a>
                        
                        <a href="{{ route('barista.queue') }}" class="nav-link {{ request()->routeIs('barista.*') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i>
                            <span>Order Queue</span>
                            <span class="badge" id="queueBadge">0</span>
                        </a>
                    @endif
                @endif

                <!-- INVENTORY -->
                @if($isStaff || $isAdmin || $isManager)
                    <div class="nav-section">Inventory</div>
                    <a href="{{ route('delivery.index') }}" class="nav-link {{ request()->routeIs('delivery.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i>
                        <span>Delivery</span>
                    </a>
                    <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i>
                        <span>Inventory</span>
                    </a>
                    <a href="{{ route('recipes.index') }}" class="nav-link {{ request()->routeIs('recipes.*') ? 'active' : '' }}">
                        <i class="fas fa-utensils"></i>
                        <span>Recipes</span>
                    </a>
                @endif

                <!-- CUSTOMERS -->
                @if($isStaff || $isAdmin || $isManager)
                    <div class="nav-section">Customers</div>
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-user-friends"></i>
                        <span>Customers</span>
                    </a>
                @endif

                <!-- MANAGEMENT - Only Admin and Manager -->
                @if($isAdmin || $isManager)
                    <div class="nav-section">Management</div>
                    <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i>
                        <span>Branches</span>
                    </a>
                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Staff</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i>
                        <span>Suppliers</span>
                    </a>
                    <a href="{{ route('warehouse.index') }}" class="nav-link {{ request()->routeIs('warehouse.*') ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i>
                        <span>Warehouse</span>
                    </a>
                @endif

                <!-- REPORTS -->
                @if($isAdmin || $isManager)
                    <div class="nav-section">Reports</div>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                    <a href="{{ route('sync.index') }}" class="nav-link {{ request()->routeIs('sync.*') ? 'active' : '' }}">
                        <i class="fas fa-sync"></i>
                        <span>Sync</span>
                    </a>
                @endif

                <!-- ===== REFUND MANAGEMENT - ADMIN ONLY ===== -->
                @if($isAdmin)
                    <div class="nav-section">Finance</div>
                    <a href="{{ route('admin.refunds') }}" class="nav-link {{ request()->routeIs('admin.refunds*') ? 'active' : '' }}">
                        <i class="fas fa-undo-alt"></i>
                        <span>Refund Requests</span>
                        @php
                            $pendingRefunds = \App\Models\Sale::where('refund_requested', true)
                                ->where('refund_status', 'pending')
                                ->count();
                        @endphp
                        @if($pendingRefunds > 0)
                            <span class="badge-refund">{{ $pendingRefunds }}</span>
                        @endif
                    </a>
                @endif

                <!-- SYSTEM - ADMIN ONLY -->
                @if($isAdmin)
                    <div class="nav-section">System</div>
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="{{ route('admin.database') }}" class="nav-link {{ request()->routeIs('admin.database') ? 'active' : '' }}">
                        <i class="fas fa-database"></i>
                        <span>Database</span>
                    </a>
                @endif
            @endif

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                    <div>
                        <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'Staff') }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div class="left-section">
                    <button class="toggle-btn" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    @if(!$isDelivery)
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="globalSearch" placeholder="Search products, customers, orders..." autocomplete="off">
                            <span class="search-clear" id="searchClear" onclick="clearSearch()">
                                <i class="fas fa-times-circle"></i>
                            </span>
                            <div class="search-results" id="searchResults"></div>
                        </div>
                    @endif
                </div>
                
                <div class="right-section">
                    <!-- Notification Bell - Only for Staff/Admin, NOT for Delivery -->
                    @if(!$isDelivery)
                    <div style="position:relative;display:inline-block;margin-left:8px;">
                        <div onclick="toggleNotif(event)" style="position:relative;display:inline-block;cursor:pointer;font-size:18px;color:#6F4E37;padding:4px 8px;transition:0.2s;">
                            <i class="fas fa-bell"></i>
                            <span id="notifCount" style="display:none;position:absolute;top:-4px;right:-4px;background:#dc3545;color:white;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;border:2px solid white;min-width:16px;text-align:center;line-height:12px;">0</span>
                            <div id="notifDropdown" style="display:none;position:absolute;top:100%;right:0;width:350px;max-height:350px;overflow-y:auto;background:white;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.15);border:1px solid #e8e8e8;z-index:999;margin-top:4px;">
                                <div style="padding:10px 14px;border-bottom:1px solid #eee;font-weight:600;font-size:13px;color:#333;display:flex;justify-content:space-between;align-items:center;background:#faf8f6;border-radius:12px 12px 0 0;">
                                    <span>📦 Notifications</span>
                                    <button onclick="markAllReadNotif(event)" style="font-size:11px;color:#6F4E37;cursor:pointer;background:none;border:none;font-weight:500;">Mark all read</button>
                                </div>
                                <div id="notifList">
                                    <div style="padding:20px;text-align:center;color:#999;font-size:12px;">
                                        <i class="fas fa-check-circle" style="font-size:28px;color:#ddd;display:block;margin-bottom:6px;"></i>
                                        No notifications
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="connection-status" id="connectionStatus">
                        <span class="dot online" id="statusDot"></span>
                        <span class="status-text online" id="statusText">Online</span>
                    </div>
                    <span class="time-display" id="timeDisplay"></span>
                </div>
            </div>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth <= 576) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                document.querySelector('.main-content').classList.toggle('expanded');
            }
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 576) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });

        function updateQueueBadge() {
            fetch('/barista/queue-data')
                .then(response => response.json())
                .then(data => {
                    const total = (data.in_store?.pending?.length || 0) + 
                                 (data.online?.pending?.length || 0);
                    const badge = document.getElementById('queueBadge');
                    if (badge) {
                        badge.textContent = total;
                        badge.style.display = total > 0 ? 'inline' : 'none';
                    }
                })
                .catch(() => {});
        }

        function checkConnection() {
            const dot = document.getElementById('statusDot');
            const text = document.getElementById('statusText');
            
            if (navigator.onLine) {
                dot.className = 'dot online';
                text.className = 'status-text online';
                text.textContent = 'Online';
            } else {
                dot.className = 'dot offline';
                text.className = 'status-text offline';
                text.textContent = 'Offline';
            }
        }

        window.addEventListener('online', checkConnection);
        window.addEventListener('offline', checkConnection);

        function updateTime() {
            const now = new Date();
            document.getElementById('timeDisplay').textContent = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
        }

        // Search
        let searchTimeout = null;
        const searchInput = document.getElementById('globalSearch');
        const searchResults = document.getElementById('searchResults');
        const searchClear = document.getElementById('searchClear');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (searchTimeout) clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.classList.remove('show');
                searchClear.style.display = 'none';
                return;
            }
            
            searchClear.style.display = 'block';
            
            searchTimeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        renderSearchResults(data);
                    })
                    .catch(() => {
                        searchResults.innerHTML = `
                            <div class="no-results">
                                <i class="fas fa-exclamation-circle"></i>
                                Error searching. Please try again.
                            </div>
                        `;
                        searchResults.classList.add('show');
                    });
            }, 300);
        });

        function renderSearchResults(data) {
            const results = searchResults;
            results.innerHTML = '';
            
            let hasResults = false;

            if (data.products && data.products.length > 0) {
                hasResults = true;
                results.innerHTML += `<div class="result-group">Products</div>`;
                data.products.forEach(item => {
                    results.innerHTML += `
                        <div class="result-item" onclick="window.location.href='/products/${item.id}'">
                            <div class="result-icon"><i class="fas fa-box"></i></div>
                            <div class="result-info">
                                <div class="result-title">${item.name}</div>
                                <div class="result-sub">₱${parseFloat(item.price).toFixed(2)}</div>
                            </div>
                            <span class="result-badge product">Product</span>
                        </div>
                    `;
                });
            }

            if (data.customers && data.customers.length > 0) {
                hasResults = true;
                results.innerHTML += `<div class="result-group">Customers</div>`;
                data.customers.forEach(item => {
                    results.innerHTML += `
                        <div class="result-item" onclick="window.location.href='/customers/${item.id}'">
                            <div class="result-icon"><i class="fas fa-user"></i></div>
                            <div class="result-info">
                                <div class="result-title">${item.name}</div>
                                <div class="result-sub">${item.email || item.customer_code || ''}</div>
                            </div>
                            <span class="result-badge customer">Customer</span>
                        </div>
                    `;
                });
            }

            if (data.orders && data.orders.length > 0) {
                hasResults = true;
                results.innerHTML += `<div class="result-group">Orders</div>`;
                data.orders.forEach(item => {
                    results.innerHTML += `
                        <div class="result-item" onclick="window.location.href='/pos/receipt/${item.id}'">
                            <div class="result-icon"><i class="fas fa-receipt"></i></div>
                            <div class="result-info">
                                <div class="result-title">Order #${item.id}</div>
                                <div class="result-sub">₱${parseFloat(item.total).toFixed(2)}</div>
                            </div>
                            <span class="result-badge order">Order</span>
                        </div>
                    `;
                });
            }

            if (data.staff && data.staff.length > 0) {
                hasResults = true;
                results.innerHTML += `<div class="result-group">Staff</div>`;
                data.staff.forEach(item => {
                    results.innerHTML += `
                        <div class="result-item" onclick="window.location.href='/staff/${item.id}'">
                            <div class="result-icon"><i class="fas fa-user-tie"></i></div>
                            <div class="result-info">
                                <div class="result-title">${item.name}</div>
                                <div class="result-sub">${item.email}</div>
                            </div>
                            <span class="result-badge staff">Staff</span>
                        </div>
                    `;
                });
            }

            if (data.branches && data.branches.length > 0) {
                hasResults = true;
                results.innerHTML += `<div class="result-group">Branches</div>`;
                data.branches.forEach(item => {
                    results.innerHTML += `
                        <div class="result-item" onclick="window.location.href='/branches/${item.id}'">
                            <div class="result-icon"><i class="fas fa-store"></i></div>
                            <div class="result-info">
                                <div class="result-title">${item.name}</div>
                                <div class="result-sub">${item.address || ''}</div>
                            </div>
                            <span class="result-badge branch">Branch</span>
                        </div>
                    `;
                });
            }

            if (data.inventory && data.inventory.length > 0) {
                hasResults = true;
                results.innerHTML += `<div class="result-group">Inventory</div>`;
                data.inventory.forEach(item => {
                    results.innerHTML += `
                        <div class="result-item" onclick="window.location.href='/inventory'">
                            <div class="result-icon"><i class="fas fa-boxes"></i></div>
                            <div class="result-info">
                                <div class="result-title">${item.name}</div>
                                <div class="result-sub">${item.quantity || 0} in stock</div>
                            </div>
                            <span class="result-badge inventory">Inventory</span>
                        </div>
                    `;
                });
            }

            if (!hasResults) {
                results.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        No results found for "<strong>${searchInput.value}</strong>"
                    </div>
                `;
            }

            results.classList.add('show');
        }

        function clearSearch() {
            searchInput.value = '';
            searchResults.classList.remove('show');
            searchClear.style.display = 'none';
            searchInput.focus();
        }

        document.addEventListener('click', function(e) {
            const container = document.querySelector('.search-container');
            if (container && !container.contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('globalSearch').focus();
            }
            if (e.key === 'Escape') {
                clearSearch();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            updateQueueBadge();
            checkConnection();
            updateTime();
            
            setInterval(updateQueueBadge, 30000);
            setInterval(checkConnection, 10000);
            setInterval(updateTime, 1000);
        });
    </script>
    
    <!-- ============================================================
         NOTIFICATION SCRIPTS - SINGLE DECLARATION
         ============================================================ -->
    @php
        $isDelivery = Auth::user() && Auth::user()->role === 'delivery';
    @endphp
    
    @if(!$isDelivery)
    <script>
        let notifs = [];
        
        function loadNotifs() {
            fetch('/staff/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        notifs = data.notifications || [];
                        renderNotifs();
                        updateNotifBadge();
                    }
                })
                .catch(() => {});
        }
        
        function renderNotifs() {
            const list = document.getElementById('notifList');
            if (!list) return;
            
            if (notifs.length === 0) {
                list.innerHTML = `
                    <div style="padding:20px;text-align:center;color:#999;font-size:12px;">
                        <i class="fas fa-check-circle" style="font-size:28px;color:#ddd;display:block;margin-bottom:6px;"></i>
                        No notifications
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifs.forEach(notif => {
                const isUnread = !notif.read;
                const icon = notif.type === 'delivered' ? 'fa-check-circle' : 'fa-truck';
                const bgColor = notif.type === 'delivered' ? '#d4edda' : '#cce5ff';
                const color = notif.type === 'delivered' ? '#28a745' : '#0d6efd';
                
                html += `
                    <div onclick="markNotifRead('${notif.id}')" style="padding:10px 14px;border-bottom:1px solid #f5f5f5;transition:0.2s;cursor:pointer;display:flex;gap:10px;align-items:flex-start;${isUnread ? 'background:#f0f7ff;border-left:3px solid #0d6efd;' : ''}">
                        <div style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;background:${bgColor};color:${color};">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:500;font-size:12px;color:#333;">${notif.title}</div>
                            <div style="font-size:11px;color:#999;margin-top:1px;">${notif.message}</div>
                        </div>
                        <div style="font-size:10px;color:#bbb;white-space:nowrap;flex-shrink:0;margin-top:2px;">${notif.time}</div>
                    </div>
                `;
            });
            
            list.innerHTML = html;
        }
        
        function updateNotifBadge() {
            const badge = document.getElementById('notifCount');
            if (!badge) return;
            
            const unread = notifs.filter(n => !n.read).length;
            if (unread > 0) {
                badge.textContent = unread > 99 ? '99+' : unread;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
        
        function toggleNotif(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('notifDropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                if (dropdown.style.display === 'block') {
                    loadNotifs();
                }
            }
        }
        
        function markNotifRead(id) {
            fetch('/staff/notifications/read/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = notifs.find(n => n.id === id);
                    if (item) item.read = true;
                    renderNotifs();
                    updateNotifBadge();
                }
            })
            .catch(() => {});
        }
        
        function markAllReadNotif(event) {
            if (event) event.stopPropagation();
            fetch('/staff/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notifs.forEach(n => n.read = true);
                    renderNotifs();
                    updateNotifBadge();
                }
            })
            .catch(() => {});
        }
        
        document.addEventListener('click', function(e) {
            const bell = document.querySelector('[onclick*="toggleNotif"]');
            if (bell && !bell.contains(e.target)) {
                const dropdown = document.getElementById('notifDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifs();
            setInterval(loadNotifs, 30000);
        });
    </script>
    @endif
    
    @stack('scripts')
</body>
</html>