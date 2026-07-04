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
        height: 260px;
        perspective: 1000px;
    }
    .slideshow-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 15px 30px;
        gap: 15px;
        transition: all 0.5s ease;
    }
    .slide-item {
        flex: 0 0 30%;
        height: 100%;
        border-radius: 12px;
        overflow: hidden;
        background: white;
        border: 1px solid #eee;
        transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        align-items: center;
        padding: 15px 20px;
        gap: 15px;
        opacity: 0.5;
        transform: scale(0.85) rotateY(10deg);
        position: relative;
    }
    .slide-item .slide-image {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        background: #f5f0eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #6F4E37;
        flex-shrink: 0;
    }
    .slide-item .slide-image img { width: 100%; height: 100%; object-fit: cover; }
    .slide-item .slide-info { flex: 1; min-width: 0; }
    .slide-item .slide-info .category { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #999; }
    .slide-item .slide-info .name { font-size: 14px; font-weight: 600; color: #333; }
    .slide-item .slide-info .price { font-size: 16px; font-weight: 700; color: #6F4E37; }
    .slide-item .slide-info .desc { font-size: 11px; color: #777; margin-top: 2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .slide-item .btn-add-small {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 14px;
        font-size: 11px;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 4px;
        white-space: nowrap;
    }
    .slide-item .btn-add-small:hover { background: #5a3d2b; }
    .slide-item .btn-add-small:disabled { background: #ccc; cursor: not-allowed; }
    
    .slide-item.active {
        flex: 0 0 40%;
        opacity: 1;
        transform: scale(1) rotateY(0deg);
        border-color: #6F4E37;
        box-shadow: 0 8px 30px rgba(111, 78, 55, 0.15);
        z-index: 2;
    }
    .slide-item.active .slide-info .name { font-size: 18px; }
    .slide-item.active .slide-info .price { font-size: 20px; }
    .slide-item.active .slide-image { width: 100px; height: 100px; font-size: 40px; }
    
    .slide-item.prev {
        transform: scale(0.85) rotateY(-10deg) translateX(-10px);
        opacity: 0.6;
    }
    .slide-item.next {
        transform: scale(0.85) rotateY(10deg) translateX(10px);
        opacity: 0.6;
    }
    .slide-item.hidden { display: none; }
    
    .slideshow-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(111, 78, 55, 0.8);
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .slideshow-nav:hover { background: #6F4E37; }
    .slideshow-nav.prev { left: 6px; }
    .slideshow-nav.next { right: 6px; }
    .slideshow-dots {
        position: absolute;
        bottom: 6px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 5;
    }
    .slideshow-dots .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ddd;
        cursor: pointer;
        border: none;
        padding: 0;
        transition: 0.3s;
    }
    .slideshow-dots .dot.active { background: #6F4E37; width: 20px; border-radius: 4px; }
    
    .btn-add {
        background: #6F4E37; color: white; border: none;
        padding: 4px 16px; border-radius: 16px; font-size: 12px;
        margin-top: 6px; cursor: pointer; transition: 0.2s;
    }
    .btn-add:hover { background: #5a3d2b; }
    .btn-add:disabled { background: #ccc; cursor: not-allowed; }
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
    .btn-check-branches:hover { background: #6F4E37; color: white; }
    
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
    .branch-availability-modal.show { display: flex; }
    .branch-availability-modal .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
        padding: 25px;
        position: relative;
        animation: fadeInUp 0.3s ease;
    }
    @keyframes fadeInUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .branch-availability-modal .modal-close {
        position: absolute; top: 12px; right: 16px;
        background: none; border: none; font-size: 22px; color: #999; cursor: pointer;
    }
    .branch-availability-modal .modal-close:hover { color: #333; }
    .branch-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 12px; border-bottom: 1px solid #f5f5f5;
    }
    .branch-item:last-child { border-bottom: none; }
    .branch-item .branch-name { font-weight: 500; font-size: 14px; }
    .branch-item .branch-status {
        font-size: 12px; font-weight: 600; padding: 2px 12px; border-radius: 12px;
    }
    .branch-item .branch-status.available { background: #d4edda; color: #155724; }
    .branch-item .branch-status.unavailable { background: #f8d7da; color: #721c24; }
    .branch-item .branch-distance { font-size: 12px; color: #999; }
    
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
        position: relative; animation: fadeInUp 0.3s ease;
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
    .address-warning .btn-setup:hover { background: #5a3d2b; color: white; }
    
    .modal-mini-map {
        height: 200px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        margin: 10px 0;
        position: relative;
        background: #f8f6f4;
    }
    .modal-mini-map #modalMap { height: 100%; width: 100%; }
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
    .pricing-summary .price-row.delivery-fee { color: #6F4E37; font-weight: 600; }
    
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
    .filter-btn:hover { border-color: #6F4E37; }
    .filter-btn.active { background: #6F4E37; color: white; border-color: #6F4E37; }
    
    /* Feedback Modal */
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
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
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
    .feedback-modal .feedback-modal-content.info::before {
        background: linear-gradient(90deg, #17a2b8, #0dcaf0);
    }
    .feedback-modal .feedback-icon {
        font-size: 64px;
        margin-bottom: 12px;
        display: inline-block;
        animation: popIn 0.5s ease;
    }
    .feedback-modal .feedback-icon.success { color: #28a745; }
    .feedback-modal .feedback-icon.error { color: #dc3545; }
    .feedback-modal .feedback-icon.warning { color: #ffc107; }
    .feedback-modal .feedback-icon.info { color: #17a2b8; }
    .feedback-modal h4 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 6px;
    }
    .feedback-modal p {
        font-size: 14px;
        color: #666;
        margin-bottom: 12px;
        line-height: 1.5;
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
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        border-bottom: 1px solid #eee;
    }
    .feedback-modal .feedback-details .detail-row:last-child { border-bottom: none; }
    .feedback-modal .feedback-details .detail-row .label { color: #999; font-weight: 500; }
    .feedback-modal .feedback-details .detail-row .value { font-weight: 600; color: #333; }
    .feedback-modal .feedback-details .detail-row .value.text-success { color: #28a745; }
    .feedback-modal .feedback-details .detail-row .value.text-danger { color: #dc3545; }
    .feedback-modal .feedback-btn {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 10px 40px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 4px;
    }
    .feedback-modal .feedback-btn:hover { background: #5a3d2b; transform: scale(1.02); }
    .feedback-modal .feedback-btn:active { transform: scale(0.98); }
    .feedback-modal .feedback-btn.btn-success { background: #28a745; }
    .feedback-modal .feedback-btn.btn-success:hover { background: #218838; }
    .feedback-modal .feedback-btn.btn-danger { background: #dc3545; }
    .feedback-modal .feedback-btn.btn-danger:hover { background: #c82333; }
    .feedback-modal .feedback-btn.btn-warning { background: #ffc107; color: #333; }
    .feedback-modal .feedback-btn.btn-warning:hover { background: #e0a800; }
    
    /* Confirmation Modal - Custom */
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
    .confirmation-modal .confirmation-content .confirmation-body {
        margin-bottom: 15px;
    }
    .confirmation-modal .confirmation-content .confirmation-body .order-summary-item {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
        border-bottom: 1px solid #f5f5f5;
    }
    .confirmation-modal .confirmation-content .confirmation-body .order-summary-item:last-child {
        border-bottom: none;
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
    .confirmation-modal .confirmation-content .confirmation-body .summary-row .value.delivery { color: #6F4E37; }
    .confirmation-modal .confirmation-content .confirmation-body .summary-row .value.discount { color: #28a745; }
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
    .confirmation-modal .confirmation-content .confirmation-actions .btn-cancel:hover {
        background: #eee;
    }
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
        .slideshow-wrapper { padding: 10px 15px; gap: 8px; }
        .slide-item { flex: 0 0 45%; padding: 10px; flex-direction: column; text-align: center; }
        .slide-item .slide-image { width: 50px; height: 50px; font-size: 20px; }
        .slide-item .slide-info .name { font-size: 12px; }
        .slide-item .slide-info .price { font-size: 14px; }
        .slide-item .slide-info .desc { display: none; }
        .slide-item.active { flex: 0 0 55%; }
        .slide-item.active .slide-image { width: 70px; height: 70px; font-size: 28px; }
        .slide-item.active .slide-info .name { font-size: 14px; }
        .slide-item.active .slide-info .price { font-size: 16px; }
        .slideshow-nav { width: 24px; height: 24px; font-size: 10px; }
        .slideshow-nav.prev { left: 2px; }
        .slideshow-nav.next { right: 2px; }
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
        .feedback-modal .feedback-modal-content { padding: 25px 20px; margin: 10px; }
        .feedback-modal .feedback-icon { font-size: 48px; }
        .feedback-modal h4 { font-size: 18px; }
        .feedback-modal p { font-size: 13px; }
        .confirmation-modal .confirmation-content { padding: 20px; margin: 10px; }
        .confirmation-modal .confirmation-content .confirmation-actions { flex-direction: column; }
        .confirmation-modal .confirmation-content .confirmation-actions .btn { width: 100%; }
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

<!-- Slideshow with Expand/Distort -->
<div class="slideshow-container mb-3" id="slideshow">
    <button class="slideshow-nav prev" onclick="slidePrev()">
        <i class="fas fa-chevron-left"></i>
    </button>
    <div class="slideshow-wrapper" id="slideshowWrapper">
        @foreach($products->take(5) as $index => $product)
            <div class="slide-item {{ $index === 0 ? 'active' : ($index === 1 ? 'next' : '') }}" data-index="{{ $index }}">
                <div class="slide-image">
                    @if($product->image && file_exists(storage_path('app/public/products/' . $product->image)))
                        <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-coffee"></i>
                    @endif
                </div>
                <div class="slide-info">
                    <div class="category">{{ $product->category ?? 'Featured' }}</div>
                    <div class="name">{{ $product->name }}</div>
                    <div class="price">₱{{ number_format($product->price, 2) }}</div>
                    @if($product->description)
                        <div class="desc">{{ Str::limit($product->description, 40) }}</div>
                    @endif
                    @if($product->is_available)
                        <button class="btn-add-small" onclick="event.stopPropagation(); addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                            <i class="fas fa-plus me-1"></i> Add
                        </button>
                    @else
                        <button class="btn-add-small" disabled>
                            <i class="fas fa-times me-1"></i> Sold Out
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <button class="slideshow-nav next" onclick="slideNext()">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="slideshow-dots" id="slideshowDots">
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

<!-- Branch Availability Modal -->
<div class="branch-availability-modal" id="branchAvailabilityModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeBranchModal()"><i class="fas fa-times"></i></button>
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

<!-- Custom Confirmation Modal -->
<div class="confirmation-modal" id="confirmationModal">
    <div class="confirmation-content">
        <button class="confirmation-close" onclick="closeConfirmationModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="confirmation-header">
            <i class="fas fa-clipboard-check"></i>
            <h4>Confirm Order</h4>
        </div>
        <div class="confirmation-body" id="confirmationBody">
            <!-- Dynamic content -->
        </div>
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
    let nearestBranchId = null;
    const discountRate = {{ $discountRate ?? 0 }};
    let currentFilter = 'all';
    let allBranches = @json($branches);
    let customerAddress = '{{ $customer->address ?? '' }}';
    let customerLat = '{{ $customer->latitude ?? '' }}';
    let customerLng = '{{ $customer->longitude ?? '' }}';
    let selectedBranchId = {{ $selectedBranchId ?? 0 }};
    let orderData = null;

    // ============= SLIDESHOW =============
    function updateSlides() {
        const items = document.querySelectorAll('.slide-item');
        const dots = document.querySelectorAll('.dot');
        const total = items.length;
        
        items.forEach((item, index) => {
            item.classList.remove('active', 'prev', 'next', 'hidden');
            if (index === slideIndex) {
                item.classList.add('active');
            } else if (index === (slideIndex - 1 + total) % total) {
                item.classList.add('prev');
            } else if (index === (slideIndex + 1) % total) {
                item.classList.add('next');
            } else {
                item.classList.add('hidden');
            }
        });
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === slideIndex);
        });
    }

    function slideNext() {
        const total = document.querySelectorAll('.slide-item').length;
        slideIndex = (slideIndex + 1) % total;
        updateSlides();
    }

    function slidePrev() {
        const total = document.querySelectorAll('.slide-item').length;
        slideIndex = (slideIndex - 1 + total) % total;
        updateSlides();
    }

    function goToSlide(index) {
        slideIndex = index;
        updateSlides();
        clearInterval(slideInterval);
        startSlideshow();
    }

    function startSlideshow() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(slideNext, 5000);
    }

    // ============= ADD TO CART =============
    window.addToCart = function(productId, productName, productPrice) {
        const productItem = document.querySelector(`.product-item[data-product-id="${productId}"]`);
        if (productItem && productItem.dataset.available === 'false') {
            alert('This product is currently sold out.');
            return;
        }
        
        const existing = cart.find(item => item.id === productId);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
        }
        updateCart();
        updateBadge(productId);
        
        const card = document.querySelector(`.product-item[data-product-id="${productId}"] .product-card`);
        if (card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => card.style.transform = '', 200);
        }
    };

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
        updatePricing();
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
                    const statusText = isAvailable ? 'Available' : 'Sold Out';
                    
                    let distanceText = '';
                    if (branch.distance) {
                        const dist = branch.distance < 1 ? (branch.distance * 1000).toFixed(0) + ' m' : branch.distance.toFixed(1) + ' km';
                        distanceText = dist;
                    }
                    
                    html += `
                        <div class="branch-item">
                            <div>
                                <div class="branch-name">${branch.name.replace('Brew & Bean Co. - ', '')}</div>
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
                                <div class="branch-name">${branch.name.replace('Brew & Bean Co. - ', '')}</div>
                            </div>
                            <span class="branch-status unavailable">Check failed</span>
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

    // ============= GEOLOCATION =============
    function getLocation() {
        if (navigator.geolocation) {
            document.getElementById('locationDisplay').textContent = 'Getting location...';
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
        } else {
            document.getElementById('locationDisplay').textContent = 'Geolocation not supported';
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
        nearestBranchId = allBranches[0]?.id;

        const select = document.getElementById('orderBranchSelect');
        const currentValue = select.value;
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
            if (branch.id == currentValue || (branch.id === nearestBranchId && !currentValue)) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        document.getElementById('locationDisplay').textContent = 'Location detected';
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
                function(error) {
                    let msg = 'Unable to get location. ';
                    if (error.code === 1) msg += 'Please allow location access.';
                    else if (error.code === 2) msg += 'Location unavailable.';
                    else msg += 'Please try again.';
                    alert(msg);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect Nearest';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect Nearest';
        }
    }

    function updateNearestBadge() {
        const select = document.getElementById('orderBranchSelect');
        const selectedOption = select?.options[select.selectedIndex];
        const badge = document.getElementById('nearestBadge');
        if (selectedOption && nearestBranchId && parseInt(selectedOption.value) === nearestBranchId) {
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }

    // ============= CART FUNCTIONS =============
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
        if (cart && cart.length > 0) {
            cart.forEach(item => { 
                subtotal += (item.price || 0) * (item.quantity || 0); 
            });
        }
        
        const discountAmount = (subtotal * discountRate) / 100;
        const deliveryCheck = document.getElementById('deliveryCheck');
        const isDelivery = deliveryCheck ? deliveryCheck.checked : false;
        const select = document.getElementById('orderBranchSelect');
        let distance = 0;
        
        if (select) {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption) {
                distance = parseFloat(selectedOption.dataset.distance) || 0;
            }
        }
        
        const deliveryFee = isDelivery ? (distance * 10) : 0;
        const total = subtotal - discountAmount + deliveryFee;
        const pointsEarned = Math.floor(total / 100);
        
        return { 
            subtotal: subtotal, 
            discountAmount: discountAmount, 
            deliveryFee: deliveryFee, 
            total: total, 
            pointsEarned: pointsEarned, 
            discountRate: discountRate,
            distance: distance,
            isDelivery: isDelivery
        };
    }

    function updatePricing() {
        const pricing = calculatePricing();
        
        const originalEl = document.getElementById('priceOriginal');
        if (originalEl) {
            originalEl.textContent = `Subtotal: ₱${pricing.subtotal.toFixed(2)}`;
        }
        
        const discountEl = document.getElementById('priceDiscount');
        if (discountEl) {
            if (pricing.discountRate > 0) {
                discountEl.textContent = `Discount: -₱${pricing.discountAmount.toFixed(2)} (${pricing.discountRate}%)`;
                discountEl.style.display = 'flex';
            } else {
                discountEl.textContent = `Discount: ₱0.00 (0%)`;
            }
        }

        const deliveryRow = document.getElementById('priceDeliveryFee');
        if (deliveryRow) {
            if (pricing.deliveryFee > 0) {
                deliveryRow.textContent = `Delivery Fee: ₱${pricing.deliveryFee.toFixed(2)} (${pricing.distance.toFixed(1)} km × ₱10/km)`;
                deliveryRow.style.display = 'flex';
            } else {
                deliveryRow.style.display = 'none';
            }
        }
        
        const totalEl = document.getElementById('priceTotal');
        if (totalEl) {
            totalEl.textContent = `Total: ₱${pricing.total.toFixed(2)}`;
        }
        
        const pointsEl = document.getElementById('pointsToEarn');
        if (pointsEl) {
            pointsEl.textContent = pricing.pointsEarned;
        }
    }

    // ============= MODAL MAP =============
    function initModalMap() {
        if (mapInitialized) return;
        const mapContainer = document.getElementById('modalMap');
        if (!mapContainer) { setTimeout(initModalMap, 500); return; }

        try {
            const defaultCenter = [14.5995, 120.9842];
            modalMap = L.map('modalMap').setView(defaultCenter, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
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
        if (!modalMap || !mapInitialized) { initModalMap(); return; }

        const select = document.getElementById('orderBranchSelect');
        if (!select) return;
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption) return;
        
        const lat = parseFloat(selectedOption.dataset.lat) || 0;
        const lng = parseFloat(selectedOption.dataset.lng) || 0;
        const name = selectedOption.dataset.name || 'Branch';
        const distance = parseFloat(selectedOption.dataset.distance) || 0;

        document.getElementById('selectedBranchName').textContent = name;
        updateNearestBadge();
        
        if (distance > 0) {
            const distanceText = distance < 1 ? (distance * 1000).toFixed(0) + ' m' : distance.toFixed(1) + ' km';
            document.getElementById('selectedBranchDistance').textContent = distanceText;
        } else {
            document.getElementById('selectedBranchDistance').textContent = '--';
        }

        if (modalMarker) modalMap.removeLayer(modalMarker);
        if (userMarker) modalMap.removeLayer(userMarker);
        if (routeLine) modalMap.removeLayer(routeLine);

        if (lat && lng) {
            const position = [lat, lng];
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

            if (userLat && userLng) {
                const userPos = [userLat, userLng];
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

                routeLine = L.polyline([userPos, position], {
                    color: '#6F4E37',
                    weight: 2,
                    opacity: 0.7,
                    dashArray: '5, 10'
                }).addTo(modalMap);

                const bounds = L.latLngBounds([userPos, position]);
                modalMap.fitBounds(bounds, { padding: [50, 50] });
                if (modalMap.getZoom() > 16) modalMap.setZoom(15);
            } else {
                modalMap.setView(position, 15);
            }
        }
    }

    // ============= ORDER MODAL =============
    function openOrderModal() {
        if (cart.length === 0) { 
            showFeedback('warning', 'Cart is Empty', 'Please add items to your cart before checking out.');
            return; 
        }
        checkCustomerAddress();

        const modal = document.getElementById('orderModal');
        const itemsList = document.getElementById('orderItemsList');
        
        let html = '';
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            html += `<div class="order-item"><span>${item.name} × ${item.quantity}</span><span>₱${subtotal.toFixed(2)}</span></div>`;
        });
        itemsList.innerHTML = html;
        
        updatePricing();
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(initModalMap, 300);
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // ============= CONFIRMATION MODAL =============
    function openConfirmationModal() {
        if (cart.length === 0) { 
            showFeedback('warning', 'Cart is Empty', 'Please add items to your cart before checking out.');
            return; 
        }

        const branchId = document.getElementById('orderBranchSelect')?.value;
        const isDelivery = document.getElementById('deliveryCheck')?.checked || false;
        const notes = document.getElementById('orderNotes')?.value || '';

        if (!branchId) { 
            showFeedback('warning', 'No Branch Selected', 'Please select a branch for pickup or delivery.');
            return; 
        }
        
        if (isDelivery && (!customerAddress || customerAddress.trim() === '')) {
            showFeedback('warning', 'Address Required', 'Please set up your delivery address in your profile first.');
            return;
        }

        // Compute totals
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.price * item.quantity; });
        
        const discountAmount = (subtotal * discountRate) / 100;
        const select = document.getElementById('orderBranchSelect');
        const selectedOption = select?.options[select.selectedIndex];
        let distance = 0;
        if (isDelivery && selectedOption) {
            distance = parseFloat(selectedOption.dataset.distance) || 0;
        }
        const deliveryFee = isDelivery ? (distance * 10) : 0;
        const totalAmount = subtotal - discountAmount + deliveryFee;
        const pointsEarned = Math.floor(totalAmount / 100);

        // Build confirmation body
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
                <span class="value">₱${subtotal.toFixed(2)}</span>
            </div>
        `;

        if (discountAmount > 0) {
            summaryHtml += `
                <div class="summary-row">
                    <span class="label">Discount (${discountRate}%)</span>
                    <span class="value discount">-₱${discountAmount.toFixed(2)}</span>
                </div>
            `;
        }

        if (deliveryFee > 0) {
            summaryHtml += `
                <div class="summary-row">
                    <span class="label">Delivery Fee (${distance.toFixed(1)} km)</span>
                    <span class="value delivery">₱${deliveryFee.toFixed(2)}</span>
                </div>
            `;
        }

        summaryHtml += `
            <div class="summary-total">
                <span>Total</span>
                <span>₱${totalAmount.toFixed(2)}</span>
            </div>
            <div class="summary-row" style="border-top:1px solid #eee;padding-top:4px;margin-top:4px;">
                <span class="label">Points to earn</span>
                <span class="value">${pointsEarned}</span>
            </div>
            <div class="summary-row">
                <span class="label">Branch</span>
                <span class="value">${selectedOption?.text || 'N/A'}</span>
            </div>
            <div class="summary-row">
                <span class="label">Type</span>
                <span class="value">${isDelivery ? 'Delivery' : 'Pickup'}</span>
            </div>
            ${isDelivery ? `
            <div class="summary-row">
                <span class="label">Address</span>
                <span class="value" style="font-size:12px;">${customerAddress}</span>
            </div>
            ` : ''}
        `;

        document.getElementById('confirmationBody').innerHTML = `
            <div style="margin-bottom:10px;font-weight:600;color:#333;">Items</div>
            ${itemsHtml}
            <div style="margin-top:12px;padding-top:10px;border-top:1px solid #eee;">
                ${summaryHtml}
            </div>
        `;

        // Store order data for placeOrder
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

    // ============= PLACE ORDER =============
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
                document.getElementById('orderNotes').value = '';
                document.getElementById('deliveryCheck').checked = false;
                closeOrderModal();
                
                setTimeout(() => {
                    location.reload();
                }, 3000);
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
            showFeedback('error', 'Error', 'An error occurred while placing your order. Please try again.');
        });
    }

    // Close confirmation modal on outside click
    document.getElementById('confirmationModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmationModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmationModal();
            closeFeedbackModal();
        }
    });

    // ============= FEEDBACK MODAL FUNCTIONS =============
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
            warning: { icon: 'fa-exclamation-triangle', color: '#ffc107', btnClass: 'btn-warning', btnText: 'OK' },
            info: { icon: 'fa-info-circle', color: '#17a2b8', btnClass: 'btn-primary', btnText: 'OK' }
        };
        
        const t = types[type] || types.info;
        
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
        const modal = document.getElementById('feedbackModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    document.getElementById('feedbackModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFeedbackModal();
        }
    });

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
        updateSlides();
        startSlideshow();
        updateCart();
        checkCustomerAddress();
        updateAvailableCount();
    });

    document.getElementById('orderModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeOrderModal();
    });
    
    document.getElementById('branchAvailabilityModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeBranchModal();
    });
</script>
@endpush
@endsection