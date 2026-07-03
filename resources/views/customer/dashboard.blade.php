@extends('layouts.customer')

@section('page-title', 'Menu')

@section('content')
<style>
    /* Base Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #f8f6f4; }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 14px 16px;
        border: 1px solid #eee;
        text-align: center;
        transition: 0.2s;
    }
    .stat-card:hover { border-color: #6F4E37; }
    .stat-card .stat-number { font-size: 22px; font-weight: 700; color: #6F4E37; }
    .stat-card .stat-label { color: #999; font-size: 12px; margin-top: 2px; }
    
    .branch-selector {
        background: white;
        border-radius: 10px;
        padding: 10px 16px;
        border: 1px solid #eee;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .branch-selector label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    .branch-selector select {
        padding: 5px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        background: white;
        flex: 1;
        min-width: 150px;
    }
    .branch-selector select:focus {
        border-color: #6F4E37;
        outline: none;
    }
    .branch-selector .branch-stock-info {
        font-size: 12px;
        color: #999;
        margin-left: auto;
    }
    .branch-selector .branch-stock-info .available-count {
        color: #28a745;
        font-weight: 600;
    }
    
    /* Slideshow */
    .slideshow-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: white;
        border: 1px solid #eee;
        height: 240px;
    }
    .slideshow-container .slide {
        display: none;
        height: 100%;
        padding: 20px 30px;
        align-items: center;
        justify-content: space-between;
        background: white;
    }
    .slideshow-container .slide.active { display: flex; }
    .slideshow-container .slide .info { flex: 1; }
    .slideshow-container .slide .info .category { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 4px; }
    .slideshow-container .slide .info .name { font-size: 22px; font-weight: 600; color: #333; }
    .slideshow-container .slide .info .price { font-size: 18px; font-weight: 600; color: #6F4E37; margin-top: 4px; }
    .slideshow-container .slide .info .desc { font-size: 13px; color: #777; margin-top: 4px; max-width: 280px; }
    .slideshow-container .slide .image {
        width: 140px; height: 140px; border-radius: 50%;
        background: #f5f0eb;
        display: flex; align-items: center; justify-content: center;
        font-size: 48px; color: #6F4E37;
        flex-shrink: 0; overflow: hidden;
    }
    .slideshow-container .slide .image img { width: 100%; height: 100%; object-fit: cover; }
    .slide-dots {
        position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 6px;
    }
    .slide-dots .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #ddd; cursor: pointer; border: none; padding: 0;
    }
    .slide-dots .dot.active { background: #6F4E37; width: 24px; border-radius: 4px; }
    
    .btn-add {
        background: #6F4E37; color: white; border: none;
        padding: 4px 16px; border-radius: 16px; font-size: 12px;
        margin-top: 6px; cursor: pointer; transition: 0.2s;
    }
    .btn-add:hover { background: #5a3d2b; }
    .btn-add:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    .btn-check-branches {
        background: transparent;
        color: #6F4E37;
        border: 1px solid #6F4E37;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 10px;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 4px;
        display: inline-block;
    }
    .btn-check-branches:hover {
        background: #6F4E37;
        color: white;
    }
    
    /* Products */
    .product-card {
        cursor: pointer; transition: 0.2s;
        border: 1px solid #eee; border-radius: 10px;
        padding: 10px; text-align: center;
        background: white; height: 100%;
        position: relative;
    }
    .product-card:hover { transform: translateY(-3px); border-color: #6F4E37; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .product-card.sold-out {
        opacity: 0.7;
        cursor: default;
    }
    .product-card.sold-out:hover {
        transform: none;
        border-color: #eee;
        box-shadow: none;
    }
    .product-card .product-image {
        width: 100%; height: 90px; border-radius: 8px;
        overflow: hidden; background: #f8f6f4;
        margin-bottom: 6px; display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .product-card .product-image img { width: 100%; height: 100%; object-fit: cover; }
    .product-card .product-image .no-image { font-size: 28px; color: #ddd; }
    .product-card .product-price { font-size: 14px; font-weight: 600; color: #6F4E37; }
    .product-card .product-name { font-size: 12px; font-weight: 500; margin-bottom: 2px; }
    .product-card .btn-add { font-size: 11px; padding: 2px 12px; }
    .product-card .in-cart-badge {
        position: absolute; top: 4px; right: 4px;
        background: #28a745; color: white;
        border-radius: 50%; width: 22px; height: 22px;
        display: none; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700;
        z-index: 6;
    }
    
    /* Sold Out Badge */
    .sold-out-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #dc3545;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        z-index: 5;
        border: 1px solid white;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }
    .sold-out-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.3);
        border-radius: 10px;
        z-index: 3;
        pointer-events: none;
    }
    
    .availability-status {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        margin-top: 2px;
    }
    .availability-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }
    .availability-dot.available { background: #28a745; }
    .availability-dot.soldout { background: #dc3545; }
    .availability-text {
        font-size: 11px;
        font-weight: 500;
    }
    .availability-text.available { color: #28a745; }
    .availability-text.soldout { color: #dc3545; }
    
    /* Branch availability modal */
    .branch-availability-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .branch-availability-modal.show {
        display: flex;
    }
    .branch-availability-modal .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
        padding: 25px;
        position: relative;
        animation: modalIn 0.3s ease;
    }
    .branch-availability-modal .modal-close {
        position: absolute;
        top: 12px;
        right: 16px;
        background: none;
        border: none;
        font-size: 22px;
        color: #999;
        cursor: pointer;
    }
    .branch-availability-modal .modal-close:hover { color: #333; }
    .branch-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        border-bottom: 1px solid #f5f5f5;
    }
    .branch-item:last-child { border-bottom: none; }
    .branch-item .branch-name {
        font-weight: 500;
        font-size: 14px;
    }
    .branch-item .branch-status {
        font-size: 12px;
        font-weight: 600;
        padding: 2px 12px;
        border-radius: 12px;
    }
    .branch-item .branch-status.available {
        background: #d4edda;
        color: #155724;
    }
    .branch-item .branch-status.unavailable {
        background: #f8d7da;
        color: #721c24;
    }
    .branch-item .branch-distance {
        font-size: 12px;
        color: #999;
    }
    
    /* Cart */
    .cart-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px;
    }
    .cart-item:last-child { border-bottom: none; }
    .qty-btn {
        width: 24px; height: 24px; border-radius: 50%;
        border: 1px solid #ddd; background: white;
        font-weight: 600; font-size: 12px; cursor: pointer; transition: 0.2s;
    }
    .qty-btn:hover { background: #6F4E37; color: white; border-color: #6F4E37; }
    .cart-total { font-size: 18px; font-weight: 600; color: #6F4E37; }
    
    /* Order Modal */
    .order-modal {
        display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6); z-index: 9999;
        align-items: center; justify-content: center; padding: 20px;
    }
    .order-modal.show { display: flex; }
    .order-modal .modal-content {
        background: white; border-radius: 16px;
        max-width: 600px;
        width: 100%;
        max-height: 95vh;
        overflow-y: auto;
        padding: 25px;
        position: relative;
        animation: modalIn 0.3s ease;
    }
    @keyframes modalIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .order-modal .modal-close {
        position: absolute; top: 12px; right: 16px;
        background: none; border: none; font-size: 22px; color: #999; cursor: pointer;
    }
    .order-modal .modal-close:hover { color: #333; }
    .order-modal .order-item {
        display: flex; justify-content: space-between;
        padding: 6px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px;
    }
    .order-modal .order-total {
        font-size: 18px; font-weight: 700; color: #6F4E37;
        margin-top: 8px; padding-top: 8px; border-top: 2px solid #eee;
    }
    .order-modal .discount-info {
        background: #d4edda; padding: 8px 12px; border-radius: 6px;
        margin: 8px 0; color: #155724; font-size: 13px;
    }
    .order-modal .points-info {
        background: #cce5ff; padding: 8px 12px; border-radius: 6px;
        margin: 8px 0; color: #004085; font-size: 13px;
    }
    
    .address-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 10px 14px;
        margin: 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #856404;
    }
    .address-warning .btn-setup {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 3px 14px;
        border-radius: 12px;
        font-size: 12px;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
    }
    .address-warning .btn-setup:hover {
        background: #5a3d2b;
        color: white;
    }
    
    /* Map */
    .modal-mini-map {
        height: 200px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        margin: 10px 0;
        position: relative;
        background: #f8f6f4;
    }
    .modal-mini-map #modalMap {
        height: 100%;
        width: 100%;
    }
    .modal-mini-map .map-loading {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: #999;
        font-size: 13px;
        text-align: center;
    }
    .modal-mini-map .map-loading i {
        font-size: 28px;
        display: block;
        margin-bottom: 6px;
        color: #6F4E37;
    }
    
    .branch-info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 10px;
        background: #f8f6f4;
        border-radius: 8px;
        margin: 6px 0;
        font-size: 13px;
    }
    .branch-info-row i { color: #6F4E37; width: 18px; }
    .branch-info-row .badge-nearest {
        background: #28a745;
        color: white;
        font-size: 10px;
        padding: 1px 10px;
        border-radius: 12px;
        font-weight: 600;
        margin-left: 6px;
    }
    .branch-info-row .distance-value {
        font-weight: 600;
        color: #6F4E37;
    }
    
    .branch-selector-wrapper {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .branch-selector-wrapper select {
        flex: 1;
    }
    .btn-detect-branch {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-detect-branch:hover { background: #5a3d2b; }
    .btn-detect-branch:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-detect-branch i { font-size: 13px; }
    
    .pricing-summary {
        background: #f8f6f4;
        border-radius: 8px;
        padding: 10px 14px;
        margin: 8px 0;
    }
    .pricing-summary .price-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 13px;
    }
    .pricing-summary .price-row.total {
        font-weight: 700;
        font-size: 16px;
        color: #6F4E37;
        border-top: 2px solid #e8e8e8;
        padding-top: 6px;
        margin-top: 4px;
    }
    .pricing-summary .price-row.discount { color: #28a745; }
    .pricing-summary .price-row.original { color: #999; text-decoration: line-through; }
    
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
    .user-marker {
        background: #4285F4;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .location-banner {
        background: white; border-radius: 10px;
        padding: 10px 16px;
        border: 1px solid #eee;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .location-banner .location-text { font-size: 13px; color: #666; }
    .location-banner .location-text i { color: #6F4E37; }
    .location-banner .btn-locate {
        background: #6F4E37; color: white; border: none;
        padding: 4px 14px; border-radius: 20px; font-size: 12px; cursor: pointer;
    }
    .location-banner .btn-locate:hover { background: #5a3d2b; }
    
    /* Filter Buttons */
    .filter-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .filter-btn {
        padding: 3px 14px;
        border-radius: 20px;
        border: 1px solid #ddd;
        background: white;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .filter-btn:hover {
        border-color: #6F4E37;
    }
    .filter-btn.active {
        background: #6F4E37;
        color: white;
        border-color: #6F4E37;
    }
    
    /* Mobile */
    @media (max-width: 576px) {
        .slideshow-container .slide {
            flex-direction: column; text-align: center; padding: 15px;
            height: auto; min-height: 260px;
        }
        .slideshow-container .slide .image { width: 100px; height: 100px; font-size: 32px; margin-top: 8px; }
        .slideshow-container .slide .info .name { font-size: 18px; }
        .slideshow-container { height: auto; min-height: 280px; }
        .col-4 { flex: 0 0 50%; max-width: 50%; }
        .product-card .product-image { height: 70px; }
        .order-modal .modal-content { padding: 20px; margin: 10px; }
        .stat-card .stat-number { font-size: 18px; }
        .modal-mini-map { height: 140px; }
        .branch-selector-wrapper { flex-direction: column; }
        .btn-detect-branch { width: 100%; justify-content: center; }
        .filter-group { justify-content: center; }
        .address-warning { flex-direction: column; text-align: center; }
        .branch-selector { flex-direction: column; align-items: stretch; text-align: center; }
        .branch-selector .branch-stock-info { margin-left: 0; }
    }
</style>

<!-- Location Banner -->
<div class="location-banner">
    <div class="location-text">
        <i class="fas fa-map-marker-alt me-1"></i>
        <span id="locationDisplay">Enable location to find nearest branch</span>
    </div>
    <button class="btn-locate" onclick="getLocation()">
        <i class="fas fa-location-dot me-1"></i> Locate Me
    </button>
</div>

<!-- Branch Selector -->
<div class="branch-selector">
    <label><i class="fas fa-store me-1" style="color:#6F4E37;"></i> Branch:</label>
    <select id="branchSelect" onchange="switchBranch()">
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" {{ $branch->id == $selectedBranchId ? 'selected' : '' }}>
                {{ str_replace('☕ Brew & Bean Co. - ', '', $branch->name) }}
            </option>
        @endforeach
    </select>
    <span class="branch-stock-info">
        <i class="fas fa-box me-1"></i>
        Available: <span class="available-count" id="availableCount">0</span> products
    </span>
</div>

<!-- Stats -->
<div class="row g-2 mb-3">
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-number">{{ $totalOrders }}</div>
            <div class="stat-label">Orders</div>
        </div>
    </div>
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-number">₱{{ number_format($totalSpent, 0) }}</div>
            <div class="stat-label">Spent</div>
        </div>
    </div>
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-number">{{ $customer->loyalty_points ?? 0 }}</div>
            <div class="stat-label">Points</div>
        </div>
    </div>
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-number">{{ $discountRate ?? 0 }}%</div>
            <div class="stat-label">Discount</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-group">
    <button class="filter-btn active" onclick="filterProducts('all')">All</button>
    <button class="filter-btn" onclick="filterProducts('available')">Available</button>
    <button class="filter-btn" onclick="filterProducts('soldout')">Sold Out</button>
</div>

<!-- Slideshow -->
<div class="slideshow-container mb-3" id="slideshow">
    @foreach($products->take(5) as $index => $product)
        <div class="slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
            <div class="info">
                <div class="category">{{ $product->category ?? 'Featured' }}</div>
                <div class="name">{{ $product->name }}</div>
                <div class="price">₱{{ number_format($product->price, 2) }}</div>
                @if($product->description)
                    <div class="desc">{{ Str::limit($product->description, 50) }}</div>
                @endif
                @if($product->is_available)
                    <button class="btn-add" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                        <i class="fas fa-plus me-1"></i> Add
                    </button>
                @else
                    <button class="btn-add" disabled style="background:#dc3545;">
                        <i class="fas fa-times me-1"></i> Sold Out
                    </button>
                @endif
            </div>
            <div class="image">
                @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                    <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <i class="fas fa-coffee"></i>
                @endif
            </div>
        </div>
    @endforeach
    <div class="slide-dots">
        @for($i = 0; $i < min(5, $products->count()); $i++)
            <button class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></button>
        @endfor
    </div>
</div>

<!-- Products & Cart Row -->
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="font-weight:600;color:#333;margin:0;">Menu</h6>
            <input type="text" id="searchProduct" class="form-control form-control-sm" placeholder="Search..." style="width:120px;border-radius:20px;border:1px solid #eee;font-size:13px;">
        </div>
        <div class="row g-2" id="productGrid">
            @foreach($products as $product)
                <div class="col-4 col-lg-3 product-item" data-name="{{ strtolower($product->name) }}" data-available="{{ $product->is_available ? 'true' : 'false' }}" data-product-id="{{ $product->id }}">
                    <div class="product-card {{ $product->is_available ? '' : 'sold-out' }}" onclick="{{ $product->is_available ? 'addToCart('.$product->id.', \''.addslashes($product->name).'\', '.$product->price.')' : '' }}">
                        @if(!$product->is_available)
                            <div class="sold-out-overlay"></div>
                            <div class="sold-out-badge">SOLD OUT</div>
                        @endif
                        <div class="product-image">
                            @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                                <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-name">{{ Str::limit($product->name, 18) }}</div>
                        <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                        
                        <div class="availability-status">
                            @if($product->is_available)
                                <span class="availability-dot available"></span>
                                <span class="availability-text available">Available</span>
                            @else
                                <span class="availability-dot soldout"></span>
                                <span class="availability-text soldout">Sold Out</span>
                            @endif
                        </div>
                        
                        @if($product->is_available)
                            <button class="btn-add" onclick="event.stopPropagation(); addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                                <i class="fas fa-plus"></i>
                            </button>
                        @else
                            <button class="btn-check-branches" onclick="event.stopPropagation(); checkOtherBranches({{ $product->id }}, '{{ addslashes($product->name) }}')">
                                <i class="fas fa-store me-1"></i> Check other branches
                            </button>
                        @endif
                        <span class="in-cart-badge" id="badge_{{ $product->id }}">0</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4 mt-3 mt-lg-0">
        <div class="card border-0 shadow-sm" style="border-radius:10px;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="mb-0" style="font-weight:600;">Your Order</h6>
            </div>
            <div class="card-body" style="max-height:320px;overflow-y:auto;padding:10px 14px;">
                <div id="cartItems">
                    <p class="text-muted text-center py-4" style="font-size:13px;">
                        <i class="fas fa-cart-plus d-block mb-2" style="font-size:22px;color:#ddd;"></i>
                        No items
                    </p>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0 pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="font-size:13px;color:#999;">Total</span>
                    <span class="cart-total" id="cartTotal">₱0.00</span>
                </div>
                <button class="btn w-100" onclick="openOrderModal()" style="background:#6F4E37;color:white;border-radius:6px;padding:8px;font-weight:600;font-size:14px;">
                    <i class="fas fa-shopping-cart me-2"></i> Checkout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
@if($recentPurchases->count() > 0)
<div class="mt-3">
    <h6 style="font-weight:600;color:#333;margin-bottom:8px;">
        <i class="fas fa-history me-2"></i>Recent Purchases
    </h6>
    <div class="card border-0 shadow-sm" style="border-radius:10px;">
        <div class="card-body p-0">
            <div style="padding:8px 14px;">
                @foreach($recentPurchases->take(3) as $sale)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:13px;">
                        <div>
                            <div>#{{ $sale->id }} - {{ str_replace('☕ Brew & Bean Co. - ', '', $sale->branch->name ?? 'Unknown') }}</div>
                            <div style="font-size:11px;color:#999;">{{ $sale->sale_date->format('M d, Y h:i A') }}</div>
                        </div>
                        <div>
                            <span class="badge bg-success">₱{{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Branch Availability Modal -->
<div class="branch-availability-modal" id="branchAvailabilityModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeBranchModal()">&times;</button>
        <h5 class="mb-3" id="branchModalTitle">Branch Availability</h5>
        <div id="branchListContainer">
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <p class="mt-2 text-muted">Checking branches...</p>
            </div>
        </div>
        <button class="btn w-100 mt-3" onclick="closeBranchModal()" style="background:#6F4E37;color:white;border-radius:6px;padding:8px;">
            Close
        </button>
    </div>
</div>

<!-- Order Modal -->
<div class="order-modal" id="orderModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeOrderModal()">&times;</button>
        <h5 class="mb-2">📋 Order Summary</h5>
        
        <!-- Address Warning -->
        <div id="addressWarning" style="display:none;">
            <div class="address-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Please complete your delivery address in your profile first.</span>
                <a href="{{ route('customer.profile') }}" class="btn-setup">
                    <i class="fas fa-user-edit"></i> Setup Address
                </a>
            </div>
        </div>
        
        <div id="orderItemsList"></div>
        
        <div class="pricing-summary" id="pricingSummary">
            <div class="price-row original" id="priceOriginal">Subtotal: ₱0.00</div>
            <div class="price-row discount" id="priceDiscount">Discount: ₱0.00 (0%)</div>
            <div class="price-row total" id="priceTotal">Total: ₱0.00</div>
            <div class="price-row" style="font-size:12px;color:#999;border-top:1px solid #e8e8e8;padding-top:4px;margin-top:2px;">
                <span>Points to earn:</span>
                <span id="pointsToEarn">0</span>
            </div>
        </div>
        
        <div class="modal-mini-map">
            <div id="modalMap">
                <div class="map-loading" id="mapLoading">
                    <i class="fas fa-map"></i>
                    <span>Loading map...</span>
                </div>
            </div>
        </div>
        
        <div id="branchInfoContainer">
            <div class="branch-info-row">
                <i class="fas fa-store"></i>
                <span id="selectedBranchName">Select a branch</span>
                <span class="badge-nearest" id="nearestBadge" style="display:none;">📍 Nearest</span>
            </div>
            <div class="branch-info-row">
                <i class="fas fa-location-arrow"></i>
                <span>Distance: <span class="distance-value" id="selectedBranchDistance">--</span></span>
            </div>
            <div class="branch-info-row" id="deliveryInfoRow" style="display:none;">
                <i class="fas fa-truck"></i>
                <span>Delivery to: <span id="deliveryAddressDisplay" style="font-weight:500;color:#333;"></span></span>
            </div>
        </div>
        
        <div class="mt-2">
            <label class="form-label fw-bold" style="font-size:12px;">Pickup Branch</label>
            <div class="branch-selector-wrapper">
                <select id="orderBranchSelect" class="form-select form-select-sm" onchange="updateModalMap(); updatePricing();">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" 
                                data-lat="{{ $branch->latitude ?? 0 }}" 
                                data-lng="{{ $branch->longitude ?? 0 }}"
                                data-name="{{ str_replace('☕ Brew & Bean Co. - ', '', $branch->name) }}">
                            {{ str_replace('☕ Brew & Bean Co. - ', '', $branch->name) }}
                        </option>
                    @endforeach
                </select>
                <button class="btn-detect-branch" onclick="detectNearestBranch()" id="detectBranchBtn">
                    <i class="fas fa-location-dot"></i> Detect Nearest
                </button>
            </div>
        </div>
        
        <div class="mt-2" id="deliveryToggleContainer">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="deliveryCheck" onchange="toggleDelivery()">
                <label class="form-check-label" for="deliveryCheck" style="font-size:12px;">
                    <i class="fas fa-truck me-1"></i> Delivery
                </label>
            </div>
        </div>
        
        <div class="mt-2">
            <input type="text" id="orderNotes" class="form-control form-control-sm" placeholder="Special instructions...">
        </div>
        
        <button class="btn w-100 mt-3" onclick="confirmOrder()" id="confirmOrderBtn" style="background:#6F4E37;color:white;border-radius:6px;padding:8px;font-weight:600;">
            <i class="fas fa-check-circle me-2"></i> Confirm Order
        </button>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let cart = [];
    let slideIndex = 0;
    let slideInterval;
    let userLat = null;
    let userLng = null;
    let modalMap = null;
    let modalMarker = null;
    let userMarker = null;
    let routeLine = null;
    let mapInitialized = false;
    let branchData = [];
    let nearestBranchId = null;
    const discountRate = {{ $discountRate ?? 0 }};
    let currentFilter = 'all';
    let allBranches = @json($branches);
    let customerAddress = '{{ $customer->address ?? '' }}';
    let customerLat = '{{ $customer->latitude ?? '' }}';
    let customerLng = '{{ $customer->longitude ?? '' }}';
    let selectedBranchId = {{ $selectedBranchId ?? 0 }};

    console.log('Customer Address:', customerAddress);
    console.log('Customer Lat/Lng:', customerLat, customerLng);
    console.log('All Branches:', allBranches);
    console.log('Selected Branch ID:', selectedBranchId);

    // ============= SWITCH BRANCH =============
    function switchBranch() {
        const branchId = document.getElementById('branchSelect').value;
        window.location.href = `/customer/dashboard?branch_id=${branchId}`;
    }

    function updateAvailableCount() {
        const available = document.querySelectorAll('.product-item[data-available="true"]').length;
        document.getElementById('availableCount').textContent = available;
    }

    // ============= CHECK ADDRESS =============
    function checkCustomerAddress() {
        const warning = document.getElementById('addressWarning');
        const deliveryToggle = document.getElementById('deliveryToggleContainer');
        const deliveryCheck = document.getElementById('deliveryCheck');
        const deliveryInfoRow = document.getElementById('deliveryInfoRow');
        const deliveryAddressDisplay = document.getElementById('deliveryAddressDisplay');
        
        if (customerAddress && customerAddress.trim() !== '') {
            warning.style.display = 'none';
            deliveryToggle.style.display = 'block';
            deliveryInfoRow.style.display = 'flex';
            deliveryAddressDisplay.textContent = customerAddress;
        } else {
            warning.style.display = 'block';
            deliveryToggle.style.display = 'none';
            deliveryCheck.checked = false;
            deliveryInfoRow.style.display = 'none';
        }
    }

    function toggleDelivery() {
        const isChecked = document.getElementById('deliveryCheck').checked;
        
        if (isChecked && (!customerAddress || customerAddress.trim() === '')) {
            alert('Please set up your delivery address in your profile first.');
            document.getElementById('deliveryCheck').checked = false;
        }
    }

    // ============= CHECK OTHER BRANCHES =============
    function checkOtherBranches(productId, productName) {
        const modal = document.getElementById('branchAvailabilityModal');
        const container = document.getElementById('branchListContainer');
        const title = document.getElementById('branchModalTitle');
        
        title.textContent = ' ' + productName + ' - Branch Availability';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <p class="mt-2 text-muted">Checking branches...</p>
            </div>
        `;
        
        let html = '';
        let hasAvailable = false;
        let checked = 0;
        const totalBranches = allBranches.length;
        
        if (totalBranches === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-store fa-2x"></i>
                    <p class="mt-2">No branches available</p>
                </div>
            `;
            return;
        }
        
        allBranches.forEach((branch) => {
            fetch(`/customer/product-stock?product_id=${productId}&branch_id=${branch.id}`)
                .then(response => response.json())
                .then(data => {
                    checked++;
                    const isAvailable = data.available || false;
                    
                    if (isAvailable) hasAvailable = true;
                    
                    const statusClass = isAvailable ? 'available' : 'unavailable';
                    const statusText = isAvailable ? '✅ Available' : '❌ Sold Out';
                    
                    let distanceText = '';
                    if (branch.distance) {
                        const dist = branch.distance < 1 ? (branch.distance * 1000).toFixed(0) + ' m' : branch.distance.toFixed(1) + ' km';
                        distanceText = `📏 ${dist}`;
                    }
                    
                    html += `
                        <div class="branch-item">
                            <div>
                                <div class="branch-name">☕ ${branch.name.replace('☕ Brew & Bean Co. - ', '')}</div>
                                <div class="branch-distance">${distanceText}</div>
                            </div>
                            <span class="branch-status ${statusClass}">${statusText}</span>
                        </div>
                    `;
                    
                    if (checked === totalBranches) {
                        if (!hasAvailable) {
                            html = `
                                <div class="text-center py-4" style="color:#dc3545;">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                    <p class="mt-2">This product is currently sold out in all branches.</p>
                                    <small class="text-muted">Please check back later.</small>
                                </div>
                            `;
                        }
                        container.innerHTML = html;
                    }
                })
                .catch(() => {
                    checked++;
                    html += `
                        <div class="branch-item">
                            <div>
                                <div class="branch-name">☕ ${branch.name.replace('☕ Brew & Bean Co. - ', '')}</div>
                            </div>
                            <span class="branch-status unavailable">⚠️ Check failed</span>
                        </div>
                    `;
                    
                    if (checked === totalBranches) {
                        container.innerHTML = html;
                    }
                });
        });
    }
    
    function closeBranchModal() {
        document.getElementById('branchAvailabilityModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // ============= FILTER PRODUCTS =============
    function filterProducts(filter) {
        currentFilter = filter;
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`.filter-btn[onclick*="${filter}"]`)?.classList.add('active');
        
        document.querySelectorAll('.product-item').forEach(item => {
            const isAvailable = item.dataset.available === 'true';
            
            if (filter === 'all') {
                item.style.display = '';
            } else if (filter === 'available') {
                item.style.display = isAvailable ? '' : 'none';
            } else if (filter === 'soldout') {
                item.style.display = isAvailable ? 'none' : '';
            }
        });
        
        updateAvailableCount();
    }

    // ============= GEOLOCATION =============
    function getLocation() {
        if (navigator.geolocation) {
            document.getElementById('locationDisplay').textContent = 'Getting location...';
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    document.getElementById('locationDisplay').textContent = '📍 Location detected';
                    findNearestBranch(userLat, userLng);
                },
                function() {
                    document.getElementById('locationDisplay').textContent = '⚠️ Unable to get location. Please enable location services.';
                }
            );
        } else {
            document.getElementById('locationDisplay').textContent = '❌ Geolocation not supported';
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function findNearestBranch(lat, lng) {
        // Calculate distances for all branches
        allBranches.forEach(branch => {
            if (branch.latitude && branch.longitude) {
                const dist = calculateDistance(lat, lng, parseFloat(branch.latitude), parseFloat(branch.longitude));
                branch.distance = dist;
                branch.distance_text = dist < 1 ? (dist * 1000).toFixed(0) + ' m' : dist.toFixed(1) + ' km';
            } else {
                branch.distance = null;
                branch.distance_text = 'N/A';
            }
        });

        // Sort by distance
        allBranches.sort((a, b) => (a.distance || 999) - (b.distance || 999));

        // Update select dropdown
        const select = document.getElementById('orderBranchSelect');
        select.innerHTML = '';
        allBranches.forEach((branch, index) => {
            const option = document.createElement('option');
            option.value = branch.id;
            option.dataset.lat = branch.latitude || 0;
            option.dataset.lng = branch.longitude || 0;
            option.dataset.name = branch.name.replace('☕ Brew & Bean Co. - ', '');
            option.dataset.distance = branch.distance || 0;
            const distanceText = branch.distance_text ? ` (${branch.distance_text})` : '';
            option.textContent = branch.name.replace('☕ Brew & Bean Co. - ', '') + distanceText;
            if (index === 0) {
                option.selected = true;
                nearestBranchId = branch.id;
            }
            select.appendChild(option);
        });

        document.getElementById('locationDisplay').textContent = '📍 Nearest branch found!';
        updateModalMap();
        updatePricing();
        updateNearestBadge();
    }

    function detectNearestBranch() {
        const btn = document.getElementById('detectBranchBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    findNearestBranch(userLat, userLng);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect Nearest';
                },
                function() {
                    alert('Unable to get location. Please enable location services or select manually.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect Nearest';
                }
            );
        } else {
            alert('Geolocation is not supported by your browser. Please select manually.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect Nearest';
        }
    }

    function updateNearestBadge() {
        const select = document.getElementById('orderBranchSelect');
        const selectedOption = select.options[select.selectedIndex];
        const badge = document.getElementById('nearestBadge');
        
        if (selectedOption && nearestBranchId && parseInt(selectedOption.value) === nearestBranchId) {
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }

    // ============= CART FUNCTIONS =============
    function addToCart(id, name, price) {
        const productItem = document.querySelector(`.product-item[data-product-id="${id}"]`);
        if (productItem && productItem.dataset.available === 'false') {
            alert('This product is currently sold out.');
            return;
        }
        
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ id: id, name: name, price: price, quantity: 1 });
        }
        updateCart();
        updateBadge(id);
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
        updateBadge(id);
    }

    function updateQuantity(id, change) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(id);
                return;
            }
            updateCart();
            updateBadge(id);
        }
    }

    function updateBadge(id) {
        const item = cart.find(i => i.id === id);
        const badge = document.getElementById('badge_' + id);
        if (badge) {
            if (item && item.quantity > 0) {
                badge.style.display = 'flex';
                badge.textContent = item.quantity;
            } else {
                badge.style.display = 'none';
            }
        }
    }

    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        const cartTotalEl = document.getElementById('cartTotal');
        
        if (!cartItems) return;
        
        if (cart.length === 0) {
            cartItems.innerHTML = `
                <p class="text-muted text-center py-4" style="font-size:13px;">
                    <i class="fas fa-cart-plus d-block mb-2" style="font-size:22px;color:#ddd;"></i>
                    No items
                </p>
            `;
            if (cartTotalEl) cartTotalEl.textContent = '₱0.00';
            return;
        }

        let html = '';
        let total = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            html += `
                <div class="cart-item">
                    <div>
                        <div style="font-weight:500;font-size:13px;">${item.name}</div>
                        <div style="font-size:11px;color:#999;">₱${item.price.toFixed(2)} × ${item.quantity}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
                        <span style="min-width:18px;text-align:center;font-weight:600;">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                        <button class="btn btn-sm btn-danger ms-1" onclick="removeFromCart(${item.id})" style="border-radius:50%;width:22px;height:22px;padding:0;font-size:10px;background:transparent;border:1px solid #f5c6cb;color:#dc3545;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        cartItems.innerHTML = html;
        if (cartTotalEl) cartTotalEl.textContent = '₱' + total.toFixed(2);
    }

    // ============= PRICING CALCULATOR =============
    function calculatePricing() {
        let subtotal = 0;
        
        cart.forEach(item => {
            subtotal += item.price * item.quantity;
        });
        
        const discountAmount = (subtotal * discountRate) / 100;
        const total = subtotal - discountAmount;
        const pointsEarned = Math.floor(total / 100);
        
        return { subtotal, discountAmount, total, pointsEarned, discountRate };
    }

    function updatePricing() {
        const pricing = calculatePricing();
        
        document.getElementById('priceOriginal').textContent = `Subtotal: ₱${pricing.subtotal.toFixed(2)}`;
        
        if (pricing.discountRate > 0) {
            document.getElementById('priceDiscount').textContent = `Discount: -₱${pricing.discountAmount.toFixed(2)} (${pricing.discountRate}%)`;
            document.getElementById('priceDiscount').style.display = 'flex';
        } else {
            document.getElementById('priceDiscount').textContent = `Discount: ₱0.00 (0%)`;
        }
        
        document.getElementById('priceTotal').textContent = `Total: ₱${pricing.total.toFixed(2)}`;
        document.getElementById('pointsToEarn').textContent = pricing.pointsEarned;
    }

    // ============= MODAL MAP =============
    function initModalMap() {
        if (mapInitialized) return;
        
        const mapContainer = document.getElementById('modalMap');
        if (!mapContainer) {
            setTimeout(initModalMap, 500);
            return;
        }

        try {
            const defaultCenter = [14.5995, 120.9842];
            
            modalMap = L.map('modalMap').setView(defaultCenter, 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            }).addTo(modalMap);

            document.getElementById('mapLoading').style.display = 'none';
            mapInitialized = true;
            updateModalMap();
        } catch (e) {
            console.log('Map init error:', e);
            setTimeout(initModalMap, 500);
        }
    }

    function updateModalMap() {
        if (!modalMap || !mapInitialized) {
            initModalMap();
            return;
        }

        const select = document.getElementById('orderBranchSelect');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption) return;
        
        const lat = parseFloat(selectedOption.dataset.lat) || 0;
        const lng = parseFloat(selectedOption.dataset.lng) || 0;
        const name = selectedOption.dataset.name || 'Branch';
        const distance = parseFloat(selectedOption.dataset.distance) || 0;

        document.getElementById('selectedBranchName').textContent = '☕ ' + name;
        updateNearestBadge();
        
        if (distance > 0) {
            const distanceText = distance < 1 ? (distance * 1000).toFixed(0) + ' m' : distance.toFixed(1) + ' km';
            document.getElementById('selectedBranchDistance').textContent = distanceText;
        } else {
            document.getElementById('selectedBranchDistance').textContent = '--';
        }

        // Remove old markers and route
        if (modalMarker) {
            modalMap.removeLayer(modalMarker);
        }
        if (userMarker) {
            modalMap.removeLayer(userMarker);
        }
        if (routeLine) {
            modalMap.removeLayer(routeLine);
        }

        if (lat && lng) {
            const position = [lat, lng];
            
            // Branch marker
            const coffeeIcon = L.divIcon({
                html: '☕',
                className: 'coffee-marker',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });
            
            modalMarker = L.marker(position, { icon: coffeeIcon })
                .addTo(modalMap)
                .bindPopup('<b>☕ ' + name + '</b><br>📍 Pickup location');

            // User location if available
            if (userLat && userLng) {
                const userPos = [userLat, userLng];
                
                // User marker
                const userIcon = L.divIcon({
                    html: '📍',
                    className: 'user-marker',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30],
                    popupAnchor: [0, -30]
                });
                
                userMarker = L.marker(userPos, { icon: userIcon })
                    .addTo(modalMap)
                    .bindPopup('📍 Your Location');

                // Draw route line between user and branch
                routeLine = L.polyline([userPos, position], {
                    color: '#6F4E37',
                    weight: 2,
                    opacity: 0.7,
                    dashArray: '5, 10'
                }).addTo(modalMap);

                // Fit bounds to show both locations
                const bounds = L.latLngBounds([userPos, position]);
                modalMap.fitBounds(bounds, { padding: [50, 50] });
                
                if (modalMap.getZoom() > 16) {
                    modalMap.setZoom(15);
                }
            } else {
                modalMap.setView(position, 15);
            }
        }
    }

    // ============= ORDER MODAL =============
    function openOrderModal() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        checkCustomerAddress();

        const modal = document.getElementById('orderModal');
        const itemsList = document.getElementById('orderItemsList');
        
        if (!itemsList) return;
        
        let html = '';
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            html += `
                <div class="order-item">
                    <span>${item.name} × ${item.quantity}</span>
                    <span>₱${subtotal.toFixed(2)}</span>
                </div>
            `;
        });
        itemsList.innerHTML = html;
        
        updatePricing();
        
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(initModalMap, 300);
        }
    }

    function closeOrderModal() {
        const modal = document.getElementById('orderModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // ============= CONFIRM ORDER =============
    function confirmOrder() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        const branchId = document.getElementById('orderBranchSelect')?.value;
        const isDelivery = document.getElementById('deliveryCheck')?.checked || false;
        const notes = document.getElementById('orderNotes')?.value || '';

        if (!branchId) {
            alert('Please select a branch.');
            return;
        }

        if (isDelivery && (!customerAddress || customerAddress.trim() === '')) {
            alert('Please set up your delivery address in your profile first.');
            return;
        }

        const items = cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity
        }));

        const btn = document.getElementById('confirmOrderBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

        fetch('{{ route("customer.place-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                items: items,
                branch_id: parseInt(branchId),
                delivery_address: isDelivery ? customerAddress : null,
                notes: notes || null
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Confirm Order';
            
            if (data.success) {
                alert(`✅ Order placed! #${data.order_id}\nTotal: ₱${data.total.toFixed(2)}\nPoints Earned: ${data.points_earned}`);
                cart = [];
                updateCart();
                document.getElementById('orderNotes').value = '';
                document.getElementById('deliveryCheck').checked = false;
                closeOrderModal();
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Confirm Order';
            alert('Error: ' + error);
        });
    }

    // ============= SLIDESHOW =============
    function startSlideshow() {
        slideInterval = setInterval(nextSlide, 4000);
    }

    function nextSlide() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        if (slides.length === 0) return;
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        slideIndex = (slideIndex + 1) % slides.length;
        if (slides[slideIndex]) slides[slideIndex].classList.add('active');
        if (dots[slideIndex]) dots[slideIndex].classList.add('active');
    }

    function goToSlide(index) {
        clearInterval(slideInterval);
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        slideIndex = index;
        if (slides[index]) slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        startSlideshow();
    }

    // ============= SEARCH =============
    document.getElementById('searchProduct')?.addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const matches = name.includes(search);
            item.style.display = matches ? '' : 'none';
        });
    });

    // ============= INIT =============
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');
        getLocation();
        startSlideshow();
        updateCart();
        checkCustomerAddress();
        updateAvailableCount();
    });

    // Close modals on outside click
    document.getElementById('orderModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeOrderModal();
        }
    });
    
    document.getElementById('branchAvailabilityModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeBranchModal();
        }
    });
</script>
@endpush
@endsection