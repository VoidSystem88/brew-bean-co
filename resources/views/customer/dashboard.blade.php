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
    
    .filter-group {
        display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px;
    }
    .filter-btn {
        padding: 3px 14px; border-radius: 20px;
        border: 1px solid #ddd; background: white;
        font-size: 12px; cursor: pointer; transition: 0.2s;
    }
    .filter-btn:hover { border-color: #6F4E37; }
    .filter-btn.active { background: #6F4E37; color: white; border-color: #6F4E37; }
    
    .location-banner {
        background: white; border-radius: 10px; padding: 10px 16px;
        border: 1px solid #eee; margin-bottom: 15px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap;
    }
    .location-banner .location-text { font-size: 13px; color: #666; }
    .location-banner .location-text i { color: #6F4E37; }
    .location-banner .btn-locate {
        background: #6F4E37; color: white; border: none;
        padding: 4px 14px; border-radius: 20px; font-size: 12px; cursor: pointer;
    }
    .location-banner .btn-locate:hover { background: #5a3d2b; }
    
    /* Checkout Modal */
    .checkout-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(6px);
    }
    .checkout-modal.show { display: flex; }
    .checkout-modal .modal-content {
        background: white;
        border-radius: 20px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        animation: slideUp 0.4s ease;
        box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        overflow: hidden;
    }
    .checkout-modal .modal-header {
        padding: 20px 24px 16px;
        border-bottom: 2px solid #6F4E37;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #faf8f6;
        flex-shrink: 0;
    }
    .checkout-modal .modal-header h4 {
        font-size: 20px; font-weight: 700; color: #333; margin: 0;
        display: flex; align-items: center; gap: 10px;
    }
    .checkout-modal .modal-header h4 i { color: #6F4E37; }
    .checkout-modal .modal-close {
        background: none; border: none; font-size: 24px; color: #999; cursor: pointer;
        padding: 4px 8px; transition: 0.2s;
    }
    .checkout-modal .modal-close:hover { color: #333; }
    .checkout-modal .modal-body {
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1;
    }
    .checkout-modal .modal-footer {
        padding: 16px 24px 20px;
        border-top: 1px solid #eee;
        background: #faf8f6;
        flex-shrink: 0;
    }
    
    .checkout-item {
        display: flex; align-items: center; gap: 14px;
        padding: 10px 0; border-bottom: 1px solid #f5f5f5;
    }
    .checkout-item .item-img {
        width: 50px; height: 50px; border-radius: 10px;
        overflow: hidden; background: #f5f0eb; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid #eee;
    }
    .checkout-item .item-img img { width: 100%; height: 100%; object-fit: cover; }
    .checkout-item .item-img .no-img { font-size: 22px; color: #ccc; }
    .checkout-item .item-info { flex: 1; min-width: 0; }
    .checkout-item .item-info .name { font-weight: 600; font-size: 14px; color: #333; }
    .checkout-item .item-info .price { font-size: 13px; color: #6F4E37; font-weight: 500; }
    .checkout-item .item-actions {
        display: flex; align-items: center; gap: 8px;
    }
    .checkout-item .item-actions .qty-btn {
        width: 28px; height: 28px; border-radius: 50%;
        border: 1px solid #ddd; background: white;
        font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .checkout-item .item-actions .qty-btn:hover {
        background: #6F4E37; color: white; border-color: #6F4E37;
    }
    .checkout-item .item-actions .qty-num {
        min-width: 24px; text-align: center; font-weight: 600; font-size: 14px;
    }
    .checkout-item .item-subtotal {
        font-weight: 700; color: #6F4E37; font-size: 14px;
        min-width: 70px; text-align: right;
    }
    .checkout-item .item-remove {
        background: none; border: none; color: #dc3545;
        cursor: pointer; font-size: 16px; padding: 4px 8px;
        opacity: 0.4; transition: 0.2s;
    }
    .checkout-item .item-remove:hover { opacity: 1; }
    
    .modal-pricing {
        background: #f8f6f4;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 12px;
    }
    .modal-pricing .price-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
        color: #666;
    }
    .modal-pricing .price-row.total {
        font-weight: 700;
        font-size: 18px;
        color: #6F4E37;
        border-top: 2px solid #e8e8e8;
        padding-top: 8px;
        margin-top: 4px;
    }
    .modal-pricing .price-row.discount { color: #28a745; }
    .modal-pricing .points-earn {
        font-size: 12px;
        color: #999;
        border-top: 1px solid #e8e8e8;
        padding-top: 4px;
        margin-top: 2px;
        display: flex;
        justify-content: space-between;
    }
    
    .address-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 10px 14px;
        margin: 10px 0;
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
    
    .modal-footer-btns {
        display: flex;
        gap: 10px;
    }
    .modal-footer-btns .btn {
        flex: 1;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
    }
    .modal-footer-btns .btn-cancel {
        background: #f5f5f5;
        color: #666;
    }
    .modal-footer-btns .btn-cancel:hover { background: #eee; }
    .modal-footer-btns .btn-confirm {
        background: #6F4E37;
        color: white;
    }
    .modal-footer-btns .btn-confirm:hover {
        background: #5a3d2b;
        transform: scale(1.02);
    }
    .modal-footer-btns .btn-confirm:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    /* ===== DELIVERY TOGGLE ===== */
    .delivery-toggle {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
        margin-bottom: 8px;
    }
    .delivery-toggle .custom-checkbox {
        position: relative;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
        cursor: pointer;
    }
    .delivery-toggle .custom-checkbox input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .delivery-toggle .custom-checkbox .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #ddd;
        border-radius: 26px;
        transition: 0.3s;
        border: 2px solid #ccc;
    }
    .delivery-toggle .custom-checkbox .checkmark::after {
        content: "";
        position: absolute;
        left: 3px;
        bottom: 3px;
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .delivery-toggle .custom-checkbox input:checked + .checkmark {
        background: #6F4E37;
        border-color: #6F4E37;
    }
    .delivery-toggle .custom-checkbox input:checked + .checkmark::after {
        transform: translateX(22px);
    }
    .delivery-toggle .custom-checkbox .check-icon {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 16px;
        z-index: 2;
        pointer-events: none;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .delivery-toggle .custom-checkbox input:checked + .checkmark .check-icon {
        display: block;
    }
    .delivery-toggle .form-check-label {
        font-size: 13px;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .delivery-toggle .form-check-label i { color: #6F4E37; }
    .delivery-toggle .delivery-fee-display {
        font-size: 12px;
        color: #6F4E37;
        font-weight: 600;
        margin-left: auto;
        background: #f0ebe6;
        padding: 2px 12px;
        border-radius: 12px;
    }
    
    /* ===== VOUCHER DROPDOWN ===== */
    .voucher-dropdown {
        margin-top: 8px;
        padding: 10px 0;
        border-top: 1px solid #eee;
    }
    .voucher-dropdown label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }
    .voucher-dropdown label i { color: #6F4E37; }
    .voucher-dropdown select {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
        background: white;
        transition: 0.2s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    .voucher-dropdown select:focus {
        border-color: #6F4E37;
        outline: none;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
    }
    .voucher-dropdown select option:disabled {
        color: #ccc;
    }
    .voucher-dropdown .voucher-info {
        font-size: 11px;
        color: #999;
        margin-top: 4px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 4px;
    }
    .voucher-dropdown .voucher-info .voucher-badge {
        padding: 2px 10px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
    }
    .voucher-dropdown .voucher-info .voucher-badge.active {
        background: #d4edda;
        color: #155724;
    }
    .voucher-dropdown .voucher-info .voucher-badge.locked {
        background: #f8d7da;
        color: #721c24;
    }
    .voucher-dropdown .voucher-info .voucher-badge.available {
        background: #cce5ff;
        color: #004085;
    }
    
    .modal-branch-selector select {
        padding: 6px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        background: white;
        width: 100%;
    }
    .modal-branch-selector select:focus {
        border-color: #6F4E37;
        outline: none;
    }
    
    .feedback-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 999999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(6px);
    }
    .feedback-modal.show { display: flex; animation: modalFadeIn 0.3s ease; }
    .feedback-modal .feedback-content {
        background: white;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
        padding: 35px 30px;
        text-align: center;
        animation: slideUp 0.4s ease;
        box-shadow: 0 40px 100px rgba(0,0,0,0.4);
    }
    .feedback-modal .feedback-icon { font-size: 64px; margin-bottom: 12px; }
    .feedback-modal .feedback-icon.success { color: #28a745; }
    .feedback-modal .feedback-icon.error { color: #dc3545; }
    .feedback-modal .feedback-icon.warning { color: #ffc107; }
    .feedback-modal h4 { font-size: 22px; font-weight: 700; color: #333; margin-bottom: 6px; }
    .feedback-modal p { font-size: 14px; color: #666; margin-bottom: 12px; }
    .feedback-modal .feedback-btn {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 10px 40px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 4px;
    }
    .feedback-modal .feedback-btn:hover { background: #5a3d2b; transform: scale(1.02); }
    .feedback-modal .feedback-btn.btn-success { background: #28a745; }
    .feedback-modal .feedback-btn.btn-danger { background: #dc3545; }
    .feedback-modal .feedback-btn.btn-warning { background: #ffc107; color: #333; }
    
    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    /* ===== FLOATING CART BUBBLE ===== */
    .floating-cart-bubble {
        position: fixed;
        bottom: 100px;
        right: 20px;
        z-index: 9998;
        background: #6F4E37;
        color: white;
        border-radius: 50px;
        padding: 12px 20px;
        display: none;
        align-items: center;
        gap: 12px;
        box-shadow: 0 8px 32px rgba(111, 78, 55, 0.4);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: none;
        font-family: inherit;
        text-decoration: none;
        min-width: 60px;
        justify-content: center;
    }
    .floating-cart-bubble.show {
        display: flex;
        animation: bubbleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .floating-cart-bubble.hide {
        animation: bubbleOut 0.4s ease forwards;
    }
    .floating-cart-bubble:hover {
        transform: translateY(-4px) scale(1.04);
        box-shadow: 0 12px 40px rgba(111, 78, 55, 0.5);
        background: #5a3d2b;
        color: white;
    }
    .floating-cart-bubble:active {
        transform: scale(0.95);
    }
    .floating-cart-bubble .bubble-icon {
        font-size: 20px;
    }
    .floating-cart-bubble .bubble-badge {
        background: #ffd700;
        color: #333;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .floating-cart-bubble .bubble-total {
        font-size: 15px;
        font-weight: 700;
        color: #ffd700;
    }
    
    /* ===== SCROLL BEHAVIOR - Fade on scroll ===== */
    .floating-cart-bubble.scrolled {
        opacity: 0.2;
        pointer-events: none;
        transform: scale(0.9);
    }
    .floating-cart-bubble.scrolled:hover {
        opacity: 0.4;
        transform: scale(0.92);
    }
    
    @keyframes bubbleIn {
        0% { opacity: 0; transform: scale(0.5) translateY(30px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes bubbleOut {
        0% { opacity: 1; transform: scale(1) translateY(0); }
        100% { opacity: 0; transform: scale(0.5) translateY(30px); }
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
        .checkout-modal .modal-content { margin: 10px; max-height: 98vh; }
        .checkout-modal .modal-header { padding: 14px 16px; }
        .checkout-modal .modal-header h4 { font-size: 17px; }
        .checkout-modal .modal-body { padding: 12px 14px; }
        .checkout-modal .modal-footer { padding: 12px 14px 16px; }
        .checkout-item { padding: 8px 0; gap: 10px; }
        .checkout-item .item-img { width: 40px; height: 40px; }
        .checkout-item .item-info .name { font-size: 13px; }
        .checkout-item .item-subtotal { font-size: 13px; min-width: 60px; }
        .checkout-item .item-actions .qty-btn { width: 24px; height: 24px; font-size: 12px; }
        .modal-footer-btns { flex-direction: column; }
        .modal-pricing .price-row.total { font-size: 16px; }
        .branch-selector { flex-direction: column; align-items: stretch; text-align: center; }
        .branch-selector .branch-stock-info { margin-left: 0; }
        .feedback-modal .feedback-content { padding: 25px 20px; margin: 10px; }
        .feedback-modal .feedback-icon { font-size: 48px; }
        .feedback-modal h4 { font-size: 18px; }
        .delivery-toggle .custom-checkbox { width: 40px; height: 22px; }
        .delivery-toggle .custom-checkbox .checkmark::after { width: 14px; height: 14px; }
        .delivery-toggle .custom-checkbox input:checked + .checkmark::after { transform: translateX(18px); }
        .voucher-dropdown select { font-size: 12px; padding: 6px 10px; }
        .floating-cart-bubble {
            bottom: 80px;
            right: 12px;
            padding: 10px 16px;
            font-size: 12px;
        }
        .floating-cart-bubble .bubble-icon { font-size: 16px; }
        .floating-cart-bubble .bubble-badge { width: 22px; height: 22px; font-size: 10px; }
        .floating-cart-bubble .bubble-total { font-size: 13px; }
    }
    
    @media (max-width: 400px) {
        .floating-cart-bubble { right: 8px; padding: 8px 12px; }
        .floating-cart-bubble .bubble-badge { width: 18px; height: 18px; font-size: 9px; }
        .floating-cart-bubble .bubble-total { font-size: 11px; }
        .floating-cart-bubble .bubble-icon { font-size: 14px; }
    }
</style>
<!-- ===== CANCELLATION NOTICE ===== -->
@if(session('cancellation_notice'))
    @php $notice = session('cancellation_notice'); @endphp
    <div class="cancellation-notice" style="background:#f8d7da;border:1px solid #f5c6cb;border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(220,53,69,0.1);">
        <div style="font-size:28px;color:#dc3545;flex-shrink:0;">
            <i class="fas fa-times-circle"></i>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700;color:#721c24;font-size:15px;">
                Order #{{ $notice['order_id'] }} Cancelled
            </div>
            <div style="color:#721c24;font-size:13px;">
                {{ $notice['message'] }}
            </div>
            <div style="color:#721c24;font-size:12px;margin-top:2px;">
                <i class="fas fa-info-circle"></i> Your payment will be refunded within 3-5 business days.
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <a href="{{ route('customer.orders') }}" style="background:#dc3545;color:white;padding:6px 16px;border-radius:20px;text-decoration:none;font-size:13px;font-weight:600;white-space:nowrap;transition:0.2s;">
                <i class="fas fa-list"></i> View Orders
            </a>
            <button onclick="this.closest('.cancellation-notice').style.display='none'" style="background:transparent;border:none;color:#721c24;font-size:20px;cursor:pointer;padding:0 4px;">
                ✕
            </button>
        </div>
    </div>
@endif
<!-- Location Banner -->
<div class="location-banner">
    <div class="location-text">
        <i class="fas fa-map-marker-alt me-1" style="color:#6F4E37;"></i>
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

<!-- Products & Cart -->
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="font-weight:600;color:#333;margin:0;">Menu</h6>
            <input type="text" id="searchProduct" class="form-control form-control-sm" placeholder="Search..." style="width:120px;border-radius:20px;border:1px solid #eee;font-size:13px;">
        </div>
        <div class="row g-2" id="productGrid">
            @foreach($products as $product)
                <div class="col-4 col-lg-3 product-item" data-name="{{ strtolower($product->name) }}" data-available="{{ $product->is_available ? 'true' : 'false' }}" data-product-id="{{ $product->id }}">
                    <div class="product-card {{ $product->is_available ? '' : 'sold-out' }}">
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
                            <button class="btn-add" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                                <i class="fas fa-plus"></i>
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
                <button class="btn w-100" onclick="openCheckoutModal()" style="background:#6F4E37;color:white;border-radius:6px;padding:8px;font-weight:600;font-size:14px;">
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

<!-- ===== FLOATING CART BUBBLE ===== -->
<button class="floating-cart-bubble" id="floatingCartBubble" onclick="openCheckout()">
    <span class="bubble-icon"><i class="fas fa-shopping-cart"></i></span>
    <span class="bubble-badge" id="bubbleCount">0</span>
    <span class="bubble-total" id="bubbleTotal">₱0.00</span>
</button>

<!-- ===== CHECKOUT MODAL ===== -->
<div class="checkout-modal" id="checkoutModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4><i class="fas fa-receipt"></i> Verify Order</h4>
            <button class="modal-close" onclick="closeCheckoutModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="modalCartItems"></div>
            
            <div id="modalAddressWarning" style="display:none;">
                <div class="address-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Please complete your delivery address in your profile first.</span>
                    <a href="{{ route('customer.profile') }}" class="btn-setup">
                        <i class="fas fa-user-edit"></i> Setup Address
                    </a>
                </div>
            </div>

            <!-- VOUCHER DROPDOWN -->
            <div class="voucher-dropdown" id="voucherDropdown">
                <label for="voucherSelect"><i class="fas fa-ticket-alt"></i> Apply Voucher</label>
                <select id="voucherSelect" onchange="applyVoucher()">
                    <option value="">No voucher</option>
                </select>
                <div class="voucher-info" id="voucherInfo">
                    <span>Select a voucher to apply</span>
                </div>
            </div>

            <div class="modal-branch-selector mt-2">
                <label style="font-size:13px;font-weight:600;margin-bottom:4px;">
                    <i class="fas fa-store me-1"></i> Pickup Branch
                </label>
                <select id="modalBranchSelect" onchange="updateModalPricing()">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" 
                                data-distance="{{ $branch->distance ?? 0 }}"
                                {{ $branch->id == $selectedBranchId ? 'selected' : '' }}>
                            {{ str_replace('Brew & Bean Co. - ', '', $branch->name) }}
                            @if($branch->distance)
                                ({{ $branch->distance_text ?? '' }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- DELIVERY TOGGLE -->
            <div class="delivery-toggle">
                <label class="custom-checkbox">
                    <input type="checkbox" id="modalDeliveryCheck" onchange="updateModalPricing()">
                    <span class="checkmark">
                        <span class="check-icon">✓</span>
                    </span>
                </label>
                <label class="form-check-label" for="modalDeliveryCheck">
                    <i class="fas fa-truck"></i> Delivery (₱10/km)
                </label>
                <span class="delivery-fee-display">
                    Fee: <span id="modalDeliveryFee">₱0.00</span>
                </span>
            </div>

            <div class="mt-2">
                <input type="text" id="modalOrderNotes" class="form-control" placeholder="Special instructions..." style="font-size:13px;padding:8px 12px;border-radius:8px;border:1px solid #ddd;">
            </div>

            <div class="modal-pricing">
                <div class="price-row">
                    <span>Subtotal</span>
                    <span id="modalSubtotal">₱0.00</span>
                </div>
                <div class="price-row discount" id="modalDiscountRow" style="display:none;">
                    <span>Discount (<span id="modalDiscountLabel">Voucher</span>)</span>
                    <span id="modalDiscount">-₱0.00</span>
                </div>
                <div class="price-row" id="modalPointsUsedRow" style="display:none;font-size:12px;color:#999;">
                    <span>Points Used</span>
                    <span id="modalPointsUsed">0</span>
                </div>
                <div class="price-row delivery-fee" id="modalDeliveryRow" style="display:none;">
                    <span>Delivery Fee</span>
                    <span id="modalDeliveryAmount">₱0.00</span>
                </div>
                <div class="price-row total">
                    <span>Total</span>
                    <span id="modalTotal">₱0.00</span>
                </div>
                <div class="points-earn">
                    <span>Points to earn</span>
                    <span id="modalPointsEarn">0</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="modal-footer-btns">
                <button class="btn btn-cancel" onclick="closeCheckoutModal()">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button class="btn btn-confirm" onclick="placeOrder()" id="modalConfirmBtn">
                    <i class="fas fa-check me-1"></i> Confirm Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== FEEDBACK MODAL ===== -->
<div class="feedback-modal" id="feedbackModal">
    <div class="feedback-content">
        <div class="feedback-icon" id="feedbackIcon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4 id="feedbackTitle">Success!</h4>
        <p id="feedbackMessage">Your order has been placed successfully.</p>
        <button class="feedback-btn" onclick="closeFeedbackModal()" id="feedbackBtn">
            <i class="fas fa-check me-2"></i> OK
        </button>
    </div>
</div>

@push('scripts')
<script>
    // ============================================================
    // STATE
    // ============================================================
    let cart = [];
    let slideIndex = 0;
    let slideInterval;
    let userLat = null;
    let userLng = null;
    let allBranches = @json($branches);
    let customerAddress = '{{ $customer->address ?? '' }}';
    let discountRate = {{ $discountRate ?? 0 }};
    let selectedBranchId = {{ $selectedBranchId ?? 0 }};
    let availableDiscounts = [];
    let selectedDiscount = null;
    let activeVoucher = null;
    let lastScrollY = 0;
    let bubbleVisible = false;

    // ============================================================
    // FLOATING BUBBLE FUNCTIONS
    // ============================================================
    function updateBubble() {
        const bubble = document.getElementById('floatingCartBubble');
        const countEl = document.getElementById('bubbleCount');
        const totalEl = document.getElementById('bubbleTotal');
        
        if (!bubble) return;
        
        if (cart && cart.length > 0) {
            let total = 0;
            let count = 0;
            cart.forEach(item => {
                total += (item.price || 0) * (item.quantity || 0);
                count += (item.quantity || 0);
            });
            
            if (countEl) countEl.textContent = count;
            if (totalEl) totalEl.textContent = '₱' + total.toFixed(2);
            
            // Show with animation
            if (!bubbleVisible) {
                bubble.classList.remove('hide');
                bubble.classList.add('show');
                bubbleVisible = true;
            }
        } else {
            // Hide with animation
            if (bubbleVisible) {
                bubble.classList.remove('show');
                bubble.classList.add('hide');
                bubbleVisible = false;
                setTimeout(() => {
                    bubble.classList.remove('hide');
                }, 400);
            }
        }
    }

    // ===== SCROLL BEHAVIOR - Fade bubble when scrolling down =====
    function handleBubbleScroll() {
        const bubble = document.getElementById('floatingCartBubble');
        if (!bubble) return;
        
        const currentScrollY = window.scrollY || window.pageYOffset;
        const scrollThreshold = 150; // pixels before fading
        
        if (currentScrollY > scrollThreshold) {
            bubble.classList.add('scrolled');
        } else {
            bubble.classList.remove('scrolled');
        }
        
        lastScrollY = currentScrollY;
    }

    // ============================================================
    // DISCOUNT FUNCTIONS
    // ============================================================
    function loadAvailableDiscounts() {
        const subtotal = calculateSubtotal();
        if (subtotal <= 0) {
            document.getElementById('voucherDropdown').style.display = 'none';
            return;
        }
        
        document.getElementById('voucherDropdown').style.display = 'block';
        
        fetch('/customer/available-discounts?subtotal=' + subtotal)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableDiscounts = data.discounts || [];
                    renderVoucherDropdown();
                }
            })
            .catch(() => {
                availableDiscounts = [];
                renderVoucherDropdown();
            });
    }
    
    function renderVoucherDropdown() {
        const select = document.getElementById('voucherSelect');
        const infoDiv = document.getElementById('voucherInfo');
        
        if (!select) return;
        
        select.innerHTML = '<option value="">No voucher</option>';
        
        if (!availableDiscounts || availableDiscounts.length === 0) {
            infoDiv.innerHTML = '<span style="color:#999;">No vouchers available</span>';
            return;
        }
        
        let hasAvailable = false;
        const subtotal = calculateSubtotal();
        
        availableDiscounts.forEach(discount => {
            const isLocked = discount.locked === true;
            const isActive = discount.is_active_voucher === true;
            const meetsMinPurchase = subtotal >= (discount.min_purchase || 0);
            const hasEnoughPoints = discount.points <= {{ $customer->loyalty_points ?? 0 }};
            const isUsable = !isLocked && (isActive || (hasEnoughPoints && meetsMinPurchase));
            
            if (isUsable) hasAvailable = true;
            
            const option = document.createElement('option');
            option.value = discount.id;
            
            let label = discount.label;
            if (isActive) label += ' ✅ Active';
            if (isLocked) label += ' 🔒 Locked';
            if (discount.value) label += ' (-₱' + discount.value + ')';
            
            option.textContent = label;
            
            if (isLocked) {
                option.disabled = true;
                let reason = '';
                if (!meetsMinPurchase && discount.min_purchase > 0) {
                    reason = ' (Min ₱' + discount.min_purchase + ')';
                } else if (!hasEnoughPoints) {
                    reason = ' (Need ' + discount.points + ' pts)';
                }
                option.textContent += reason;
            } else if (isActive) {
                option.textContent += ' ✅';
            } else if (!meetsMinPurchase) {
                option.disabled = true;
                option.textContent += ' (Min ₱' + discount.min_purchase + ')';
            } else if (!hasEnoughPoints) {
                option.disabled = true;
                option.textContent += ' (Need ' + discount.points + ' pts)';
            }
            
            select.appendChild(option);
        });
        
        if (hasAvailable) {
            infoDiv.innerHTML = '<span style="color:#28a745;">✅ Select a voucher to apply</span>';
        } else {
            infoDiv.innerHTML = '<span style="color:#999;">No vouchers available for this order</span>';
        }
        
        setTimeout(function() {
            const activeVoucherOption = Array.from(select.options).find(opt => {
                const discount = availableDiscounts.find(d => d.id === opt.value);
                return discount && discount.is_active_voucher === true && !opt.disabled;
            });
            if (activeVoucherOption) {
                select.value = activeVoucherOption.value;
                applyVoucher();
            }
        }, 100);
    }
    
    function applyVoucher() {
        const select = document.getElementById('voucherSelect');
        const voucherId = select.value;
        
        if (!voucherId) {
            selectedDiscount = null;
            updateModalPricing();
            return;
        }
        
        const discount = availableDiscounts.find(d => d.id === voucherId);
        if (!discount) return;
        
        if (discount.locked) {
            alert('This voucher is locked.');
            select.value = '';
            return;
        }
        
        const subtotal = calculateSubtotal();
        if (discount.min_purchase > 0 && subtotal < discount.min_purchase) {
            alert('Minimum purchase of ₱' + discount.min_purchase + ' required.');
            select.value = '';
            return;
        }
        
        if (discount.points > 0 && discount.points > {{ $customer->loyalty_points ?? 0 }}) {
            alert('You need ' + discount.points + ' points.');
            select.value = '';
            return;
        }
        
        selectedDiscount = discount;
        updateModalPricing();
    }

    function calculateSubtotal() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.price * item.quantity;
        });
        return subtotal;
    }

    // ============================================================
    // CART FUNCTIONS
    // ============================================================
    function addToCart(productId, productName, productPrice) {
        const existing = cart.find(item => item.id === productId);
        if (existing) {
            existing.quantity += 1;
        } else {
            const img = document.querySelector(`.product-item[data-product-id="${productId}"] img`);
            cart.push({ 
                id: productId, 
                name: productName, 
                price: productPrice, 
                quantity: 1, 
                image: img ? img.src : null 
            });
        }
        updateCart();
        updateBadge(productId);
        updateBubble();
        
        // Animate the product card
        const card = document.querySelector(`.product-item[data-product-id="${productId}"] .product-card`);
        if (card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => { card.style.transform = ''; }, 200);
        }
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
        updateBadge(id);
        updateBubble();
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
            updateBubble();
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

    // ============================================================
    // CHECKOUT MODAL
    // ============================================================
    function openCheckoutModal() {
        if (cart.length === 0) {
            const bubble = document.getElementById('floatingCartBubble');
            if (bubble) {
                bubble.style.background = '#dc3545';
                setTimeout(() => { bubble.style.background = ''; }, 500);
            }
            return;
        }
        document.getElementById('checkoutModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        renderModalItems();
        loadAvailableDiscounts();
        updateModalPricing();
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function renderModalItems() {
        const container = document.getElementById('modalCartItems');
        
        if (cart.length === 0) {
            container.innerHTML = '<div class="text-center py-4"><i class="fas fa-shopping-cart fa-3x text-muted mb-2"></i><p>Your cart is empty</p></div>';
            return;
        }

        let html = '';
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            const imgHtml = item.image 
                ? `<img src="${item.image}" alt="${item.name}">` 
                : `<div class="no-img"><i class="fas fa-coffee"></i></div>`;
            
            html += `
                <div class="checkout-item">
                    <div class="item-img">${imgHtml}</div>
                    <div class="item-info">
                        <div class="name">${item.name}</div>
                        <div class="price">₱${item.price.toFixed(2)}</div>
                    </div>
                    <div class="item-actions">
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
                        <span class="qty-num">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    </div>
                    <div class="item-subtotal">₱${subtotal.toFixed(2)}</div>
                    <button class="item-remove" onclick="removeFromCart(${item.id})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function updateModalPricing() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.price * item.quantity; });
        
        const discountAmount = selectedDiscount ? selectedDiscount.value : 0;
        const select = document.getElementById('modalBranchSelect');
        const selectedOption = select?.options[select.selectedIndex];
        let distance = parseFloat(selectedOption?.dataset.distance) || 0;
        const isDelivery = document.getElementById('modalDeliveryCheck')?.checked || false;
        const deliveryFee = isDelivery ? (distance * 10) : 0;
        const total = subtotal - discountAmount + deliveryFee;
        const pointsEarned = Math.floor(total / 100);

        document.getElementById('modalSubtotal').textContent = '₱' + subtotal.toFixed(2);
        
        const discountRow = document.getElementById('modalDiscountRow');
        const discountLabel = document.getElementById('modalDiscountLabel');
        if (discountAmount > 0 && selectedDiscount) {
            discountRow.style.display = 'flex';
            discountLabel.textContent = selectedDiscount.label || 'Voucher';
            document.getElementById('modalDiscount').textContent = '-₱' + discountAmount.toFixed(2);
        } else {
            discountRow.style.display = 'none';
        }

        const pointsRow = document.getElementById('modalPointsUsedRow');
        if (selectedDiscount && selectedDiscount.points > 0) {
            pointsRow.style.display = 'flex';
            document.getElementById('modalPointsUsed').textContent = selectedDiscount.points;
        } else {
            pointsRow.style.display = 'none';
        }

        const deliveryRow = document.getElementById('modalDeliveryRow');
        if (deliveryFee > 0) {
            deliveryRow.style.display = 'flex';
            document.getElementById('modalDeliveryAmount').textContent = '₱' + deliveryFee.toFixed(2);
            document.getElementById('modalDeliveryFee').textContent = '₱' + deliveryFee.toFixed(2);
        } else {
            deliveryRow.style.display = 'none';
            document.getElementById('modalDeliveryFee').textContent = '₱0.00';
        }

        document.getElementById('modalTotal').textContent = '₱' + total.toFixed(2);
        document.getElementById('modalPointsEarn').textContent = pointsEarned;
    }

    function openCheckout() {
        if (cart.length === 0) {
            const bubble = document.getElementById('floatingCartBubble');
            if (bubble) {
                bubble.style.background = '#dc3545';
                setTimeout(() => { bubble.style.background = ''; }, 500);
            }
            return;
        }
        openCheckoutModal();
    }

    // ============================================================
    // PLACE ORDER
    // ============================================================
    function placeOrder() {
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        const branchId = document.getElementById('modalBranchSelect')?.value;
        const isDelivery = document.getElementById('modalDeliveryCheck')?.checked || false;
        const notes = document.getElementById('modalOrderNotes')?.value || '';

        if (!branchId) {
            alert('Please select a branch.');
            return;
        }

        const btn = document.getElementById('modalConfirmBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

        const items = cart.map(item => ({ product_id: item.id, quantity: item.quantity }));

        let useDiscount = false;
        let discountId = null;
        
        if (selectedDiscount) {
            useDiscount = true;
            discountId = selectedDiscount.id;
        }

        const payload = {
            items: items,
            branch_id: parseInt(branchId),
            is_delivery: isDelivery,
            delivery_address: isDelivery ? customerAddress : null,
            notes: notes,
            use_points_discount: useDiscount,
            discount_id: discountId
        };

        fetch('{{ route("customer.place-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirm Order';
            
            if (data.success) {
                showFeedback('success', 'Order Placed!', 'Your order #' + data.order_id + ' has been placed successfully.');
                cart = [];
                updateCart();
                updateBubble();
                closeCheckoutModal();
                document.getElementById('modalOrderNotes').value = '';
                document.getElementById('modalDeliveryCheck').checked = false;
                selectedDiscount = null;
                document.getElementById('voucherSelect').value = '';
                setTimeout(() => location.reload(), 2000);
            } else {
                showFeedback('error', 'Order Failed', data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirm Order';
            showFeedback('error', 'Error', 'An error occurred. Please try again.');
        });
    }

    // ============================================================
    // FEEDBACK MODAL
    // ============================================================
    function showFeedback(type, title, message) {
        const modal = document.getElementById('feedbackModal');
        const icon = document.getElementById('feedbackIcon');
        const titleEl = document.getElementById('feedbackTitle');
        const msgEl = document.getElementById('feedbackMessage');
        const btn = document.getElementById('feedbackBtn');
        
        icon.className = 'feedback-icon ' + type;
        
        const types = {
            success: { icon: 'fa-check-circle', color: '#28a745', btnClass: 'btn-success' },
            error: { icon: 'fa-times-circle', color: '#dc3545', btnClass: 'btn-danger' },
            warning: { icon: 'fa-exclamation-triangle', color: '#ffc107', btnClass: 'btn-warning' }
        };
        
        const t = types[type] || types.warning;
        icon.innerHTML = `<i class="fas ${t.icon}" style="color:${t.color}"></i>`;
        titleEl.textContent = title;
        msgEl.textContent = message;
        btn.className = 'feedback-btn ' + t.btnClass;
        btn.innerHTML = `<i class="fas fa-check me-2"></i> OK`;
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // ============================================================
    // OTHER FUNCTIONS
    // ============================================================
    function switchBranch() {
        const branchId = document.getElementById('branchSelect').value;
        window.location.href = `/customer/dashboard?branch_id=${branchId}`;
    }

    function updateAvailableCount() {
        const available = document.querySelectorAll('.product-item[data-available="true"]:not([style*="display: none"])').length;
        document.getElementById('availableCount').textContent = available;
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
        setTimeout(updateAvailableCount, 100);
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

        const select = document.getElementById('modalBranchSelect');
        select.innerHTML = '';
        allBranches.forEach((branch) => {
            const option = document.createElement('option');
            option.value = branch.id;
            option.dataset.distance = branch.distance || 0;
            const distanceText = branch.distance_text ? ` (${branch.distance_text})` : '';
            option.textContent = branch.name.replace('Brew & Bean Co. - ', '') + distanceText;
            select.appendChild(option);
        });

        document.getElementById('locationDisplay').textContent = 'Location detected';
        updateModalPricing();
    }

    // ============================================================
    // SLIDESHOW
    // ============================================================
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

    // ============================================================
    // SEARCH
    // ============================================================
    document.getElementById('searchProduct')?.addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            item.style.display = name.includes(search) ? '' : 'none';
        });
    });

    // ============================================================
    // INIT
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        getLocation();
        startSlideshow();
        updateCart();
        updateAvailableCount();
        updateBubble();
        
        // Setup scroll listener for bubble
        window.addEventListener('scroll', handleBubbleScroll, { passive: true });
        
        // Close modal on outside click
        document.getElementById('checkoutModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCheckoutModal();
        });
        
        document.getElementById('feedbackModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeFeedbackModal();
        });

        // Escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCheckoutModal();
                closeFeedbackModal();
            }
        });
    });
</script>
@endpush
@endsection 