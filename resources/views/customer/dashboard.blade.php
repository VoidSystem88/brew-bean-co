@extends('layouts.customer')

@section('page-title', 'Menu')

@section('content')
<style>
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
    .branch-selector label { font-size: 13px; font-weight: 600; color: #333; margin: 0; }
    .branch-selector select {
        padding: 5px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        background: white;
        flex: 1;
        min-width: 150px;
    }
    .branch-selector select:focus { border-color: #6F4E37; outline: none; }
    .branch-selector .branch-stock-info {
        font-size: 12px;
        color: #999;
        margin-left: auto;
    }
    .branch-selector .branch-stock-info .available-count { color: #28a745; font-weight: 600; }
    
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
    .btn-add:disabled { background: #ccc; cursor: not-allowed; }
    
    .product-card {
        cursor: pointer; transition: 0.2s;
        border: 1px solid #eee; border-radius: 10px;
        padding: 10px; text-align: center;
        background: white; height: 100%;
        position: relative;
    }
    .product-card:hover { transform: translateY(-3px); border-color: #6F4E37; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .product-card.sold-out { opacity: 0.7; cursor: default; }
    .product-card.sold-out:hover { transform: none; border-color: #eee; box-shadow: none; }
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
    
    .sold-out-badge {
        position: absolute; top: 8px; right: 8px;
        background: #dc3545; color: white;
        padding: 2px 10px; border-radius: 12px;
        font-size: 10px; font-weight: 700;
        letter-spacing: 0.5px; z-index: 5;
        border: 1px solid white;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }
    .sold-out-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.3); border-radius: 10px; z-index: 3;
        pointer-events: none;
    }
    
    .availability-status {
        display: flex; align-items: center; justify-content: center; gap: 4px; margin-top: 2px;
    }
    .availability-dot {
        display: inline-block; width: 7px; height: 7px; border-radius: 50%;
    }
    .availability-dot.available { background: #28a745; }
    .availability-dot.soldout { background: #dc3545; }
    .availability-text { font-size: 11px; font-weight: 500; }
    .availability-text.available { color: #28a745; }
    .availability-text.soldout { color: #dc3545; }
    
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
    
    .order-modal {
        display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6); z-index: 9999;
        align-items: center; justify-content: center; padding: 20px;
    }
    .order-modal.show { display: flex; }
    .order-modal .modal-content {
        background: white; border-radius: 16px;
        max-width: 600px; width: 100%; max-height: 95vh;
        overflow-y: auto; padding: 25px;
        position: relative; animation: modalIn 0.3s ease;
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
    .address-warning .btn-setup:hover { background: #5a3d2b; color: white; }
    
    .pricing-summary {
        background: #f8f6f4;
        border-radius: 8px;
        padding: 10px 14px;
        margin: 8px 0;
    }
    .pricing-summary .price-row {
        display: flex; justify-content: space-between;
        padding: 3px 0;
        font-size: 13px;
    }
    .pricing-summary .price-row.total {
        font-weight: 700; font-size: 16px; color: #6F4E37;
        border-top: 2px solid #e8e8e8; padding-top: 6px; margin-top: 4px;
    }
    .pricing-summary .price-row.discount { color: #28a745; }
    .pricing-summary .price-row.original { color: #999; text-decoration: line-through; }
    .pricing-summary .price-row.delivery-fee { color: #6F4E37; font-weight: 600; }
    
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
    .branch-info-row .distance-value { font-weight: 600; color: #6F4E37; }
    
    .branch-selector-wrapper {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .branch-selector-wrapper select { flex: 1; }
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
    
    .filter-group {
        display: flex;
        gap: 6px; flex-wrap: wrap; margin-bottom: 10px;
    }
    .filter-btn {
        padding: 3px 14px; border-radius: 20px;
        border: 1px solid #ddd; background: white;
        font-size: 12px; cursor: pointer; transition: 0.2s;
    }
    .filter-btn:hover { border-color: #6F4E37; }
    .filter-btn.active { background: #6F4E37; color: white; border-color: #6F4E37; }
    
    .feedback-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(4px);
    }
    .feedback-modal.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    .feedback-modal .feedback-modal-content {
        background: white;
        border-radius: 20px;
        max-width: 420px;
        width: 100%;
        padding: 35px 30px;
        text-align: center;
        animation: slideUp 0.4s ease;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }
    .feedback-modal .feedback-modal-content::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
    }
    .feedback-modal .feedback-modal-content.success::before {
        background: linear-gradient(90deg, #28a745, #20c997);
    }
    .feedback-modal .feedback-modal-content.error::before {
        background: linear-gradient(90deg, #dc3545, #ff6b6b);
    }
    .feedback-modal .feedback-modal-content.warning::before {
        background: linear-gradient(90deg, #ffc107, #fd7e14);
    }
    .feedback-modal .feedback-icon {
        font-size: 64px; margin-bottom: 12px; display: inline-block;
        animation: popIn 0.5s ease;
    }
    .feedback-modal .feedback-icon.success { color: #28a745; }
    .feedback-modal .feedback-icon.error { color: #dc3545; }
    .feedback-modal .feedback-icon.warning { color: #ffc107; }
    .feedback-modal h4 {
        font-size: 22px; font-weight: 700; color: #333; margin-bottom: 6px;
    }
    .feedback-modal p {
        font-size: 14px; color: #666; margin-bottom: 12px; line-height: 1.5;
    }
    .feedback-modal .feedback-details {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px 16px;
        margin: 10px 0 16px;
        text-align: left;
        font-size: 13px;
        max-height: 150px;
        overflow-y: auto;
    }
    .feedback-modal .feedback-details .detail-row {
        display: flex; justify-content: space-between;
        padding: 4px 0; border-bottom: 1px solid #eee;
    }
    .feedback-modal .feedback-details .detail-row:last-child { border-bottom: none; }
    .feedback-modal .feedback-details .detail-row .label { color: #999; font-weight: 500; }
    .feedback-modal .feedback-details .detail-row .value { font-weight: 600; color: #333; }
    .feedback-modal .feedback-details .detail-row .value.text-success { color: #28a745; }
    .feedback-modal .feedback-btn {
        background: #6F4E37; color: white; border: none;
        padding: 10px 40px; border-radius: 30px;
        font-weight: 600; font-size: 14px;
        cursor: pointer; transition: all 0.2s;
        margin-top: 4px;
    }
    .feedback-modal .feedback-btn:hover { background: #5a3d2b; transform: scale(1.02); }
    .feedback-modal .feedback-btn.btn-success { background: #28a745; }
    .feedback-modal .feedback-btn.btn-danger { background: #dc3545; }
    .feedback-modal .feedback-btn.btn-warning { background: #ffc107; color: #333; }
    
    .confirmation-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 99998;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(6px);
    }
    .confirmation-modal.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    .confirmation-modal .confirmation-content {
        background: white;
        border-radius: 20px;
        max-width: 500px;
        width: 100%;
        padding: 30px 30px 25px;
        animation: slideUp 0.4s ease;
        box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }
    .confirmation-modal .confirmation-content .confirmation-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 2px solid #6F4E37;
        margin-bottom: 15px;
    }
    .confirmation-modal .confirmation-content .confirmation-header i {
        font-size: 28px;
        color: #6F4E37;
    }
    .confirmation-modal .confirmation-content .confirmation-header h4 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    .confirmation-modal .confirmation-content .confirmation-body .order-summary-item {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
        border-bottom: 1px solid #f5f5f5;
    }
    .confirmation-modal .confirmation-content .confirmation-body .summary-total {
        font-weight: 700;
        font-size: 18px;
        color: #6F4E37;
        border-top: 2px solid #6F4E37;
        padding-top: 8px;
        margin-top: 6px;
        display: flex;
        justify-content: space-between;
    }
    .confirmation-modal .confirmation-content .confirmation-body .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 13px;
        color: #666;
    }
    .confirmation-modal .confirmation-content .confirmation-body .summary-row .label { color: #999; }
    .confirmation-modal .confirmation-content .confirmation-body .summary-row .value { font-weight: 500; color: #333; }
    .confirmation-modal .confirmation-content .confirmation-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }
    .confirmation-modal .confirmation-content .confirmation-actions .btn {
        flex: 1;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: 0.2s;
    }
    .confirmation-modal .confirmation-content .confirmation-actions .btn-cancel {
        background: #f5f5f5;
        color: #666;
    }
    .confirmation-modal .confirmation-content .confirmation-actions .btn-cancel:hover { background: #eee; }
    .confirmation-modal .confirmation-content .confirmation-actions .btn-confirm {
        background: #6F4E37;
        color: white;
    }
    .confirmation-modal .confirmation-content .confirmation-actions .btn-confirm:hover {
        background: #5a3d2b;
        transform: scale(1.02);
    }
    .confirmation-modal .confirmation-content .confirmation-actions .btn-confirm:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .confirmation-modal .confirmation-close {
        position: absolute;
        top: 12px;
        right: 16px;
        background: none;
        border: none;
        font-size: 22px;
        color: #999;
        cursor: pointer;
    }
    .confirmation-modal .confirmation-close:hover { color: #333; }
    
    /* Floating Checkout Button */
    .floating-checkout {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        background: #6F4E37;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 12px 24px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 4px 16px rgba(111, 78, 55, 0.35);
        cursor: pointer;
        transition: all 0.3s ease;
        display: none;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    .floating-checkout:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 24px rgba(111, 78, 55, 0.45);
        background: #5a3d2b;
        color: white;
    }
    .floating-checkout .badge-count {
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
    .floating-checkout .total-amount {
        font-weight: 700;
        color: #ffd700;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        60% { transform: scale(1.3); }
        80% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }
    
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
        .branch-selector-wrapper { flex-direction: column; }
        .btn-detect-branch { width: 100%; justify-content: center; }
        .filter-group { justify-content: center; }
        .address-warning { flex-direction: column; text-align: center; }
        .branch-selector { flex-direction: column; align-items: stretch; text-align: center; }
        .branch-selector .branch-stock-info { margin-left: 0; }
        .feedback-modal .feedback-modal-content { padding: 25px 20px; margin: 10px; }
        .feedback-modal .feedback-icon { font-size: 48px; }
        .feedback-modal h4 { font-size: 18px; }
        .confirmation-modal .confirmation-content { padding: 20px; margin: 10px; }
        .confirmation-modal .confirmation-content .confirmation-actions { flex-direction: column; }
        .floating-checkout {
            bottom: 16px;
            right: 16px;
            padding: 10px 18px;
            font-size: 13px;
        }
        .floating-checkout .badge-count {
            width: 20px;
            height: 20px;
            font-size: 10px;
        }
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
                {{ str_replace('Brew & Bean Co. - ', '', $branch->name) }}
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

<!-- Floating Checkout Button -->
<a href="#" class="floating-checkout" id="floatingCheckout" onclick="event.preventDefault(); openOrderModal();">
    <i class="fas fa-shopping-cart"></i>
    <span id="floatingItemCount" class="badge-count">0</span>
    <span>Checkout</span>
    <span class="total-amount" id="floatingTotal">₱0.00</span>
</a>

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
                            <div>#{{ $sale->id }} - {{ str_replace('Brew & Bean Co. - ', '', $sale->branch->name ?? 'Unknown') }}</div>
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

<!-- Order Modal -->
<div class="order-modal" id="orderModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeOrderModal()"><i class="fas fa-times"></i></button>
        <h5 class="mb-2"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
        
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
            <div class="price-row delivery-fee" id="priceDeliveryFee" style="display:none;"><i class="fas fa-truck me-1"></i>Delivery Fee: ₱0.00</div>
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
                <span class="badge-nearest" id="nearestBadge" style="display:none;">Nearest</span>
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
                                data-name="{{ str_replace('Brew & Bean Co. - ', '', $branch->name) }}"
                                data-distance="{{ $branch->distance ?? 0 }}"
                                {{ $branch->id == $selectedBranchId ? 'selected' : '' }}>
                            {{ str_replace('Brew & Bean Co. - ', '', $branch->name) }}
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
                    <i class="fas fa-truck me-1"></i> Delivery (₱10/km)
                </label>
            </div>
        </div>
        
        <div class="mt-2">
            <input type="text" id="orderNotes" class="form-control form-control-sm" placeholder="Special instructions...">
        </div>
        
        <button class="btn w-100 mt-3" onclick="openConfirmationModal()" id="confirmOrderBtn" style="background:#6F4E37;color:white;border-radius:6px;padding:8px;font-weight:600;">
            <i class="fas fa-check-circle me-2"></i> Confirm Order
        </button>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="confirmation-modal" id="confirmationModal">
    <div class="confirmation-content">
        <button class="confirmation-close" onclick="closeConfirmationModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="confirmation-header">
            <i class="fas fa-clipboard-check"></i>
            <h4>Confirm Order</h4>
        </div>
        <div class="confirmation-body" id="confirmationBody"></div>
        <div class="confirmation-actions">
            <button class="btn btn-cancel" onclick="closeConfirmationModal()">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button class="btn btn-confirm" onclick="placeOrder()" id="confirmPlaceBtn">
                <i class="fas fa-check me-1"></i> Confirm Order
            </button>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="feedback-modal" id="feedbackModal">
    <div class="feedback-modal-content">
        <div class="feedback-icon" id="feedbackIcon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4 id="feedbackTitle">Success!</h4>
        <p id="feedbackMessage">Your order has been placed successfully.</p>
        <div id="feedbackDetails" class="feedback-details"></div>
        <button class="feedback-btn" onclick="closeFeedbackModal()" id="feedbackBtn">
            <i class="fas fa-check me-2"></i> OK
        </button>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];
    let slideIndex = 0;
    let slideInterval;
    let userLat = null;
    let userLng = null;
    let allBranches = @json($branches);
    let customerAddress = '{{ $customer->address ?? '' }}';
    let discountRate = {{ $discountRate ?? 0 }};
    let selectedBranchId = {{ $selectedBranchId ?? 0 }};
    let orderData = null;

    // ============= ADD TO CART =============
    window.addToCart = function(productId, productName, productPrice) {
        const existing = cart.find(item => item.id === productId);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
        }
        updateCart();
        updateBadge(productId);
        updateFloatingButton();
    };

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
        updateBadge(id);
        updateFloatingButton();
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
            updateFloatingButton();
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
            updateFloatingButton();
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
        updateFloatingButton();
    }

    // ============= FLOATING CHECKOUT BUTTON =============
    function updateFloatingButton() {
        const btn = document.getElementById('floatingCheckout');
        const countEl = document.getElementById('floatingItemCount');
        const totalEl = document.getElementById('floatingTotal');
        
        if (!btn) return;
        
        let total = 0;
        let count = 0;
        if (cart && cart.length > 0) {
            cart.forEach(item => {
                total += (item.price || 0) * (item.quantity || 0);
                count += (item.quantity || 0);
            });
        }
        
        if (count > 0) {
            btn.style.display = 'flex';
            countEl.textContent = count;
            totalEl.textContent = '₱' + total.toFixed(2);
        } else {
            btn.style.display = 'none';
        }
    }

    // ============= SWITCH BRANCH =============
    function switchBranch() {
        const branchId = document.getElementById('branchSelect').value;
        window.location.href = `/customer/dashboard?branch_id=${branchId}`;
    }

    function updateAvailableCount() {
        const available = document.querySelectorAll('.product-item[data-available="true"]').length;
        document.getElementById('availableCount').textContent = available;
    }

    function checkCustomerAddress() {
        const warning = document.getElementById('addressWarning');
        const deliveryToggle = document.getElementById('deliveryToggleContainer');
        const deliveryCheck = document.getElementById('deliveryCheck');
        
        if (customerAddress && customerAddress.trim() !== '') {
            warning.style.display = 'none';
            deliveryToggle.style.display = 'block';
        } else {
            warning.style.display = 'block';
            deliveryToggle.style.display = 'none';
            deliveryCheck.checked = false;
        }
    }

    function toggleDelivery() {
        const isChecked = document.getElementById('deliveryCheck').checked;
        if (isChecked && (!customerAddress || customerAddress.trim() === '')) {
            alert('Please set up your delivery address in your profile first.');
            document.getElementById('deliveryCheck').checked = false;
        }
        updatePricing();
    }

    function filterProducts(filter) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.filter-btn[onclick*="${filter}"]`)?.classList.add('active');
        
        document.querySelectorAll('.product-item').forEach(item => {
            const isAvailable = item.dataset.available === 'true';
            if (filter === 'all') item.style.display = '';
            else if (filter === 'available') item.style.display = isAvailable ? '' : 'none';
            else if (filter === 'soldout') item.style.display = isAvailable ? 'none' : '';
        });
        updateAvailableCount();
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    document.getElementById('locationDisplay').textContent = 'Location detected';
                    findNearestBranch(userLat, userLng);
                },
                function() {
                    document.getElementById('locationDisplay').textContent = 'Unable to get location.';
                }
            );
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function findNearestBranch(lat, lng) {
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

        allBranches.sort((a, b) => (a.distance || 999) - (b.distance || 999));

        const select = document.getElementById('orderBranchSelect');
        select.innerHTML = '';
        allBranches.forEach((branch) => {
            const option = document.createElement('option');
            option.value = branch.id;
            option.dataset.lat = branch.latitude || 0;
            option.dataset.lng = branch.longitude || 0;
            option.dataset.name = branch.name.replace('Brew & Bean Co. - ', '');
            option.dataset.distance = branch.distance || 0;
            const distanceText = branch.distance_text ? ` (${branch.distance_text})` : '';
            option.textContent = branch.name.replace('Brew & Bean Co. - ', '') + distanceText;
            select.appendChild(option);
        });

        document.getElementById('locationDisplay').textContent = 'Location detected';
        updateModalMap();
        updatePricing();
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
                    alert('Unable to get location. Please enable location services.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect Nearest';
                }
            );
        }
    }

    function updateModalMap() {
        // Simple map update - just show location
        const select = document.getElementById('orderBranchSelect');
        const selectedOption = select?.options[select.selectedIndex];
        if (!selectedOption) return;
        
        const name = selectedOption.dataset.name || 'Branch';
        const distance = parseFloat(selectedOption.dataset.distance) || 0;

        document.getElementById('selectedBranchName').textContent = name;
        
        if (distance > 0) {
            const distanceText = distance < 1 ? (distance * 1000).toFixed(0) + ' m' : distance.toFixed(1) + ' km';
            document.getElementById('selectedBranchDistance').textContent = distanceText;
        } else {
            document.getElementById('selectedBranchDistance').textContent = '--';
        }
    }

    function calculatePricing() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.price * item.quantity; });
        
        const discountAmount = (subtotal * discountRate) / 100;
        const select = document.getElementById('orderBranchSelect');
        const selectedOption = select?.options[select.selectedIndex];
        let distance = parseFloat(selectedOption?.dataset.distance) || 0;
        const isDelivery = document.getElementById('deliveryCheck')?.checked || false;
        const deliveryFee = isDelivery ? (distance * 10) : 0;
        const total = subtotal - discountAmount + deliveryFee;
        const pointsEarned = Math.floor(total / 100);
        
        return { subtotal, discountAmount, deliveryFee, total, pointsEarned, discountRate, distance };
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

        const deliveryRow = document.getElementById('priceDeliveryFee');
        if (pricing.deliveryFee > 0) {
            deliveryRow.textContent = `Delivery Fee: ₱${pricing.deliveryFee.toFixed(2)} (${pricing.distance.toFixed(1)} km × ₱10/km)`;
            deliveryRow.style.display = 'flex';
        } else {
            deliveryRow.style.display = 'none';
        }
        
        document.getElementById('priceTotal').textContent = `Total: ₱${pricing.total.toFixed(2)}`;
        document.getElementById('pointsToEarn').textContent = pricing.pointsEarned;
    }

    function openOrderModal() {
        if (cart.length === 0) { 
            showFeedback('warning', 'Cart is Empty', 'Please add items to your cart before checking out.');
            return; 
        }
        checkCustomerAddress();
        updatePricing();
        document.getElementById('orderModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        updateModalMap();
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function openConfirmationModal() {
        if (cart.length === 0) { 
            showFeedback('warning', 'Cart is Empty', 'Please add items to your cart before checking out.');
            return; 
        }

        const branchId = document.getElementById('orderBranchSelect')?.value;
        const isDelivery = document.getElementById('deliveryCheck')?.checked || false;
        const notes = document.getElementById('orderNotes')?.value || '';

        if (!branchId) { 
            showFeedback('warning', 'No Branch Selected', 'Please select a branch.');
            return; 
        }
        
        if (isDelivery && (!customerAddress || customerAddress.trim() === '')) {
            showFeedback('warning', 'Address Required', 'Please set up your delivery address in your profile first.');
            return;
        }

        const pricing = calculatePricing();
        const selectedOption = document.getElementById('orderBranchSelect')?.options[document.getElementById('orderBranchSelect')?.selectedIndex];
        
        let itemsHtml = '';
        cart.forEach(item => {
            const subtotalItem = item.price * item.quantity;
            itemsHtml += `
                <div class="order-summary-item">
                    <span>${item.name} × ${item.quantity}</span>
                    <span>₱${subtotalItem.toFixed(2)}</span>
                </div>
            `;
        });

        let summaryHtml = `
            <div class="summary-row">
                <span class="label">Subtotal</span>
                <span class="value">₱${pricing.subtotal.toFixed(2)}</span>
            </div>
        `;

        if (pricing.discountAmount > 0) {
            summaryHtml += `
                <div class="summary-row">
                    <span class="label">Discount (${pricing.discountRate}%)</span>
                    <span class="value discount">-₱${pricing.discountAmount.toFixed(2)}</span>
                </div>
            `;
        }

        if (pricing.deliveryFee > 0) {
            summaryHtml += `
                <div class="summary-row">
                    <span class="label">Delivery Fee (${pricing.distance.toFixed(1)} km)</span>
                    <span class="value delivery">₱${pricing.deliveryFee.toFixed(2)}</span>
                </div>
            `;
        }

        summaryHtml += `
            <div class="summary-total">
                <span>Total</span>
                <span>₱${pricing.total.toFixed(2)}</span>
            </div>
            <div class="summary-row" style="border-top:1px solid #eee;padding-top:4px;margin-top:4px;">
                <span class="label">Points to earn</span>
                <span class="value">${pricing.pointsEarned}</span>
            </div>
            <div class="summary-row">
                <span class="label">Branch</span>
                <span class="value">${selectedOption?.text || 'N/A'}</span>
            </div>
            <div class="summary-row">
                <span class="label">Type</span>
                <span class="value">${isDelivery ? 'Delivery' : 'Pickup'}</span>
            </div>
        `;

        document.getElementById('confirmationBody').innerHTML = `
            <div style="margin-bottom:10px;font-weight:600;color:#333;">Items</div>
            ${itemsHtml}
            <div style="margin-top:12px;padding-top:10px;border-top:1px solid #eee;">
                ${summaryHtml}
            </div>
        `;

        orderData = {
            branchId: parseInt(branchId),
            isDelivery: isDelivery,
            notes: notes || null,
            items: cart.map(item => ({ product_id: item.id, quantity: item.quantity }))
        };

        document.getElementById('confirmationModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.remove('show');
        document.body.style.overflow = '';
        orderData = null;
    }

    function placeOrder() {
        if (!orderData) return;

        const btn = document.getElementById('confirmPlaceBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

        fetch('{{ route("customer.place-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                items: orderData.items,
                branch_id: orderData.branchId,
                is_delivery: orderData.isDelivery,
                delivery_address: orderData.isDelivery ? customerAddress : null,
                notes: orderData.notes
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirm Order';
            closeConfirmationModal();
            
            if (data.success) {
                const details = `
                    <div class="detail-row">
                        <span class="label">Order #</span>
                        <span class="value">${data.order_id}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Total</span>
                        <span class="value text-success">₱${data.total.toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Points Earned</span>
                        <span class="value">+${data.points_earned}</span>
                    </div>
                    ${data.delivery_fee > 0 ? `
                    <div class="detail-row">
                        <span class="label">Delivery Fee</span>
                        <span class="value">₱${data.delivery_fee.toFixed(2)}</span>
                    </div>
                    ` : ''}
                `;
                
                showFeedback('success', 'Order Placed!', `Your order #${data.order_id} has been placed successfully.`, details);
                
                cart = [];
                updateCart();
                updateFloatingButton();
                document.getElementById('orderNotes').value = '';
                document.getElementById('deliveryCheck').checked = false;
                closeOrderModal();
                
                setTimeout(() => { location.reload(); }, 3000);
            } else {
                if (data.message.includes('out of stock') || data.message.includes('stock')) {
                    showFeedback('error', 'Out of Stock', data.message);
                } else {
                    showFeedback('error', 'Order Failed', data.message);
                }
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirm Order';
            closeConfirmationModal();
            showFeedback('error', 'Error', 'An error occurred. Please try again.');
        });
    }

    function showFeedback(type, title, message, details = '') {
        const modal = document.getElementById('feedbackModal');
        const icon = document.getElementById('feedbackIcon');
        const titleEl = document.getElementById('feedbackTitle');
        const msgEl = document.getElementById('feedbackMessage');
        const detailsEl = document.getElementById('feedbackDetails');
        const btn = document.getElementById('feedbackBtn');
        const content = modal.querySelector('.feedback-modal-content');
        
        content.className = 'feedback-modal-content';
        icon.className = 'feedback-icon';
        
        const types = {
            success: { icon: 'fa-check-circle', color: '#28a745', btnClass: 'btn-success', btnText: 'Continue' },
            error: { icon: 'fa-times-circle', color: '#dc3545', btnClass: 'btn-danger', btnText: 'Try Again' },
            warning: { icon: 'fa-exclamation-triangle', color: '#ffc107', btnClass: 'btn-warning', btnText: 'OK' }
        };
        
        const t = types[type] || types.warning;
        
        content.classList.add(type);
        icon.className = 'feedback-icon ' + type;
        icon.innerHTML = `<i class="fas ${t.icon}" style="color:${t.color}"></i>`;
        titleEl.textContent = title;
        msgEl.textContent = message;
        
        if (details) {
            detailsEl.innerHTML = details;
            detailsEl.style.display = 'block';
        } else {
            detailsEl.style.display = 'none';
        }
        
        btn.className = 'feedback-btn ' + t.btnClass;
        btn.innerHTML = `<i class="fas fa-check me-2"></i> ${t.btnText}`;
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // ============= SLIDESHOW =============
    function startSlideshow() { slideInterval = setInterval(nextSlide, 4000); }

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
            item.style.display = name.includes(search) ? '' : 'none';
        });
    });

    // ============= INIT =============
    document.addEventListener('DOMContentLoaded', function() {
        getLocation();
        startSlideshow();
        updateCart();
        checkCustomerAddress();
        updateAvailableCount();
        setTimeout(updateFloatingButton, 200);
    });

    document.getElementById('orderModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeOrderModal();
    });
    
    document.getElementById('confirmationModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeConfirmationModal();
    });

    document.getElementById('feedbackModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeFeedbackModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmationModal();
            closeFeedbackModal();
        }
    });
</script>
@endpush
@endsection