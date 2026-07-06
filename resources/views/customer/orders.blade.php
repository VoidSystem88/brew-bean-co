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
    
    /* ===== FIXED PROGRESS TIMELINE - HANDLES COMPLETED STATUS ===== */
    .progress-timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0 8px;
        position: relative;
        margin: 0 4px;
    }
    
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
    
    .progress-timeline .step.completed .step-icon {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        box-shadow: 0 4px 12px rgba(111, 78, 55, 0.25);
        transform: scale(1.05);
    }
    
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
    
    .progress-timeline .step.pending .step-icon {
        background: #f5f5f5;
        border-color: #eee;
        color: #ccc;
    }
    
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
    
    /* ===== COMPLETED BANNER ===== */
    .completed-banner {
        background: #d4edda;
        border-radius: 6px;
        padding: 6px 10px;
        margin: 6px 0 4px;
        font-size: 12px;
        color: #155724;
        display: flex;
        align-items: center;
        gap: 6px;
        border-left: 3px solid #28a745;
    }
    
    .completed-banner i {
        font-size: 14px;
        color: #28a745;
    }
    
    .cancelled-banner {
        background: #f8d7da;
        border-radius: 6px;
        padding: 6px 10px;
        margin: 6px 0 4px;
        font-size: 12px;
        color: #721c24;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .cancelled-banner i {
        font-size: 14px;
    }
    
    .failed-banner {
        background: #f8d7da;
        border-radius: 6px;
        padding: 6px 10px;
        margin: 6px 0 4px;
        font-size: 12px;
        color: #721c24;
        display: flex;
        align-items: center;
        gap: 6px;
        border-left: 3px solid #dc3545;
    }
    
    .failed-banner i {
        font-size: 14px;
        color: #dc3545;
    }
    
    /* ===== REFUND STATUS BADGE ===== */
    .refund-badge {
        font-size: 10px;
        padding: 2px 10px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .refund-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .refund-badge.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .refund-badge.denied {
        background: #f8d7da;
        color: #721c24;
    }
    
    .refund-badge.completed {
        background: #cce5ff;
        color: #004085;
    }
    
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
    
    .order-card .order-actions .btn-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }
    
    .order-card .order-actions .btn-warning:hover {
        background: #ffc107;
        color: #333;
    }
    
    .order-card .order-actions .btn-secondary {
        background: #e9ecef;
        color: #666;
        border: 1px solid #dee2e6;
        cursor: default;
        pointer-events: none;
    }
    
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
    
    /* ============================================================
       REFUND MODAL
       ============================================================ */
    .refund-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: overlayFade 0.3s ease;
    }
    
    .refund-modal-overlay.show {
        display: flex;
    }
    
    .refund-modal {
        background: white;
        border-radius: 24px;
        max-width: 520px;
        width: 100%;
        max-height: 95vh;
        overflow: hidden;
        animation: modalPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.35);
    }
    
    @keyframes modalPop {
        0% {
            opacity: 0;
            transform: scale(0.8) translateY(30px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .refund-modal-header {
        padding: 18px 24px;
        border-bottom: 2px solid #6F4E37;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #faf8f6;
    }
    
    .refund-modal-header h4 {
        margin: 0;
        font-weight: 700;
        color: #333;
        font-size: 18px;
    }
    
    .refund-modal-header h4 i {
        margin-right: 10px;
        color: #6F4E37;
    }
    
    .refund-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0 8px;
        transition: 0.2s;
    }
    
    .refund-modal-close:hover {
        color: #333;
    }
    
    .refund-modal-body {
        padding: 24px;
        overflow-y: auto;
        max-height: 65vh;
    }
    
    .refund-order-info {
        background: #f8f6f4;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }
    
    .refund-order-info p {
        margin: 2px 0;
        font-size: 14px;
    }
    
    .refund-order-info p strong {
        color: #333;
    }
    
    .refund-form-group {
        margin-bottom: 14px;
    }
    
    .refund-label {
        font-weight: 600;
        font-size: 13px;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }
    
    .refund-select {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 14px;
        background: white;
        transition: 0.2s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    
    .refund-select:focus {
        border-color: #6F4E37;
        outline: none;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
    }
    
    .refund-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 14px;
        transition: 0.2s;
        resize: vertical;
        font-family: inherit;
    }
    
    .refund-textarea:focus {
        border-color: #6F4E37;
        outline: none;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
    }
    
    .refund-amount-display {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #f8f6f4;
        border-radius: 8px;
        margin: 4px 0;
    }
    
    .refund-amount-display .amount-label {
        color: #666;
        font-size: 13px;
    }
    
    .refund-amount-display .amount-value {
        font-weight: 700;
        font-size: 18px;
        color: #6F4E37;
    }
    
    .refund-slider {
        width: 100%;
        height: 6px;
        -webkit-appearance: none;
        appearance: none;
        background: linear-gradient(to right, #6F4E37, #6F4E37 100%, #ddd 100%);
        border-radius: 10px;
        outline: none;
        margin: 8px 0;
    }
    
    .refund-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #6F4E37;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(111, 78, 55, 0.3);
    }
    
    .refund-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #6F4E37;
        cursor: pointer;
        border: 2px solid white;
    }
    
    .refund-slider-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #999;
    }
    
    .refund-slider-labels #refundSliderPercent {
        font-weight: 700;
        color: #6F4E37;
    }
    
    .refund-attachment-hint {
        background: #f8f6f4;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin: 12px 0;
        border: 1px dashed #ddd;
    }
    
    .refund-attachment-hint i {
        color: #6F4E37;
    }
    
    .refund-attachment-hint span {
        font-size: 13px;
        color: #666;
        flex: 1;
    }
    
    .btn-attach {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .btn-attach:hover {
        background: #5a3d2b;
    }
    
    .refund-policy-notice {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        color: #856404;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-top: 12px;
    }
    
    .refund-policy-notice i {
        margin-top: 2px;
        flex-shrink: 0;
    }
    
    .refund-modal-footer {
        padding: 16px 24px 20px;
        border-top: 1px solid #eee;
        background: #faf8f6;
        display: flex;
        gap: 10px;
    }
    
    .refund-modal-footer .btn {
        flex: 1;
        padding: 10px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    
    .refund-modal-footer .btn:active {
        transform: scale(0.95);
    }
    
    .refund-modal-footer .btn-secondary {
        background: #f5f5f5;
        color: #666;
    }
    
    .refund-modal-footer .btn-secondary:hover {
        background: #eee;
    }
    
    .refund-modal-footer .btn-warning {
        background: #ffc107;
        color: #333;
    }
    
    .refund-modal-footer .btn-warning:hover {
        background: #e0a800;
        transform: scale(1.02);
    }
    
    .refund-modal-footer .btn-warning:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    @keyframes overlayFade {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* ============================================================
       SUCCESS MODAL
       ============================================================ */
    .success-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: overlayFade 0.4s ease;
    }
    
    .success-modal-overlay.show {
        display: flex;
    }
    
    .success-modal {
        background: white;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
        padding: 40px 32px 32px;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: modalBounceIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.3);
    }
    
    @keyframes modalBounceIn {
        0% {
            opacity: 0;
            transform: scale(0.7) translateY(40px);
        }
        60% {
            opacity: 1;
            transform: scale(1.02) translateY(-4px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .success-modal .success-icon-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 16px;
    }
    
    .success-modal .success-icon-wrapper .success-ring {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 50%;
        border: 4px solid #d4edda;
        animation: ringPulse 2s ease-in-out infinite;
    }
    
    @keyframes ringPulse {
        0% {
            transform: scale(1);
            opacity: 0.6;
        }
        50% {
            transform: scale(1.15);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 0;
        }
    }
    
    .success-modal .success-icon-wrapper .success-ring:nth-child(2) {
        animation-delay: 0.6s;
    }
    
    .success-modal .success-icon-wrapper .success-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: white;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 32px rgba(40, 167, 69, 0.35);
        animation: iconPop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes iconPop {
        0% {
            transform: scale(0) rotate(-30deg);
        }
        60% {
            transform: scale(1.1) rotate(4deg);
        }
        100% {
            transform: scale(1) rotate(0deg);
        }
    }
    
    .success-modal .success-icon-wrapper .success-check {
        animation: checkDraw 0.6s ease 0.3s both;
    }
    
    @keyframes checkDraw {
        0% {
            stroke-dashoffset: 60;
            opacity: 0;
        }
        100% {
            stroke-dashoffset: 0;
            opacity: 1;
        }
    }
    
    .confetti-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        overflow: hidden;
        border-radius: 24px;
    }
    
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        opacity: 0;
        animation: confettiFall 2.5s ease-in forwards;
    }
    
    .confetti:nth-child(1) { left: 10%; animation-delay: 0.1s; background: #ff6b6b; transform: rotate(45deg); }
    .confetti:nth-child(2) { left: 20%; animation-delay: 0.3s; background: #feca57; transform: rotate(90deg); }
    .confetti:nth-child(3) { left: 30%; animation-delay: 0.5s; background: #48dbfb; transform: rotate(135deg); }
    .confetti:nth-child(4) { left: 40%; animation-delay: 0.7s; background: #ff9ff3; transform: rotate(20deg); }
    .confetti:nth-child(5) { left: 50%; animation-delay: 0.2s; background: #54a0ff; transform: rotate(70deg); }
    .confetti:nth-child(6) { left: 60%; animation-delay: 0.4s; background: #5f27cd; transform: rotate(110deg); }
    .confetti:nth-child(7) { left: 70%; animation-delay: 0.6s; background: #ff9f43; transform: rotate(30deg); }
    .confetti:nth-child(8) { left: 80%; animation-delay: 0.8s; background: #00d2d3; transform: rotate(80deg); }
    .confetti:nth-child(9) { left: 90%; animation-delay: 0.15s; background: #f368e0; transform: rotate(140deg); }
    .confetti:nth-child(10) { left: 15%; animation-delay: 0.45s; background: #ffc048; transform: rotate(55deg); }
    .confetti:nth-child(11) { left: 45%; animation-delay: 0.25s; background: #4bc0c0; transform: rotate(100deg); }
    .confetti:nth-child(12) { left: 75%; animation-delay: 0.55s; background: #e15f41; transform: rotate(15deg); }
    .confetti:nth-child(13) { left: 55%; animation-delay: 0.35s; background: #c44569; transform: rotate(65deg); }
    .confetti:nth-child(14) { left: 85%; animation-delay: 0.65s; background: #3dc1d3; transform: rotate(120deg); }
    .confetti:nth-child(15) { left: 25%; animation-delay: 0.75s; background: #e77f67; transform: rotate(40deg); }
    
    @keyframes confettiFall {
        0% {
            opacity: 1;
            transform: translateY(-20px) rotate(0deg) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(300px) rotate(720deg) scale(0.3);
        }
    }
    
    .success-modal .modal-title {
        font-size: 22px;
        font-weight: 700;
        color: #28a745;
        margin-bottom: 6px;
        animation: fadeUp 0.5s ease 0.2s both;
    }
    
    .success-modal .modal-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 4px;
        animation: fadeUp 0.5s ease 0.3s both;
    }
    
    .success-modal .modal-order-id {
        display: inline-block;
        background: #f8f6f4;
        padding: 4px 16px;
        border-radius: 20px;
        font-weight: 700;
        color: var(--primary);
        font-size: 14px;
        margin: 6px 0 14px;
        animation: fadeUp 0.5s ease 0.4s both;
    }
    
    .success-modal .modal-message {
        font-size: 13px;
        color: #999;
        margin-bottom: 20px;
        line-height: 1.5;
        animation: fadeUp 0.5s ease 0.5s both;
    }
    
    .success-modal .modal-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        animation: fadeUp 0.5s ease 0.6s both;
    }
    
    .success-modal .modal-actions .btn {
        flex: 1;
        padding: 10px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        text-align: center;
        min-width: 80px;
    }
    
    .success-modal .modal-actions .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .success-modal .modal-actions .btn-primary:hover {
        background: var(--primary-dark);
        transform: scale(1.02);
    }
    
    .success-modal .modal-actions .btn-outline {
        background: transparent;
        color: #666;
        border: 1px solid #e8e8e8;
    }
    
    .success-modal .modal-actions .btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 480px) {
        .orders-container { padding: 0; }
        .orders-header { padding: 12px 16px; }
        .orders-header h2 { font-size: 17px; }
        .orders-list { padding: 6px 8px 70px; gap: 10px; }
        .order-card .card-content { padding: 12px 14px 10px; }
        
        .progress-timeline .step .step-icon { width: 22px; height: 22px; font-size: 9px; }
        .progress-timeline .step .step-label { font-size: 6px; }
        .progress-timeline::before { left: 6%; right: 6%; }
        .progress-timeline .progress-track { left: 6%; max-width: 88%; }
        
        .order-card .order-total { font-size: 14px; }
        .order-card .order-actions .btn { font-size: 10px; padding: 4px 10px; }
        .filter-dropdown { right: -10px; min-width: 140px; }
        
        .refund-modal {
            margin: 10px;
            border-radius: 16px;
        }
        .refund-modal-header { padding: 14px 16px; }
        .refund-modal-body { padding: 16px; max-height: 60vh; }
        .refund-modal-footer { padding: 12px 16px 16px; flex-direction: column; }
        .refund-modal-footer .btn { flex: none; }
        .refund-amount-display .amount-value { font-size: 16px; }
        
        .success-modal {
            padding: 30px 20px 24px;
            margin: 10px;
        }
        .success-modal .success-icon-wrapper {
            width: 80px;
            height: 80px;
        }
        .success-modal .success-icon-wrapper .success-icon {
            width: 80px;
            height: 80px;
            font-size: 36px;
        }
        .success-modal .modal-title { font-size: 19px; }
        .success-modal .modal-actions { flex-direction: column; }
        .success-modal .modal-actions .btn { flex: none; }
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
                <button class="filter-option" data-filter="failed" onclick="applyFilter('failed')">
                    <span class="dot" style="background:#dc3545;"></span> Failed
                    <span class="count">{{ $orders->filter(fn($o) => $o->delivery_status === 'failed')->count() }}</span>
                </button>
                <button class="filter-option" data-filter="refund" onclick="applyFilter('refund')">
                    <span class="dot" style="background:#ffc107;"></span> Refund Requests
                    <span class="count">{{ $orders->filter(fn($o) => $o->refund_status && $o->refund_status !== 'none')->count() }}</span>
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
                    
                    // ===== FIX: For completed orders, show 100% progress =====
                    $isCompleted = $status === 'completed';
                    $isCancelled = $status === 'cancelled';
                    $isFailed = $status === 'failed';
                    
                    $progressPercent = 0;
                    if ($isCompleted) {
                        $progressPercent = 100;
                    } elseif (!$isCancelled && !$isFailed) {
                        $progressPercent = ($currentIndex / ($totalSteps - 1)) * 100;
                    }
                    
                    $branchName = str_replace('☕ Brew & Bean Co. - ', '', $order->branch->name ?? 'N/A');
                    
                    $hasRefund = isset($order->refund_status) && $order->refund_status !== 'none';
                    $refundStatus = $order->refund_status ?? 'none';
                    $refundRequested = $order->refund_requested ?? false;
                    
                    $refundBadgeClass = 'pending';
                    $refundBadgeText = 'Pending';
                    if ($refundStatus === 'approved') { $refundBadgeClass = 'approved'; $refundBadgeText = 'Approved'; }
                    elseif ($refundStatus === 'denied') { $refundBadgeClass = 'denied'; $refundBadgeText = 'Denied'; }
                    elseif ($refundStatus === 'completed') { $refundBadgeClass = 'completed'; $refundBadgeText = 'Completed'; }
                    elseif ($refundStatus === 'processing') { $refundBadgeClass = 'pending'; $refundBadgeText = 'Processing'; }
                @endphp
                <div class="order-card" data-status="{{ $status }}" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                    <div class="card-content">
                        <!-- Header -->
                        <div class="order-header">
                            <span class="order-id">Order <span>#{{ $order->id }}</span></span>
                            <span class="order-date">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <!-- Progress Timeline -->
                        <div class="progress-timeline">
                            <!-- Progress track - 100% for completed orders -->
                            <div class="progress-track" style="width: {{ $progressPercent }}%;"></div>
                            
                            @foreach($stepLabels as $index => $label)
                                @php
                                    // ===== FIX: Determine step status =====
                                    $stepStatus = 'pending';
                                    
                                    if ($isCompleted) {
                                        // All steps are completed
                                        $stepStatus = 'completed';
                                    } elseif ($isCancelled || $isFailed) {
                                        // For cancelled/failed, mark steps up to current as completed
                                        if ($index < $currentIndex) {
                                            $stepStatus = 'completed';
                                        } elseif ($index === $currentIndex) {
                                            $stepStatus = 'pending'; // Show as pending/cancelled
                                        } else {
                                            $stepStatus = 'pending';
                                        }
                                    } elseif ($index < $currentIndex) {
                                        $stepStatus = 'completed';
                                    } elseif ($index === $currentIndex) {
                                        $stepStatus = 'active';
                                    }
                                    
                                    // For cancelled, override the last step
                                    if ($isCancelled && $index === $currentIndex) {
                                        $stepStatus = 'pending';
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
                        
                        <!-- ===== COMPLETED BANNER ===== -->
                        @if($isCompleted)
                            <div class="completed-banner">
                                <i class="fas fa-check-circle"></i>
                                <span><strong>Delivered</strong></span>
                                @if($order->delivery_completed_at)
                                    <span style="opacity:0.7;font-size:11px;">
                                        at {{ \Carbon\Carbon::parse($order->delivery_completed_at)->format('M d, h:i A') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Cancelled/Failed Banner -->
                        @if($isCancelled)
                            <div class="cancelled-banner">
                                <i class="fas fa-times-circle"></i>
                                <span><strong>Cancelled</strong></span>
                                @if($order->cancellation_reason)
                                    <span style="opacity:0.7;">- {{ $order->cancellation_reason }}</span>
                                @endif
                            </div>
                        @endif
                        
                        @if($isFailed)
                            <div class="failed-banner">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><strong>Delivery Failed</strong></span>
                                @if($order->delivery_notes)
                                    <span style="opacity:0.7;">- {{ $order->delivery_notes }}</span>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Refund Status Badge -->
                        @if($hasRefund)
                            <div style="margin:4px 0;">
                                <span class="refund-badge {{ $refundBadgeClass }}">
                                    <i class="fas fa-undo-alt"></i> Refund: {{ $refundBadgeText }}
                                </span>
                            </div>
                        @endif
                        
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
                        <div class="order-actions">
                            @if(!$isCancelled && !$isFailed && !$isCompleted)
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
                            @endif
                            
                            <!-- Refund Button - Only for Completed Orders -->
                            @if($isCompleted && !$hasRefund && !$order->refund_requested)
                                <button class="btn btn-warning" onclick="openRefundModal({{ $order->id }})">
                                    <i class="fas fa-undo-alt me-1"></i> Request Refund
                                </button>
                            @endif
                            
                            <!-- Refund Status Display -->
                            @if($hasRefund || $order->refund_requested)
                                <span class="btn btn-secondary" style="cursor:default;pointer-events:none;">
                                    <i class="fas fa-clock me-1"></i> 
                                    @if($refundStatus === 'approved') ✅ Approved
                                    @elseif($refundStatus === 'denied') ❌ Denied
                                    @elseif($refundStatus === 'completed') ✅ Completed
                                    @elseif($refundStatus === 'processing') 🔄 Processing
                                    @else ⏳ Pending
                                    @endif
                                </span>
                            @endif
                            
                            <!-- View Details - Always show -->
                            <a href="{{ route('customer.track', $order->id) }}" class="btn btn-outline">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($orders->hasPages())
            <div style="padding: 0 16px 20px;">
                {{ $orders->links() }}
            </div>
        @endif
    @else
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

<!-- ============================================================
     REFUND MODAL
     ============================================================ -->
<div class="refund-modal-overlay" id="refundModal">
    <div class="refund-modal">
        <div class="refund-modal-header">
            <h4><i class="fas fa-undo-alt"></i> Request Refund</h4>
            <button class="refund-modal-close" onclick="closeRefundModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="refund-modal-body">
            <div class="refund-order-info" id="refundOrderInfo">
                <p><strong>Order #<span id="refundOrderId"></span></strong></p>
                <p>Total: <strong>₱<span id="refundOrderTotal"></span></strong></p>
                <p>Date: <span id="refundOrderDate"></span></p>
            </div>

            <div class="refund-form-group">
                <label class="refund-label">Reason for Refund <span class="text-danger">*</span></label>
                <select id="refundReason" class="refund-select" required>
                    <option value="">-- Select Reason --</option>
                    <option value="wrong_item">Wrong item delivered</option>
                    <option value="damaged">Damaged or defective item</option>
                    <option value="missing_items">Missing items</option>
                    <option value="late_delivery">Late delivery</option>
                    <option value="cancelled">Order cancelled but charged</option>
                    <option value="duplicate">Duplicate charge</option>
                    <option value="not_received">Order not received</option>
                    <option value="taste_issue">Quality/taste issue</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="refund-form-group">
                <label class="refund-label">Description</label>
                <textarea id="refundDescription" class="refund-textarea" 
                          placeholder="Please provide details about your refund request..." 
                          rows="4"></textarea>
            </div>

            <div class="refund-form-group">
                <label class="refund-label">Refund Amount</label>
                <div class="refund-amount-display">
                    <span class="amount-label">Requesting:</span>
                    <span class="amount-value" id="refundAmountDisplay">₱0.00</span>
                </div>
                <small class="text-muted">You can request a full or partial refund</small>
                <input type="range" id="refundAmountSlider" class="refund-slider" 
                       min="0" max="100" value="100" step="1" 
                       oninput="updateRefundAmount(this.value)">
                <div class="refund-slider-labels">
                    <span>₱0</span>
                    <span id="refundSliderPercent">100%</span>
                    <span>₱<span id="refundMaxAmount">0</span></span>
                </div>
            </div>

            <div class="refund-attachment-hint">
                <i class="fas fa-paperclip"></i>
                <span>Attach screenshot or photo (optional)</span>
                <button class="btn-attach" onclick="document.getElementById('refundAttachment').click()">
                    <i class="fas fa-upload"></i> Attach File
                </button>
                <input type="file" id="refundAttachment" accept="image/*,.pdf" style="display:none;" onchange="updateAttachmentName()">
                <span id="attachmentName" style="font-size:12px;color:#6F4E37;display:none;"></span>
            </div>

            <div class="refund-policy-notice">
                <i class="fas fa-info-circle"></i>
                <span>Refund requests are reviewed within <strong>1-3 business days</strong>. 
                You will be notified via email once a decision is made.</span>
            </div>
        </div>
        <div class="refund-modal-footer">
            <button class="btn btn-secondary" onclick="closeRefundModal()">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button class="btn btn-warning" onclick="submitRefundRequest()" id="submitRefundBtn">
                <i class="fas fa-paper-plane me-1"></i> Submit Refund Request
            </button>
        </div>
    </div>
</div>

<!-- ============================================================
     SUCCESS MODAL
     ============================================================ -->
<div class="success-modal-overlay" id="successModal">
    <div class="success-modal">
        <div class="confetti-container">
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
        </div>
        
        <div class="success-icon-wrapper">
            <div class="success-ring"></div>
            <div class="success-ring"></div>
            <div class="success-icon">
                <svg class="success-check" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" stroke-dasharray="60" stroke-dashoffset="60"/>
                </svg>
            </div>
        </div>
        
        <h3 class="modal-title" id="successTitle">Refund Request Submitted!</h3>
        <p class="modal-subtitle" id="successSubtitle">Your refund request has been received.</p>
        <div class="modal-order-id" id="successOrderId">#0000</div>
        <p class="modal-message" id="successMessage">You will receive an email notification once your request is reviewed.</p>
        
        <div class="modal-actions">
            <button class="btn btn-primary" onclick="closeSuccessModal()">
                <i class="fas fa-check me-1"></i> Got it
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentFilter = 'all';
    let refundOrderId = null;
    let refundTotal = 0;
    let successModalTimeout = null;
    
    // ============================================================
    // FILTER FUNCTIONS
    // ============================================================
    function toggleFilter() {
        const dropdown = document.getElementById('filterDropdown');
        dropdown.classList.toggle('show');
    }
    
    function applyFilter(filter) {
        currentFilter = filter;
        
        document.querySelectorAll('.filter-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.filter === filter);
            const dot = opt.querySelector('.dot');
            if (dot) {
                dot.classList.toggle('active-dot', opt.dataset.filter === filter);
            }
        });
        
        document.getElementById('filterDropdown').classList.remove('show');
        
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
    
    // ============================================================
    // REFUND MODAL FUNCTIONS
    // ============================================================
    function openRefundModal(orderId) {
        refundOrderId = orderId;
        
        // Get order details from the card
        const card = document.querySelector(`.order-card`);
        if (!card) return;
        
        // Find the order total from the card
        const totalEl = card.querySelector('.order-total');
        if (totalEl) {
            refundTotal = parseFloat(totalEl.textContent.replace('₱', '').replace(/,/g, '')) || 0;
        }
        
        // Set order info
        document.getElementById('refundOrderId').textContent = orderId;
        document.getElementById('refundOrderTotal').textContent = refundTotal.toFixed(2);
        document.getElementById('refundOrderDate').textContent = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Reset form
        document.getElementById('refundReason').value = '';
        document.getElementById('refundDescription').value = '';
        document.getElementById('refundAmountSlider').value = 100;
        document.getElementById('refundMaxAmount').textContent = refundTotal.toFixed(2);
        document.getElementById('refundAmountDisplay').textContent = '₱' + refundTotal.toFixed(2);
        document.getElementById('refundSliderPercent').textContent = '100%';
        document.getElementById('attachmentName').style.display = 'none';
        document.getElementById('refundAttachment').value = '';
        
        // Show modal
        document.getElementById('refundModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function closeRefundModal() {
        document.getElementById('refundModal').classList.remove('show');
        document.body.style.overflow = '';
        refundOrderId = null;
    }
    
    function updateRefundAmount(value) {
        const percent = parseInt(value);
        const amount = (refundTotal * percent) / 100;
        
        document.getElementById('refundAmountDisplay').textContent = '₱' + amount.toFixed(2);
        document.getElementById('refundSliderPercent').textContent = percent + '%';
        
        // Update slider background
        const slider = document.getElementById('refundAmountSlider');
        const color = percent === 100 ? '#28a745' : '#6F4E37';
        slider.style.background = `linear-gradient(to right, ${color} 0%, ${color} ${percent}%, #ddd ${percent}%, #ddd 100%)`;
    }
    
    function updateAttachmentName() {
        const fileInput = document.getElementById('refundAttachment');
        const nameDisplay = document.getElementById('attachmentName');
        
        if (fileInput.files.length > 0) {
            nameDisplay.textContent = '📎 ' + fileInput.files[0].name;
            nameDisplay.style.display = 'inline-block';
        } else {
            nameDisplay.style.display = 'none';
        }
    }
    
    function submitRefundRequest() {
        const reason = document.getElementById('refundReason').value;
        const description = document.getElementById('refundDescription').value;
        const percent = parseInt(document.getElementById('refundAmountSlider').value);
        const amount = (refundTotal * percent) / 100;
        
        if (!reason) {
            alert('Please select a reason for your refund request.');
            document.getElementById('refundReason').focus();
            return;
        }
        
        if (amount <= 0) {
            alert('Please request a valid refund amount.');
            return;
        }
        
        const btn = document.getElementById('submitRefundBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';
        
        // Prepare data
        const data = {
            order_id: refundOrderId,
            reason: reason,
            description: description,
            amount: amount,
            percent: percent
        };
        
        // Send refund request to server
        fetch('/customer/refund/request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Refund Request';
            
            if (result.success) {
                closeRefundModal();
                showSuccessModal(
                    refundOrderId,
                    '✅ Refund Request Submitted!',
                    'Your refund request has been received and is being reviewed.',
                    'You will receive an email notification once your request is processed.'
                );
                setTimeout(() => location.reload(), 3000);
            } else {
                alert('❌ ' + (result.message || 'Failed to submit refund request. Please try again.'));
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Refund Request';
            alert('❌ An error occurred. Please try again.');
            console.error('Error:', error);
        });
    }
    
    // Close refund modal on outside click
    document.getElementById('refundModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRefundModal();
        }
    });
    
    // ============================================================
    // SUCCESS MODAL FUNCTIONS
    // ============================================================
    function showSuccessModal(orderId, title, subtitle, message) {
        const modal = document.getElementById('successModal');
        document.getElementById('successTitle').textContent = title;
        document.getElementById('successSubtitle').textContent = subtitle;
        document.getElementById('successOrderId').textContent = '#' + orderId;
        document.getElementById('successMessage').textContent = message;
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        if (successModalTimeout) clearTimeout(successModalTimeout);
        successModalTimeout = setTimeout(() => {
            closeSuccessModal();
        }, 5000);
    }
    
    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        if (successModalTimeout) {
            clearTimeout(successModalTimeout);
            successModalTimeout = null;
        }
    }
    
    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSuccessModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRefundModal();
            closeSuccessModal();
        }
    });
    
    // ============================================================
    // CANCEL ORDER
    // ============================================================
    function cancelOrder(orderId) {
        if (!confirm('Cancel this order?')) return;
        
        const btn = event?.target?.closest?.('.btn-danger');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
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
                alert('✅ Order cancelled successfully.');
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
    
    // ============================================================
    // AUTO-REFRESH
    // ============================================================
    document.addEventListener('click', function(e) {
        const container = document.querySelector('.header-actions');
        if (container && !container.contains(e.target)) {
            document.getElementById('filterDropdown').classList.remove('show');
        }
    });
    
    let refreshInterval;
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            const hasPending = document.querySelector('.order-card[data-status="pending"], .order-card[data-status="preparing"], .order-card[data-status="ready"], .order-card[data-status="out_for_delivery"]');
            if (hasPending) {
                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
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