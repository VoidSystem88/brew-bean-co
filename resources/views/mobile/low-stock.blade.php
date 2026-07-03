@extends('layouts.mobile')

@section('page-title', 'Low Stock Alerts')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <span style="font-size:14px;font-weight:500;">Items needing restock</span>
    <span class="badge bg-danger">{{ $lowStockItems->count() }}</span>
</div>

@forelse($lowStockItems as $item)
    <div style="background:white;border-radius:12px;padding:16px;margin-bottom:12px;border-left:4px solid #dc3545;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex;justify-content:space-between;">
            <strong>{{ $item->item_name }}</strong>
            <span class="badge bg-danger">Low</span>
        </div>
        <small style="color:#999;">{{ $item->category }}</small>
        <div style="display:flex;justify-content:space-between;margin-top:8px;">
            <div>
                <small>Stock: <strong>{{ number_format($item->stock, 2) }}</strong></small>
                <br>
                <small>Alert Level: {{ $item->threshold }}</small>
            </div>
            <div>
                <small style="color:#999;">{{ $item->branch_name }}</small>
            </div>
        </div>
        <button class="btn btn-success btn-sm" style="width:100%;margin-top:8px;font-size:13px;" onclick="alert('Restock requested for {{ $item->item_name }}')">
            <i class="fas fa-box"></i> Request Restock
        </button>
    </div>
@empty
    <div style="background:white;border-radius:12px;padding:30px;text-align:center;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <i class="fas fa-check-circle text-success" style="font-size:40px;"></i>
        <h6 style="margin-top:8px;">All Stock Levels Are Healthy!</h6>
        <p style="color:#999;font-size:14px;">No items need restocking.</p>
    </div>
@endforelse
@endsection