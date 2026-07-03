@extends('layouts.app')

@section('page-title', 'Add New Branch')

@section('content')
<style>
    .preview-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px dashed #6F4E37;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .preview-box:hover {
        border-color: #4CAF50;
        box-shadow: 0 4px 15px rgba(111, 78, 55, 0.15);
    }
    .preview-name {
        font-size: 24px;
        font-weight: 700;
        color: #6F4E37;
    }
    .preview-name .highlight {
        color: #4CAF50;
    }
    .preview-label {
        font-size: 13px;
        color: #999;
        margin-top: 8px;
    }
    .coffee-icon {
        font-size: 32px;
        display: block;
        margin-bottom: 10px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2 text-primary"></i>
                        Add New Branch
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('branches.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Location Field -->
                            <div class="col-md-12 mb-3">
                                <label for="location" class="form-label fw-bold">
                                    <i class="fas fa-map-pin me-1 text-danger"></i> Branch Location *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 fw-semibold" style="font-size: 13px;">
                                        ☕ Brew & Bean Co. -
                                    </span>
                                    <input type="text" 
                                           name="location" 
                                           id="location" 
                                           class="form-control border-start-0 @error('location') is-invalid @enderror" 
                                           placeholder="e.g., Makati, BGC, Cebu, Tagoloan"
                                           value="{{ old('location') }}"
                                           required
                                           autofocus>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Just type the location name. The system will automatically add "☕ Brew & Bean Co. - " prefix.
                                </small>
                            </div>

                            <!-- Live Preview -->
                            <div class="col-md-12 mb-4">
                                <div class="preview-box">
                                    <span class="coffee-icon">☕</span>
                                    <div class="preview-name" id="previewName">
                                        Brew & Bean Co. - <span class="highlight">[Your Location]</span>
                                    </div>
                                    <div class="preview-label">
                                        <i class="fas fa-eye me-1"></i> Live Preview - Branch name will be generated automatically
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label fw-bold">
                                    <i class="fas fa-location-dot me-1 text-success"></i> Full Address
                                </label>
                                <textarea name="address" 
                                          id="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Enter complete address of the branch">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-bold">
                                        <i class="fas fa-phone me-1 text-info"></i> Phone Number
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           placeholder="e.g., 02-8123-4567"
                                           value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-1 text-warning"></i> Email Address
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="e.g., branch@brewbeanco.com"
                                           value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-plus-circle me-1"></i> Create Branch
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card mt-3 bg-light">
                <div class="card-body py-2">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1 text-warning"></i>
                        <strong>Tip:</strong> The branch name will automatically be formatted as 
                        <span class="fw-bold">"☕ Brew & Bean Co. - [Your Location]"</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live preview on input
    document.getElementById('location').addEventListener('input', function() {
        const location = this.value.trim();
        const preview = document.getElementById('previewName');
        if (location) {
            preview.innerHTML = '☕ Brew & Bean Co. - <span class="highlight">' + location + '</span>';
        } else {
            preview.innerHTML = '☕ Brew & Bean Co. - <span class="highlight">[Your Location]</span>';
        }
    });

    // Auto capitalize first letter of each word
    document.getElementById('location').addEventListener('blur', function() {
        if (this.value) {
            this.value = this.value.toLowerCase().replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
            // Trigger input event to update preview
            this.dispatchEvent(new Event('input'));
        }
    });

    // Prevent form submission on Enter key in location field
    document.getElementById('location').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
</script>
@endpush
@endsection