@if($sales->count() > 0)
    @foreach($sales as $sale)
        @php
            $status = $sale->orders->first()->status ?? 'pending';
            $statusClass = $status;
            $isDelivery = $sale->delivery_status === 'pending_delivery' || $sale->delivery_address;
            $itemCount = $sale->orders->count();
            $totalItems = $sale->orders->sum('quantity');
        @endphp
        <div class="order-card" data-sale="{{ $sale->id }}" onclick="viewOrder({{ $sale->id }})">
            <div class="order-header">
                <div>
                    <span class="order-id">#{{ $sale->id }}</span>
                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                    @if($isDelivery)
                        <span class="delivery-badge delivery"><i class="fas fa-truck"></i> Delivery</span>
                    @else
                        <span class="delivery-badge pickup"><i class="fas fa-store"></i> Pickup</span>
                    @endif
                </div>
                <span class="order-time"><i class="far fa-clock"></i> {{ $sale->created_at->diffForHumans() }}</span>
            </div>
            
            <div class="order-customer">
                <i class="fas fa-user me-1"></i> {{ $sale->customer->name ?? 'Walk-in Customer' }}
            </div>
            
            <div class="order-items">
                <i class="fas fa-list me-1"></i> {{ $totalItems }} item(s) 
                @foreach($sale->orders->take(3) as $order)
                    <span class="badge bg-light text-dark me-1">{{ $order->product->name ?? 'Product' }} × {{ $order->quantity }}</span>
                @endforeach
                @if($sale->orders->count() > 3)
                    <span class="badge bg-secondary">+{{ $sale->orders->count() - 3 }} more</span>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="order-total">₱{{ number_format($sale->total_amount, 2) }}</span>
                <div class="order-actions" onclick="event.stopPropagation();">
                    @if($status === 'pending')
                        <button class="btn btn-success btn-sm btn-accept" onclick="acceptOrder({{ $sale->id }})">
                            <i class="fas fa-check"></i> Accept
                        </button>
                    @endif
                    
                    @if($status === 'preparing')
                        <button class="btn btn-primary btn-sm btn-ready" onclick="markReady({{ $sale->id }})">
                            <i class="fas fa-utensils"></i> Ready
                        </button>
                    @endif
                    
                    @if($status === 'ready')
                        <button class="btn btn-success btn-sm btn-complete" onclick="completeOrder({{ $sale->id }})">
                            <i class="fas fa-check-double"></i> Complete
                        </button>
                    @endif
                    
                    @if($status !== 'completed' && $status !== 'cancelled')
                        <button class="btn btn-outline-danger btn-sm" onclick="updateOrderStatus({{ $sale->orders->first()->id }}, 'cancelled', {{ $sale->id }})">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    @endif
                    
                    <button class="btn btn-outline-secondary btn-sm" onclick="viewOrder({{ $sale->id }})">
                        <i class="fas fa-eye"></i> View
                    </button>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>No orders in this category</p>
        <small class="text-muted">New orders will appear here automatically</small>
    </div>
@endif