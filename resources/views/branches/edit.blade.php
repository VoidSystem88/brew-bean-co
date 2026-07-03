@extends('layouts.app')

@section('page-title', 'Edit Branch')

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
    .current-badge {
        background: #6F4E37;
        color: white;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 12px;
    }
    
    .autocomplete-container {
        position: relative;
    }
    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 9999;
        display: none;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .autocomplete-dropdown.show {
        display: block;
    }
    .autocomplete-item {
        padding: 8px 14px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        border-bottom: 1px solid #f5f5f5;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item:hover {
        background: #f8f6f4;
    }
    .autocomplete-item .suggestion-icon {
        color: #6F4E37;
        font-size: 14px;
        width: 20px;
    }
    .autocomplete-item .suggestion-text {
        flex: 1;
    }
    .autocomplete-item .suggestion-type {
        font-size: 11px;
        color: #999;
        background: #f5f5f5;
        padding: 2px 10px;
        border-radius: 12px;
    }
    .autocomplete-item .suggestion-highlight {
        font-weight: 700;
        color: #6F4E37;
    }
    .autocomplete-loading {
        padding: 12px 14px;
        text-align: center;
        color: #999;
        font-size: 13px;
    }
    .input-with-icon {
        position: relative;
    }
    .input-with-icon .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        z-index: 2;
    }
    .input-with-icon textarea {
        padding-left: 36px;
    }
    .input-with-icon input {
        padding-left: 36px;
    }
    
    .selected-location-info {
        background: #e8f5e9;
        border: 1px solid #c8e6c9;
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 8px;
        display: none;
    }
    .selected-location-info.show {
        display: block;
    }
    .selected-location-info .info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        padding: 2px 0;
    }
    .selected-location-info .info-row i {
        color: #6F4E37;
        width: 18px;
    }
    .selected-location-info .info-row .label {
        color: #666;
        font-weight: 500;
        min-width: 70px;
    }
    
    .coord-input {
        font-family: monospace;
        font-size: 13px;
    }
    
    .btn-detect-coords {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap;
    }
    .btn-detect-coords:hover {
        background: #138496;
        color: white;
    }
    .btn-detect-coords:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .coord-status {
        font-size: 12px;
        margin-top: 4px;
    }
    .coord-status.success {
        color: #28a745;
    }
    .coord-status.error {
        color: #dc3545;
    }
    
    @media (max-width: 576px) {
        .selected-location-info .info-row {
            flex-wrap: wrap;
        }
        .btn-detect-coords {
            width: 100%;
            margin-top: 4px;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2 text-warning"></i>
                        Edit Branch
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('branches.update', $branch) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Current Branch Info -->
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-secondary">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Current Branch:</strong> 
                                    <span class="badge current-badge">{{ $branch->name }}</span>
                                </div>
                            </div>

                            <!-- Branch Name -->
                            <div class="col-md-12 mb-3">
                                <label for="location" class="form-label fw-bold">
                                    <i class="fas fa-store me-1 text-warning"></i> Branch Name *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 fw-semibold" style="font-size: 13px;">
                                        ☕ Brew & Bean Co. -
                                    </span>
                                    <input type="text" 
                                           name="location" 
                                           id="location" 
                                           class="form-control border-start-0 @error('location') is-invalid @enderror" 
                                           placeholder="e.g., Makati, BGC, Cebu"
                                           value="{{ old('location', $location) }}"
                                           required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Live Preview -->
                            <div class="col-md-12 mb-4">
                                <div class="preview-box">
                                    <span class="coffee-icon">☕</span>
                                    <div class="preview-name" id="previewName">
                                        Brew & Bean Co. - <span class="highlight">{{ $location }}</span>
                                    </div>
                                    <div class="preview-label">
                                        <i class="fas fa-eye me-1"></i> Live Preview - Updated branch name
                                    </div>
                                </div>
                            </div>

                            <!-- Full Address with Autocomplete -->
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label fw-bold">
                                    <i class="fas fa-location-dot me-1 text-success"></i> Full Address *
                                </label>
                                <div class="autocomplete-container">
                                    <div class="input-with-icon">
                                        <span class="input-icon"><i class="fas fa-search text-muted"></i></span>
                                        <textarea name="address" 
                                                  id="address" 
                                                  class="form-control @error('address') is-invalid @enderror" 
                                                  rows="3"
                                                  placeholder="Start typing to search for location (e.g., Cagayan, Makati, BGC)">{{ old('address', $branch->address) }}</textarea>
                                    </div>
                                    <div class="autocomplete-dropdown" id="autocompleteDropdown">
                                        <div id="autocompleteResults">
                                            <div class="autocomplete-loading" style="display:none;">
                                                <i class="fas fa-spinner fa-spin"></i> Searching...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Auto-detect Coordinates Button -->
                            <div class="col-md-12 mb-2">
                                <button type="button" class="btn-detect-coords" onclick="detectCoordinates()" id="detectCoordsBtn">
                                    <i class="fas fa-satellite-dish me-1"></i> Auto-Detect Coordinates from Address
                                </button>
                                <span id="coordStatus" class="coord-status"></span>
                            </div>

                            <!-- Coordinates -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label fw-bold">
                                        <i class="fas fa-globe me-1 text-info"></i> Latitude
                                    </label>
                                    <input type="number" 
                                           name="latitude" 
                                           id="latitude" 
                                           class="form-control coord-input @error('latitude') is-invalid @enderror" 
                                           placeholder="e.g., 14.5995"
                                           step="any"
                                           value="{{ old('latitude', $branch->latitude) }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label fw-bold">
                                        <i class="fas fa-globe me-1 text-info"></i> Longitude
                                    </label>
                                    <input type="number" 
                                           name="longitude" 
                                           id="longitude" 
                                           class="form-control coord-input @error('longitude') is-invalid @enderror" 
                                           placeholder="e.g., 120.9842"
                                           step="any"
                                           value="{{ old('longitude', $branch->longitude) }}">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Selected Location Info -->
                            <div class="col-md-12 mb-3">
                                <div class="selected-location-info" id="selectedLocationInfo">
                                    <div class="info-row">
                                        <i class="fas fa-map-pin"></i>
                                        <span class="label">Location:</span>
                                        <span id="selectedLocationName" style="font-weight:600;color:#6F4E37;"></span>
                                    </div>
                                    <div class="info-row">
                                        <i class="fas fa-globe"></i>
                                        <span class="label">Coordinates:</span>
                                        <span id="selectedLocationCoords" style="color:#666;"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone & Email -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-bold">
                                        <i class="fas fa-phone me-1 text-info"></i> Phone Number
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           placeholder="e.g., 02-8123-4567"
                                           value="{{ old('phone', $branch->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-1 text-warning"></i> Email Address
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="e.g., branch@brewbeanco.com"
                                           value="{{ old('email', $branch->email) }}">
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
                                <i class="fas fa-save me-1"></i> Update Branch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let searchTimeout = null;
    let selectedSuggestion = null;

    // Live preview on branch name input
    document.getElementById('location').addEventListener('input', function() {
        const location = this.value.trim();
        const preview = document.getElementById('previewName');
        if (location) {
            preview.innerHTML = '☕ Brew & Bean Co. - <span class="highlight">' + escapeHtml(location) + '</span>';
        } else {
            preview.innerHTML = '☕ Brew & Bean Co. - <span class="highlight">[Your Location]</span>';
        }
    });

    // Autocomplete on address field
    document.getElementById('address').addEventListener('input', function() {
        const query = this.value.trim();
        const dropdown = document.getElementById('autocompleteDropdown');
        const results = document.getElementById('autocompleteResults');
        const loading = results.querySelector('.autocomplete-loading');
        
        if (searchTimeout) clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            dropdown.classList.remove('show');
            return;
        }
        
        loading.style.display = 'block';
        results.innerHTML = '';
        results.appendChild(loading);
        dropdown.classList.add('show');
        
        searchTimeout = setTimeout(() => {
            fetch(`/branches/search-location?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    results.innerHTML = '';
                    
                    if (data.length === 0) {
                        results.innerHTML = `
                            <div class="autocomplete-item" style="cursor:default;color:#999;">
                                <span class="suggestion-icon"><i class="fas fa-exclamation-circle"></i></span>
                                <span class="suggestion-text">No locations found</span>
                            </div>
                        `;
                        return;
                    }
                    
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                            <span class="suggestion-icon"><i class="fas fa-map-marker-alt"></i></span>
                            <span class="suggestion-text">${highlightMatch(item.display_name, query)}</span>
                            <span class="suggestion-type">${item.type || 'Location'}</span>
                        `;
                        div.addEventListener('click', function() {
                            selectSuggestion(item);
                        });
                        results.appendChild(div);
                    });
                })
                .catch(() => {
                    loading.style.display = 'none';
                    results.innerHTML = `
                        <div class="autocomplete-item" style="cursor:default;color:#dc3545;">
                            <span class="suggestion-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <span class="suggestion-text">Error searching. Please try again.</span>
                        </div>
                    `;
                });
        }, 400);
    });

    function selectSuggestion(item) {
        document.getElementById('address').value = item.display_name;
        document.getElementById('latitude').value = item.lat;
        document.getElementById('longitude').value = item.lon;
        
        updateSelectedInfo(item.display_name, item.lat, item.lon);
        
        document.getElementById('autocompleteDropdown').classList.remove('show');
    }

    function updateSelectedInfo(name, lat, lng) {
        const info = document.getElementById('selectedLocationInfo');
        info.classList.add('show');
        document.getElementById('selectedLocationName').textContent = name.split(',')[0] || 'Location';
        document.getElementById('selectedLocationCoords').textContent = `${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`;
    }

    // Auto-Detect Coordinates
    function detectCoordinates() {
        const address = document.getElementById('address').value.trim();
        const btn = document.getElementById('detectCoordsBtn');
        const status = document.getElementById('coordStatus');
        
        if (!address) {
            status.className = 'coord-status error';
            status.textContent = '❌ Please enter an address first.';
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Detecting...';
        status.className = 'coord-status';
        status.textContent = '⏳ Detecting coordinates...';
        
        fetch(`/branches/geocode?address=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-satellite-dish me-1"></i> Auto-Detect Coordinates from Address';
                
                if (data.success) {
                    document.getElementById('latitude').value = data.latitude;
                    document.getElementById('longitude').value = data.longitude;
                    updateSelectedInfo(address, data.latitude, data.longitude);
                    
                    status.className = 'coord-status success';
                    status.textContent = '✅ Coordinates detected successfully!';
                } else {
                    status.className = 'coord-status error';
                    status.textContent = '❌ ' + data.message;
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-satellite-dish me-1"></i> Auto-Detect Coordinates from Address';
                status.className = 'coord-status error';
                status.textContent = '❌ Error detecting coordinates. Please try again.';
            });
    }

    function highlightMatch(text, query) {
        const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
        return text.replace(regex, '<span class="suggestion-highlight">$1</span>');
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function escapeHtml(string) {
        if (!string) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return string.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const container = document.querySelector('.autocomplete-container');
        if (container && !container.contains(e.target)) {
            document.getElementById('autocompleteDropdown').classList.remove('show');
        }
    });

    // Auto capitalize branch name on blur
    document.getElementById('location').addEventListener('blur', function() {
        if (this.value) {
            this.value = this.value.toLowerCase().replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
            this.dispatchEvent(new Event('input'));
        }
    });

    // Show existing coordinates if any
    document.addEventListener('DOMContentLoaded', function() {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        const address = document.getElementById('address').value;
        
        if (lat && lng && address) {
            updateSelectedInfo(address, lat, lng);
        }
    });
</script>
@endpush
@endsection