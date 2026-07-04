@extends('layouts.app')

@section('page-title', 'Settings')

@section('content')
<style>
    .settings-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    
    .logo-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        overflow: hidden;
        background: #f8f6f4;
        transition: 0.3s;
        position: relative;
    }
    .logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .logo-preview .placeholder {
        color: #ccc;
        text-align: center;
        font-size: 14px;
    }
    .logo-preview .placeholder i {
        font-size: 40px;
        display: block;
        margin-bottom: 5px;
    }
    .logo-preview:hover .overlay {
        opacity: 1;
    }
    .logo-preview .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: 0.3s;
        cursor: pointer;
        border-radius: 50%;
    }
    .logo-preview .overlay i {
        font-size: 24px;
    }
    
    .upload-area {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
        background: #fafafa;
    }
    .upload-area:hover {
        border-color: #6F4E37;
        background: #f8f6f4;
    }
    .upload-area i {
        font-size: 36px;
        color: #ccc;
        display: block;
        margin-bottom: 8px;
    }
    .upload-area .file-info {
        font-size: 12px;
        color: #999;
    }
    .upload-area .file-info .max-size {
        color: #6F4E37;
        font-weight: 600;
    }
    
    .logo-info {
        font-size: 12px;
        color: #999;
        text-align: center;
        margin-top: 6px;
    }
    .logo-info .size {
        color: #6F4E37;
        font-weight: 600;
    }
    
    .brand-input {
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        padding: 10px 14px;
        width: 100%;
        font-size: 14px;
        transition: 0.3s;
    }
    .brand-input:focus {
        border-color: #6F4E37;
        outline: none;
        box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
    }
    
    .preview-box {
        background: #2d1f14;
        color: #e8e0d8;
        padding: 12px 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
    }
    .preview-box .preview-logo {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        background: #f8f6f4;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .preview-box .preview-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .preview-box .preview-logo .placeholder {
        color: #999;
        font-size: 14px;
    }
    .preview-box .preview-text {
        flex: 1;
    }
    .preview-box .preview-text .name {
        font-weight: 700;
        font-size: 15px;
        color: white;
    }
    .preview-box .preview-text .tagline {
        font-size: 11px;
        color: #999;
    }
    
    .crop-modal .modal-dialog {
        max-width: 700px;
    }
    .crop-modal .modal-body {
        padding: 20px;
    }
    .crop-container {
        width: 100%;
        height: 450px;
        background: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }
    .crop-container img {
        max-width: 100%;
        display: block;
    }
    
    .crop-instructions {
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
        padding: 10px 14px;
        background: #f8f6f4;
        border-radius: 6px;
        border-left: 3px solid #6F4E37;
    }
    .crop-instructions i {
        color: #6F4E37;
        margin-right: 6px;
    }
    
    .qr-logo-preview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px solid #e8e8e8;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8f6f4;
        flex-shrink: 0;
    }
    .qr-logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .qr-logo-preview .placeholder {
        color: #ccc;
        font-size: 28px;
    }
    
    @media (max-width: 576px) {
        .logo-preview {
            width: 100px;
            height: 100px;
        }
        .crop-modal .modal-dialog {
            max-width: 100%;
            margin: 10px;
        }
        .crop-container {
            height: 300px;
        }
        .preview-box {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-cog me-2"></i>Settings</h2>
    </div>

    <!-- Brand Settings -->
    <div class="settings-card">
        <h5 class="mb-3"><i class="fas fa-font me-2" style="color:#6F4E37;"></i>Brand Information</h5>
        <p class="text-muted" style="font-size:13px;">Customize your store name and tagline that appears in the sidebar.</p>
        
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">Brand Name</label>
                <input type="text" class="brand-input" id="brandName" value="{{ $settings['brand_name'] }}" placeholder="Enter brand name">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Tagline</label>
                <input type="text" class="brand-input" id="brandTagline" value="{{ $settings['brand_tagline'] }}" placeholder="Enter tagline">
            </div>
        </div>
        
        <div class="mt-3">
            <button class="btn btn-primary" onclick="updateBrand()">
                <i class="fas fa-save me-2"></i> Save Brand
            </button>
            <span id="brandMessage" class="ms-2" style="font-size:14px;"></span>
        </div>
        
        <!-- Live Preview -->
        <div class="preview-box mt-3">
            <div class="preview-logo">
                @php
                    $logoPath = Storage::disk('public')->exists('settings/logo.png') 
                        ? asset('storage/settings/logo.png') 
                        : null;
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" id="previewSidebarLogo">
                @else
                    <div class="placeholder"><i class="fas fa-store"></i></div>
                @endif
            </div>
            <div class="preview-text">
                <div class="name" id="previewBrandName">{{ $settings['brand_name'] }}</div>
                <div class="tagline" id="previewBrandTagline">{{ $settings['brand_tagline'] }}</div>
            </div>
        </div>
    </div>

    <!-- Logo Settings -->
    <div class="settings-card">
        <h5 class="mb-3"><i class="fas fa-image me-2" style="color:#6F4E37;"></i>Logo</h5>
        <p class="text-muted" style="font-size:13px;">Upload your store logo. It will appear in the sidebar.</p>
        
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <div class="logo-preview" id="logoPreview" onclick="document.getElementById('logoInput').click()">
                    <div class="placeholder" id="logoPlaceholder">
                        <i class="fas fa-store"></i>
                        <span>No Logo</span>
                    </div>
                    <div class="overlay">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="logo-info" id="logoInfo"></div>
                <button class="btn btn-danger btn-sm mt-2" onclick="removeLogo()" id="removeLogoBtn" style="display:none;">
                    <i class="fas fa-trash"></i> Remove Logo
                </button>
            </div>
            <div class="col-md-9">
                <div class="upload-area" onclick="document.getElementById('logoInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to upload logo</p>
                    <div class="file-info">
                        <span class="max-size">Max 5MB</span> • PNG, JPG, GIF, WebP, SVG
                    </div>
                </div>
                <input type="file" id="logoInput" accept="image/*" style="display:none;">
            </div>
        </div>
        
        <!-- Crop Modal -->
        <div class="modal fade crop-modal" id="cropModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-crop-alt me-2" style="color:#6F4E37;"></i>Crop Logo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="crop-instructions">
                            <i class="fas fa-info-circle"></i>
                            Drag the corners or edges to select the area you want to keep. 
                            The logo will be cropped to a square (1:1 ratio).
                        </div>
                        <div class="crop-container">
                            <img id="cropImage" src="" alt="Crop preview">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button class="btn btn-primary" onclick="saveCroppedLogo()" id="saveCropBtn">
                            <i class="fas fa-check"></i> Save Logo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Settings -->
    <div class="settings-card">
        <h5 class="mb-3"><i class="fas fa-qrcode me-2" style="color:#6F4E37;"></i>QR Code Settings</h5>
        <p class="text-muted" style="font-size:13px;">Customize your QR code with your store logo. This QR code will be used for customer checkout.</p>
        
        <div class="d-flex align-items-center gap-3">
            <div class="qr-logo-preview">
                @php
                    $qrLogoExists = Storage::disk('public')->exists('qr-logo.png');
                    $qrLogoPath = $qrLogoExists ? asset('storage/qr-logo.png') : null;
                @endphp
                @if($qrLogoExists)
                    <img src="{{ $qrLogoPath }}" alt="QR Logo">
                @else
                    <span class="placeholder"><i class="fas fa-qrcode"></i></span>
                @endif
            </div>
            <div>
                <div class="mb-1">
                    <span class="badge {{ $qrLogoExists ? 'bg-success' : 'bg-secondary' }}">
                        {{ $qrLogoExists ? 'Logo Set' : 'No Logo' }}
                    </span>
                </div>
                <a href="{{ route('qr.settings') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-cog me-1"></i> Manage QR Settings
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<script>
    let cropper = null;
    let selectedFile = null;

    // ===== BRAND FUNCTIONS =====
    function updateBrand() {
        const name = document.getElementById('brandName').value.trim();
        const tagline = document.getElementById('brandTagline').value.trim();
        const msg = document.getElementById('brandMessage');
        
        if (!name) {
            msg.innerHTML = '<span style="color:#dc3545;"> Brand name is required</span>';
            return;
        }
        
        const btn = document.querySelector('.btn-primary');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
        
        fetch('{{ route("admin.settings.brand") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                brand_name: name,
                brand_tagline: tagline
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Brand';
            
            if (data.success) {
                document.getElementById('previewBrandName').textContent = data.brand_name;
                document.getElementById('previewBrandTagline').textContent = data.brand_tagline || '';
                msg.innerHTML = '<span style="color:#28a745;"> ' + data.message + '</span>';
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                msg.innerHTML = '<span style="color:#dc3545;"> ' + data.message + '</span>';
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Brand';
            msg.innerHTML = '<span style="color:#dc3545;"> Error saving brand</span>';
        });
    }

    // ===== LOGO FUNCTIONS =====
    function loadLogo() {
        fetch('/admin/settings/logo')
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    const preview = document.getElementById('logoPreview');
                    preview.innerHTML = `<img src="${data.path}?_=${Date.now()}" alt="Logo">`;
                    document.getElementById('removeLogoBtn').style.display = 'inline-block';
                    document.getElementById('logoInfo').innerHTML = `
                        <i class="fas fa-check-circle" style="color:#28a745;"></i>
                        <span class="size">${data.size || ''}</span>
                    `;
                }
            })
            .catch(() => {});
    }

    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = this.files[0];
        if (!file) return;

        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File is too large. Maximum size is 5MB.');
            this.value = '';
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            alert('Please upload a valid image file.');
            this.value = '';
            return;
        }

        selectedFile = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('cropImage');
            img.src = e.target.result;
            
            const modal = new bootstrap.Modal(document.getElementById('cropModal'));
            modal.show();
            
            setTimeout(() => {
                if (cropper) cropper.destroy();
                
                img.onload = function() {
                    cropper = new Cropper(img, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        background: false,
                        responsive: true,
                        checkOrientation: false,
                        minCropBoxWidth: 50,
                        minCropBoxHeight: 50,
                    });
                };
                
                if (img.complete) {
                    img.onload();
                }
            }, 300);
        };
        reader.readAsDataURL(file);
    });

    function saveCroppedLogo() {
        if (!cropper || !selectedFile) {
            alert('Please select an image first.');
            return;
        }

        const cropData = cropper.getData();
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
        modal.hide();

        const formData = new FormData();
        formData.append('logo', selectedFile);
        formData.append('crop_data', JSON.stringify({
            x: cropData.x,
            y: cropData.y,
            width: cropData.width,
            height: cropData.height
        }));

        const btn = document.getElementById('saveCropBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';

        fetch('{{ route("admin.settings.logo") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Save Logo';

            if (data.success) {
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = `<img src="${data.path}?_=${Date.now()}" alt="Logo">`;
                document.getElementById('removeLogoBtn').style.display = 'inline-block';
                document.getElementById('logoInfo').innerHTML = `
                    <i class="fas fa-check-circle" style="color:#28a745;"></i>
                    <span class="size">${data.size || ''}</span>
                `;
                document.getElementById('logoInput').value = '';
                
                const sidebarLogo = document.querySelector('.preview-logo');
                if (sidebarLogo) {
                    sidebarLogo.innerHTML = `<img src="${data.path}?_=${Date.now()}" alt="Logo">`;
                }
                
                alert(' ' + data.message);
            } else {
                alert(' ' + data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Save Logo';
            alert('Error uploading logo: ' + error);
        });
    }

    function removeLogo() {
        if (!confirm('Remove the current logo?')) return;

        fetch('{{ route("admin.settings.logo.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = `
                    <div class="placeholder">
                        <i class="fas fa-store"></i>
                        <span>No Logo</span>
                    </div>
                `;
                document.getElementById('removeLogoBtn').style.display = 'none';
                document.getElementById('logoInfo').innerHTML = '';
                
                const sidebarLogo = document.querySelector('.preview-logo');
                if (sidebarLogo) {
                    sidebarLogo.innerHTML = `<div class="placeholder"><i class="fas fa-store"></i></div>`;
                }
                
                alert(' ' + data.message);
            } else {
                alert(' ' + data.message);
            }
        })
        .catch(error => {
            alert('Error removing logo: ' + error);
        });
    }

    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadLogo();
    });
</script>
@endpush
@endsection