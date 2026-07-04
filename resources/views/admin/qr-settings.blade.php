@extends('layouts.app')

@section('page-title', 'QR Code Settings')

@section('content')
<style>
    .qr-settings-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .qr-preview-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        border: 1px solid #e8e8e8;
        text-align: center;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .qr-preview-container img {
        max-width: 280px;
        max-height: 280px;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 10px;
        background: white;
    }
    
    .qr-preview-container .qr-placeholder {
        color: #999;
        font-size: 14px;
    }
    
    .qr-preview-container .qr-placeholder i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
        color: #ddd;
    }
    
    .upload-zone {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #fafafa;
        margin-bottom: 15px;
    }
    .upload-zone:hover {
        border-color: #6F4E37;
        background: #f8f6f4;
    }
    .upload-zone i {
        font-size: 48px;
        color: #ccc;
        display: block;
        margin-bottom: 10px;
    }
    .upload-zone p {
        color: #999;
        margin: 0;
    }
    .upload-zone .hint {
        font-size: 12px;
        color: #ccc;
        margin-top: 4px;
    }
    
    .logo-preview-small {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #eee;
        margin: 10px auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f6f4;
    }
    .logo-preview-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .logo-preview-small .placeholder {
        color: #ccc;
        font-size: 28px;
    }
    
    .btn-save-logo {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 8px 30px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-save-logo:hover {
        background: #5a3d2b;
    }
    .btn-save-logo:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .btn-remove-logo {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-remove-logo:hover {
        background: #c82333;
    }
    
    .btn-download-qr {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 30px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-download-qr:hover {
        background: #218838;
        color: white;
    }
    
    .status-badge {
        display: inline-block;
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-badge.has-logo {
        background: #d4edda;
        color: #155724;
    }
    .status-badge.no-logo {
        background: #f8d7da;
        color: #721c24;
    }
    
    @media (max-width: 576px) {
        .qr-preview-container img {
            max-width: 200px;
            max-height: 200px;
        }
        .upload-zone {
            padding: 20px;
        }
        .upload-zone i {
            font-size: 32px;
        }
    }
</style>

<div class="qr-settings-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-qrcode me-2" style="color:#6F4E37;"></i>QR Code Settings</h2>
        <a href="{{ route('admin.settings') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Settings
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Logo Upload Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Logo Settings</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Current Status:</span>
                            <span class="status-badge {{ $logoExists ? 'has-logo' : 'no-logo' }}">
                                {{ $logoExists ? 'Logo Set' : 'No Logo' }}
                            </span>
                        </div>
                        @if($logoExists)
                            <div class="logo-preview-small">
                                <img src="{{ asset('storage/qr-logo.png') }}" alt="QR Logo">
                            </div>
                            <button class="btn-remove-logo btn-sm w-100 mt-2" onclick="removeLogo()">
                                <i class="fas fa-trash"></i> Remove Logo
                            </button>
                        @endif
                    </div>

                    <div class="upload-zone" onclick="document.getElementById('logoInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload logo image</p>
                        <div class="hint">PNG, JPG, GIF, WebP • Max 5MB</div>
                    </div>
                    <input type="file" id="logoInput" accept="image/*" style="display:none;">
                </div>
            </div>
        </div>

        <!-- QR Preview Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">QR Code Preview</h6>
                </div>
                <div class="card-body">
                    <div class="qr-preview-container" id="qrPreview">
                        <img src="{{ route('qr.preview') }}" alt="QR Code" style="max-width:250px;max-height:250px;">
                    </div>
                    
                    <div class="d-flex gap-2 mt-3 justify-content-center">
                        <a href="{{ route('qr.download') }}" class="btn-download-qr" target="_blank">
                            <i class="fas fa-download"></i> Download QR
                        </a>
                        <button class="btn btn-outline-secondary" onclick="refreshPreview()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                    
                    <div class="mt-2 text-center">
                        <small class="text-muted">QR code will be used for customer checkouts</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 text-muted" style="font-size: 12px; text-align: center; border-top: 1px solid #e8e8e8; padding-top: 12px;">
        <i class="fas fa-info-circle me-1"></i>
        The QR code will be displayed on customer profiles and can be scanned for quick checkout.
    </div>
</div>

@push('scripts')
<script>
    function refreshPreview() {
        const container = document.getElementById('qrPreview');
        container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2">Loading...</p></div>';
        
        fetch('{{ route("qr.preview") }}?_=' + Date.now())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                container.innerHTML = `<img src="${url}" alt="QR Code" style="max-width:250px;max-height:250px;">`;
            })
            .catch(() => {
                container.innerHTML = `
                    <div class="qr-placeholder">
                        <i class="fas fa-qrcode"></i>
                        <p>Preview not available</p>
                        <small class="text-muted">Try refreshing again</small>
                    </div>
                `;
            });
    }

    function removeLogo() {
        if (!confirm('Are you sure you want to remove the QR logo?')) return;

        fetch('{{ route("qr.remove-logo") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }

    // File upload handler
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Please upload a valid image file (PNG, JPG, GIF, WebP).');
            this.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum size is 5MB.');
            this.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('logo', file);

        const btn = document.querySelector('.btn-save-logo') || document.createElement('button');
        btn.disabled = true;

        fetch('{{ route("qr.upload-logo") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    });

    // Auto refresh on load
    setTimeout(refreshPreview, 500);
</script>
@endpush
@endsection