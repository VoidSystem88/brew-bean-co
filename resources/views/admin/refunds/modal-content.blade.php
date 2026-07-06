<div class="refund-detail-container modal-scrollable">
    
    <!-- Refund Summary -->
    <div class="detail-section">
        <div class="section-title">
            <i class="fas fa-receipt"></i> Refund Summary
        </div>
        
        <div class="detail-row">
            <span class="label">Order #</span>
            <span class="value">#{{ $refund->id }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Customer</span>
            <span class="value">{{ $refund->customer->name ?? 'Unknown' }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Email</span>
            <span class="value">{{ $refund->customer->email ?? 'N/A' }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Branch</span>
            <span class="value">{{ str_replace('☕ Brew & Bean Co. - ', '', $refund->branch->name ?? 'N/A') }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Order Date</span>
            <span class="value">{{ $refund->created_at ? \Carbon\Carbon::parse($refund->created_at)->format('M d, Y h:i A') : 'N/A' }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Original Total</span>
            <span class="value">₱{{ number_format($refund->total_amount, 2) }}</span>
        </div>
        
        <div class="detail-row" style="border-bottom: 2px solid #6F4E37; padding-bottom: 10px; margin-bottom: 2px;">
            <span class="label" style="font-weight: 600; color: #333;">Refund Amount</span>
            <span class="value highlight refund-amount-display">₱{{ number_format($refund->refund_amount ?? $refund->total_amount, 2) }}</span>
        </div>
        
        <div class="detail-row" style="margin-top: 4px;">
            <span class="label">Status</span>
            <span class="value">
                <span class="status-badge {{ $refund->refund_status }}">
                    {{ ucfirst($refund->refund_status) }}
                </span>
            </span>
        </div>
        
        @if($refund->refund_processed_at)
        <div class="detail-row">
            <span class="label">Processed At</span>
            <span class="value">{{ \Carbon\Carbon::parse($refund->refund_processed_at)->format('M d, Y h:i A') }}</span>
        </div>
        @endif
        
        @if($refund->refund_notes)
        <div class="detail-row">
            <span class="label">Admin Notes</span>
            <span class="value" style="font-weight: 400; color: #666;">{{ $refund->refund_notes }}</span>
        </div>
        @endif
    </div>

    <!-- Refund Reason -->
    <div class="detail-section">
        <div class="section-title">
            <i class="fas fa-comment"></i> Refund Reason
        </div>
        
        <div class="reason-box">
            <div class="reason-title">
                <i class="fas fa-tag" style="font-size: 12px;"></i>
                {{ $refund->refund_reason ?? 'No reason provided' }}
            </div>
            
            @if($refund->refund_description)
                <div class="reason-desc">{{ $refund->refund_description }}</div>
            @endif
            
            <div class="reason-date">
                <i class="fas fa-clock"></i>
                Requested on: {{ $refund->refund_requested_at ? \Carbon\Carbon::parse($refund->refund_requested_at)->format('M d, Y h:i A') : 'N/A' }}
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="detail-section">
        <div class="section-title">
            <i class="fas fa-shopping-bag"></i> Order Items
        </div>
        
        @forelse($refund->orders as $item)
            <div class="item-row">
                <span class="item-name">{{ $item->product->name ?? 'Unknown' }}</span>
                <span class="item-qty">×{{ $item->quantity }}</span>
                <span class="item-price">₱{{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}</span>
            </div>
        @empty
            <div style="text-align: center; color: #999; padding: 8px 0; font-size: 13px;">
                No items found
            </div>
        @endforelse
        
        <div class="total-row">
            <span>Total</span>
            <span>₱{{ number_format($refund->total_amount, 2) }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="detail-section" style="border: none; background: transparent; padding: 0; box-shadow: none;">
        <div class="action-buttons">
            @if($refund->refund_status === 'pending')
                <button class="btn btn-approve" onclick="processRefund({{ $refund->id }}, 'approve')">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="btn btn-deny" onclick="processRefund({{ $refund->id }}, 'deny')">
                    <i class="fas fa-times"></i> Deny
                </button>
            @endif
            
            @if($refund->refund_status === 'approved')
                <button class="btn btn-complete" onclick="processRefund({{ $refund->id }}, 'complete')">
                    <i class="fas fa-check-double"></i> Complete
                </button>
            @endif
            
            <button class="btn btn-close" data-bs-dismiss="modal">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>