@extends('layouts.mobile')

@section('page-title', 'Sales Summary')

@section('content')
<div style="display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap;">
    <div style="flex:1;min-width:100%;background:white;border-radius:12px;padding:16px;border-left:4px solid #28a745;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="font-size:13px;font-weight:600;color:#333;margin-bottom:5px;">Today</div>
        <div style="font-size:22px;font-weight:700;color:#28a745;">${{ number_format($todaySales ?? 0, 2) }}</div>
        <small style="color:#999;">{{ $todayCount ?? 0 }} transactions</small>
    </div>
    <div style="flex:1;min-width:calc(50% - 5px);background:white;border-radius:12px;padding:16px;border-left:4px solid #007bff;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="font-size:13px;font-weight:600;color:#333;margin-bottom:5px;">This Week</div>
        <div style="font-size:22px;font-weight:700;color:#007bff;">${{ number_format($weekSales ?? 0, 2) }}</div>
    </div>
    <div style="flex:1;min-width:calc(50% - 5px);background:white;border-radius:12px;padding:16px;border-left:4px solid #6f42c1;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="font-size:13px;font-weight:600;color:#333;margin-bottom:5px;">This Month</div>
        <div style="font-size:22px;font-weight:700;color:#6f42c1;">${{ number_format($monthSales ?? 0, 2) }}</div>
    </div>
</div>

<div style="background:white;border-radius:12px;padding:16px;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    <div style="font-size:13px;font-weight:600;color:#333;margin-bottom:8px;">Top Selling Products</div>
    @forelse($topProducts ?? [] as $product)
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #eee;">
            <span>{{ $product->name }}</span>
            <span class="badge bg-primary">{{ $product->total_sold }} sold</span>
        </div>
    @empty
        <p style="color:#999;text-align:center;margin:10px 0;">No sales data yet</p>
    @endforelse
</div>
@endsection