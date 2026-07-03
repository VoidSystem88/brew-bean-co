<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>@yield('page-title', 'Brew & Bean Co.')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-brown: #6F4E37;
            --secondary-brown: #8B6B4A;
        }
        body {
            background: #f8f6f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 60px;
        }
        .customer-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border-bottom: 1px solid #eee;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .customer-nav .brand {
            font-weight: 700;
            font-size: 18px;
            color: #6F4E37;
            text-decoration: none;
        }
        .customer-nav .brand i {
            color: #ffd700;
            margin-right: 6px;
        }
        .customer-nav .menu-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: #333;
            cursor: pointer;
            padding: 4px 8px;
        }
        .customer-nav .menu-btn:hover {
            color: #6F4E37;
        }
        .customer-nav .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .customer-nav .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .customer-nav .user-info .avatar {
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
        }
        .customer-nav .user-info .user-name {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .mobile-sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 999;
        }
        .mobile-sidebar-overlay.show { display: block; }
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100%;
            background: white;
            z-index: 1000;
            padding: 20px;
            transition: left 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .mobile-sidebar.open { left: 0; }
        .mobile-sidebar .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
            float: right;
        }
        .mobile-sidebar .close-btn:hover { color: #333; }
        .mobile-sidebar .avatar {
            width: 56px; height: 56px; border-radius: 50%;
            background: #6F4E37; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 700;
            margin: 0 auto 8px;
        }
        .mobile-sidebar .customer-name { text-align: center; font-weight: 600; font-size: 16px; color: #333; }
        .mobile-sidebar .customer-code { text-align: center; font-size: 12px; color: #999; margin-bottom: 10px; }
        .mobile-sidebar .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 8px;
            color: #666; text-decoration: none; transition: 0.2s; font-size: 14px;
        }
        .mobile-sidebar .nav-link:hover { background: #f8f6f4; color: #333; }
        .mobile-sidebar .nav-link.active { background: #f5f0eb; color: #6F4E37; font-weight: 500; }
        .mobile-sidebar .nav-link i { width: 20px; text-align: center; color: #999; }
        .mobile-sidebar .divider { height: 1px; background: #eee; margin: 10px 0; }
        .mobile-sidebar .logout-btn {
            width: 100%; text-align: left;
            background: none; border: none;
            padding: 10px 14px; border-radius: 8px;
            color: #dc3545; font-size: 14px;
            transition: 0.2s; display: flex; align-items: center; gap: 10px; cursor: pointer;
        }
        .mobile-sidebar .logout-btn:hover { background: #fff5f5; }

        .sidebar-qr {
            background: #f8f6f4;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            margin: 10px 0;
        }
        .sidebar-qr canvas { max-width: 120px; height: auto; }
        .sidebar-qr .qr-label { font-size: 11px; color: #999; margin-top: 4px; }

        .sidebar-content { flex: 1; }
        .sidebar-footer { border-top: 1px solid #eee; padding-top: 10px; }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 30px;
        }

        @media (max-width: 768px) {
            body { padding-top: 56px; }
            .customer-nav { padding: 8px 15px; }
            .customer-nav .brand { font-size: 16px; }
            .customer-nav .user-info .user-name { display: none; }
            .main-content { padding: 15px; }
            .mobile-sidebar { width: 280px; left: -280px; }
        }
        
        /* Leaflet map fix */
        .leaflet-container {
            z-index: 1;
        }
        .modal-mini-map .leaflet-container {
            height: 100%;
            width: 100%;
        }
        
        /* Coffee Marker for Leaflet */
        .coffee-marker {
            background: #6F4E37;
            color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .home-marker {
            background: #4285F4;
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Badge for orders */
        .order-badge {
            background: #dc3545;
            color: white;
            font-size: 10px;
            padding: 1px 8px;
            border-radius: 10px;
            margin-left: auto;
        }
    </style>
</head>
<body>

    <!-- Customer Top Navigation -->
    <nav class="customer-nav">
        <div>
            <button class="menu-btn" onclick="toggleMobileSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('customer.dashboard') }}" class="brand">
                <i class="fas fa-mug-hot"></i> Brew & Bean
            </a>
        </div>
        <div class="nav-links">
            @auth('customer')
                <div class="user-info">
                    <span class="user-name">{{ Auth::guard('customer')->user()->name }}</span>
                    <div class="avatar">{{ strtoupper(substr(Auth::guard('customer')->user()->name, 0, 1)) }}</div>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" id="mobileOverlay" onclick="toggleMobileSidebar()"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <button class="close-btn" onclick="toggleMobileSidebar()">
            <i class="fas fa-times"></i>
        </button>

        @auth('customer')
        <!-- Profile -->
        <div style="text-align:center;padding-top:10px;">
            <div class="avatar">{{ strtoupper(substr(Auth::guard('customer')->user()->name, 0, 1)) }}</div>
            <div class="customer-name">{{ Auth::guard('customer')->user()->name }}</div>
            <div class="customer-code">{{ Auth::guard('customer')->user()->customer_code }}</div>
        </div>

        <div class="divider"></div>

        <!-- Menu Links -->
        <div class="sidebar-content">
            <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Menu
            </a>
            <a href="{{ route('customer.loyalty') }}" class="nav-link {{ request()->routeIs('customer.loyalty') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Loyalty Points
            </a>
            <a href="{{ route('customer.orders') }}" class="nav-link {{ request()->routeIs('customer.orders') ? 'active' : '' }}">
                <i class="fas fa-box"></i> My Orders
                <span class="order-badge" id="orderBadge" style="display:none;">0</span>
            </a>
            <a href="{{ route('customer.profile') }}" class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i> My Profile
            </a>
        </div>

        <div class="divider"></div>

        <!-- QR Code -->
        <div class="sidebar-qr">
            <div id="sidebar-qrcode"></div>
            <div class="qr-label"><i class="fas fa-qrcode me-1"></i> Scan for faster checkout</div>
        </div>

        <div class="divider"></div>

        <!-- Logout -->
        <div class="sidebar-footer">
            <form action="{{ route('customer.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        @endauth
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <script>
        function toggleMobileSidebar() {
            document.getElementById('mobileSidebar').classList.toggle('open');
            document.getElementById('mobileOverlay').classList.toggle('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const qrContainer = document.getElementById('sidebar-qrcode');
            if (qrContainer && typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: '{{ route('customer.qr', Auth::guard('customer')->user()->id ?? 0) }}',
                    width: 120,
                    height: 120,
                    colorDark: '#6F4E37',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>