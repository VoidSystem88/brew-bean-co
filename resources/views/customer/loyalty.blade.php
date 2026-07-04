@extends('layouts.customer')

@section('page-title', 'Loyalty Program')

@section('content')
<style>
    :root {
        --primary: #6F4E37;
        --primary-light: #8B6B4A;
        --primary-dark: #4A3228;
        --gold: #C9A96E;
        --bg: #F5EDE6;
        --card-shadow: 0 2px 12px rgba(74, 50, 40, 0.08);
        --radius: 12px;
    }
    
    .loyalty-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    /* Points Summary */
    .points-summary {
        background: white;
        border-radius: var(--radius);
        padding: 28px 30px;
        border: 1px solid #e0d6ce;
        text-align: center;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }
    .points-summary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--gold), var(--primary));
    }
    .points-number {
        font-size: 48px;
        font-weight: 700;
        color: var(--primary-dark);
        line-height: 1;
    }
    .points-label {
        font-size: 14px;
        color: #8a7a6a;
        margin-top: 4px;
        letter-spacing: 0.5px;
        font-weight: 500;
    }
    .points-tier {
        display: inline-block;
        padding: 4px 24px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        margin-top: 8px;
        letter-spacing: 0.3px;
    }
    .points-progress {
        margin-top: 16px;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
    .points-progress .progress-track {
        background: #ede8e2;
        border-radius: 4px;
        height: 6px;
        overflow: hidden;
    }
    .points-progress .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--gold));
        border-radius: 4px;
        transition: width 0.6s ease;
    }
    .points-progress .progress-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #999;
        margin-top: 4px;
    }
    
    /* Section Title */
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ede8e2;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title .icon {
        width: 24px;
        text-align: center;
        color: var(--primary);
        font-size: 15px;
    }
    .section-title .badge-count {
        background: var(--primary);
        color: white;
        font-size: 11px;
        padding: 1px 12px;
        border-radius: 12px;
        font-weight: 600;
        margin-left: auto;
    }
    .section-title .badge-locked {
        background: #b0a090;
        color: white;
        font-size: 11px;
        padding: 1px 12px;
        border-radius: 12px;
        font-weight: 600;
        margin-left: 6px;
    }
    
    /* Voucher Cards - Better Design */
    .voucher-card {
        background: white;
        border-radius: var(--radius);
        padding: 14px 18px;
        border: 1px solid #e0d6ce;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.25s ease;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .voucher-card:hover:not(.locked) {
        border-color: var(--primary);
        box-shadow: 0 4px 16px rgba(111, 78, 55, 0.12);
        transform: translateY(-2px);
    }
    .voucher-card.locked {
        opacity: 0.75;
        background: #faf8f6;
        border-style: dashed;
        border-color: #d5ccc4;
    }
    .voucher-card.locked .voucher-icon {
        opacity: 0.5;
        filter: grayscale(0.8);
    }
    .voucher-card .voucher-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }
    .voucher-card.locked .voucher-icon {
        background: #b0a090 !important;
    }
    .voucher-card .voucher-info {
        flex: 1;
        min-width: 0;
    }
    .voucher-card .voucher-info .voucher-name {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .voucher-card .voucher-info .voucher-details {
        font-size: 12px;
        color: #8a7a6a;
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 3px;
    }
    .voucher-card .voucher-info .voucher-details span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .voucher-card .voucher-info .voucher-details .highlight {
        color: var(--primary);
        font-weight: 600;
    }
    .voucher-card .lock-badge {
        background: #b0a090;
        color: white;
        font-size: 10px;
        padding: 1px 10px;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .voucher-card .voucher-status {
        flex-shrink: 0;
        min-width: 100px;
        text-align: right;
    }
    
    /* Buttons */
    .btn-use-voucher {
        background: var(--primary);
        color: white;
        border: none;
        padding: 6px 20px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        letter-spacing: 0.3px;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 2px 6px rgba(111, 78, 55, 0.2);
    }
    .btn-use-voucher:hover:not(:disabled) {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(111, 78, 55, 0.3);
        color: white;
    }
    .btn-use-voucher:disabled {
        background: #cbc0b6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .btn-use-voucher.used {
        background: #5a8a5a;
        pointer-events: none;
    }
    
    .voucher-status .points-needed {
        font-size: 12px;
        color: #8a7a6a;
        font-weight: 500;
    }
    .voucher-status .points-needed .num {
        color: var(--primary);
        font-weight: 700;
    }
    .voucher-status .points-needed .num.insufficient {
        color: #b04040;
        font-weight: 700;
    }
    
    /* Progress indicator for locked vouchers */
    .voucher-progress {
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .voucher-progress .progress-track-mini {
        flex: 1;
        max-width: 120px;
        height: 4px;
        background: #ede8e2;
        border-radius: 2px;
        overflow: hidden;
    }
    .voucher-progress .progress-track-mini .fill {
        height: 100%;
        background: var(--primary);
        border-radius: 2px;
        transition: width 0.6s ease;
    }
    .voucher-progress .progress-track-mini .fill.danger {
        background: #b04040;
    }
    .voucher-progress .progress-text {
        font-size: 11px;
        color: #8a7a6a;
        white-space: nowrap;
    }
    .voucher-progress .progress-text .need {
        color: #b04040;
        font-weight: 600;
    }
    .voucher-progress .progress-text .have {
        color: var(--primary);
        font-weight: 600;
    }
    
    /* Active Voucher Banner */
    .active-voucher-banner {
        background: #e8f0e8;
        border: 1px solid #bdd6bd;
        border-radius: var(--radius);
        padding: 12px 18px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .active-voucher-banner .voucher-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .active-voucher-banner .voucher-info .voucher-tag {
        background: #4a7a4a;
        color: white;
        padding: 2px 14px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .active-voucher-banner .voucher-info .voucher-text {
        font-size: 14px;
        color: #2d5a2d;
        font-weight: 500;
    }
    .active-voucher-banner .voucher-info .voucher-text small {
        font-weight: 400;
        font-size: 12px;
        color: #4a7a4a;
    }
    .active-voucher-banner .btn-remove-voucher {
        background: none;
        border: 1px solid #c8a0a0;
        color: #7a4040;
        font-size: 13px;
        cursor: pointer;
        padding: 4px 14px;
        border-radius: 4px;
        transition: 0.2s;
        font-weight: 500;
    }
    .active-voucher-banner .btn-remove-voucher:hover {
        background: #f5e8e8;
        border-color: #a07070;
    }
    
    /* How to Earn */
    .how-to-earn {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .how-to-earn .earn-item {
        background: white;
        border-radius: var(--radius);
        padding: 16px 14px;
        border: 1px solid #e0d6ce;
        text-align: center;
        transition: 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .how-to-earn .earn-item:hover {
        border-color: var(--primary);
        box-shadow: var(--card-shadow);
    }
    .how-to-earn .earn-item .earn-icon {
        font-size: 26px;
        color: var(--primary);
        margin-bottom: 6px;
    }
    .how-to-earn .earn-item .earn-title {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }
    .how-to-earn .earn-item .earn-desc {
        font-size: 12px;
        color: #8a7a6a;
        margin-top: 2px;
    }
    
    /* History */
    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #ede8e2;
        font-size: 13px;
    }
    .history-item:last-child { border-bottom: none; }
    .history-item .history-left .history-order {
        font-weight: 500;
        color: #333;
    }
    .history-item .history-left .history-date {
        font-size: 11px;
        color: #8a7a6a;
    }
    .history-item .history-right {
        text-align: right;
    }
    .history-item .history-right .points-earned {
        font-weight: 600;
        color: #4a8a4a;
    }
    .history-item .history-right .points-used {
        font-weight: 600;
        color: #b04040;
    }
    .history-item .history-right .points-net {
        font-weight: 600;
        color: var(--primary);
    }
    .history-item .history-right .history-amount {
        font-size: 11px;
        color: #8a7a6a;
    }
    
    .history-empty {
        text-align: center;
        padding: 30px 20px;
        color: #8a7a6a;
        font-size: 14px;
        background: #faf8f6;
        border-radius: var(--radius);
    }
    .history-empty .empty-icon {
        font-size: 36px;
        color: #d5ccc4;
        margin-bottom: 8px;
        display: block;
    }
    
    .alert-info {
        background: #e8eef5;
        border: 1px solid #c8d8e8;
        border-radius: var(--radius);
        padding: 10px 16px;
        color: #3a5a7a;
        font-size: 13px;
        margin-bottom: 16px;
    }
    .alert-info i {
        margin-right: 8px;
    }
    .alert-info a {
        color: #3a5a7a;
        font-weight: 600;
        text-decoration: underline;
        cursor: pointer;
    }
    
    .info-note {
        margin-top: 20px;
        font-size: 12px;
        color: #8a7a6a;
        text-align: center;
        border-top: 1px solid #ede8e2;
        padding-top: 16px;
    }
    .info-note i {
        margin-right: 6px;
    }
    
    @media (max-width: 576px) {
        .voucher-card {
            flex-direction: column;
            text-align: center;
            padding: 16px;
        }
        .voucher-card .voucher-info .voucher-details {
            justify-content: center;
        }
        .voucher-card .voucher-status {
            width: 100%;
            text-align: center;
        }
        .voucher-card .voucher-status .btn-use-voucher {
            width: 100%;
        }
        .voucher-card .voucher-info .voucher-name {
            justify-content: center;
        }
        .points-number {
            font-size: 36px;
        }
        .how-to-earn {
            grid-template-columns: 1fr 1fr;
        }
        .history-item {
            flex-direction: column;
            text-align: center;
        }
        .history-item .history-right {
            text-align: center;
        }
        .active-voucher-banner {
            flex-direction: column;
            text-align: center;
        }
        .active-voucher-banner .voucher-info {
            flex-direction: column;
        }
        .voucher-progress {
            flex-direction: column;
            align-items: center;
        }
        .voucher-progress .progress-track-mini {
            max-width: 100%;
            width: 100%;
        }
        .points-summary {
            padding: 20px;
        }
        .section-title {
            font-size: 14px;
        }
    }
</style>

<div class="loyalty-container">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="font-weight:700;color:var(--primary-dark);font-size:22px;">
            <i class="fas fa-star" style="color:var(--gold);"></i> Loyalty Program
        </h2>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary btn-sm" style="background:#e0d6ce;border:none;color:#4a3a2a;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Active Voucher Banner -->
    @if(session('active_voucher'))
        @php $voucher = session('active_voucher'); @endphp
        <div class="active-voucher-banner">
            <div class="voucher-info">
                <span class="voucher-tag">ACTIVE</span>
                <span class="voucher-text">
                    <i class="fas fa-ticket-alt" style="color:#4a7a4a;"></i>
                    {{ $voucher['label'] }} - ₱{{ $voucher['value'] }} off
                    <small>(min. ₱{{ $voucher['min_purchase'] }})</small>
                </span>
            </div>
            <form action="{{ route('customer.remove-voucher') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-remove-voucher">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    @endif

    <!-- Points Summary -->
    <div class="points-summary">
        <div class="points-number">{{ number_format($currentPoints) }}</div>
        <div class="points-label">Available Points</div>
        <div class="points-tier" style="background: {{ $currentTier['color'] }}20; color: {{ $currentTier['color'] }};">
            <i class="fas fa-trophy"></i> {{ $currentTier['label'] }} Tier
            @if($currentTier['discount'] > 0)
                ({{ $currentTier['discount'] }}% Discount)
            @endif
        </div>
        
        @if($nextTier)
            <div class="points-progress">
                <div class="progress-track">
                    @php
                        $progress = min(($currentPoints / $nextTier['points']) * 100, 100);
                    @endphp
                    <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                </div>
                <div class="progress-labels">
                    <span>{{ number_format($currentPoints) }} pts</span>
                    <span>{{ $nextTier['points'] - $currentPoints }} pts to {{ $nextTier['label'] }}</span>
                </div>
            </div>
        @else
            <div class="mt-2" style="color:#4a8a4a;font-weight:600;font-size:14px;">
                <i class="fas fa-crown"></i> Maximum tier reached!
            </div>
        @endif
    </div>

    <!-- How to Earn Points -->
    <div class="section-title">
        <span class="icon"><i class="fas fa-info-circle"></i></span>
        How to Earn Points
    </div>
    <div class="how-to-earn">
        <div class="earn-item">
            <div class="earn-icon"><i class="fas fa-shopping-bag"></i></div>
            <div class="earn-title">Make a Purchase</div>
            <div class="earn-desc">1 point per ₱100 spent</div>
        </div>
        <div class="earn-item">
            <div class="earn-icon"><i class="fas fa-gift"></i></div>
            <div class="earn-title">Birthday Bonus</div>
            <div class="earn-desc">Double points on your birthday</div>
        </div>
        <div class="earn-item">
            <div class="earn-icon"><i class="fas fa-bullhorn"></i></div>
            <div class="earn-title">Special Promotions</div>
            <div class="earn-desc">Bonus points on selected items</div>
        </div>
    </div>

    <!-- All Vouchers -->
    <div class="section-title">
        <span class="icon"><i class="fas fa-ticket-alt"></i></span>
        Available Vouchers
        @php
            $availableCount = collect($allVouchers)->where('points', '<=', $currentPoints)->count();
            $lockedCount = collect($allVouchers)->where('points', '>', $currentPoints)->count();
        @endphp
        <span class="badge-count">{{ $availableCount }}</span>
        @if($lockedCount > 0)
            <span class="badge-locked">{{ $lockedCount }} locked</span>
        @endif
    </div>

    @if(session('active_voucher'))
        <div class="alert-info">
            <i class="fas fa-info-circle"></i>
            You have an active voucher. You can only use one voucher per order.
            <a href="{{ route('customer.remove-voucher') }}" onclick="event.preventDefault(); document.getElementById('remove-form').submit();">Cancel it</a>
            <form id="remove-form" action="{{ route('customer.remove-voucher') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    @endif

    @php
        $allVouchers = [
            ['id' => 'small', 'points' => 50, 'value' => 20, 'label' => 'Small Discount', 'min_purchase' => 100, 'icon' => 'fa-coffee', 'color' => '#6F8A6F'],
            ['id' => 'medium', 'points' => 100, 'value' => 50, 'label' => 'Medium Discount', 'min_purchase' => 200, 'icon' => 'fa-mug-hot', 'color' => '#5A7A8A'],
            ['id' => 'large', 'points' => 200, 'value' => 100, 'label' => 'Large Discount', 'min_purchase' => 400, 'icon' => 'fa-crown', 'color' => '#B8974A'],
            ['id' => 'premium', 'points' => 400, 'value' => 200, 'label' => 'Premium Discount', 'min_purchase' => 800, 'icon' => 'fa-gem', 'color' => '#7A5A8A'],
            ['id' => 'vip', 'points' => 800, 'value' => 500, 'label' => 'VIP Discount', 'min_purchase' => 1500, 'icon' => 'fa-star', 'color' => '#8A5A4A'],
        ];
    @endphp

    @foreach($allVouchers as $voucher)
        @php
            $canRedeem = $currentPoints >= $voucher['points'];
            $isActive = session('active_voucher') && session('active_voucher.id') == $voucher['id'];
            $progress = min(($currentPoints / $voucher['points']) * 100, 100);
            $pointsNeeded = $voucher['points'] - $currentPoints;
        @endphp
        <div class="voucher-card {{ !$canRedeem ? 'locked' : '' }}">
            <!-- Icon -->
            <div class="voucher-icon" style="background: {{ $canRedeem ? $voucher['color'] : '#b0a090' }};">
                <i class="fas {{ $voucher['icon'] }}"></i>
            </div>
            
            <!-- Info -->
            <div class="voucher-info">
                <div class="voucher-name">
                    {{ $voucher['label'] }}
                    @if(!$canRedeem)
                        <span class="lock-badge"><i class="fas fa-lock"></i> Locked</span>
                    @endif
                </div>
                <div class="voucher-details">
                    <span><i class="fas fa-tag" style="color:var(--primary);"></i> ₱{{ $voucher['value'] }} off</span>
                    <span><i class="fas fa-shopping-cart" style="color:var(--primary);"></i> Min. ₱{{ $voucher['min_purchase'] }}</span>
                    <span><i class="fas fa-star" style="color:#C9A96E;"></i> {{ $voucher['points'] }} pts</span>
                </div>
                @if(!$canRedeem)
                    <div class="voucher-progress">
                        <div class="progress-track-mini">
                            <div class="fill danger" style="width: {{ $progress }}%;"></div>
                        </div>
                        <span class="progress-text">
                            <span class="need">{{ number_format($pointsNeeded) }}</span> more pts needed
                        </span>
                    </div>
                @endif
            </div>
            
            <!-- Status / Button -->
            <div class="voucher-status">
                @if($isActive)
                    <span class="btn-use-voucher used">
                        <i class="fas fa-check"></i> Active
                    </span>
                @elseif($canRedeem)
                    <a href="{{ route('customer.use-voucher', $voucher['id']) }}" class="btn-use-voucher">
                        <i class="fas fa-ticket-alt"></i> Use Voucher
                    </a>
                @else
                    <span class="points-needed">
                        <span class="num insufficient">{{ number_format($pointsNeeded) }}</span> pts needed
                    </span>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Points History -->
    <div class="section-title" style="margin-top:24px;">
        <span class="icon"><i class="fas fa-history"></i></span>
        Points History
        <span class="badge-count">{{ $pointsHistory->count() }}</span>
    </div>

    @if($pointsHistory->count() > 0)
        @foreach($pointsHistory as $sale)
            @php
                $hasUsed = $sale->points_used > 0;
                $netPoints = $sale->points_net ?? $sale->points_earned;
            @endphp
            <div class="history-item">
                <div class="history-left">
                    <div class="history-order">Order #{{ $sale->id }}</div>
                    <div class="history-date">{{ $sale->sale_date->format('M d, Y h:i A') }}</div>
                </div>
                <div class="history-right">
                    @if($hasUsed)
                        <div class="points-used">-{{ $sale->points_used }} pts</div>
                    @endif
                    <div class="points-earned">+{{ $sale->points_earned }} pts</div>
                    <div class="history-amount">Net: <span class="points-net">{{ $netPoints }}</span> pts</div>
                </div>
            </div>
        @endforeach
    @else
        <div class="history-empty">
            <span class="empty-icon"><i class="fas fa-inbox"></i></span>
            No points history yet
        </div>
    @endif

    <!-- Info Note -->
    <div class="info-note">
        <i class="fas fa-info-circle"></i>
        Points are earned on every purchase. 1 point per ₱100 spent.
        Vouchers are applied automatically at checkout.
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert-info');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>
@endpush
@endsection