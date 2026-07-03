@extends('layouts.app')

@section('page-title', 'Add Product')

@section('content')
<style>
    .image-upload-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
        border: 2px dashed #ddd;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        background: #f8f9fa;
        transition: all 0.3s;
    }
    .image-upload-container:hover {
        border-color: #6F4E37;
    }
    .image-upload-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-upload-container .upload-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
    }
    .image-upload-container .upload-placeholder i {
        font-size: 48px;
        margin-bottom: 10px;
    }
    .image-upload-container .upload-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 8px;
        text-align: center;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .image-upload-container:hover .upload-overlay {
        opacity: 1;
    }
    .crop-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .crop-modal.active {
        display: flex;
    }
    .crop-modal .crop-box {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    .crop-modal .crop-box h5 {
        margin-bottom: 15px;
    }
    .crop-modal .crop-box .crop-container {
        position: relative;
        max-height: 400px;
        overflow: hidden;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .crop-modal .crop-box .crop-container img {
        width: 100%;
        height: auto;
    }
    .crop-modal .crop-box .crop-controls {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        justify-content: center;
    }
    .crop-modal .crop-box .crop-controls .btn {
        flex: 1;
    }
    .feedback-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        padding: 15px 25px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: translateX(120%);
        transition: transform 0.5s ease;
        min-width: 300px;
    }
    .feedback-toast.show {
        transform: translateX(0);
    }
    .feedback-toast.success {
        background: #28a745;
    }
    .feedback-toast.error {
        background: #dc3545;
    }
    .feedback-toast .fa {
        margin-right: 10px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add Product</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf

                        <div class="row">
                            <!-- Image Upload -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Product Image</label>
                                <div class="image-upload-container" onclick="document.getElementById('imageInput').click()">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <i class="fas fa-camera"></i>
                                        <span>Click to upload</span>
                                        <small style="font-size: 11px;">Recommended: Square image</small>
                                    </div>
                                    <img id="imagePreview" style="display: none;" alt="Product Image">
                                    <div class="upload-overlay">
                                        <i class="fas fa-edit me-1"></i> Change image
                                    </div>
                                </div>
                                <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                                <small class="text-muted">Click the box to upload. Max 5MB. JPG, PNG, GIF supported.</small>
                            </div>

                            <!-- Product Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Product Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="e.g., Cappuccino"
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label fw-bold">Category</label>
                                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    <option value="Coffee" {{ old('category') == 'Coffee' ? 'selected' : '' }}>☕ Coffee</option>
                                    <option value="Tea" {{ old('category') == 'Tea' ? 'selected' : '' }}>🍵 Tea</option>
                                    <option value="Pastry" {{ old('category') == 'Pastry' ? 'selected' : '' }}>🥐 Pastry</option>
                                    <option value="Sandwich" {{ old('category') == 'Sandwich' ? 'selected' : '' }}>🥪 Sandwich</option>
                                    <option value="Dessert" {{ old('category') == 'Dessert' ? 'selected' : '' }}>🍰 Dessert</option>
                                    <option value="Beverage" {{ old('category') == 'Beverage' ? 'selected' : '' }}>🥤 Beverage</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label fw-bold">Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" 
                                           name="price" 
                                           id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('price') }}"
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="2"
                                          placeholder="Optional">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Save Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Toast -->
<div class="feedback-toast" id="feedbackToast">
    <i class="fas" id="feedbackIcon"></i>
    <span id="feedbackMessage"></span>
</div>

<!-- Crop Modal -->
<div class="crop-modal" id="cropModal">
    <div class="crop-box">
        <h5><i class="fas fa-crop-alt me-2"></i>Crop Image</h5>
        <p class="text-muted small">Drag to adjust the crop area. Image will be cropped to square.</p>
        
        <div class="crop-container">
            <img id="cropImage" src="" alt="Crop Image">
        </div>

        <div class="crop-controls">
            <button class="btn btn-secondary" onclick="closeCrop()">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button class="btn btn-primary" onclick="applyCrop()">
                <i class="fas fa-check me-1"></i> Apply Crop
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cropper = null;
    let currentFile = null;

    function showFeedback(message, type = 'success') {
        const toast = document.getElementById('feedbackToast');
        const icon = document.getElementById('feedbackIcon');
        const msg = document.getElementById('feedbackMessage');
        
        toast.className = 'feedback-toast ' + type;
        icon.className = 'fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle');
        msg.textContent = message;
        
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }

    function handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            showFeedback('Image size must be less than 5MB.', 'error');
            event.target.value = '';
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showFeedback('Only JPG, PNG, GIF, and WEBP images are allowed.', 'error');
            event.target.value = '';
            return;
        }

        currentFile = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cropImage').src = e.target.result;
            document.getElementById('cropModal').classList.add('active');
            
            setTimeout(() => {
                if (cropper) {
                    cropper.destroy();
                }
                const image = document.getElementById('cropImage');
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }, 100);
        };
        reader.readAsDataURL(file);
    }

    function applyCrop() {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        canvas.toBlob(function(blob) {
            const file = new File([blob], currentFile.name, { type: currentFile.type });
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                document.getElementById('uploadPlaceholder').style.display = 'none';
                showFeedback('Image uploaded and cropped successfully!', 'success');
            };
            reader.readAsDataURL(file);
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('imageInput').files = dataTransfer.files;
            
            closeCrop();
        }, currentFile.type);
    }

    function closeCrop() {
        document.getElementById('cropModal').classList.remove('active');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('cropImage').src = '';
    }

    document.getElementById('productForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const price = document.getElementById('price').value;
        const btn = document.getElementById('submitBtn');

        if (!name) {
            e.preventDefault();
            showFeedback('⚠️ Please enter a product name.', 'error');
            document.getElementById('name').focus();
            return;
        }

        if (!price || price <= 0) {
            e.preventDefault();
            showFeedback('⚠️ Please enter a valid price.', 'error');
            document.getElementById('price').focus();
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...';
    });

    @if(session('success'))
        showFeedback('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showFeedback('{{ session('error') }}', 'error');
    @endif

    @if($errors->any())
        showFeedback('Please check your inputs and try again.', 'error');
    @endif
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
@endpush
@endsection