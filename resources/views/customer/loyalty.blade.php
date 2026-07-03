@extends('layouts.customer')

@section('page-title', 'Loyalty Points')

@section('content')
<style>
    .points-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e8e8e8;
        text-align: center;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    .points-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6F4E37, #ffd700, #6F4E37);
    }
    .points-number {
        font-size: 56px;
        font-weight: 700;
        color: #6F4E37;
        line-height: 1;
    }
    .points-label {
        font-size: 16px;
        color: #999;
        margin-top: 4px;
    }
    .tier-badge {
        display: inline-block;
        padding: 4px 20px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 18px;
        margin-top: 8px;
    }
    
    .tier-card {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e8e8e8;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.2s;
    }
    .tier-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .tier-card.active {
        border-color: #6F4E37;
        background: #f8f6f4;
    }
    .tier-card .tier-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .tier-card .tier-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        color: white;
    }
    .tier-card .tier-name {
        font-weight: 600;
        font-size: 16px;
    }
    .tier-card .tier-details {
        font-size: 13px;
        color: #999;
    }
    .tier-card .tier-status {
        font-size: 13px;
        font-weight: 600;
    }
    .tier-card .tier-status.locked { color: #dc3545; }
    .tier-card .tier-status.unlocked { color: #28a745; }
    .tier-card .tier-status.current { color: #6F4E37; }
    
    .progress-container {
        background: #f8f9fa;
        border-radius: 8px;
        height: 8px;
        overflow: hidden;
        margin: 10px 0;
    }
    .progress-container .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #6F4E37, #ffd700);
        border-radius: 8px;
        transition: width 0.5s ease;
    }
    
    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    .history-item:last-child { border-bottom: none; }
    .history-item .points-earned {
        font-weight: 600;
        color: #28a745;
    }
    .history-item .points-used {
        font-weight: 600;
        color: #dc3545;
    }
    
    @media (max-width: 576px) {
        .points-number { font-size: 40px; }
        .tier-card { flex-direction: column; text-align: center; }
        .tier-card .tier-info { flex-direction: column; }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-star me-2"></i>Loyalty Points</h2>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Points Summary -->
    <div class="points-card">
        <div class="points-number">{{ number_format($currentPoints) }}</div>
        <div class="points-label">Your Loyalty Points</div>
        <div class="tier-badge" style="background: {{ $currentTier['color'] }}20; color: {{ $currentTier['color'] }};">
            {{ $currentTier['label'] }} Tier
            @if($currentTier['discount'] > 0)
                ({{ $currentTier['discount'] }}% Discount)
            @endif
        </div>
        
        @if($nextTier)
            <div class="mt-3">
                <div class="d-flex justify-content-between" style="font-size: 13px;">
                    <span class="text-muted">Current: {{ number_format($currentPoints) }} pts</span>
                    <span class="text-muted">{{ $nextTier['points'] - $currentPoints }} pts to {{ $nextTier['label'] }}</span>
                </div>
                <div class="progress-container">
                    @php
                        $progress = min(($currentPoints / $nextTier['points']) * 100, 100);
                    @endphp
                    <div class="progress-bar" style="width: {{ $progress }}%;"></div>
                </div>
                <div class="text-muted" style="font-size: 12px;">
                    <i class="fas fa-info-circle me-1"></i>
                    Next tier: <strong>{{ $nextTier['label'] }}</strong> - {{ $nextTier['discount'] }}% discount
                </div>
            </div>
        @else
            <div class="mt-3 text-success">
                <i class="fas fa-crown me-1"></i>
                You're at the highest tier! Amazing!
            </div>
        @endif
    </div>

    <!-- How to Earn -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>How to Earn Points</h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="p-2 border rounded text-center h-100">
                        <i class="fas fa-shopping-bag" style="font-size: 24px; color: #6F4E37;"></i>
                        <div class="fw-bold mt-1">1 pt per ₱100</div>
                        <div class="text-muted" style="font-size: 12px;">Earn points on every purchase</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 border rounded text-center h-100">
                        <i class="fas fa-gift" style="font-size: 24px; color: #ffc107;"></i>
                        <div class="fw-bold mt-1">Birthday Bonus</div>
                        <div class="text-muted" style="font-size: 12px;">Double points on your birthday</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 border rounded text-center h-100">
                        <i class="fas fa-trophy" style="font-size: 24px; color: #fd7e14;"></i>
                        <div class="fw-bold mt-1">Special Promos</div>
                        <div class="text-muted" style="font-size: 12px;">Bonus points on selected items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Tiers -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Discount Tiers</h6>
        </div>
        <div class="card-body pt-0">
            @foreach($tiers as $tier)
                @php
                    $isActive = $currentPoints >= $tier['points'];
                    $isCurrent = $currentTier['points'] == $tier['points'];
                    $statusClass = $isCurrent ? 'current' : ($isActive ? 'unlocked' : 'locked');
                    $statusText = $isCurrent ? 'Current' : ($isActive ? '✓ Unlocked' : '🔒 Locked');
                @endphp
                <div class="tier-card {{ $isCurrent ? 'active' : '' }}">
                    <div class="tier-info">
                        <div class="tier-icon" style="background: {{ $tier['color'] }};">
                            {{ substr($tier['label'], 0, 1) }}
                        </div>
                        <div>
                            <div class="tier-name">{{ $tier['label'] }}</div>
                            <div class="tier-details">
                                {{ $tier['discount'] }}% off
                                @if($tier['points'] > 0)
                                    • {{ number_format($tier['points']) }} points required
                                @else
                                    • Automatic
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tier-status {{ $statusClass }}">
                        @if($isCurrent)
                            <span class="badge bg-primary">Current</span>
                        @elseif($isActive)
                            <span class="badge bg-success">Unlocked</span>
                        @else
                            <span class="badge bg-secondary">Locked</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Points History -->
    @if($pointsHistory->count() > 0)
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="mb-0"><i class="fas fa-history me-2"></i>Points History</h6>
        </div>
        <div class="card-body p-0">
            <div style="padding: 0 16px;">
                @foreach($pointsHistory as $sale)
                    <div class="history-item">
                        <div>
                            <div>Order #{{ $sale->id }}</div>
                            <div class="text-muted" style="font-size: 11px;">
                                {{ $sale->sale_date->format('M d, Y h:i A') }}
                                @if($sale->discount_rate > 0)
                                    • {{ $sale->discount_rate }}% discount applied
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="points-earned">+{{ $sale->points_earned }} pts</div>
                            <div style="font-size: 11px; color: #999;">₱{{ number_format($sale->total_amount, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Info Note -->
    <div class="mt-3 text-muted" style="font-size: 12px; text-align: center;">
        <i class="fas fa-info-circle me-1"></i>
        Points are earned on every purchase. 1 point = ₱1 value.
        Discounts are automatically applied at checkout based on your tier.
    </div>
</div>
@endsection