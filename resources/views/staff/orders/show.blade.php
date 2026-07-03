<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold">Order Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Order #</strong></td>
                <td>{{ $sale->id }}</td>
            </tr>
            <tr>
                <td><strong>Date</strong></td>
                <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>
                    <span class="status-badge {{ $sale->orders->first()->status ?? 'pending' }}">
                        {{ ucfirst($sale->orders->first()->status ?? 'pending') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Type</strong></td>
                <td>
                    @if($sale->delivery_address)
                        <span class="delivery-badge delivery"><i class="fas fa-truck"></i> Delivery</span>
                    @else
                        <span class="delivery-badge pickup"><i class="fas fa-store"></i> Pickup</span>
                    @endif
                </td>
            </tr>
            @if($sale->delivery_address)
                <tr>
                    <td><strong>Delivery Address</strong></td>
                    <td>{{ $sale->delivery_address }}</td>
                </tr>
            @endif
            @if($sale->order_notes)
                <tr>
                    <td><strong>Notes</strong></td>
                    <td>{{ $sale->order_notes }}</td>
                </tr>
            @endif
            <tr>
                <td><strong>Branch</strong></td>
                <td>{{ $sale->branch->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold">Customer Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Name</strong></td>
                <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>{{ $sale->customer->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Phone</strong></td>
                <td>{{ $sale->customer->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Loyalty Points</strong></td>
                <td>{{ $sale->customer->loyalty_points ?? 0 }}</td>
            </tr>
            @if($sale->discount_rate > 0)
                <tr>
                    <td><strong>Discount</strong></td>
                    <td class="text-success">{{ $sale->discount_rate }}% (₱{{ number_format($sale->discount_amount, 2) }})</td>
                </tr>
            @endif
        </table>
    </div>
</div>

<div class="mt-3">
    <h6 class="fw-bold">Order Items</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->orders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $order->product->name ?? 'Unknown' }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>₱{{ number_format($order->product->price ?? 0, 2) }}</td>
                        <td>₱{{ number_format(($order->product->price ?? 0) * $order->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                    <td><strong>₱{{ number_format($sale->original_amount ?? $sale->total_amount, 2) }}</strong></td>
                </tr>
                @if($sale->discount_amount > 0)
                    <tr class="table-light">
                        <td colspan="4" class="text-end text-success"><strong>Discount ({{ $sale->discount_rate }}%)</strong></td>
                        <td><strong class="text-success">-₱{{ number_format($sale->discount_amount, 2) }}</strong></td>
                    </tr>
                @endif
                <tr class="table-active">
                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                    <td><strong>₱{{ number_format($sale->total_amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-3 text-end">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>