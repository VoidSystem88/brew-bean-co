@extends('layouts.mobile')

@section('page-title', 'Mobile Test')

@section('content')
<div style="background:white;padding:30px;border-radius:12px;text-align:center;margin-top:20px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <div style="font-size:60px;margin-bottom:10px;">📱</div>
    <h2 style="color:#28a745;">✅ Mobile View is Working!</h2>
    <p style="color:#666;margin:15px 0;">If you see this, the mobile layout is loading correctly.</p>
    <p style="color:#999;font-size:14px;">You should see a hamburger menu ☰ in the top left corner.</p>
    <hr>
    <div style="text-align:left;background:#f8f9fa;padding:15px;border-radius:8px;font-size:13px;">
        <strong>📋 Sidebar items:</strong>
        <ul style="margin-top:8px;list-style:none;padding:0;">
            <li>📊 Dashboard</li>
            <li>⚠️ Low Stock Alerts</li>
            <li>📈 Quick Sales Summary</li>
            <li>👤 Profile</li>
            <li>🚪 Logout</li>
        </ul>
    </div>
    <div style="margin-top:20px;">
        <a href="{{ route('mobile.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>
@endsection