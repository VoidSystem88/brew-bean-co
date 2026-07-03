@extends('layouts.app')

@section('page-title', 'Settings')

@section('content')
<style>
    .settings-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform 0.2s;
        margin-bottom: 16px;
        text-align: center;
    }
    .settings-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .settings-card .icon {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
    }
    .settings-card .title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    .settings-card .desc {
        color: #999;
        font-size: 13px;
        margin: 4px 0 16px;
    }
    .btn-install {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 10px 40px;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
    }
    .btn-install:hover {
        background: #5d3e2a;
        color: white;
    }
    .btn-install:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .badge-installed {
        background: #d4edda;
        color: #155724;
        padding: 8px 20px;
        border-radius: 40px;
        font-weight: 600;
        display: inline-block;
    }
    .btn-backup {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 40px;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
    }
    .btn-backup:hover {
        background: #218838;
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cog me-2"></i>Settings</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="white-space: pre-line;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        <!-- Install App -->
        <div class="col-md-6">
            <div class="settings-card" style="border: 2px solid #C9A96E;">
                <span class="icon">📱</span>
                <div class="title">Install App</div>
                <div class="desc">Add Brew & Bean Co. to your phone home screen</div>
                <button id="installPWA" class="btn-install">
                    <i class="fas fa-download me-2"></i> Install App
                </button>
                <div id="installStatus" style="display:none; margin-top: 8px;">
                    <span class="badge-installed">✅ App is installed</span>
                </div>
            </div>
        </div>

        <!-- Backup Database -->
        <div class="col-md-6">
            <div class="settings-card" style="border: 2px solid #28a745;">
                <span class="icon">💾</span>
                <div class="title">Backup Database</div>
                <div class="desc">Export all data as JSON backup file</div>
                <a href="{{ route('admin.export.backup') }}" class="btn-backup">
                    <i class="fas fa-download me-2"></i> Download Backup
                </a>
            </div>
        </div>

        <!-- Database Management Link -->
        <div class="col-md-12">
            <div class="settings-card" style="border: 2px solid #6F4E37;">
                <span class="icon">🗄️</span>
                <div class="title">Database Management</div>
                <div class="desc">Import products, items, and restore from backup</div>
                <a href="{{ route('admin.database') }}" class="btn-install" style="background: #6F4E37;">
                    <i class="fas fa-arrow-right me-2"></i> Go to Database Management
                </a>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="col-md-12">
            <div class="settings-card">
                <span class="icon">📊</span>
                <div class="title">System Stats</div>
                <div class="desc">Current database summary</div>
                <div class="row g-2 mt-2">
                    <div class="col-3">
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 8px;">
                            <div style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['products'] }}</div>
                            <div style="font-size:12px;color:#999;">Products</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 8px;">
                            <div style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['items'] }}</div>
                            <div style="font-size:12px;color:#999;">Ingredients</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 8px;">
                            <div style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['branches'] }}</div>
                            <div style="font-size:12px;color:#999;">Branches</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 8px;">
                            <div style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['suppliers'] }}</div>
                            <div style="font-size:12px;color:#999;">Suppliers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // PWA Install
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const btn = document.getElementById('installPWA');
        btn.style.display = 'inline-block';
        btn.innerHTML = '<i class="fas fa-download me-2"></i> Install App';
    });
    
    if (window.matchMedia('(display-mode: standalone)').matches) {
        document.getElementById('installPWA').style.display = 'none';
        document.getElementById('installStatus').style.display = 'block';
    }
    
    document.getElementById('installPWA').addEventListener('click', async function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const result = await deferredPrompt.userChoice;
            
            if (result.outcome === 'accepted') {
                this.innerHTML = '<i class="fas fa-check me-2"></i> Installed!';
                this.disabled = true;
                this.style.background = '#28a745';
                document.getElementById('installStatus').style.display = 'block';
                alert('✅ Brew & Bean Co. installed on your device!');
            } else {
                alert('❌ Installation cancelled.');
            }
            deferredPrompt = null;
        } else {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);
            
            if (isIOS) {
                alert('📱 On iPhone:\n\n1. Tap Share button\n2. Tap "Add to Home Screen"\n3. Tap "Add"');
            } else if (isAndroid) {
                alert('📱 On Android:\n\n1. Tap Chrome menu (⋮)\n2. Tap "Add to Home Screen"\n3. Tap "Add"');
            } else {
                alert('📱 Open this page on your mobile browser and click Install again.');
            }
        }
    });
</script>
@endpush
@endsection
