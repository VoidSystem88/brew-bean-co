@extends('layouts.mobile')

@section('page-title', 'Profile')

@section('content')
<div style="text-align:center;margin-bottom:16px;">
    <div style="width:70px;height:70px;border-radius:50%;background:#6F4E37;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:28px;font-weight:600;">
        {{ substr($user->name ?? 'U', 0, 1) }}
    </div>
    <h5 style="margin-top:8px;">{{ $user->name }}</h5>
    <p style="color:#999;font-size:14px;">{{ $user->email }}</p>
    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
</div>

<div style="background:white;border-radius:12px;padding:16px;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee;">
        <span style="color:#999;">Role</span>
        <span>{{ ucfirst($user->role) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee;">
        <span style="color:#999;">Branch</span>
        <span>{{ $user->branch->name ?? 'Global' }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;padding:8px 0;">
        <span style="color:#999;">Status</span>
        <span class="badge bg-success">Active</span>
    </div>
</div>

<div style="background:white;border-radius:12px;padding:16px;margin-top:12px;border:1px solid #e8e8e8;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    <button class="btn btn-danger" style="width:100%;font-size:14px;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
</div>
@endsection