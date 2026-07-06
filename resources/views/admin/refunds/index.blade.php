@extends('layouts.app')

@section('page-title', 'Refund Requests')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 18px 22px;
        border: 1px solid #e8e8e8;
        transition: all 0.2s;
        height: 100%;
        text-align: center;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #6F4E37;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
    }
    .stat-card .stat-label {
        font-size: 13px;
        color: #999;
        font-weight: 500;
    }
    .stat-card.pending .stat-number { color: #ffc107; }
    .stat-card.approved .stat-number { color: #28a745; }
    .stat-card.denied .stat-number { color: #dc3545; }
    .stat-card.completed .stat-number { color: #17a2b8; }
    
    .refund-card {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e8e8e8;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .refund-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .status-badge {
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-badge.pending { background: #fff3cd; color: #856404; }
    .status-badge.approved { background: #d4edda; color: #155724; }
    .status-badge.denied { background: #f8d7da; color: #721c24; }
    .status-badge.completed { background: #cce5ff; color: #004085; }
    
    .btn-approve {
        background: #28a745;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-approve:hover {
        background: #218838;
        color: white;
    }
    .btn-deny {
        background: #dc3545;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-deny:hover {
        background: #c82333;
        color: white;
    }
    .btn-complete {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-complete:hover {
        background: #138496;
        color: white;
    }
    
    /* ===== REFUND DETAIL MODAL ===== */
    .refund-detail-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 4px 0;
    }
    
    .detail-section {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 12px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0ebe6;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-title i {
        color: #6F4E37;
        font-size: 16px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
        align-items: center;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-row .label {
        color: #888;
        font-weight: 500;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        min-width: 110px;
    }
    
    .detail-row .value {
        font-weight: 600;
        color: #333;
        text-align: right;
        word-break: break-word;
        max-width: 60%;
    }
    
    .detail-row .value.highlight {
        color: #6F4E37;
        font-size: 18px;
    }
    
    .refund-amount-display {
        font-size: 22px;
        font-weight: 700;
        color: #6F4E37;
    }
    
    .reason-box {
        background: #f8f6f4;
        border-radius: 8px;
        padding: 12px 16px;
        border-left: 4px solid #6F4E37;
    }
    
    .reason-box .reason-title {
        font-weight: 600;
        color: #6F4E37;
        font-size: 14px;
    }
    
    .reason-box .reason-desc {
        color: #555;
        font-size: 13px;
        margin-top: 3px;
        line-height: 1.4;
    }
    
    .reason-box .reason-date {
        color: #999;
        font-size: 11px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    
    .item-row:last-child {
        border-bottom: none;
    }
    
    .item-row .item-name {
        flex: 1;
        font-weight: 500;
        color: #333;
    }
    
    .item-row .item-qty {
        width: 50px;
        text-align: center;
        color: #888;
        font-size: 12px;
    }
    
    .item-row .item-price {
        width: 85px;
        text-align: right;
        font-weight: 500;
        color: #555;
    }
    
    .total-row {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 2px solid #e8e8e8;
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        font-size: 16px;
        color: #6F4E37;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 4px;
    }
    
    .action-buttons .btn {
        padding: 8px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        font-family: inherit;
    }
    
    .action-buttons .btn:active {
        transform: scale(0.95);
    }
    
    .action-buttons .btn-approve {
        background: #28a745;
        color: white;
    }
    .action-buttons .btn-approve:hover {
        background: #218838;
    }
    
    .action-buttons .btn-deny {
        background: #dc3545;
        color: white;
    }
    .action-buttons .btn-deny:hover {
        background: #c82333;
    }
    
    .action-buttons .btn-complete {
        background: #17a2b8;
        color: white;
    }
    .action-buttons .btn-complete:hover {
        background: #138496;
    }
    
    .action-buttons .btn-close {
        background: #6c757d;
        color: white;
    }
    .action-buttons .btn-close:hover {
        background: #5a6268;
    }
    
    .modal-scrollable {
        max-height: 75vh;
        overflow-y: auto;
        padding-right: 4px;
    }
    
    .modal-scrollable::-webkit-scrollbar {
        width: 4px;
    }
    .modal-scrollable::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .modal-scrollable::-webkit-scrollbar-thumb {
        background: #6F4E37;
        border-radius: 10px;
    }
    
    @media (max-width: 576px) {
        .detail-section {
            padding: 12px 14px;
        }
        .detail-row {
            font-size: 12px;
            flex-wrap: wrap;
        }
        .detail-row .label {
            min-width: 80px;
            font-size: 11px;
        }
        .detail-row .value {
            max-width: 55%;
            font-size: 12px;
        }
        .refund-amount-display {
            font-size: 18px;
        }
        .action-buttons .btn {
            flex: 1;
            justify-content: center;
            font-size: 12px;
            padding: 6px 12px;
        }
        .item-row {
            font-size: 12px;
        }
        .item-row .item-price {
            width: 70px;
        }
        .total-row {
            font-size: 14px;
        }
        .section-title {
            font-size: 13px;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-undo-alt me-2"></i>Refund Requests</h2>
        <span class="badge bg-warning">{{ $pendingCount }} Pending</span>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card pending">
                <div class="stat-number">{{ $pendingCount }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card approved">
                <div class="stat-number">{{ $approvedCount }}</div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card denied">
                <div class="stat-number">{{ $deniedCount }}</div>
                <div class="stat-label">Denied</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card completed">
                <div class="stat-number">{{ $completedCount }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Refund List -->
    @if($refunds->count() > 0)
        @foreach($refunds as $refund)
            <div class="refund-card">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div>
                            <strong>Order #{{ $refund->id }}</strong>
                            <br>
                            <small class="text-muted">{{ $refund->customer->name ?? 'Unknown' }}</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span class="status-badge {{ $refund->refund_status }}">
                            {{ ucfirst($refund->refund_status) }}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <span class="fw-bold" style="color:#6F4E37;">
                            ₱{{ number_format($refund->refund_amount ?? $refund->total_amount, 2) }}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">
                            {{ $refund->refund_requested_at ? \Carbon\Carbon::parse($refund->refund_requested_at)->diffForHumans() : 'N/A' }}
                        </small>
                    </div>
                    <div class="col-md-3 text-end">
                        @if($refund->refund_status === 'pending')
                            <button class="btn-approve" onclick="processRefund({{ $refund->id }}, 'approve')">
                                <i class="fas fa-check me-1"></i> Approve
                            </button>
                            <button class="btn-deny" onclick="processRefund({{ $refund->id }}, 'deny')">
                                <i class="fas fa-times me-1"></i> Deny
                            </button>
                        @endif
                        @if($refund->refund_status === 'approved')
                            <button class="btn-complete" onclick="processRefund({{ $refund->id }}, 'complete')">
                                <i class="fas fa-check-double me-1"></i> Complete
                            </button>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary" onclick="viewRefund({{ $refund->id }})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </div>
                </div>
                @if($refund->refund_reason)
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-comment me-1"></i> {{ $refund->refund_reason }}
                        @if($refund->refund_description)
                            - {{ $refund->refund_description }}
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        <div class="mt-3">
            {{ $refunds->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h5>No Refund Requests</h5>
            <p class="text-muted">All refund requests have been processed.</p>
        </div>
    @endif
</div>

<!-- ============================================================
     REFUND DETAIL MODAL - Nasa Index lang ito, hindi separate page
     ============================================================ -->
<div class="modal fade" id="refundDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="border-bottom: 2px solid #6F4E37; background: #faf8f6; padding: 16px 24px;">
                <h5 class="modal-title" style="font-weight: 700; color: #333;">
                    <i class="fas fa-undo-alt me-2" style="color: #6F4E37;"></i> Refund Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="refundDetailContent" style="padding: 20px 24px;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading refund details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function processRefund(id, action) {
        const actions = {
            'approve': { 
                confirm: '✅ Approve this refund request?', 
                url: '/admin/refunds/' + id + '/approve' 
            },
            'deny': { 
                confirm: '❌ Deny this refund request?', 
                url: '/admin/refunds/' + id + '/deny' 
            },
            'complete': { 
                confirm: '📦 Mark this refund as completed?', 
                url: '/admin/refunds/' + id + '/complete' 
            }
        };
        
        const actionData = actions[action];
        if (!actionData) return;
        
        if (!confirm(actionData.confirm)) return;
        
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
        
        fetch(actionData.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert('Error: ' + error);
        });
    }

    function viewRefund(id) {
        const modal = new bootstrap.Modal(document.getElementById('refundDetailModal'));
        const content = document.getElementById('refundDetailContent');
        
        content.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading refund details...</p>
            </div>
        `;
        
        modal.show();
        
        fetch('/admin/refunds/' + id)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading refund details. Please try again.
                    </div>
                `;
            });
    }
</script>
@endpush
@endsection