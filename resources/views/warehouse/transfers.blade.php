@extends('layouts.app')

@section('page-title', 'Transfer History')

@section('content')
<style>
    .transfer-card {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        border: 1px solid #e8e8e8;
        margin-bottom: 12px;
        transition: 0.2s;
        overflow: hidden;
    }
    .transfer-card:hover {
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
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-badge.received {
        background: #d4edda;
        color: #155724;
    }
    .status-badge.cancelled {
        background: #f8d7da;
        color: #721c24;
    }
    .pending-count {
        background: #ffc107;
        color: #000;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .view-only-badge {
        background: #e9ecef;
        color: #6c757d;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .transfer-item-name {
        font-weight: 600;
        font-size: 15px;
        word-break: break-word;
    }
    .transfer-detail {
        font-size: 13px;
        color: #666;
        word-break: break-word;
    }
    .transfer-detail i {
        width: 16px;
        margin-right: 4px;
    }
    .transfer-time {
        font-size: 12px;
        color: #999;
    }
    .transfer-notes {
        font-size: 12px;
        color: #856404;
        background: #fff8e1;
        padding: 2px 10px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 4px;
        word-break: break-word;
    }
    
    /* Fix: Remove extra padding that causes overlap */
    .content-wrapper {
        padding: 0;
        max-width: 100%;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .transfer-card .row > div {
            margin-bottom: 8px;
        }
        .transfer-card .text-end {
            text-align: left !important;
        }
        .transfer-card .col-md-4.text-end {
            text-align: left !important;
        }
    }
</style>

<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Transfer History</h2>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @php
                $pendingCount = $transfers->where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <span class="pending-count">
                    <i class="fas fa-clock me-1"></i> {{ $pendingCount }} pending
                </span>
            @endif
            <span class="view-only-badge"><i class="fas fa-eye me-1"></i> View Only</span>
            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($transfers->count() > 0)
        @foreach($transfers as $transfer)
            <div class="transfer-card">
                <div class="row align-items-center">
                    <div class="col-md-3 col-sm-12">
                        <div class="transfer-item-name">{{ $transfer->item->name ?? 'Unknown Item' }}</div>
                        <div class="transfer-detail">
                            <i class="fas fa-box"></i> Qty: {{ number_format($transfer->quantity, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="transfer-detail">
                            <i class="fas fa-store"></i> {{ str_replace('☕ Brew & Bean Co. - ', '', $transfer->toBranch->name ?? 'Unknown') }}
                        </div>
                        <div class="transfer-time">
                            <i class="fas fa-clock"></i> {{ $transfer->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <span class="status-badge {{ $transfer->status }}">
                            @if($transfer->status === 'pending')
                                ⏳ Pending
                            @elseif($transfer->status === 'received')
                                ✅ Received
                            @else
                                {{ ucfirst($transfer->status) }}
                            @endif
                        </span>
                        @if($transfer->status === 'received' && $transfer->received_at)
                            <div class="transfer-time mt-1">
                                <i class="fas fa-check-circle text-success"></i> {{ $transfer->received_at->format('M d, Y h:i A') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 col-sm-12">
                        @if($transfer->status === 'pending')
                            <span class="text-muted" style="font-size: 13px;">
                                <i class="fas fa-clock me-1"></i> Waiting for staff to receive
                            </span>
                        @endif
                        @if($transfer->notes)
                            <div class="transfer-notes">
                                <i class="fas fa-comment me-1"></i> {{ $transfer->notes }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-3 text-muted small">
            <i class="fas fa-info-circle me-1"></i>
            Showing <strong>{{ $transfers->count() }}</strong> transfer(s)
            @if(auth()->user()->isStaff())
                <span class="ms-2">| Your branch: <strong>{{ str_replace('☕ Brew & Bean Co. - ', '', auth()->user()->branch->name ?? 'N/A') }}</strong></span>
            @endif
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
            <h5>No transfers yet</h5>
            <p class="text-muted">Transfer items from warehouse to branches.</p>
        </div>
    @endif
</div>