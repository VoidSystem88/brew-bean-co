<!DOCTYPE html>
<html>
<head>
    <title>Order Cancelled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #6F4E37, #4A3228);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .sub {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 4px;
        }
        .content {
            padding: 30px;
        }
        .cancelled-badge {
            background: #dc3545;
            color: white;
            padding: 8px 24px;
            border-radius: 30px;
            display: inline-block;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 16px;
        }
        .order-info {
            background: #f8f6f4;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 16px 0;
        }
        .order-info .row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
        }
        .order-info .row .label {
            color: #999;
        }
        .order-info .row .value {
            font-weight: 500;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        th {
            background: #f8f6f4;
            font-weight: 600;
            color: #666;
        }
        .total-row {
            font-weight: 700;
            font-size: 16px;
            color: #6F4E37;
        }
        .total-row td {
            border-top: 2px solid #6F4E37;
            padding-top: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: #6F4E37;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 16px;
            transition: 0.2s;
        }
        .btn:hover {
            background: #5a3d2b;
        }
        .btn-green {
            background: #28a745;
        }
        .btn-green:hover {
            background: #218838;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .support {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 12px 16px;
            color: #856404;
            font-size: 14px;
            margin: 16px 0;
        }
        .support i {
            margin-right: 8px;
        }
        .refund-info {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 12px 16px;
            color: #2e7d32;
            font-size: 14px;
            margin: 16px 0;
        }
        @media (max-width: 480px) {
            .content { padding: 20px; }
            .header { padding: 20px; }
            .header h1 { font-size: 20px; }
            .order-info .row { flex-direction: column; gap: 2px; }
            table { font-size: 13px; }
            th, td { padding: 6px 8px; }
            .btn-group { flex-direction: column; }
            .btn { text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Brew & Bean Co.</h1>
            <div class="sub">Order Cancellation Notice</div>
        </div>
        
        <div class="content">
            <div style="text-align:center;">
                <span class="cancelled-badge">❌ ORDER CANCELLED</span>
            </div>
            
            <p>Dear <strong>{{ $order['customer_name'] ?? 'Customer' }}</strong>,</p>
            
            <p>We regret to inform you that your order <strong>#{{ $order['order_id'] }}</strong> has been <strong style="color:#dc3545;">cancelled</strong> by our staff.</p>
            
            <div class="support">
                <i class="fas fa-info-circle"></i>
                <strong>Need help?</strong> Please contact our support team if you have any questions about this cancellation.
            </div>
            
            <div class="refund-info">
                <i class="fas fa-credit-card"></i>
                <strong>Refund Information:</strong> Your payment will be refunded within 3-5 business days.
            </div>
            
            <div class="order-info">
                <div class="row">
                    <span class="label">Order #</span>
                    <span class="value">{{ $order['order_id'] }}</span>
                </div>
                <div class="row">
                    <span class="label">Status</span>
                    <span class="value" style="color:#dc3545;">Cancelled</span>
                </div>
                <div class="row">
                    <span class="label">Cancelled At</span>
                    <span class="value">{{ now()->format('M d, Y h:i A') }}</span>
                </div>
                @if(isset($order['reason']))
                <div class="row">
                    <span class="label">Reason</span>
                    <span class="value">{{ $order['reason'] }}</span>
                </div>
                @endif
            </div>
            
            <h4 style="margin:16px 0 8px;">Order Items</h4>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order['items'] as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td style="text-align:right;">₱{{ number_format($item['quantity'] * ($item['price'] ?? 0), 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right;">Total</td>
                        <td style="text-align:right;">₱{{ number_format($order['total_amount'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="btn-group">
                <a href="{{ route('customer.orders') }}" class="btn">
                    <i class="fas fa-list"></i> View Orders
                </a>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-green">
                    <i class="fas fa-shopping-bag"></i> Order Again
                </a>
            </div>
            
            <p style="color:#666;font-size:14px;margin-top:20px;text-align:center;">
                <i class="fas fa-info-circle"></i>
                We apologize for the inconvenience. Your satisfaction is our priority.
            </p>
        </div>
        
        <div class="footer">
            <p>Brew & Bean Co. - Your Coffee Shop</p>
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p style="margin-top:4px;">📧 {{ config('mail.from.address') }} | 📞 {{ config('app.phone', 'N/A') }}</p>
        </div>
    </div>
</body>
</html>