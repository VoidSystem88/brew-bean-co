<!DOCTYPE html>
<html>
<head>
    <title>Restock Order</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #6F4E37, #4A3228); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #6F4E37; color: white; }
        tr.low { background: #fff3cd; }
        tr.critical { background: #f8d7da; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 12px; }
        .badge-critical { background: #dc3545; color: white; }
        .badge-low { background: #ffc107; color: #333; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            margin: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .btn-approve { background: #22c55e; color: white; }
        .btn-approve:hover { background: #16a34a; }
        .btn-reject { background: #dc2626; color: white; }
        .btn-reject:hover { background: #b91c1c; }
        .btn-view { background: #6F4E37; color: white; }
        .btn-view:hover { background: #5d3e2a; }
        .order-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .status-pending { color: #ffc107; }
        .status-delivered { color: #28a745; }
        .btn-group { text-align: center; margin: 25px 0; }
        .note { background: #fff3cd; padding: 10px; border-radius: 5px; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>☕ Brew & Bean Co. Pro</h2>
            <h3>📦 Restock Order Request</h3>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $supplier->name }}</strong>,</p>
            <p>This is an automated restock request from <strong>{{ $branch->name }}</strong>.</p>

            <div class="order-info">
                <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->order_date->format('F d, Y') }}</p>
                <p><strong>Branch:</strong> {{ $branch->name }}</p>
                <p><strong>Branch Address:</strong> {{ $branch->location }}</p>
                <p><strong>Expected Delivery:</strong> {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('F d, Y') : 'To be confirmed' }}</p>
                <p><strong>Status:</strong> <span class="badge" style="background: #ffc107; color: #333;">PENDING</span></p>
            </div>

            <h4>📋 Items Needed:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Urgency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="{{ $item->current_stock <= 2 ? 'critical' : ($item->current_stock <= 5 ? 'low' : '') }}">
                        <td><strong>{{ $item->item->name }}</strong></td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ $item->item->unit }}</td>
                        <td>{{ number_format($item->current_stock, 2) }}</td>
                        <td>
                            @if($item->current_stock <= 2)
                                <span class="badge badge-critical">🔴 CRITICAL</span>
                            @elseif($item->current_stock <= 5)
                                <span class="badge badge-low">🟡 LOW</span>
                            @else
                                <span class="badge" style="background: #28a745; color: white;">🟢 NORMAL</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin: 20px 0; padding: 15px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #007bff;">
                <p><strong>📋 Delivery Instructions:</strong></p>
                <p>Please deliver to: <strong>{{ $branch->location }}</strong></p>
                <p>Contact: <strong>{{ $branch->contact_number }}</strong></p>
            </div>

            @if($order->notes)
            <div class="note">
                <p><strong>📝 Notes:</strong> {{ $order->notes }}</p>
            </div>
            @endif

            <!-- APPROVAL BUTTONS -->
            <div class="btn-group">
                <p style="margin-bottom: 15px; font-weight: 600; color: #333;">Please confirm if you can fulfill this order:</p>
                
                <a href="{{ $approveUrl }}" class="btn btn-approve" style="display: inline-block;">
                    ✅ Approve Order
                </a>
                <a href="{{ $rejectUrl }}" class="btn btn-reject" style="display: inline-block;">
                    ❌ Reject Order
                </a>
                <br><br>
                <a href="{{ url('/restock/orders/' . $order->id) }}" class="btn btn-view" style="display: inline-block;">
                    👁️ View Details
                </a>
            </div>

            <p style="color: #666; font-size: 14px; margin-top: 20px;">
                This is an automated notification. Please click one of the buttons above to confirm or reject this order.
            </p>
        </div>
        <div class="footer">
            <p>Brew & Bean Co. Pro - Coffee Shop Management System</p>
            <p>This email was sent automatically. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
