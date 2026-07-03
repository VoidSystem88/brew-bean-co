<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Brew & Bean Co.')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-brown: #6F4E37;
            --secondary-brown: #8B6B4A;
            --light-beige: #F5F0EB;
        }
        * {
            box-sizing: border-box;
        }
        body {
            background: #f5f0eb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .sidebar {
            background: var(--primary-brown);
            min-height: 100vh;
            padding: 20px 0;
            color: white;
            position: fixed;
            width: 250px;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .sidebar-brand {
            padding: 0 20px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
            flex-shrink: 0;
        }
        .sidebar-brand h3 {
            font-weight: 700;
            margin: 0;
            color: white;
        }
        .sidebar-brand h3 i {
            color: #ffd700;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.7);
        }
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 5px 0 10px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }
        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
            font-size: 14px;
            white-space: nowrap;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }
        .sidebar .nav-link .badge-low-stock {
            background: #dc3545;
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .sidebar .nav-section {
            padding: 8px 20px 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.3);
            font-weight: 600;
        }
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 8px 0;
            flex-shrink: 0;
            background: var(--primary-brown);
        }
        .sidebar-footer .nav-link {
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            padding: 8px 20px;
            border-radius: 8px;
            margin: 1px 10px;
            transition: all 0.2s;
        }
        .sidebar-footer .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }
        .sidebar-footer .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px 30px;
            min-height: 100vh;
            width: calc(100% - 250px);
            max-width: 100%;
            overflow-x: hidden;
        }
        .main-content .container-fluid {
            padding-left: 0;
            padding-right: 0;
            max-width: 100%;
        }
        .navbar-custom {
            background: white;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .navbar-custom .left-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .navbar-custom .right-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .navbar-custom .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-custom .user-info .avatar {
            width: 38px;
            height: 38px;
            background: var(--primary-brown);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
        .navbar-custom .search-box {
            position: relative;
            min-width: 250px;
        }
        .navbar-custom .search-box input {
            padding: 8px 16px 8px 40px;
            border-radius: 20px;
            border: 1px solid #e8e8e8;
            font-size: 14px;
            width: 100%;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        .navbar-custom .search-box input:focus {
            outline: none;
            border-color: #6F4E37;
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
            background: white;
        }
        .navbar-custom .search-box .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .navbar-custom .search-box .search-shortcut {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: #999;
            background: #e8e8e8;
            padding: 1px 8px;
            border-radius: 4px;
        }
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            border: 1px solid #e8e8e8;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            z-index: 9999;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            margin-top: 4px;
        }
        .search-results.show {
            display: block;
        }
        .search-results .result-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            color: #333;
        }
        .search-results .result-item:hover {
            background: #f8f6f4;
        }
        .search-results .result-item:last-child {
            border-bottom: none;
        }
        .search-results .result-item .result-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .search-results .result-item .result-info {
            flex: 1;
        }
        .search-results .result-item .result-info .result-title {
            font-weight: 500;
            font-size: 14px;
        }
        .search-results .result-item .result-info .result-sub {
            font-size: 12px;
            color: #999;
        }
        .search-results .result-item .result-badge {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 12px;
            background: #e9ecef;
            color: #666;
            font-weight: 500;
        }
        .search-results .no-results {
            padding: 20px;
            text-align: center;
            color: #999;
        }
        .search-results .search-more {
            padding: 10px 16px;
            text-align: center;
            border-top: 1px solid #f5f5f5;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }
        .search-results .search-more a {
            color: #6F4E37;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
        }
        .search-results .search-more a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
                width: 100%;
            }
            .sidebar-toggle {
                display: block !important;
            }
            .navbar-custom .search-box {
                min-width: 120px;
                flex: 1;
            }
            .navbar-custom .search-box input {
                font-size: 13px;
                padding: 6px 12px 6px 32px;
            }
            .navbar-custom .search-box .search-shortcut {
                display: none;
            }
            .navbar-custom {
                padding: 8px 12px;
            }
            .navbar-custom .right-section {
                gap: 8px;
            }
            .search-results {
                position: fixed;
                top: 60px;
                left: 10px;
                right: 10px;
                max-height: 60vh;
            }
        }
        .sidebar-toggle {
            display: none;
            background: var(--primary-brown);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
        }
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-dot.online {
            background: #28a745;
        }
        .status-dot.offline {
            background: #dc3545;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-mug-hot me-2"></i>Brew & Bean</h3>
            <small>Management System</small>
        </div>

        <div class="sidebar-menu">
            @auth
                @php
                    $user = Auth::user();
                    $isAdmin = $user->isAdmin();
                    $isManager = $user->isManager();
                    $isStaff = $user->isStaff();
                    
                    $lowStockCount = 0;
                    if ($isAdmin || $isManager) {
                        $lowStockCount = DB::table('items')
                            ->join('warehouse_stock', 'items.id', '=', 'warehouse_stock.item_id')
                            ->whereColumn('warehouse_stock.stock_quantity', '<=', 'warehouse_stock.low_stock_threshold')
                            ->count();
                    }
                @endphp

                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>

                @if($isStaff)
                    <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i> POS
                    </a>

                    <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> Inventory
                    </a>

                    <a href="{{ route('delivery.index') }}" class="nav-link {{ request()->routeIs('delivery.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Deliveries
                    </a>

                    <a href="{{ route('recipes.index') }}" class="nav-link {{ request()->routeIs('recipes.*') ? 'active' : '' }}">
                        <i class="fas fa-utensils"></i> Recipes
                    </a>

                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-user-friends"></i> Customers
                    </a>

                    <a href="{{ route('barista.queue') }}" class="nav-link {{ request()->routeIs('barista.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i> Queue
                    </a>

                @elseif($isAdmin || $isManager)
                    <div class="nav-section">Management</div>
                    <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i> Branches
                    </a>
                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Staff
                    </a>
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-user-friends"></i> Customers
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Suppliers
                    </a>

                    <div class="nav-section">Inventory</div>
                    <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> Inventory
                        @if($lowStockCount > 0)
                            <span class="badge-low-stock">{{ $lowStockCount }} low</span>
                        @endif
                    </a>
                    <a href="{{ route('warehouse.index') }}" class="nav-link {{ request()->routeIs('warehouse.*') ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i> Warehouse
                    </a>

                    <div class="nav-section">Products</div>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="{{ route('recipes.index') }}" class="nav-link {{ request()->routeIs('recipes.*') ? 'active' : '' }}">
                        <i class="fas fa-utensils"></i> Recipes
                    </a>

                    <div class="nav-section">Reports</div>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>

                    @if($isAdmin)
                        <div class="nav-section">System</div>
                        <a href="{{ route('admin.database') }}" class="nav-link {{ request()->routeIs('admin.database') ? 'active' : '' }}">
                            <i class="fas fa-database"></i> Database
                        </a>
                        <a href="{{ route('sync.index') }}" class="nav-link {{ request()->routeIs('sync.*') ? 'active' : '' }}">
                            <i class="fas fa-sync"></i> Sync
                        </a>
                    @endif
                @endif
            @endauth
        </div>

        <div class="sidebar-footer">
            @auth
                @if($isAdmin || $isManager)
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; width: 100%; text-align: left; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar-custom">
            <div class="left-section">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 d-none d-sm-block">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="right-section">
                <!-- Search Box -->
                <div class="search-box">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="globalSearch" placeholder="Search..." onkeyup="handleSearch(this.value)" autocomplete="off">
                    <span class="search-shortcut">Ctrl+K</span>
                    <div class="search-results" id="searchResults"></div>
                </div>

                <div class="user-info">
                    <span id="connectionStatus">
                        <span class="status-dot online" id="statusDot"></span>
                        <span id="statusText" class="text-muted d-none d-md-inline" style="font-size: 13px;">Online</span>
                    </span>
                    @auth
                        <span class="text-muted d-none d-md-inline">{{ Auth::user()->name }}</span>
                        <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    @endauth
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-detect offline/online status
        function updateConnectionStatus() {
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');
            
            if (navigator.onLine) {
                statusDot.className = 'status-dot online';
                statusText.textContent = 'Online';
                statusText.style.color = '#28a745';
            } else {
                statusDot.className = 'status-dot offline';
                statusText.textContent = 'Offline';
                statusText.style.color = '#dc3545';
            }
        }

        updateConnectionStatus();
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);

        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar on outside click
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Global Search
        let searchTimeout;

        function handleSearch(query) {
            const resultsContainer = document.getElementById('searchResults');
            
            if (query.length < 2) {
                resultsContainer.classList.remove('show');
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        renderSearchResults(data, query);
                    })
                    .catch(() => {
                        resultsContainer.innerHTML = '<div class="no-results">Error searching</div>';
                        resultsContainer.classList.add('show');
                    });
            }, 300);
        }

        function renderSearchResults(data, query) {
            const container = document.getElementById('searchResults');
            
            if (data.length === 0) {
                container.innerHTML = `<div class="no-results">No results found for "<strong>${query}</strong>"</div>`;
                container.classList.add('show');
                return;
            }

            let html = '';
            data.forEach(item => {
                const iconMap = {
                    'product': 'fa-box',
                    'customer': 'fa-user',
                    'branch': 'fa-store',
                    'staff': 'fa-user-tie',
                    'item': 'fa-boxes',
                    'sale': 'fa-receipt',
                    'order': 'fa-clock'
                };
                const colorMap = {
                    'product': '#6F4E37',
                    'customer': '#0d6efd',
                    'branch': '#28a745',
                    'staff': '#6c757d',
                    'item': '#ffc107',
                    'sale': '#17a2b8',
                    'order': '#fd7e14'
                };
                const icon = iconMap[item.type] || 'fa-search';
                const color = colorMap[item.type] || '#6F4E37';
                
                html += `
                    <a href="${item.url}" class="result-item">
                        <div class="result-icon" style="background: ${color}20; color: ${color};">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div class="result-info">
                            <div class="result-title">${item.title}</div>
                            <div class="result-sub">${item.subtitle || ''}</div>
                        </div>
                        <span class="result-badge">${item.type}</span>
                    </a>
                `;
            });

            html += `
                <div class="search-more">
                    <a href="{{ route('search.results') }}?q=${encodeURIComponent(query)}">View all results →</a>
                </div>
            `;

            container.innerHTML = html;
            container.classList.add('show');
        }

        // Close search results on outside click
        document.addEventListener('click', function(e) {
            const searchBox = document.querySelector('.search-box');
            if (searchBox && !searchBox.contains(e.target)) {
                document.getElementById('searchResults').classList.remove('show');
            }
        });

        // Keyboard shortcut: Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('globalSearch').focus();
            }
            if (e.key === 'Escape') {
                document.getElementById('globalSearch').blur();
                document.getElementById('searchResults').classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>