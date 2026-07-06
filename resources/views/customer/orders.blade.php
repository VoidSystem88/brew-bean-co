@extends('layouts.customer')

@section('page-title', 'My Orders')

@section('content')
<style>
    :root {
        --primary: #6F4E37;
        --primary-light: #8B6B4A;
        --primary-dark: #4A3228;
        --bg: #F5EDE6;
        --card-shadow: 0 2px 16px rgba(74, 50, 40, 0.08);
        --radius: 16px;
    }
    
    * { box-sizing: border-box; }
    
    .orders-container {
        max-width: 480px;
        margin: 0 auto;
        padding: 0 0 20px 0;
    }
    
    /* Header */
    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px 12px;
        background: white;
        border-bottom: 1px solid #f0ebe6;
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(12px);
        background: rgba(255,255,255,0.92);
    }
    
    .orders-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.3px;
    }
    
    .orders-header .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }
    
    .orders-header .header-actions .badge-count {
        background: var(--primary);
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    
    .orders-header .header-actions .filter-btn {
        background: none;
        border: none;
        font-size: 18px;
        color: #999;
        cursor: pointer;
        padding: 4px;
        transition: 0.2s;
    }
    
    .orders-header .header-actions .filter-btn:hover {
        color: var(--primary);
    }
    
    /* Filter Dropdown */
    .filter-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        border: 1px solid #f0ebe6;
        padding: 8px 0;
        min-width: 160px;
        z-index: 20;
        margin-top: 4px;
    }
    
    .filter-dropdown.show {
        display: block;
        animation: slideDown 0.2s ease;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .filter-dropdown .filter-option {
        padding: 10px 18px;
        font-size: 13px;
        color: #666;
        cursor: pointer;
        transition: 0.15s;
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }
    
    .filter-dropdown .filter-option:hover {
        background: #f8f6f4;
        color: var(--primary);
    }
    
    .filter-dropdown .filter-option.active {
        color: var(--primary);
        font-weight: 600;
        background: #f8f6f4;
    }
    
    .filter-dropdown .filter-option .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ddd;
        flex-shrink: 0;
    }
    
    .filter-dropdown .filter-option .dot.active-dot {
        background: var(--primary);
    }
    
    .filter-dropdown .filter-option .count {
        margin-left: auto;
        font-size: 11px;
        color: #ccc;
    }
    
    /* Order Cards */
    .orders-list {
        padding: 8px 12px 80px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .order-card {
        background: white;
        border-radius: var(--radius);
        border: 1px solid #f0ebe6;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: var(--card-shadow);
        animation: cardIn 0.4s ease;
    }
    
    .order-card:active {
        transform: scale(0.98);
    }
    
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .order-card .card-content {
        padding: 16px 18px 14px;
    }
    
    /* Order Header */
    .order-card .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .order-card .order-id {
        font-size: 13px;
        font-weight: 600;
        color: #888;
        letter-spacing: 0.3px;
    }
    
    .order-card .order-id span {
        color: var(--primary);
        font-weight: 700;
    }
    
    .order-card .order-date {
        font-size: 11px;
        color: #bbb;
    }
    
    /* ===== FIXED: CENTERED PROGRESS TIMELINE ===== */
    .progress-timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0 8px;
        position: relative;
        margin: 0 4px;
    }
    
    /* Background line - centered */
    .progress-timeline::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #f0ebe6;
        transform: translateY(-50%);
        z-index: 0;
        border-radius: 2px;
    }
    
    /* Progress fill line - centered */
    .progress-timeline .progress-track {
        position: absolute;
        top: 50%;
        left: 10%;
        height: 2px;
        background: var(--primary);
        transform: translateY(-50%);
        z-index: 1;
        transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        width: 0%;
        border-radius: 2px;
        max-width: 80%;
    }
    
    /* Each step - centered */
    .progress-timeline .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        z-index: 2;
        flex: 1;
        position: relative;
        cursor: default;
    }
    
    /* Step icon - perfectly centered on the line */
    .progress-timeline .step .step-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 2.5px solid #f0ebe6;
        background: white;
        color: #ddd;
        position: relative;
        z-index: 3;
        flex-shrink: 0;
    }
    
    /* Completed state */
    .progress-timeline .step.completed .step-icon {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        box-shadow: 0 4px 12px rgba(111, 78, 55, 0.25);
        transform: scale(1.05);
    }
    
    /* Active state with pulse */
    .progress-timeline .step.active .step-icon {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        box-shadow: 0 0 0 4px rgba(111, 78, 55, 0.15);
        animation: pulse-active 2s infinite;
    }
    
    @keyframes pulse-active {
        0% { box-shadow: 0 0 0 0 rgba(111, 78, 55, 0.2); }
        70% { box-shadow: 0 0 0 8px rgba(111, 78, 55, 0); }
        100% { box-shadow: 0 0 0 0 rgba(111, 78, 55, 0); }
    }
    
    /* Pending state */
    .progress-timeline .step.pending .step-icon {
        background: #f5f5f5;
        border-color: #eee;
        color: #ccc;
    }
    
    /* Step labels */
    .progress-timeline .step .step-label {
        font-size: 7px;
        font-weight: 500;
        color: #bbb;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: 0.3s;
        text-align: center;
        line-height: 1.2;
        white-space: nowrap;
    }
    
    .progress-timeline .step.completed .step-label,
    .progress-timeline .step.active .step-label {
        color: var(--primary);
        font-weight: 600;
    }
    
    .progress-timeline .step.pending .step-label {
        color: #ddd;
    }
    
    /* Order Details */
    .order-card .order-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #f5f5f5;
        margin-top: 6px;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .order-card .order-items {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        flex: 1;
    }
    
    .order-card .order-items .item-chip {
        background: #f8f6f4;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        color: #888;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .order-card .order-items .item-chip .qty {
        color: #ccc;
        font-size: 10px;
    }
    
    .order-card .order-items .more-chip {
        background: transparent;
        padding: 2px 6px;
        font-size: 10px;
        color: #ccc;
    }
    
    .order-card .order-total {
        font-weight: 700;
        font-size: 16px;
        color: var(--primary);
        letter-spacing: -0.3px;
        flex-shrink: 0;
        margin-left: auto;
    }
    
    /* Actions */
    .order-card .order-actions {
        display: flex;
        gap: 6px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f5f5f5;
        flex-wrap: wrap;
    }
    
    .order-card .order-actions .btn {
        flex: 1;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-width: 60px;
    }
    
    .order-card .order-actions .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .order-card .order-actions .btn-primary:hover {
        background: var(--primary-dark);
        transform: scale(1.02);
    }
    
    .order-card .order-actions .btn-outline {
        background: transparent;
        color: #888;
        border: 1px solid #e8e8e8;
    }
    
    .order-card .order-actions .btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .order-card .order-actions .btn-danger {
        background: #fff5f5;
        color: #dc3545;
        border: 1px solid #f5c6cb;
    }
    
    .order-card .order-actions .btn-danger:hover {
        background: #dc3545;
        color: white;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--radius);
        border: 1px solid #f0ebe6;
        margin: 20px 12px;
    }
    
    .empty-state .empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #f8f6f4;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
        color: #ddd;
    }
    
    .empty-state h4 {
        font-size: 17px;
        color: #333;
        margin-bottom: 6px;
        font-weight: 600;
    }
    
    .empty-state p {
        font-size: 13px;
        color: #999;
        margin-bottom: 20px;
    }
    
    .empty-state .btn-shop {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 32px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    
    .empty-state .btn-shop:hover {
        background: var(--primary-dark);
        transform: scale(1.02);
    }
    
    /* Responsive */
    @media (max-width: 480px) {
        .orders-container { padding: 0; }
        .orders-header { padding: 12px 16px; }
        .orders-header h2 { font-size: 17px; }
        .orders-list { padding: 6px 8px 70px; gap: 10px; }
        .order-card .card-content { padding: 12px 14px 10px; }
        
        /* Mobile timeline */
        .progress-timeline .step .step-icon { width: 22px; height: 22px; font-size: 9px; }
        .progress-timeline .step .step-label { font-size: 6px; }
        .progress-timeline::before { left: 6%; right: 6%; }
        .progress-timeline .progress-track { left: 6%; max-width: 88%; }
        
        .order-card .order-total { font-size: 14px; }
        .order-card .order-actions .btn { font-size: 10px; padding: 4px 10px; }
        .filter-dropdown { right: -10px; min-width: 140px; }
    }
    
    @media (min-width: 600px) {
        .orders-container { max-width: 480px; padding: 0; }
        .orders-list { padding: 8px 12px 80px; }
    }
    
    @media (max-width: 380px) {
        .progress-timeline .step .step-label { font-size: 5px; letter-spacing: 0.2px; }
        .progress-timeline .step .step-icon { width: 18px; height: 18px; font-size: 7px; border-width: 2px; }
        .progress-timeline { padding: 8px 0 4px; }
        .progress-timeline::before { left: 4%; right: 4%; }
        .progress-timeline .progress-track { left: 4%; max-width: 92%; }
    }
</style>

<div class="orders-container">
    <!-- Header -->
    <header class="orders-header">
        <h2>My Orders</h2>
        <div class="header-actions">
            @php
                $pendingCount = $orders->filter(function($o) { 
                    return in_array($o->delivery_status, ['pending', 'preparing', 'ready', 'out_for_delivery']); 
                })->count();
            @endphp
            @if($pendingCount > 0)
                <span class="badge-count">{{ $pendingCount }}</span>
            @endif
            <button class="filter-btn" onclick="toggleFilter()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M6 12h12M10 18h4"/>
                </svg>
            </button>
            
            <!-- Filter Dropdown -->
            <div class="filter-dropdown" id="filterDropdown">
                <button class="filter-option active" data-filter="all" onclick="applyFilter('all')">
                    <span class="dot active-dot"></span> All Orders
                    <span class="count">{{ $orders->total() }}</span>
                </button>
                <button class="filter-option" data-filter="pending" onclick="applyFilter('pending')">
                    <span class="dot" style="background:#ffc107;"></span> Pending
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'pending')->count() }}</span>
                </button>
                <button class="filter-option" data-filter="preparing" onclick="applyFilter('preparing')">
                    <span class="dot" style="background:#17a2b8;"></span> Preparing
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'preparing')->count() }}</span>
                </button>
                <button class="filter-option" data-filter="ready" onclick="applyFilter('ready')">
                    <span class="dot" style="background:#28a745;"></span> Ready
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'ready')->count() }}</span>
                </button>
                <button class="filter-option" data-filter="out_for_delivery" onclick="applyFilter('out_for_delivery')">
                    <span class="dot" style="background:#6F4E37;"></span> Out for Delivery
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'out_for_delivery')->count() }}</span>
                </button>
                <button class="filter-option" data-filter="completed" onclick="applyFilter('completed')">
                    <span class="dot" style="background:#28a745;"></span> Completed
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'completed')->count() }}</span>
                </button>
                <button class="filter-option" data-filter="cancelled" onclick="applyFilter('cancelled')">
                    <span class="dot" style="background:#dc3545;"></span> Cancelled
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'cancelled')->count() }}</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="orders-list" id="ordersList">
            @foreach($orders as $order)
                @php
                    $status = $order->delivery_status ?? 'pending';
                    $statusFlow = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed'];
                    $currentIndex = array_search($status, $statusFlow);
                    if ($currentIndex === false) $currentIndex = 0;
                    
                    $stepLabels = ['Placed', 'Preparing', 'Ready', 'Delivering', 'Completed'];
                    $totalSteps = count($stepLabels);
                    $progressPercent = ($currentIndex / ($totalSteps - 1)) * 100;
                    
                    $branchName = str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name ?? 'N/A');
                    $isCancelled = $status === 'cancelled';
                @endphp
                <div class="order-card" data-status="{{ $status }}" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                    <div class="card-content">
                        <!-- Header -->
                        <div class="order-header">
                            <span class="order-id">Order <span>#{{ $order->id }}</span></span>
                            <span class="order-date">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <!-- Progress Timeline - Centered -->
                        <div class="progress-timeline">
                            <div class="progress-track" style="width: {{ $isCancelled ? '0%' : $progressPercent }}%;"></div>
                            @foreach($stepLabels as $index => $label)
                                @php
                                    $stepStatus = 'pending';
                                    if ($isCancelled) {
                                        $stepStatus = 'pending';
                                    } elseif ($index < $currentIndex) {
                                        $stepStatus = 'completed';
                                    } elseif ($index === $currentIndex) {
                                        $stepStatus = 'active';
                                    }
                                @endphp
                                <div class="step {{ $stepStatus }}">
                                    <div class="step-icon">
                                        @if($stepStatus === 'completed')
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        @elseif($stepStatus === 'active')
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        @else
                                            <span style="font-size:8px;opacity:0.3;">●</span>
                                        @endif
                                    </div>
                                    <span class="step-label">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Order Details -->
                        <div class="order-details">
                            <div class="order-items">
                                @foreach($order->orders->take(2) as $item)
                                    <span class="item-chip">
                                        {{ \Illuminate\Support\Str::limit($item->product->name ?? 'Unknown', 20) }}
                                        <span class="qty">×{{ $item->quantity }}</span>
                                    </span>
                                @endforeach
                                @if($order->orders->count() > 2)
                                    <span class="more-chip">+{{ $order->orders->count() - 2 }}</span>
                                @endif
                            </div>
                            <span class="order-total">₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        
                        <!-- Actions -->
                        @if(!$isCancelled)
                            <div class="order-actions">
                                @if($status === 'pending')
                                    <button class="btn btn-danger" onclick="cancelOrder({{ $order->id }})">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Cancel
                                    </button>
                                @endif
                                @if(in_array($status, ['ready', 'out_for_delivery']))
                                    <a href="{{ route('customer.track', $order->id) }}" class="btn btn-primary">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polyline points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18" r="2.5"/><circle cx="18.5" cy="18" r="2.5"/></svg>
                                        Track
                                    </a>
                                @endif
                                <a href="{{ route('customer.track', $order->id) }}" class="btn btn-outline">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Details
                                </a>
                            </div>
                        @else
                            <div class="order-actions">
                                <span style="font-size:11px;color:#dc3545;padding:4px 0;">Order cancelled</span>
                                <a href="{{ route('customer.track', $order->id) }}" class="btn btn-outline" style="flex:0 1 auto;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    View
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
            <div style="padding: 0 16px 20px;">
                {{ $orders->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <h4>No orders yet</h4>
            <p>Start exploring our menu and place your first order</p>
            <a href="{{ route('customer.dashboard') }}" class="btn-shop">
                Browse Menu
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    let currentFilter = 'all';
    
    function toggleFilter() {
        const dropdown = document.getElementById('filterDropdown');
        dropdown.classList.toggle('show');
    }
    
    function applyFilter(filter) {
        currentFilter = filter;
        
        // Update dropdown
        document.querySelectorAll('.filter-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.filter === filter);
            const dot = opt.querySelector('.dot');
            if (dot) {
                dot.classList.toggle('active-dot', opt.dataset.filter === filter);
            }
        });
        
        // Close dropdown
        document.getElementById('filterDropdown').classList.remove('show');
        
        // Filter cards
        const cards = document.querySelectorAll('.order-card');
        let visible = 0;
        cards.forEach(card => {
            const status = card.dataset.status;
            if (filter === 'all' || status === filter) {
                card.style.display = 'block';
                card.style.animation = 'cardIn 0.3s ease';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function cancelOrder(orderId) {
        if (!confirm('Cancel this order?')) return;
        
        const btn = event?.target?.closest?.('.btn-danger');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '...';
        }
        
        fetch('/customer/orders/' + orderId + '/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order cancelled successfully.');
                location.reload();
            } else {
                alert(data.message || 'Failed to cancel order');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = 'Cancel';
                }
            }
        })
        .catch(() => {
            alert('Error cancelling order. Please try again.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Cancel';
            }
        });
    }
    
    // Close filter on outside click
    document.addEventListener('click', function(e) {
        const container = document.querySelector('.header-actions');
        if (container && !container.contains(e.target)) {
            document.getElementById('filterDropdown').classList.remove('show');
        }
    });
    
    // Auto-refresh pending orders
    let refreshInterval;
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            const hasPending = document.querySelector('.order-card[data-status="pending"], .order-card[data-status="preparing"], .order-card[data-status="ready"], .order-card[data-status="out_for_delivery"]');
            if (hasPending) {
                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        // Reload the page to refresh order status
                        location.reload();
                    })
                    .catch(() => {});
            }
        }, 30000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();
    });
</script>
@endpush
@endsection