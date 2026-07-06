<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>@yield('page-title', 'Brew & Bean Co.')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-brown: #6F4E37;
            --secondary-brown: #8B6B4A;
            --gold: #C9A96E;
            --bottom-nav-height: 68px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f8f6f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 60px;
            padding-bottom: calc(var(--bottom-nav-height) + 16px);
        }
        
        .customer-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border-bottom: 1px solid #eee;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .customer-nav .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #333;
        }
        
        .customer-nav .brand .logo {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            background: var(--primary-brown);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .customer-nav .brand .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .customer-nav .brand .logo .placeholder {
            color: white;
            font-size: 18px;
        }
        
        .customer-nav .brand .brand-text {
            font-weight: 700;
            font-size: 16px;
            color: var(--primary-brown);
        }
        
        .customer-nav .brand .brand-text small {
            display: block;
            font-weight: 400;
            font-size: 10px;
            color: #999;
            margin-top: -2px;
        }
        
        .customer-nav .points-badge {
            background: linear-gradient(135deg, #6F4E37, #8B6B4A);
            color: white;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(111, 78, 55, 0.25);
            transition: all 0.3s ease;
        }
        
        .customer-nav .points-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(111, 78, 55, 0.35);
            color: white;
        }
        
        .customer-nav .points-badge i {
            color: #ffd700;
            font-size: 13px;
        }
        
        .customer-nav .points-badge .pts {
            font-size: 10px;
            font-weight: 400;
            opacity: 0.8;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px 16px 20px;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 999;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: var(--bottom-nav-height);
            padding: 4px 0 env(safe-area-inset-bottom, 4px);
            box-shadow: 0 -2px 12px rgba(0,0,0,0.06);
        }
        
        .bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            text-decoration: none;
            color: #999;
            font-size: 10px;
            font-weight: 500;
            padding: 4px 12px;
            border: none;
            background: none;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            min-width: 56px;
        }
        
        .bottom-nav .nav-item i {
            font-size: 22px;
            transition: all 0.2s;
        }
        
        .bottom-nav .nav-item.active {
            color: var(--primary-brown);
        }
        
        .bottom-nav .nav-item.active i {
            transform: scale(1.05);
        }
        
        .bottom-nav .nav-item .nav-label {
            font-size: 9px;
            letter-spacing: 0.3px;
        }
        
        .bottom-nav .nav-item .notif-dot {
            position: absolute;
            top: 0;
            right: 6px;
            width: 10px;
            height: 10px;
            background: #dc3545;
            border-radius: 50%;
            border: 2px solid white;
            display: none;
            animation: pulse-dot 1.5s infinite;
        }
        
        .bottom-nav .nav-item .notif-dot.show {
            display: block;
        }
        
        .bottom-nav .nav-item .notif-badge {
            position: absolute;
            top: -2px;
            right: 2px;
            background: #dc3545;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 10px;
            border: 2px solid white;
            min-width: 18px;
            text-align: center;
            line-height: 14px;
            display: none;
        }
        
        .bottom-nav .nav-item .notif-badge.show {
            display: block;
        }
        
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.3); }
        }
        
        @media (max-width: 576px) {
            body { padding-top: 56px; }
            .customer-nav { height: 56px; padding: 6px 12px; }
            .customer-nav .brand .brand-text { font-size: 14px; }
            .customer-nav .brand .brand-text small { display: none; }
            .customer-nav .points-badge { 
                padding: 3px 10px;
                font-size: 11px;
            }
            .customer-nav .points-badge i { font-size: 11px; }
            .main-content { padding: 10px 12px 16px; }
            .bottom-nav .nav-item { padding: 2px 8px; min-width: 48px; }
            .bottom-nav .nav-item i { font-size: 20px; }
            .bottom-nav .nav-item .nav-label { font-size: 8px; }
            .bottom-nav .nav-item .notif-badge {
                font-size: 8px;
                padding: 0 5px;
                min-width: 16px;
                line-height: 12px;
            }
            .bottom-nav .nav-item .notif-dot { width: 8px; height: 8px; right: 4px; }
        }
        
        .leaflet-container { z-index: 1; }
        
        .coffee-marker {
            background: var(--primary-brown);
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
        
        .sidebar-qr {
            background: #f8f6f4;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            margin: 10px 0;
        }
        .sidebar-qr img {
            max-width: 120px;
            height: auto;
            border-radius: 8px;
            border: 2px solid #e8e8e8;
            padding: 6px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
            
        /* ===== CHECKOUT BUBBLE ===== */
        .checkout-bubble {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 999;
            background: #6F4E37;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 20px rgba(111, 78, 55, 0.4);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: none;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-family: inherit;
            min-width: 60px;
            justify-content: center;
        }
        
        .checkout-bubble:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 30px rgba(111, 78, 55, 0.5);
            background: #5a3d2b;
            color: white;
        }
        
        .checkout-bubble:active {
            transform: scale(0.95);
        }
        
        .checkout-bubble .bubble-badge {
            background: #ffd700;
            color: #333;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        
        .checkout-bubble .bubble-total {
            color: #ffd700;
            font-size: 15px;
            font-weight: 700;
        }
        
        .checkout-bubble.show {
            display: flex;
            animation: bubbleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .checkout-bubble.hide {
            animation: bubbleOut 0.3s ease forwards;
        }
        
        @keyframes bubbleIn {
            0% { transform: scale(0.5) translateY(40px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }
        
        @keyframes bubbleOut {
            0% { transform: scale(1) translateY(0); opacity: 1; }
            100% { transform: scale(0.5) translateY(40px); opacity: 0; }
        }
        
        .checkout-bubble .bubble-icon {
            font-size: 18px;
        }
        
        @media (max-width: 576px) {
            .checkout-bubble {
                bottom: 72px;
                right: 12px;
                padding: 10px 16px;
                font-size: 12px;
                min-width: 50px;
            }
            .checkout-bubble .bubble-badge {
                width: 20px;
                height: 20px;
                font-size: 10px;
            }
            .checkout-bubble .bubble-total {
                font-size: 13px;
            }
            .checkout-bubble .bubble-icon {
                font-size: 15px;
            }
        }
</style>
</head>
<body>

    <nav class="customer-nav">
        <a href="{{ route('customer.dashboard') }}" class="brand">
            <div class="logo">
                @php
                    $logoPath = Storage::disk('public')->exists('settings/logo.png') 
                        ? asset('storage/settings/logo.png') 
                        : null;
                    $brandName = Storage::disk('public')->exists('settings/brand_name.txt') 
                        ? Storage::disk('public')->get('settings/brand_name.txt') 
                        : 'Brew & Bean Co.';
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="{{ $brandName }}">
                @else
                    <span class="placeholder"><i class="fas fa-store"></i></span>
                @endif
            </div>
            <div class="brand-text">
                {{ $brandName }}
                <small>Your Coffee Shop</small>
            </div>
        </a>

        @auth('customer')
            @php
                $customer = Auth::guard('customer')->user();
                $points = $customer->loyalty_points ?? 0;
            @endphp
            <a href="{{ route('customer.loyalty') }}" class="points-badge" title="View loyalty points">
                <i class="fas fa-star"></i>
                <span>{{ number_format($points) }}</span>
                <span class="pts">pts</span>
            </a>
        @endauth
    </nav>
    <!-- ===== CHECKOUT BUBBLE ===== -->
    <button class="checkout-bubble" id="checkoutBubble" onclick="openCheckout()">
        <span class="bubble-icon"><i class="fas fa-shopping-cart"></i></span>
        <span class="bubble-badge" id="bubbleCount">0</span>
        <span class="bubble-total" id="bubbleTotal">₱0.00</span>
    </button>

    <div class="main-content">
        @yield('content')
    </div>

    <nav class="bottom-nav" id="bottomNav">
        <a href="{{ route('customer.dashboard') }}" class="nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" data-page="menu">
            <i class="fas fa-home"></i>
            <span class="nav-label">Menu</span>
        </a>
        
        <a href="{{ route('customer.orders') }}" class="nav-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}" data-page="orders">
            <i class="fas fa-box"></i>
            <span class="nav-label">Orders</span>
            <span class="notif-badge" id="orderBadge">0</span>
        </a>
        
        <a href="{{ route('customer.loyalty') }}" class="nav-item {{ request()->routeIs('customer.loyalty') ? 'active' : '' }}" data-page="loyalty">
            <i class="fas fa-star"></i>
            <span class="nav-label">Rewards</span>
        </a>
        
        <a href="{{ route('customer.profile') }}" class="nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}" data-page="profile">
            <i class="fas fa-user"></i>
            <span class="nav-label">Profile</span>
        </a>
        
        <form action="{{ route('customer.logout') }}" method="POST" class="nav-item" style="padding:0;margin:0;background:none;border:none;cursor:pointer;">
            @csrf
            <button type="submit" class="nav-item" style="background:none;border:none;padding:4px 12px;cursor:pointer;font-family:inherit;font-size:10px;color:#999;display:flex;flex-direction:column;align-items:center;gap:2px;width:100%;">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-label">Logout</span>
            </button>
        </form>
    </nav>
    <!-- ===== CHECKOUT BUBBLE ===== -->
    <button class="checkout-bubble" id="checkoutBubble" onclick="openCheckout()">
        <span class="bubble-icon"><i class="fas fa-shopping-cart"></i></span>
        <span class="bubble-badge" id="bubbleCount">0</span>
        <span class="bubble-total" id="bubbleTotal">₱0.00</span>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function checkOrderBadge() {
                fetch('/customer/orders/count')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('orderBadge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.classList.add('show');
                            } else {
                                badge.classList.remove('show');
                            }
                        }
                    })
                    .catch(() => {});
            }
            
            checkOrderBadge();
            setInterval(checkOrderBadge, 30000);
            
            const navItems = document.querySelectorAll('.bottom-nav .nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (this.tagName === 'FORM') return;
                    navItems.forEach(n => n.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
    
    @stack('scripts')
    <script>
    // ===== CHECKOUT BUBBLE =====
    let bubbleCart = [];
    let bubbleTotal = 0;
    let bubbleCount = 0;
    
    function updateBubble(cartItems) {
        const bubble = document.getElementById('checkoutBubble');
        const countEl = document.getElementById('bubbleCount');
        const totalEl = document.getElementById('bubbleTotal');
        
        if (!bubble) return;
        
        if (cartItems && cartItems.length > 0) {
            let total = 0;
            let count = 0;
            cartItems.forEach(item => {
                total += (item.price || 0) * (item.quantity || 0);
                count += (item.quantity || 0);
            });
            bubbleTotal = total;
            bubbleCount = count;
            
            countEl.textContent = count;
            totalEl.textContent = '₱' + total.toFixed(2);
            bubble.classList.add('show');
            bubble.classList.remove('hide');
        } else {
            bubble.classList.remove('show');
            bubble.classList.add('hide');
            setTimeout(() => {
                bubble.classList.remove('hide');
            }, 300);
        }
    }
    
    function openCheckout() {
        if (bubbleCount === 0) {
            const bubble = document.getElementById('checkoutBubble');
            bubble.style.background = '#dc3545';
            setTimeout(() => {
                bubble.style.background = '';
            }, 500);
            return;
        }
        if (typeof openCheckoutModal === 'function') {
            openCheckoutModal();
        } else {
            window.location.href = '/customer/dashboard#checkout';
        }
    }
    
    // Listen for cart updates from dashboard
    document.addEventListener('cartUpdated', function(e) {
        if (e.detail && e.detail.cart) {
            updateBubble(e.detail.cart);
        }
    });
    </script>
</body>
</html>