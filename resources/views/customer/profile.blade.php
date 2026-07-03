@extends('layouts.customer')

@section('page-title', 'My Profile')

@section('content')
<style>
    .profile-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e8e8e8;
        padding: 25px;
        margin-bottom: 20px;
    }
    .profile-card .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }
    .profile-card .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #6F4E37;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .profile-card .profile-name {
        font-size: 22px;
        font-weight: 700;
        color: #333;
    }
    .profile-card .profile-code {
        color: #999;
        font-size: 14px;
    }
    
    .form-control:focus {
        border-color: #6F4E37;
        box-shadow: 0 0 0 0.2rem rgba(111, 78, 55, 0.25);
    }
    .btn-brown {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-brown:hover {
        background: #5a3d2b;
        color: white;
    }
    .btn-brown:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .map-container {
        height: 250px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        margin-top: 10px;
    }
    .map-container #profileMap {
        height: 100%;
        width: 100%;
    }
    
    .detect-btn {
        background: #4285F4;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
    }
    .detect-btn:hover {
        background: #3367d6;
        color: white;
    }
    .detect-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .info-badge {
        display: inline-block;
        background: #d4edda;
        color: #155724;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .info-badge i {
        margin-right: 4px;
    }
    
    .address-display {
        background: #f8f6f4;
        padding: 12px 16px;
        border-radius: 8px;
        border-left: 3px solid #6F4E37;
        margin: 8px 0;
        font-size: 14px;
    }
    
    .saved-location {
        background: #e8f5e9;
        padding: 8px 14px;
        border-radius: 8px;
        color: #2e7d32;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    @media (max-width: 576px) {
        .profile-card .profile-header {
            flex-direction: column;
            text-align: center;
        }
        .map-container { height: 200px; }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-circle me-2"></i>My Profile</h2>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Profile Information -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <div class="profile-name">{{ $customer->name }}</div>
                <div class="profile-code">
                    <i class="fas fa-id-card me-1"></i> {{ $customer->customer_code }}
                </div>
                <div class="mt-1">
                    <span class="info-badge">
                        <i class="fas fa-star"></i> {{ $customer->loyalty_points ?? 0 }} points
                    </span>
                </div>
            </div>
        </div>

        <form id="profileForm" onsubmit="updateProfile(event)">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">Full Name</label>
                    <input type="text" class="form-control" id="profileName" value="{{ $customer->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">Email</label>
                    <input type="email" class="form-control" value="{{ $customer->email }}" disabled style="background:#f5f5f5;">
                    <small class="text-muted">Email cannot be changed</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">Phone Number</label>
                    <input type="text" class="form-control" id="profilePhone" value="{{ $customer->phone ?? '' }}" placeholder="Enter phone number">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">Delivery Address</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="profileAddress" value="{{ $customer->address ?? '' }}" placeholder="Enter delivery address">
                        <button class="btn btn-outline-secondary" type="button" onclick="detectLocation()" title="Auto-detect location">
                            <i class="fas fa-location-dot"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for lat/lng -->
            <input type="hidden" id="profileLat" value="{{ $customer->latitude ?? '' }}">
            <input type="hidden" id="profileLng" value="{{ $customer->longitude ?? '' }}">

            <!-- Map -->
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label fw-bold" style="font-size:13px;margin:0;">
                        <i class="fas fa-map me-1"></i> Location Map
                    </label>
                    <button type="button" class="detect-btn" onclick="detectLocation()" id="detectBtn">
                        <i class="fas fa-location-dot me-1"></i> Detect Location
                    </button>
                </div>
                <div class="map-container">
                    <div id="profileMap"></div>
                </div>
                <div id="locationStatus" class="mt-2" style="font-size:13px;color:#999;">
                    @if($customer->address)
                        <div class="saved-location">
                            <i class="fas fa-check-circle"></i> Location saved: {{ $customer->address }}
                        </div>
                    @else
                        <i class="fas fa-info-circle me-1"></i> Click "Detect Location" to set your address
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-brown" id="saveProfileBtn">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
                <span id="profileMessage" class="ms-2" style="font-size:14px;"></span>
            </div>
        </form>
    </div>

    <!-- Quick Stats -->
    <div class="row g-2">
        <div class="col-4">
            <div class="profile-card" style="padding:15px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#6F4E37;">{{ $customer->sales()->count() }}</div>
                <div style="font-size:12px;color:#999;">Total Orders</div>
            </div>
        </div>
        <div class="col-4">
            <div class="profile-card" style="padding:15px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#6F4E37;">₱{{ number_format($customer->sales()->sum('total_amount') ?? 0, 0) }}</div>
                <div style="font-size:12px;color:#999;">Total Spent</div>
            </div>
        </div>
        <div class="col-4">
            <div class="profile-card" style="padding:15px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#6F4E37;">{{ $customer->loyalty_points ?? 0 }}</div>
                <div style="font-size:12px;color:#999;">Loyalty Points</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Leaflet CSS & JS (FREE - No API Key) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let profileMap = null;
    let profileMarker = null;
    let mapInitialized = false;
    let detecting = false;

    // Initialize Map
    function initProfileMap(lat, lng) {
        const mapContainer = document.getElementById('profileMap');
        if (!mapContainer) return;

        if (!profileMap) {
            const defaultCenter = lat && lng ? [lat, lng] : [14.5995, 120.9842];
            
            profileMap = L.map('profileMap').setView(defaultCenter, 14);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(profileMap);

            mapInitialized = true;
        }

        // Add marker if lat/lng exists
        if (lat && lng) {
            updateMapMarker(lat, lng);
        }
    }

    function updateMapMarker(lat, lng) {
        if (!profileMap || !mapInitialized) return;

        if (profileMarker) {
            profileMap.removeLayer(profileMarker);
        }

        const position = [lat, lng];
        
        // Custom marker
        const homeIcon = L.divIcon({
            html: '🏠',
            className: 'home-marker',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        profileMarker = L.marker(position, { icon: homeIcon })
            .addTo(profileMap)
            .bindPopup('<b>Your Location</b><br>📍 ' + (document.getElementById('profileAddress').value || 'Saved address'));

        profileMap.setView(position, 15);
    }

    // Detect Location
    function detectLocation() {
        if (detecting) return;
        
        if (!navigator.geolocation) {
            document.getElementById('locationStatus').innerHTML = '<span style="color:#dc3545;">❌ Geolocation not supported</span>';
            return;
        }

        const btn = document.getElementById('detectBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Detecting...';
        detecting = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('profileLat').value = lat;
                document.getElementById('profileLng').value = lng;
                
                // Get address from coordinates (reverse geocoding)
                fetchAddressFromCoords(lat, lng);
                
                // Update map
                initProfileMap(lat, lng);
                updateMapMarker(lat, lng);
                
                document.getElementById('locationStatus').innerHTML = `
                    <div class="saved-location">
                        <i class="fas fa-check-circle"></i> Location detected! Click Save to update.
                    </div>
                `;
                
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-location-dot me-1"></i> Detect Location';
                detecting = false;
            },
            function(error) {
                let msg = 'Unable to get location. ';
                if (error.code === 1) msg += 'Please allow location access.';
                else if (error.code === 2) msg += 'Location unavailable.';
                else msg += 'Please try again.';
                
                document.getElementById('locationStatus').innerHTML = `<span style="color:#dc3545;">❌ ${msg}</span>`;
                
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-location-dot me-1"></i> Detect Location';
                detecting = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // Reverse Geocoding using OpenStreetMap Nominatim (FREE)
    function fetchAddressFromCoords(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('profileAddress').value = data.display_name;
                } else {
                    document.getElementById('profileAddress').value = `${lat}, ${lng}`;
                }
            })
            .catch(() => {
                document.getElementById('profileAddress').value = `${lat}, ${lng}`;
            });
    }

    // Update Profile
    function updateProfile(event) {
        event.preventDefault();
        
        const btn = document.getElementById('saveProfileBtn');
        const message = document.getElementById('profileMessage');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
        message.innerHTML = '';

        const data = {
            name: document.getElementById('profileName').value,
            phone: document.getElementById('profilePhone').value,
            address: document.getElementById('profileAddress').value,
            latitude: document.getElementById('profileLat').value || null,
            longitude: document.getElementById('profileLng').value || null
        };

        fetch('{{ route("customer.update-profile") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Changes';
            
            if (result.success) {
                message.innerHTML = '<span style="color:#28a745;">✅ ' + result.message + '</span>';
                
                // Update avatar
                const firstLetter = result.customer.name.charAt(0).toUpperCase();
                document.querySelector('.profile-avatar').textContent = firstLetter;
                document.querySelector('.profile-name').textContent = result.customer.name;
                
                // Update location status
                if (result.customer.address) {
                    document.getElementById('locationStatus').innerHTML = `
                        <div class="saved-location">
                            <i class="fas fa-check-circle"></i> Location saved: ${result.customer.address}
                        </div>
                    `;
                }
            } else {
                message.innerHTML = '<span style="color:#dc3545;">❌ ' + result.message + '</span>';
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Changes';
            message.innerHTML = '<span style="color:#dc3545;">❌ Error saving profile</span>';
            console.error('Error:', error);
        });
    }

    // Load saved location on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedLat = document.getElementById('profileLat').value;
        const savedLng = document.getElementById('profileLng').value;
        
        if (savedLat && savedLng) {
            initProfileMap(parseFloat(savedLat), parseFloat(savedLng));
        } else {
            initProfileMap(null, null);
        }
    });
</script>
@endpush
@endsection