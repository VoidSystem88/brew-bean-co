@extends('layouts.app')

@section('page-title', 'Barista Queue')

@section('content')
<style>
    .queue-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
        background: white;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #e8e8e8;
    }
    
    .queue-header .title-section {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .queue-header .title-section h2 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #333;
    }
    
    .queue-header .title-section h2 i {
        color: #6F4E37;
        margin-right: 8px;
    }
    
    .btn-refresh {
        background: transparent;
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 13px;
        color: #666;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .btn-refresh:hover {
        background: #f5f0eb;
        border-color: #6F4E37;
    }
    
    .btn-refresh i {
        margin-right: 4px;
    }
    
    .btn-pos {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-pos:hover {
        background: #5a3d2b;
        color: white;
    }
    
    .queue-stats-mini {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .queue-stats-mini .stat-pill {
        background: #f8f6f4;
        padding: 4px 12px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 500;
        color: #666;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    
    .queue-stats-mini .stat-pill .num {
        font-weight: 700;
        color: #6F4E37;
    }
    
    .queue-stats-mini .stat-pill.pending { background: #fff3cd; color: #856404; }
    .queue-stats-mini .stat-pill.preparing { background: #cce5ff; color: #004085; }
    .queue-stats-mini .stat-pill.ready { background: #d4edda; color: #155724; }
    
    .queue-stats-mini .stat-pill .num {
        color: inherit;
    }
    
    .queue-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-height: calc(100vh - 180px);
        overflow-y: auto;
        padding-right: 4px;
    }
    
    .queue-container::-webkit-scrollbar {
        width: 5px;
    }
    .queue-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .queue-container::-webkit-scrollbar-thumb {
        background: #6F4E37;
        border-radius: 10px;
    }
    
    .queue-section {
        background: white;
        border-radius: 10px;
        border: 1px solid #e8e8e8;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .queue-section .section-header {
        padding: 10px 16px;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #e8e8e8;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fafafa;
    }
    
    .queue-section .section-header .badge-count {
        background: #6F4E37;
        color: white;
        padding: 0 10px;
        border-radius: 10px;
        font-size: 12px;
        margin-left: auto;
    }
    
    .queue-section .section-header.in-store {
        border-bottom-color: #6F4E37;
    }
    
    .queue-section .section-header.online {
        border-bottom-color: #1976d2;
    }
    
    .queue-section .section-header .header-icon {
        font-size: 16px;
        width: 24px;
        text-align: center;
    }
    
    .queue-columns {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0;
        min-height: 120px;
    }
    
    .queue-column {
        padding: 10px;
        border-right: 1px solid #f0f0f0;
        min-height: 120px;
        max-height: 350px;
        overflow-y: auto;
    }
    
    .queue-column:last-child {
        border-right: none;
    }
    
    .queue-column::-webkit-scrollbar {
        width: 4px;
    }
    .queue-column::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .queue-column::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    
    .queue-column .column-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #999;
        text-align: center;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 8px;
        position: sticky;
        top: 0;
        background: white;
        z-index: 2;
        letter-spacing: 0.5px;
    }
    
    .queue-column .column-title .count {
        background: #f0f0f0;
        padding: 0 8px;
        border-radius: 10px;
        font-size: 11px;
        margin-left: 4px;
    }
    
    .queue-column.status-pending .column-title { border-bottom-color: #ffc107; color: #856404; }
    .queue-column.status-preparing .column-title { border-bottom-color: #17a2b8; color: #004085; }
    .queue-column.status-serve .column-title { border-bottom-color: #28a745; color: #155724; }
    .queue-column.status-deliver .column-title { border-bottom-color: #0d47a1; color: #0d47a1; }
    
    .order-card-small {
        background: white;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
        border: 1px solid #eee;
        transition: 0.15s;
        cursor: pointer;
        position: relative;
    }
    
    .order-card-small:hover {
        border-color: #6F4E37;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    }
    
    .order-card-small .order-id {
        font-weight: 700;
        color: #6F4E37;
        font-size: 14px;
    }
    
    .order-card-small .order-customer {
        font-size: 13px;
        color: #333;
        font-weight: 500;
    }
    
    .order-card-small .order-items {
        font-size: 12px;
        color: #999;
    }
    
    .order-card-small .order-total {
        font-weight: 600;
        color: #6F4E37;
        font-size: 14px;
    }
    
    .order-card-small .order-time {
        font-size: 11px;
        color: #bbb;
    }
    
    .order-card-small .order-actions {
        display: flex;
        gap: 6px;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    
    .order-card-small .order-actions .btn {
        font-size: 11px;
        padding: 4px 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    
    .order-card-small .order-type-badge {
        font-size: 10px;
        padding: 1px 10px;
        border-radius: 10px;
        position: absolute;
        top: 6px;
        right: 6px;
    }
    
    .order-type-badge.in-store {
        background: #f8f6f4;
        color: #6F4E37;
    }
    
    .order-type-badge.online {
        background: #e3f2fd;
        color: #0d47a1;
    }
    
    .empty-column {
        text-align: center;
        padding: 30px 10px;
        color: #ccc;
        font-size: 13px;
    }
    
    .empty-column i {
        font-size: 28px;
        display: block;
        margin-bottom: 8px;
        color: #eee;
    }
    
    .btn-complete-order {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 16px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-complete-order:hover {
        background: #218838;
        color: white;
    }
    
    .btn-accept {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-accept:hover {
        background: #218838;
        color: white;
    }
    
    .btn-ready {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-ready:hover {
        background: #138496;
        color: white;
    }
    
    .btn-cancel {
        background: #dc3545;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-cancel:hover {
        background: #c82333;
        color: white;
    }
    
    .btn-view {
        background: #6c757d;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-view:hover {
        background: #5a6268;
        color: white;
    }
    
    .btn-assign-rider {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-assign-rider:hover {
        background: #5a3d2b;
        color: white;
    }
    
    /* ============================================================
       CONFIRMATION MODAL
       ============================================================ */
    .confirm-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 99998;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: overlayFade 0.3s ease;
    }
    
    .confirm-modal-overlay.show {
        display: flex;
    }
    
    @keyframes overlayFade {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .confirm-modal {
        background: white;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
        padding: 32px 28px 28px;
        text-align: center;
        animation: confirmPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.35);
    }
    
    @keyframes confirmPop {
        0% {
            opacity: 0;
            transform: scale(0.8) translateY(30px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .confirm-modal .confirm-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 12px;
        animation: iconPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .confirm-modal .confirm-icon.question {
        background: #fff3cd;
        color: #f59e0b;
    }
    
    .confirm-modal .confirm-icon.warning {
        background: #f8d7da;
        color: #dc3545;
    }
    
    @keyframes iconPop {
        0% {
            transform: scale(0) rotate(-20deg);
        }
        60% {
            transform: scale(1.1) rotate(3deg);
        }
        100% {
            transform: scale(1) rotate(0deg);
        }
    }
    
    .confirm-modal .confirm-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 4px;
    }
    
    .confirm-modal .confirm-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 4px;
    }
    
    .confirm-modal .confirm-order-id {
        display: inline-block;
        background: #f8f6f4;
        padding: 4px 16px;
        border-radius: 20px;
        font-weight: 700;
        color: #6F4E37;
        font-size: 14px;
        margin: 4px 0 12px;
    }
    
    .confirm-modal .confirm-message {
        font-size: 13px;
        color: #999;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .confirm-modal .confirm-actions {
        display: flex;
        gap: 10px;
    }
    
    .confirm-modal .confirm-actions .btn {
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
    
    .confirm-modal .confirm-actions .btn:active {
        transform: scale(0.95);
    }
    
    .confirm-modal .confirm-actions .btn-cancel {
        background: #f5f5f5;
        color: #666;
    }
    
    .confirm-modal .confirm-actions .btn-cancel:hover {
        background: #eee;
    }
    
    .confirm-modal .confirm-actions .btn-confirm {
        background: #6F4E37;
        color: white;
    }
    
    .confirm-modal .confirm-actions .btn-confirm:hover {
        background: #5a3d2b;
        transform: scale(1.02);
    }
    
    .confirm-modal .confirm-actions .btn-confirm.success {
        background: #28a745;
    }
    
    .confirm-modal .confirm-actions .btn-confirm.success:hover {
        background: #218838;
    }
    
    .confirm-modal .confirm-actions .btn-confirm.danger {
        background: #dc3545;
    }
    
    .confirm-modal .confirm-actions .btn-confirm.danger:hover {
        background: #c82333;
    }
    
    /* ============================================================
       FEEDBACK MODAL
       ============================================================ */
    .feedback-modal-overlay {
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
    
    .feedback-modal-overlay.show {
        display: flex;
    }
    
    .feedback-modal {
        background: white;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
        padding: 40px 32px 32px;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: modalPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.35);
    }
    
    @keyframes modalPop {
        0% {
            opacity: 0;
            transform: scale(0.7) translateY(40px) rotate(-5deg);
        }
        60% {
            opacity: 1;
            transform: scale(1.02) translateY(-4px) rotate(0.5deg);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0) rotate(0deg);
        }
    }
    
    .feedback-modal .icon-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 16px;
    }
    
    .feedback-modal .icon-wrapper .ring {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 50%;
        border: 4px solid;
        animation: ringPulse 2s ease-in-out infinite;
    }
    
    .feedback-modal .icon-wrapper .ring:nth-child(2) {
        animation-delay: 0.6s;
    }
    
    @keyframes ringPulse {
        0% {
            transform: scale(1);
            opacity: 0.6;
        }
        50% {
            transform: scale(1.2);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 0;
        }
    }
    
    .feedback-modal .icon-wrapper .icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 44px;
        color: white;
        position: relative;
        z-index: 2;
        animation: iconPop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .feedback-modal .icon-wrapper.success .ring {
        border-color: #28a745;
    }
    .feedback-modal .icon-wrapper.success .icon {
        background: linear-gradient(135deg, #28a745, #20c997);
        box-shadow: 0 8px 32px rgba(40, 167, 69, 0.35);
    }
    
    .feedback-modal .icon-wrapper.warning .ring {
        border-color: #ffc107;
    }
    .feedback-modal .icon-wrapper.warning .icon {
        background: linear-gradient(135deg, #ffc107, #f59e0b);
        box-shadow: 0 8px 32px rgba(255, 193, 7, 0.35);
    }
    
    .feedback-modal .icon-wrapper.error .ring {
        border-color: #dc3545;
    }
    .feedback-modal .icon-wrapper.error .icon {
        background: linear-gradient(135deg, #dc3545, #c82333);
        box-shadow: 0 8px 32px rgba(220, 53, 69, 0.35);
    }
    
    .feedback-modal .icon-wrapper.info .ring {
        border-color: #17a2b8;
    }
    .feedback-modal .icon-wrapper.info .icon {
        background: linear-gradient(135deg, #17a2b8, #0d6efd);
        box-shadow: 0 8px 32px rgba(23, 162, 184, 0.35);
    }
    
    .feedback-modal .icon-wrapper .check-icon {
        stroke-dasharray: 60;
        stroke-dashoffset: 60;
        animation: drawCheck 0.6s ease 0.3s both;
    }
    
    @keyframes drawCheck {
        0% {
            stroke-dashoffset: 60;
            opacity: 0;
        }
        100% {
            stroke-dashoffset: 0;
            opacity: 1;
        }
    }
    
    .feedback-modal .icon-wrapper .spin-icon {
        animation: spinIcon 1.2s linear infinite;
    }
    
    @keyframes spinIcon {
        100% { transform: rotate(360deg); }
    }
    
    .feedback-modal .modal-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 4px;
        animation: fadeUp 0.5s ease 0.2s both;
    }
    
    .feedback-modal .modal-title.success { color: #28a745; }
    .feedback-modal .modal-title.warning { color: #f59e0b; }
    .feedback-modal .modal-title.error { color: #dc3545; }
    .feedback-modal .modal-title.info { color: #0d6efd; }
    
    .feedback-modal .modal-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 4px;
        animation: fadeUp 0.5s ease 0.3s both;
    }
    
    .feedback-modal .modal-order-id {
        display: inline-block;
        background: #f8f6f4;
        padding: 4px 16px;
        border-radius: 20px;
        font-weight: 700;
        color: #6F4E37;
        font-size: 14px;
        margin: 4px 0 12px;
        animation: fadeUp 0.5s ease 0.35s both;
    }
    
    .feedback-modal .modal-message {
        font-size: 13px;
        color: #999;
        margin-bottom: 20px;
        line-height: 1.5;
        animation: fadeUp 0.5s ease 0.4s both;
    }
    
    .feedback-modal .modal-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        animation: fadeUp 0.5s ease 0.5s both;
    }
    
    .feedback-modal .modal-actions .btn {
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
    
    .feedback-modal .modal-actions .btn-primary {
        background: #6F4E37;
        color: white;
    }
    
    .feedback-modal .modal-actions .btn-primary:hover {
        background: #5a3d2b;
        transform: scale(1.02);
    }
    
    .feedback-modal .modal-actions .btn-success {
        background: #28a745;
        color: white;
    }
    
    .feedback-modal .modal-actions .btn-success:hover {
        background: #218838;
        transform: scale(1.02);
    }
    
    .feedback-modal .modal-actions .btn-danger {
        background: #dc3545;
        color: white;
    }
    
    .feedback-modal .modal-actions .btn-danger:hover {
        background: #c82333;
        transform: scale(1.02);
    }
    
    .feedback-modal .modal-actions .btn-outline {
        background: transparent;
        color: #666;
        border: 1px solid #e8e8e8;
    }
    
    .feedback-modal .modal-actions .btn-outline:hover {
        border-color: #6F4E37;
        color: #6F4E37;
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
    
    .confetti-piece {
        position: absolute;
        width: 8px;
        height: 8px;
        opacity: 0;
        animation: confettiFall 2.5s ease-in forwards;
    }
    
    .confetti-piece:nth-child(1) { left: 10%; animation-delay: 0.1s; background: #ff6b6b; transform: rotate(45deg); }
    .confetti-piece:nth-child(2) { left: 20%; animation-delay: 0.3s; background: #feca57; transform: rotate(90deg); }
    .confetti-piece:nth-child(3) { left: 30%; animation-delay: 0.5s; background: #48dbfb; transform: rotate(135deg); }
    .confetti-piece:nth-child(4) { left: 40%; animation-delay: 0.7s; background: #ff9ff3; transform: rotate(20deg); }
    .confetti-piece:nth-child(5) { left: 50%; animation-delay: 0.2s; background: #54a0ff; transform: rotate(70deg); }
    .confetti-piece:nth-child(6) { left: 60%; animation-delay: 0.4s; background: #5f27cd; transform: rotate(110deg); }
    .confetti-piece:nth-child(7) { left: 70%; animation-delay: 0.6s; background: #ff9f43; transform: rotate(30deg); }
    .confetti-piece:nth-child(8) { left: 80%; animation-delay: 0.8s; background: #00d2d3; transform: rotate(80deg); }
    .confetti-piece:nth-child(9) { left: 90%; animation-delay: 0.15s; background: #f368e0; transform: rotate(140deg); }
    .confetti-piece:nth-child(10) { left: 15%; animation-delay: 0.45s; background: #ffc048; transform: rotate(55deg); }
    .confetti-piece:nth-child(11) { left: 45%; animation-delay: 0.25s; background: #4bc0c0; transform: rotate(100deg); }
    .confetti-piece:nth-child(12) { left: 75%; animation-delay: 0.55s; background: #e15f41; transform: rotate(15deg); }
    
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
    
    @media (max-width: 576px) {
        .feedback-modal {
            padding: 30px 20px 24px;
            margin: 10px;
        }
        .feedback-modal .icon-wrapper {
            width: 80px;
            height: 80px;
        }
        .feedback-modal .icon-wrapper .icon {
            width: 80px;
            height: 80px;
            font-size: 34px;
        }
        .feedback-modal .modal-title { font-size: 19px; }
        .feedback-modal .modal-actions { flex-direction: column; }
        .feedback-modal .modal-actions .btn { flex: none; }
        
        .confirm-modal {
            padding: 24px 20px 20px;
            margin: 10px;
        }
        .confirm-modal .confirm-icon {
            width: 56px;
            height: 56px;
            font-size: 24px;
        }
        .confirm-modal .confirm-title { font-size: 17px; }
        .confirm-modal .confirm-actions { flex-direction: column; }
        .confirm-modal .confirm-actions .btn { flex: none; }
    }
</style>

<!-- Queue Header -->
<div class="queue-header">
    <div class="title-section">
        <h2><i class="fas fa-mug-hot"></i> Barista Queue</h2>
        <button class="btn-refresh" onclick="refreshQueue()">
            <i class="fas fa-sync-alt"></i> <span id="lastUpdate">Now</span>
        </button>
        <a href="{{ route('pos.index') }}" class="btn-pos">
            <i class="fas fa-cash-register"></i> POS
        </a>
    </div>
    <div class="queue-stats-mini">
        <span class="stat-pill"><i class="fas fa-store"></i> In-Store: <span class="num" id="inStoreTotalStat">0</span></span>
        <span class="stat-pill"><i class="fas fa-shopping-cart"></i> Online: <span class="num" id="onlineTotalStat">0</span></span>
        <span class="stat-pill pending"><i class="fas fa-clock"></i> <span class="num" id="pendingTotalStat">0</span></span>
        <span class="stat-pill preparing"><i class="fas fa-spinner"></i> <span class="num" id="preparingTotalStat">0</span></span>
        <span class="stat-pill ready"><i class="fas fa-check"></i> <span class="num" id="readyTotalStat">0</span></span>
    </div>
</div>

<!-- Queue Containers -->
<div class="queue-container" id="queueContainer">
    <!-- In-Store Orders -->
    <div class="queue-section">
        <div class="section-header in-store">
            <span class="header-icon"><i class="fas fa-store"></i></span>
            In-Store Orders
            <span class="badge-count" id="inStoreTotal">0</span>
        </div>
        <div class="queue-columns">
            <div class="queue-column status-pending" id="inStorePending">
                <div class="column-title">
                    <i class="fas fa-clock"></i> Pending
                    <span class="count" id="inStorePendingCount">0</span>
                </div>
                <div id="inStorePendingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-preparing" id="inStorePreparing">
                <div class="column-title">
                    <i class="fas fa-spinner"></i> Preparing
                    <span class="count" id="inStorePreparingCount">0</span>
                </div>
                <div id="inStorePreparingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-serve" id="inStoreServe">
                <div class="column-title">
                    <i class="fas fa-check-circle"></i> Serve
                    <span class="count" id="inStoreServeCount">0</span>
                </div>
                <div id="inStoreServeList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Online Orders -->
    <div class="queue-section">
        <div class="section-header online">
            <span class="header-icon"><i class="fas fa-shopping-cart"></i></span>
            Online Orders
            <span class="badge-count" id="onlineTotal">0</span>
        </div>
        <div class="queue-columns">
            <div class="queue-column status-pending" id="onlinePending">
                <div class="column-title">
                    <i class="fas fa-clock"></i> Pending
                    <span class="count" id="onlinePendingCount">0</span>
                </div>
                <div id="onlinePendingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-preparing" id="onlinePreparing">
                <div class="column-title">
                    <i class="fas fa-spinner"></i> Preparing
                    <span class="count" id="onlinePreparingCount">0</span>
                </div>
                <div id="onlinePreparingList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
            <div class="queue-column status-deliver" id="onlineDeliver">
                <div class="column-title">
                    <i class="fas fa-truck"></i> Assign Rider
                    <span class="count" id="onlineDeliverCount">0</span>
                </div>
                <div id="onlineDeliverList">
                    <div class="empty-column">
                        <i class="fas fa-inbox"></i>
                        No orders
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Rider Modal -->
<div class="modal fade" id="assignRiderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Assign Delivery Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assign_sale_id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Rider</label>
                    <select id="rider_select" class="form-select">
                        <option value="">-- Select Rider --</option>
                    </select>
                </div>
                <div class="text-muted" style="font-size:12px;">
                    <i class="fas fa-info-circle"></i> 
                    Only delivery riders will appear in the list.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAssignRider()">
                    <i class="fas fa-check me-1"></i> Assign Rider
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade order-detail-modal" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     CONFIRMATION MODAL
     ============================================================ -->
<div class="confirm-modal-overlay" id="confirmModal">
    <div class="confirm-modal">
        <div class="confirm-icon question" id="confirmIcon">
            <i class="fas fa-question-circle"></i>
        </div>
        <h3 class="confirm-title" id="confirmTitle">Confirm Action</h3>
        <p class="confirm-subtitle" id="confirmSubtitle">Are you sure you want to proceed?</p>
        <div class="confirm-order-id" id="confirmOrderId">#0000</div>
        <p class="confirm-message" id="confirmMessage">This action cannot be undone.</p>
        <div class="confirm-actions">
            <button class="btn btn-cancel" onclick="closeConfirmModal(false)">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button class="btn btn-confirm" id="confirmBtn" onclick="confirmAction()">
                <i class="fas fa-check me-1"></i> Confirm
            </button>
        </div>
    </div>
</div>

<!-- ============================================================
     FEEDBACK MODAL
     ============================================================ -->
<div class="feedback-modal-overlay" id="feedbackModal">
    <div class="feedback-modal">
        <div class="confetti-container" id="confettiContainer">
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
            <div class="confetti-piece"></div>
        </div>
        
        <div class="icon-wrapper" id="feedbackIconWrapper">
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="icon" id="feedbackIcon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline class="check-icon" points="20 6 9 17 4 12" stroke-dasharray="60" stroke-dashoffset="60"/>
                </svg>
            </div>
        </div>
        
        <h3 class="modal-title" id="feedbackTitle">Success!</h3>
        <p class="modal-subtitle" id="feedbackSubtitle">Order updated successfully</p>
        <div class="modal-order-id" id="feedbackOrderId">#0000</div>
        <p class="modal-message" id="feedbackMessage">The order has been processed.</p>
        
        <div class="modal-actions">
            <button class="btn btn-primary" onclick="closeFeedbackModal()" id="feedbackBtn">
                <i class="fas fa-check me-1"></i> Continue
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let refreshInterval = null;
    let isRefreshing = false;
    let feedbackTimeout = null;
    let pendingAction = null;
    let pendingSaleId = null;

    // ============================================================
    // CONFIRMATION MODAL FUNCTIONS
    // ============================================================
    function showConfirmModal(title, subtitle, orderId, message, action, actionType) {
        const modal = document.getElementById('confirmModal');
        const icon = document.getElementById('confirmIcon');
        const titleEl = document.getElementById('confirmTitle');
        const subtitleEl = document.getElementById('confirmSubtitle');
        const orderIdEl = document.getElementById('confirmOrderId');
        const msgEl = document.getElementById('confirmMessage');
        const btn = document.getElementById('confirmBtn');
        
        // Reset icon
        icon.className = 'confirm-icon';
        if (actionType === 'danger' || actionType === 'cancel') {
            icon.classList.add('warning');
            icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
        } else {
            icon.classList.add('question');
            icon.innerHTML = '<i class="fas fa-question-circle"></i>';
        }
        
        // Set text
        titleEl.textContent = title;
        subtitleEl.textContent = subtitle || '';
        orderIdEl.textContent = orderId ? '#' + orderId : '';
        orderIdEl.style.display = orderId ? 'block' : 'none';
        msgEl.textContent = message || '';
        
        // Style button
        btn.className = 'btn btn-confirm';
        if (actionType === 'success' || actionType === 'accept') {
            btn.classList.add('success');
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Yes, Accept';
        } else if (actionType === 'danger' || actionType === 'cancel') {
            btn.classList.add('danger');
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> Yes, Cancel';
        } else if (actionType === 'ready') {
            btn.classList.add('success');
            btn.innerHTML = '<i class="fas fa-utensils me-1"></i> Yes, Mark Ready';
        } else if (actionType === 'complete') {
            btn.classList.add('success');
            btn.innerHTML = '<i class="fas fa-check-double me-1"></i> Yes, Complete';
        } else {
            btn.classList.add('success');
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Confirm';
        }
        
        // Store pending action
        pendingAction = action;
        pendingSaleId = orderId;
        
        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmModal(confirmed) {
        const modal = document.getElementById('confirmModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        if (confirmed && pendingAction) {
            pendingAction();
        }
        
        pendingAction = null;
        pendingSaleId = null;
    }

    function confirmAction() {
        closeConfirmModal(true);
    }

    // Close on outside click
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmModal(false);
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmModal(false);
        }
    });

    // ============================================================
    // FEEDBACK MODAL FUNCTIONS
    // ============================================================
    function showFeedback(type, title, subtitle, orderId, message) {
        const modal = document.getElementById('feedbackModal');
        const iconWrapper = document.getElementById('feedbackIconWrapper');
        const icon = document.getElementById('feedbackIcon');
        const titleEl = document.getElementById('feedbackTitle');
        const subtitleEl = document.getElementById('feedbackSubtitle');
        const orderIdEl = document.getElementById('feedbackOrderId');
        const msgEl = document.getElementById('feedbackMessage');
        const btn = document.getElementById('feedbackBtn');
        const confetti = document.getElementById('confettiContainer');
        
        iconWrapper.className = 'icon-wrapper';
        titleEl.className = 'modal-title';
        
        const configs = {
            success: {
                wrapperClass: 'success',
                iconHtml: `<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline class="check-icon" points="20 6 9 17 4 12" stroke-dasharray="60" stroke-dashoffset="60"/>
                </svg>`,
                titleClass: 'success',
                btnClass: 'btn-success',
                btnText: '<i class="fas fa-check me-1"></i> Continue',
                showConfetti: true
            },
            warning: {
                wrapperClass: 'warning',
                iconHtml: `<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v4M12 17h.01" stroke-width="2"/>
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                </svg>`,
                titleClass: 'warning',
                btnClass: 'btn-primary',
                btnText: '<i class="fas fa-check me-1"></i> Got it',
                showConfetti: false
            },
            error: {
                wrapperClass: 'error',
                iconHtml: `<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>`,
                titleClass: 'error',
                btnClass: 'btn-danger',
                btnText: '<i class="fas fa-times me-1"></i> Close',
                showConfetti: false
            },
            info: {
                wrapperClass: 'info',
                iconHtml: `<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>`,
                titleClass: 'info',
                btnClass: 'btn-primary',
                btnText: '<i class="fas fa-check me-1"></i> OK',
                showConfetti: false
            }
        };
        
        const config = configs[type] || configs.info;
        
        iconWrapper.classList.add(config.wrapperClass);
        icon.innerHTML = config.iconHtml;
        titleEl.classList.add(config.titleClass);
        titleEl.textContent = title;
        subtitleEl.textContent = subtitle || '';
        orderIdEl.textContent = orderId ? '#' + orderId : '';
        orderIdEl.style.display = orderId ? 'block' : 'none';
        msgEl.textContent = message || '';
        btn.className = 'btn ' + config.btnClass;
        btn.innerHTML = config.btnText;
        
        if (config.showConfetti) {
            confetti.style.display = 'block';
            confetti.querySelectorAll('.confetti-piece').forEach(piece => {
                piece.style.animation = 'none';
                setTimeout(() => {
                    piece.style.animation = '';
                }, 10);
            });
        } else {
            confetti.style.display = 'none';
        }
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        if (type === 'success') {
            if (feedbackTimeout) clearTimeout(feedbackTimeout);
            feedbackTimeout = setTimeout(() => {
                closeFeedbackModal();
            }, 4000);
        }
    }

    function closeFeedbackModal() {
        const modal = document.getElementById('feedbackModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        if (feedbackTimeout) {
            clearTimeout(feedbackTimeout);
            feedbackTimeout = null;
        }
    }

    document.getElementById('feedbackModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFeedbackModal();
        }
    });

    // ============================================================
    // QUEUE FUNCTIONS
    // ============================================================
    function loadQueue() {
        if (isRefreshing) return;
        isRefreshing = true;
        
        fetch('/barista/queue-data')
            .then(response => response.json())
            .then(data => {
                renderQueue(data);
                document.getElementById('lastUpdate').textContent = 'Now';
                isRefreshing = false;
            })
            .catch(error => {
                console.error('Error loading queue:', error);
                isRefreshing = false;
            });
    }

    function renderQueue(data) {
        renderColumn('inStorePending', data.in_store.pending);
        renderColumn('inStorePreparing', data.in_store.preparing);
        renderColumn('inStoreServe', data.in_store.serve);
        renderColumn('onlinePending', data.online.pending);
        renderColumn('onlinePreparing', data.online.preparing);
        renderColumn('onlineDeliver', data.online.deliver);
        
        const inStoreTotal = data.in_store.pending.length + data.in_store.preparing.length + data.in_store.serve.length;
        const onlineTotal = data.online.pending.length + data.online.preparing.length + data.online.deliver.length;
        const pendingTotal = data.in_store.pending.length + data.online.pending.length;
        const preparingTotal = data.in_store.preparing.length + data.online.preparing.length;
        const readyTotal = data.in_store.serve.length + data.online.deliver.length;
        
        document.getElementById('inStorePendingCount').textContent = data.in_store.pending.length;
        document.getElementById('inStorePreparingCount').textContent = data.in_store.preparing.length;
        document.getElementById('inStoreServeCount').textContent = data.in_store.serve.length;
        document.getElementById('inStoreTotal').textContent = inStoreTotal;
        document.getElementById('inStoreTotalStat').textContent = inStoreTotal;
            
        document.getElementById('onlinePendingCount').textContent = data.online.pending.length;
        document.getElementById('onlinePreparingCount').textContent = data.online.preparing.length;
        document.getElementById('onlineDeliverCount').textContent = data.online.deliver.length;
        document.getElementById('onlineTotal').textContent = onlineTotal;
        document.getElementById('onlineTotalStat').textContent = onlineTotal;
        
        document.getElementById('pendingTotalStat').textContent = pendingTotal;
        document.getElementById('preparingTotalStat').textContent = preparingTotal;
        document.getElementById('readyTotalStat').textContent = readyTotal;
    }

    function renderColumn(containerId, orders) {
        const container = document.getElementById(containerId + 'List');
        if (!container) return;
        
        if (orders.length === 0) {
            container.innerHTML = `
                <div class="empty-column">
                    <i class="fas fa-inbox"></i>
                    No orders
                </div>
            `;
            return;
        }
        
        let html = '';
        orders.forEach(order => {
            const status = order.status || 'pending';
            const isOnline = order.type === 'online';
            const typeBadge = isOnline ? 'online' : 'in-store';
            const typeLabel = isOnline ? 'Online' : 'In-Store';
            
            html += `
                <div class="order-card-small" onclick="viewOrder(${order.sale_id})">
                    <span class="order-type-badge ${typeBadge}">${typeLabel}</span>
                    <div class="order-id">#${order.sale_id}</div>
                    <div class="order-customer">
                        <i class="fas fa-user"></i> ${order.customer_name || 'Walk-in'}
                    </div>
                    <div class="order-items">
                        <i class="fas fa-list"></i> ${order.item_count} item(s)
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="order-total">₱${parseFloat(order.total).toFixed(2)}</span>
                        <span class="order-time">${order.time_ago || 'Just now'}</span>
                    </div>
                    <div class="order-actions" onclick="event.stopPropagation();">
                        ${getActionButtons(order.sale_id, status, isOnline)}
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function getActionButtons(saleId, status, isOnline) {
        let buttons = '';
        
        if (status === 'pending') {
            buttons += `
                <button class="btn-accept" onclick="requestAccept(${saleId})">
                    <i class="fas fa-check"></i> Accept
                </button>
            `;
        }
        
        if (status === 'preparing') {
            buttons += `
                <button class="btn-ready" onclick="requestReady(${saleId})">
                    <i class="fas fa-utensils"></i> Ready
                </button>
            `;
        }
        
        if (status === 'ready') {
            if (isOnline) {
                buttons += `
                    <button class="btn-assign-rider" onclick="assignRider(${saleId})">
                        <i class="fas fa-truck"></i> Assign Rider
                    </button>
                `;
            } else {
                buttons += `
                    <button class="btn-complete-order" onclick="requestComplete(${saleId})">
                        <i class="fas fa-check-double"></i> Complete
                    </button>
                `;
            }
        }
        
        if (status !== 'completed' && status !== 'cancelled') {
            buttons += `
                <button class="btn-cancel" onclick="requestCancel(${saleId})">
                    <i class="fas fa-times"></i>
                </button>
            `;
        }
        
        buttons += `
            <button class="btn-view" onclick="viewOrder(${saleId})">
                <i class="fas fa-eye"></i>
            </button>
        `;
        
        return buttons;
    }

    // ============================================================
    // ORDER ACTIONS WITH CONFIRMATION
    // ============================================================
    function requestAccept(saleId) {
        showConfirmModal(
            'Accept Order',
            'This order will be moved to preparing.',
            saleId,
            'The customer will be notified that their order is now being prepared.',
            function() { executeAccept(saleId); },
            'accept'
        );
    }

    function executeAccept(saleId) {
        const btn = document.querySelector(`[onclick*="requestAccept(${saleId})"]`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        
        fetch(`/barista/orders/${saleId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Accept';
            }
            
            if (data.success) {
                showFeedback(
                    'success',
                    '✅ Order Accepted!',
                    'The order has been accepted and is now being prepared.',
                    saleId,
                    'The customer has been notified that their order is now being prepared.'
                );
                loadQueue();
            } else {
                showFeedback(
                    'error',
                    '❌ Failed to Accept',
                    'There was an error accepting this order.',
                    saleId,
                    data.message || 'Please try again.'
                );
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Accept';
            }
            showFeedback(
                'error',
                '❌ Error',
                'An error occurred while accepting the order.',
                saleId,
                'Please check your connection and try again.'
            );
        });
    }

    function requestReady(saleId) {
        showConfirmModal(
            'Mark as Ready',
            'This order will be marked as ready for pickup/delivery.',
            saleId,
            'The customer will be notified that their order is ready.',
            function() { executeReady(saleId); },
            'ready'
        );
    }

    function executeReady(saleId) {
        const btn = document.querySelector(`[onclick*="requestReady(${saleId})"]`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        
        fetch(`/barista/orders/${saleId}/ready`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-utensils"></i> Ready';
            }
            
            if (data.success) {
                showFeedback(
                    'success',
                    '✅ Order Ready!',
                    'The order has been marked as ready.',
                    saleId,
                    'The customer has been notified that their order is ready.'
                );
                loadQueue();
            } else {
                showFeedback(
                    'error',
                    '❌ Failed to Mark Ready',
                    'There was an error marking this order as ready.',
                    saleId,
                    data.message || 'Please try again.'
                );
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-utensils"></i> Ready';
            }
            showFeedback(
                'error',
                '❌ Error',
                'An error occurred while marking the order as ready.',
                saleId,
                'Please check your connection and try again.'
            );
        });
    }

    function requestComplete(saleId) {
        showConfirmModal(
            'Complete Order',
            'This order will be marked as completed.',
            saleId,
            'The customer will be notified that their order is complete.',
            function() { executeComplete(saleId); },
            'complete'
        );
    }

    function executeComplete(saleId) {
        const btn = document.querySelector(`[onclick*="requestComplete(${saleId})"]`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        
        fetch(`/barista/orders/${saleId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double"></i> Complete';
            }
            
            if (data.success) {
                showFeedback(
                    'success',
                    '🎉 Order Completed!',
                    'The order has been completed successfully.',
                    saleId,
                    'The customer has been notified that their order is complete.'
                );
                loadQueue();
            } else {
                showFeedback(
                    'error',
                    '❌ Failed to Complete',
                    'There was an error completing this order.',
                    saleId,
                    data.message || 'Please try again.'
                );
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double"></i> Complete';
            }
            showFeedback(
                'error',
                '❌ Error',
                'An error occurred while completing the order.',
                saleId,
                'Please check your connection and try again.'
            );
        });
    }

    function requestCancel(saleId) {
        showConfirmModal(
            'Cancel Order',
            'This order will be cancelled.',
            saleId,
            'The customer will be notified of the cancellation.',
            function() { executeCancel(saleId); },
            'cancel'
        );
    }

    function executeCancel(saleId) {
        const btn = document.querySelector(`[onclick*="requestCancel(${saleId})"]`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        
        fetch(`/barista/orders/${saleId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-times"></i>';
            }
            
            if (data.success) {
                showFeedback(
                    'warning',
                    '⚠️ Order Cancelled',
                    'The order has been cancelled.',
                    saleId,
                    'The customer has been notified of the cancellation.'
                );
                loadQueue();
            } else {
                showFeedback(
                    'error',
                    '❌ Failed to Cancel',
                    'There was an error cancelling this order.',
                    saleId,
                    data.message || 'Please try again.'
                );
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-times"></i>';
            }
            showFeedback(
                'error',
                '❌ Error',
                'An error occurred while cancelling the order.',
                saleId,
                'Please check your connection and try again.'
            );
        });
    }

    // ============================================================
    // OTHER FUNCTIONS
    // ============================================================
    function assignRider(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('assignRiderModal'));
        document.getElementById('assign_sale_id').value = saleId;
        
        const select = document.getElementById('rider_select');
        select.innerHTML = '<option value="">Loading riders...</option>';
        select.disabled = true;
        
        fetch('/delivery/riders', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            select.innerHTML = '<option value="">-- Select Rider --</option>';
            if (data.success && data.riders && data.riders.length > 0) {
                data.riders.forEach(rider => {
                    const option = document.createElement('option');
                    option.value = rider.id;
                    option.textContent = rider.name + ' (' + rider.email + ')';
                    select.appendChild(option);
                });
                select.disabled = false;
            } else {
                select.innerHTML = '<option value="">No riders available</option>';
                select.disabled = true;
            }
        })
        .catch(() => {
            select.innerHTML = '<option value="">Error loading riders</option>';
            select.disabled = true;
        });
        
        modal.show();
    }

    function confirmAssignRider() {
        const saleId = document.getElementById('assign_sale_id').value;
        const riderId = document.getElementById('rider_select').value;
        
        if (!riderId) {
            alert('Please select a rider.');
            return;
        }
        
        const btn = document.querySelector('#assignRiderModal .btn-primary');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Assigning...';
        
        fetch('/delivery/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sale_id: parseInt(saleId),
                delivery_person_id: parseInt(riderId)
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Assign Rider';
            
            if (data.success) {
                showFeedback(
                    'success',
                    '✅ Rider Assigned!',
                    'A delivery rider has been assigned to this order.',
                    saleId,
                    'Rider: ' + (document.getElementById('rider_select').selectedOptions[0]?.text || 'Unknown')
                );
                bootstrap.Modal.getInstance(document.getElementById('assignRiderModal')).hide();
                loadQueue();
            } else {
                showFeedback(
                    'error',
                    '❌ Failed to Assign Rider',
                    'There was an error assigning a rider.',
                    saleId,
                    data.message || 'Please try again.'
                );
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Assign Rider';
            showFeedback(
                'error',
                '❌ Error',
                'An error occurred while assigning a rider.',
                saleId,
                'Please check your connection and try again.'
            );
        });
    }

    function viewOrder(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
        const content = document.getElementById('orderDetailContent');
        
        content.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <p class="mt-2">Loading order details...</p>
            </div>
        `;
        
        modal.show();
        
        fetch(`/barista/orders/${saleId}`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        Error loading order details. Please try again.
                    </div>
                `;
            });
    }

    function refreshQueue() {
        loadQueue();
        showFeedback(
            'info',
            '🔄 Refreshing...',
            'The queue is being refreshed.',
            null,
            'Please wait a moment.'
        );
        setTimeout(closeFeedbackModal, 1500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadQueue();
        refreshInterval = setInterval(loadQueue, 15000);
    });
</script>
@endpush
@endsection