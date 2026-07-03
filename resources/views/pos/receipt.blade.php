<!DOCTYPE html>
<html>
<head>
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .receipt-container {
            max-width: 320px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .receipt-header .store-name {
            font-size: 20px;
            font-weight: 700;
            color: #6F4E37;
        }
        .receipt-header .store-sub {
            font-size: 11px;
            color: #999;
        }
        .receipt-header .receipt-title {
            font-size: 14px;
            font-weight: 600;
            margin-top: 4px;
        }
        
        .receipt-info {
            font-size: 12px;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .receipt-info .row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        
        .receipt-items {
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .receipt-items .item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 3px 0;
        }
        .receipt-items .item .name { flex: 1; }
        .receipt-items .item .qty { width: 30px; text-align: center; }
        .receipt-items .item .price { width: 70px; text-align: right; }
        
        .receipt-totals {
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .receipt-totals .row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 2px 0;
        }
        .receipt-totals .row.total {
            font-weight: 700;
            font-size: 16px;
            border-top: 2px solid #333;
            padding-top: 6px;
            margin-top: 4px;
        }
        
        .receipt-footer {
            text-align: center;
            font-size: 11px;
            color: #999;
            padding-top: 8px;
            border-top: 2px dashed #333;
            margin-top: 10px;
        }
        .receipt-footer .thankyou {
            font-size: 14px;
            font-weight: 600;
            color: #6F4E37;
        }
        
        .actions {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .actions .btn {
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .actions .btn-print {
            background: #6F4E37;
            color: white;
        }
        .actions .btn-print:hover {
            background: #5a3d2b;
        }
        .actions .btn-regenerate {
            background: #17a2b8;
            color: white;
        }
        .actions .btn-regenerate:hover {
            background: #138496;
        }
        .actions .btn-back {
            background: #6c757d;
            color: white;
        }
        .actions .btn-back:hover {
            background: #5a6268;
        }
        
        .regenerate-note {
            font-size: 10px;
            color: #999;
            text-align: center;
            margin-top: 8px;
            padding: 4px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        @media print {
            body { background: white; padding: 0; }
            .receipt-container { box-shadow: none; border-radius: 0; }
            .actions { display: none; }
            .regenerate-note { display: none; }
            .no-print { display: none; }
        }
        
        @media (max-width: 400px) {
            .receipt-container { padding: 15px; }
            .receipt-header .store-name { font-size: 17px; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Receipt Content -->
        <div class="receipt-header">
            <div class="store-name">☕ Brew & Bean Co.</div>
            <div class="store-sub">{{ $sale->branch->name ?? 'Main Branch' }}</div>
            <div class="receipt-title">SALES RECEIPT</div>
        </div>

        <div class="receipt-info">
            <div class="row">
                <span>Receipt #:</span>
                <span><strong>{{ $sale->id }}</strong></span>
            </div>
            <div class="row">
                <span>Date:</span>
                <span>{{ $sale->sale_date->format('M d, Y h:i A') }}</span>
            </div>
            <div class="row">
                <span>Cashier:</span>
                <span>{{ $sale->user->name ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span>Customer:</span>
                <span>{{ $sale->customer->name ?? $sale->walkin_name ?? 'Walk-in' }}</span>
            </div>
            @if($sale->customer)
                <div class="row">
                    <span>Points Earned:</span>
                    <span>+{{ floor($sale->total_amount / 100) }}</span>
                </div>
            @endif
        </div>

        <div class="receipt-items">
            <div style="display:flex;justify-content:space-between;font-weight:700;font-size:12px;border-bottom:1px solid #ddd;padding-bottom:4px;margin-bottom:4px;">
                <span>Item</span>
                <span style="width:30px;text-align:center;">Qty</span>
                <span style="width:70px;text-align:right;">Amount</span>
            </div>
            @foreach($sale->items as $item)
                <div class="item">
                    <span class="name">{{ $item->product->name ?? 'Unknown' }}</span>
                    <span class="qty">{{ $item->quantity }}</span>
                    <span class="price">₱{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="receipt-totals">
            <div class="row">
                <span>Subtotal</span>
                <span>₱{{ number_format($sale->original_amount ?? $sale->total_amount, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
                <div class="row" style="color:#28a745;">
                    <span>Discount ({{ $sale->discount_rate }}%)</span>
                    <span>-₱{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="row total">
                <span>TOTAL</span>
                <span>₱{{ number_format($sale->total_amount, 2) }}</span>
            </div>
            @if($sale->payment_method)
                <div class="row">
                    <span>Payment</span>
                    <span>{{ ucfirst($sale->payment_method) }}</span>
                </div>
            @endif
            @if($sale->amount_paid)
                <div class="row">
                    <span>Amount Paid</span>
                    <span>₱{{ number_format($sale->amount_paid, 2) }}</span>
                </div>
                <div class="row">
                    <span>Change</span>
                    <span>₱{{ number_format($sale->amount_paid - $sale->total_amount, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="receipt-footer">
            <div class="thankyou">Thank You!</div>
            <div>Please come again</div>
            <div style="margin-top:4px;font-size:10px;">
                {{ $sale->created_at->format('Y-m-d H:i:s') }}
            </div>
            @if($sale->regenerated_at ?? false)
                <div style="margin-top:4px;color:#17a2b8;font-size:10px;">
                    🔄 Regenerated: {{ $sale->regenerated_at->format('M d, Y h:i A') }}
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="actions no-print">
            <button class="btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <button class="btn btn-regenerate" onclick="regenerateReceipt({{ $sale->id }})">
                <i class="fas fa-sync"></i> Regenerate
            </button>
            <a href="{{ route('pos.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="regenerate-note no-print">
            <i class="fas fa-info-circle"></i> 
            Regenerate to get a fresh copy of this receipt
        </div>
    </div>

    <script>
        function regenerateReceipt(saleId) {
            if (!confirm('Regenerate receipt for order #' + saleId + '?')) return;
            
            const btn = document.querySelector('.btn-regenerate');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            fetch('/pos/receipt/' + saleId + '/regenerate', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Replace entire page content with new receipt
                document.open();
                document.write(html);
                document.close();
            })
            .catch(error => {
                alert('Error regenerating receipt: ' + error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync"></i> Regenerate';
            });
        }

        // Auto-print if requested
        @if(request()->has('print'))
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        @endif
    </script>
</body>
</html>