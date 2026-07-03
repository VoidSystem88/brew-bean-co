@extends('layouts.customer')

@section('page-title', 'Find a Branch')

@section('content')
<style>
    .map-container {
        height: 400px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        margin-bottom: 20px;
    }
    .map-container #map {
        height: 100%;
        width: 100%;
    }
    .branch-card {
        background: white;
        border-radius: 10px;
        padding: 14px 16px;
        border: 1px solid #e8e8e8;
        margin-bottom: 10px;
        transition: 0.2s;
        cursor: pointer;
    }
    .branch-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .branch-card.active {
        border-color: #6F4E37;
        background: #f8f6f4;
    }
    .branch-card .branch-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }
    .branch-card .branch-address {
        font-size: 13px;
        color: #999;
    }
    .branch-card .branch-distance {
        font-size: 13px;
        color: #28a745;
        font-weight: 600;
    }
    .branch-card .branch-badge {
        font-size: 11px;
        padding: 2px 12px;
        border-radius: 12px;
        background: #6F4E37;
        color: white;
        font-weight: 600;
    }
    .nearest-badge {
        background: #28a745;
        color: white;
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .loading-map {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .loading-map i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        color: #6F4E37;
    }
    .locate-btn {
        background: #6F4E37;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
    }
    .locate-btn:hover {
        background: #5a3d2b;
    }
    .locate-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    @media (max-width: 576px) {
        .map-container { height: 300px; }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-map-marked-alt me-2"></i>Find a Branch</h2>
        <button class="locate-btn" onclick="locateMe()">
            <i class="fas fa-location-dot me-1"></i> Locate Me
        </button>
    </div>

    <!-- Map -->
    <div class="map-container">
        <div id="map">
            <div class="loading-map">
                <i class="fas fa-map"></i>
                <p>Loading map...</p>
                <small class="text-muted">Please enable location services</small>
            </div>
        </div>
    </div>

    <!-- Branches List -->
    <div id="branchesList">
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
            <p class="mt-2 text-muted">Loading branches...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let map;
    let markers = [];
    let userMarker;
    let userLat = null;
    let userLng = null;
    let branches = [];
    let infoWindow;

    function initMap() {
        // Default center (Manila)
        const defaultCenter = { lat: 14.5995, lng: 120.9842 };
        
        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultCenter,
            zoom: 12,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });

        infoWindow = new google.maps.InfoWindow();

        // Try to get user location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    map.setCenter({ lat: userLat, lng: userLng });
                    map.setZoom(13);
                    addUserMarker(userLat, userLng);
                    loadBranches(userLat, userLng);
                },
                function() {
                    loadBranches();
                }
            );
        } else {
            loadBranches();
        }
    }

    function addUserMarker(lat, lng) {
        if (userMarker) {
            userMarker.setMap(null);
        }
        userMarker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#4285F4',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2
            },
            title: 'Your Location'
        });

        infoWindow.setContent('<div style="font-weight:600;padding:4px;">📍 Your Location</div>');
        infoWindow.open(map, userMarker);
    }

    function loadBranches(lat, lng) {
        const url = lat && lng 
            ? `/customer/branches/nearby?lat=${lat}&lng=${lng}`
            : '/customer/branches/nearby';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                branches = data.branches || [];
                renderBranches(branches, data.nearest);
                addBranchMarkers(branches);
            })
            .catch(() => {
                document.getElementById('branchesList').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                        <p class="mt-2 text-muted">Error loading branches. Please try again.</p>
                    </div>
                `;
            });
    }

    function addBranchMarkers(branches) {
        // Clear existing markers
        markers.forEach(marker => marker.setMap(null));
        markers = [];

        branches.forEach((branch, index) => {
            if (branch.latitude && branch.longitude) {
                const position = { 
                    lat: parseFloat(branch.latitude), 
                    lng: parseFloat(branch.longitude) 
                };
                
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: branch.name,
                    label: {
                        text: (index + 1).toString(),
                        color: '#ffffff',
                        fontSize: '12px',
                        fontWeight: 'bold'
                    },
                    icon: {
                        url: 'http://maps.google.com/mapfiles/ms/icons/brown-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                const content = `
                    <div style="padding:8px;max-width:250px;">
                        <div style="font-weight:700;font-size:16px;color:#6F4E37;">
                            ☕ ${branch.name.replace('☕ Brew & Bean Co. - ', '')}
                        </div>
                        <div style="font-size:13px;color:#666;margin:4px 0;">
                            <i class="fas fa-map-pin"></i> ${branch.address || 'No address'}
                        </div>
                        ${branch.distance ? `
                            <div style="font-size:13px;color:#28a745;font-weight:600;">
                                <i class="fas fa-location-arrow"></i> ${branch.distance_text || branch.distance + ' km'}
                            </div>
                        ` : ''}
                        <div style="margin-top:6px;">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${branch.latitude},${branch.longitude}" 
                               target="_blank" 
                               style="background:#6F4E37;color:white;padding:4px 12px;border-radius:6px;text-decoration:none;font-size:12px;">
                                <i class="fas fa-directions"></i> Get Directions
                            </a>
                        </div>
                    </div>
                `;

                marker.addListener('click', function() {
                    infoWindow.setContent(content);
                    infoWindow.open(map, marker);
                    highlightBranch(index);
                });

                markers.push(marker);
            }
        });

        // Fit bounds to show all markers
        if (markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            markers.forEach(marker => {
                bounds.extend(marker.getPosition());
            });
            if (userLat && userLng) {
                bounds.extend({ lat: userLat, lng: userLng });
            }
            map.fitBounds(bounds);
            // Don't zoom too far
            google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                if (this.getZoom() > 15) {
                    this.setZoom(15);
                }
            });
        }
    }

    function renderBranches(branches, nearest) {
        const container = document.getElementById('branchesList');
        
        if (branches.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-store fa-2x text-muted"></i>
                    <p class="mt-2 text-muted">No branches found.</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        branches.forEach((branch, index) => {
            const isNearest = nearest && nearest.id === branch.id;
            const location = branch.name.replace('☕ Brew & Bean Co. - ', '');
            
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="branch-card ${isNearest ? 'active' : ''}" onclick="focusBranch(${index})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="branch-name">${location}</div>
                            ${isNearest ? '<span class="nearest-badge">📍 Nearest</span>' : ''}
                        </div>
                        <div class="branch-address">
                            <i class="fas fa-map-pin me-1"></i> ${branch.address || 'Address not available'}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            ${branch.distance ? `
                                <span class="branch-distance">
                                    <i class="fas fa-location-arrow me-1"></i> ${branch.distance_text || branch.distance + ' km'}
                                </span>
                            ` : ''}
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${branch.latitude},${branch.longitude}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-directions"></i> Directions
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        container.innerHTML = html;
    }

    function focusBranch(index) {
        if (markers[index]) {
            google.maps.event.trigger(markers[index], 'click');
            highlightBranch(index);
            
            // Center map on marker
            map.setCenter(markers[index].getPosition());
            map.setZoom(16);
        }
    }

    function highlightBranch(index) {
        document.querySelectorAll('.branch-card').forEach((card, i) => {
            card.classList.toggle('active', i === index);
        });
    }

    function locateMe() {
        if (navigator.geolocation) {
            document.querySelector('.locate-btn').disabled = true;
            document.querySelector('.locate-btn').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Locating...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    map.setCenter({ lat: userLat, lng: userLng });
                    map.setZoom(14);
                    addUserMarker(userLat, userLng);
                    loadBranches(userLat, userLng);
                    
                    document.querySelector('.locate-btn').disabled = false;
                    document.querySelector('.locate-btn').innerHTML = '<i class="fas fa-location-dot me-1"></i> Locate Me';
                },
                function() {
                    alert('Unable to get location. Please enable location services.');
                    document.querySelector('.locate-btn').disabled = false;
                    document.querySelector('.locate-btn').innerHTML = '<i class="fas fa-location-dot me-1"></i> Locate Me';
                }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Google Maps is loaded
        if (typeof google !== 'undefined' && google.maps) {
            initMap();
        } else {
            // Wait for Google Maps to load
            window.initMap = initMap;
        }
    });
</script>
@endpush
@endsection