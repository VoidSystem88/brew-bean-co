<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .header { background: #6F4E37; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .low { color: #dc3545; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; background: #6F4E37; color: white; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>? Brew & Bean Co. Pro</h2>
            <h3>?? Low Stock Alert</h3>
        </div>
        <div class="content">
            <p><strong>Branch:</strong> {{ $branch->name }}</p>
            <p><strong>Alert Time:</strong> {{ now()->format('F d, Y H:i') }}</p>
            
            <h4>Items Needing Restock:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Alert Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td class="low">{{ number_format($item->stock, 2) }}</td>
                        <td>{{ $item->threshold }}</td>
                        <td><span style="background: #dc3545; color: white; padding: 3px 10px; border-radius: 3px; font-size: 12px;">LOW</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <p><strong>Total Items:</strong> {{ count($items) }}</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/inventory') }}" class="btn">View Inventory</a>
                <a href="{{ url('/inventory/restock-all') }}" class="btn" style="background: #28a745;">One-Click Restock All</a>
            </div>
            
            <p style="color: #666; font-size: 14px;">
                This is an automated alert. Please restock these items as soon as possible.
            </p>
        </div>
        <div class="footer">
            <p>Brew & Bean Co. Pro - Coffee Shop Management System</p>
            <p>This email was sent automatically by your inventory system.</p>
        </div>
    </div>
</body>
</html>

