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
        transition: 0.2s;
    }
    .profile-card:hover {
        border-color: #6F4E37;
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
        position: relative;
    }
    .profile-card .profile-avatar .birthday-crown {
        position: absolute;
        top: -8px;
        right: -8px;
        font-size: 20px;
        background: #ffd700;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: none;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
    }
    .profile-card .profile-avatar .birthday-crown.show {
        display: flex;
        animation: crownPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes crownPop {
        0% { transform: scale(0) rotate(-30deg); }
        60% { transform: scale(1.2) rotate(5deg); }
        100% { transform: scale(1) rotate(0deg); }
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
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .info-badge.points {
        background: #d4edda;
        color: #155724;
    }
    .info-badge.birthday {
        background: #fff3cd;
        color: #856404;
        animation: pulse-birthday 2s infinite;
    }
    
    @keyframes pulse-birthday {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
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
    
    .birthday-banner {
        background: linear-gradient(135deg, #ffd700, #f59e0b);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: none;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        animation: slideDown 0.6s ease;
    }
    
    .birthday-banner.show {
        display: flex;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .birthday-banner .banner-icon {
        font-size: 40px;
        flex-shrink: 0;
        animation: bounce-cake 2s infinite;
    }
    
    @keyframes bounce-cake {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-6px) rotate(3deg); }
    }
    
    .birthday-banner .banner-content {
        flex: 1;
    }
    
    .birthday-banner .banner-content .banner-title {
        font-weight: 700;
        font-size: 18px;
        color: #7c5a00;
    }
    
    .birthday-banner .banner-content .banner-message {
        font-size: 14px;
        color: #7c5a00;
        opacity: 0.9;
    }
    
    .birthday-banner .banner-badge {
        background: #7c5a00;
        color: #ffd700;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 13px;
        white-space: nowrap;
    }
    
    .qr-code-section {
        background: #f8f6f4;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 16px;
        border: 1px dashed #6F4E37;
    }
    
    .qr-code-section .qr-placeholder {
        width: 120px;
        height: 120px;
        background: white;
        border-radius: 8px;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #6F4E37;
        border: 1px solid #e8e8e8;
    }
    
    .qr-code-section .qr-label {
        font-size: 12px;
        color: #999;
    }
    
    @media (max-width: 576px) {
        .profile-card .profile-header {
            flex-direction: column;
            text-align: center;
        }
        .map-container { height: 200px; }
        .birthday-banner {
            flex-direction: column;
            text-align: center;
            padding: 14px 16px;
        }
        .birthday-banner .banner-icon { font-size: 32px; }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-circle me-2"></i>My Profile</h2>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Birthday Bonus Banner -->
    @php
        $isBirthday = false;
        $birthdayDate = null;
        if ($customer->birthday) {
            $birthdayDate = \Carbon\Carbon::parse($customer->birthday);
            $today = \Carbon\Carbon::today();
            $isBirthday = $birthdayDate->month == $today->month && $birthdayDate->day == $today->day;
        }
    @endphp
    
    @if($isBirthday)
    <div class="birthday-banner show" id="birthdayBanner">
        <div class="banner-icon">🎂</div>
        <div class="banner-content">
            <div class="banner-title">🎉 Happy Birthday, {{ $customer->name }}!</div>
            <div class="banner-message">
                You've earned <strong>🎁 DOUBLE POINTS</strong> on all your purchases today!
            </div>
        </div>
        <div class="banner-badge">
            <i class="fas fa-star"></i> 2x Points
        </div>
    </div>
    @endif

    <!-- Profile Information -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar" id="profileAvatar">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
                <div class="birthday-crown {{ $isBirthday ? 'show' : '' }}" id="birthdayCrown">
                    👑
                </div>
            </div>
            <div>
                <div class="profile-name" id="profileNameDisplay">{{ $customer->name }}</div>
                <div class="profile-code">
                    <i class="fas fa-id-card me-1"></i> {{ $customer->customer_code }}
                </div>
                <div class="mt-1">
                    <span class="info-badge points">
                        <i class="fas fa-star"></i> {{ $customer->loyalty_points ?? 0 }} points
                    </span>
                    @if($isBirthday)
                    <span class="info-badge birthday ms-1" id="birthdayBadge">
                        <i class="fas fa-gift"></i> 🎂 Birthday Bonus Active!
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <form id="profileForm" onsubmit="updateProfile(event)">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">
                        <i class="fas fa-user me-1"></i> Full Name
                    </label>
                    <input type="text" class="form-control" id="profileName" value="{{ $customer->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">
                        <i class="fas fa-envelope me-1"></i> Email
                    </label>
                    <input type="email" class="form-control" value="{{ $customer->email }}" disabled style="background:#f5f5f5;">
                    <small class="text-muted">Email cannot be changed</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">
                        <i class="fas fa-phone me-1"></i> Phone Number
                    </label>
                    <input type="text" class="form-control" id="profilePhone" value="{{ $customer->phone ?? '' }}" placeholder="Enter phone number">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:13px;">
                        <i class="fas fa-birthday-cake me-1"></i> Birthday
                        <span class="text-muted" style="font-weight:400;font-size:11px;">
                            (Earn double points on your birthday!)
                        </span>
                    </label>
                    <input type="date" class="form-control" id="profileBirthday" 
                           value="{{ $customer->birthday ? \Carbon\Carbon::parse($customer->birthday)->format('Y-m-d') : '' }}">
                    @if($isBirthday)
                        <small class="text-success">
                            <i class="fas fa-gift"></i> 🎂 Happy Birthday! Double points today!
                        </small>
                    @endif
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold" style="font-size:13px;">
                        <i class="fas fa-map-pin me-1"></i> Delivery Address
                    </label>
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

            <!-- QR Code Section -->
            <div class="qr-code-section">
                <div class="qr-placeholder">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="qr-label">
                    <i class="fas fa-id-card"></i> Scan this QR code for quick checkout
                </div>
                <div style="font-size:11px;color:#999;margin-top:4px;">
                    Member Code: <strong>{{ $customer->customer_code }}</strong>
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
            const status = document.getElementById('locationStatus');
            if (status) {
                status.innerHTML = '<span style="color:#dc3545;">❌ Geolocation not supported</span>';
            }
            return;
        }

        const btn = document.getElementById('detectBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Detecting...';
        }
        detecting = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                const latField = document.getElementById('profileLat');
                const lngField = document.getElementById('profileLng');
                if (latField) latField.value = lat;
                if (lngField) lngField.value = lng;
                
                // Get address from coordinates (reverse geocoding)
                fetchAddressFromCoords(lat, lng);
                
                // Update map
                initProfileMap(lat, lng);
                updateMapMarker(lat, lng);
                
                const status = document.getElementById('locationStatus');
                if (status) {
                    status.innerHTML = `
                        <div class="saved-location">
                            <i class="fas fa-check-circle"></i> Location detected! Click Save to update.
                        </div>
                    `;
                }
                
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-dot me-1"></i> Detect Location';
                }
                detecting = false;
            },
            function(error) {
                let msg = 'Unable to get location. ';
                if (error.code === 1) msg += 'Please allow location access.';
                else if (error.code === 2) msg += 'Location unavailable.';
                else msg += 'Please try again.';
                
                const status = document.getElementById('locationStatus');
                if (status) {
                    status.innerHTML = `<span style="color:#dc3545;">❌ ${msg}</span>`;
                }
                
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-dot me-1"></i> Detect Location';
                }
                detecting = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // Reverse Geocoding
    function fetchAddressFromCoords(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                const addressField = document.getElementById('profileAddress');
                if (addressField) {
                    if (data && data.display_name) {
                        addressField.value = data.display_name;
                    } else {
                        addressField.value = `${lat}, ${lng}`;
                    }
                }
            })
            .catch(() => {
                const addressField = document.getElementById('profileAddress');
                if (addressField) {
                    addressField.value = `${lat}, ${lng}`;
                }
            });
    }

    // Update Profile
    function updateProfile(event) {
        event.preventDefault();
        
        const btn = document.getElementById('saveProfileBtn');
        const message = document.getElementById('profileMessage');
        
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
        }
        if (message) {
            message.innerHTML = '';
        }

        const nameField = document.getElementById('profileName');
        const phoneField = document.getElementById('profilePhone');
        const birthdayField = document.getElementById('profileBirthday');
        const addressField = document.getElementById('profileAddress');
        const latField = document.getElementById('profileLat');
        const lngField = document.getElementById('profileLng');

        const data = {
            name: nameField ? nameField.value : '',
            phone: phoneField ? phoneField.value : '',
            birthday: birthdayField ? birthdayField.value : null,
            address: addressField ? addressField.value : '',
            latitude: latField ? latField.value || null : null,
            longitude: lngField ? lngField.value || null : null
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
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Changes';
            }
            
            if (result.success) {
                if (message) {
                    message.innerHTML = '<span style="color:#28a745;"> ' + result.message + '</span>';
                }
                
                // Update avatar
                const firstLetter = result.customer.name.charAt(0).toUpperCase();
                const avatar = document.getElementById('profileAvatar');
                if (avatar) {
                    avatar.textContent = firstLetter;
                    // Re-add the crown element if it exists
                    const crown = document.createElement('div');
                    crown.className = 'birthday-crown';
                    crown.id = 'birthdayCrown';
                    crown.textContent = '👑';
                    avatar.appendChild(crown);
                }
                
                // Update name display
                const nameDisplay = document.getElementById('profileNameDisplay');
                if (nameDisplay) {
                    nameDisplay.textContent = result.customer.name;
                }
                
                // Check if birthday is today and show banner
                if (result.customer.birthday) {
                    const today = new Date();
                    const birthday = new Date(result.customer.birthday);
                    const isBirthdayToday = birthday.getMonth() === today.getMonth() && 
                                          birthday.getDate() === today.getDate();
                    
                    const banner = document.getElementById('birthdayBanner');
                    const crown = document.getElementById('birthdayCrown');
                    const badge = document.getElementById('birthdayBadge');
                    
                    if (banner) {
                        if (isBirthdayToday) {
                            banner.classList.add('show');
                        } else {
                            banner.classList.remove('show');
                        }
                    }
                    
                    if (crown) {
                        if (isBirthdayToday) {
                            crown.classList.add('show');
                        } else {
                            crown.classList.remove('show');
                        }
                    }
                    
                    if (badge) {
                        if (isBirthdayToday) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
                
                // Update location status
                if (result.customer.address) {
                    const status = document.getElementById('locationStatus');
                    if (status) {
                        status.innerHTML = `
                            <div class="saved-location">
                                <i class="fas fa-check-circle"></i> Location saved: ${result.customer.address}
                            </div>
                        `;
                    }
                }
            } else {
                if (message) {
                    message.innerHTML = '<span style="color:#dc3545;">❌ ' + result.message + '</span>';
                }
            }
        })
        .catch(error => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Changes';
            }
            if (message) {
                message.innerHTML = '<span style="color:#dc3545;">❌ Error saving profile</span>';
            }
            console.error('Error:', error);
        });
    }

    // Load saved location on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedLat = document.getElementById('profileLat')?.value;
        const savedLng = document.getElementById('profileLng')?.value;
        
        if (savedLat && savedLng) {
            initProfileMap(parseFloat(savedLat), parseFloat(savedLng));
        } else {
            initProfileMap(null, null);
        }
    });
</script>
@endpush
@endsection