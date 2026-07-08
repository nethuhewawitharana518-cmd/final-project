@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="d-flex">
    @include('customer.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Order Details: #{{ $order->reservation_code }}</h4>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-success btn-sm px-3">
                <i class="fa fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="content-area">
            <div class="row g-4">
                {{-- Detail Summary Card --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Surplus Food Reservation</h5>
                                <span class="text-muted small">Placed on: {{ $order->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <span class="status-badge {{ $order->status }} px-3 py-2 fs-6">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        {{-- Partner Business & Fulfillment --}}
                        @php
                            $isDelivery = $order->delivery_method === 'delivery';
                            $deliveryAddress = $isDelivery ? $order->delivery_address : '';
                        @endphp
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-2">Partner Business</h6>
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold mb-1 text-white">{{ $order->business->business_name }}</h6>
                                    <p class="text-muted small mb-0"><i class="fa fa-map-marker-alt me-2 text-success"></i>{{ $order->business->address }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-2">Fulfillment Details</h6>
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    @if($isDelivery)
                                        <h6 class="fw-bold mb-1 text-white"><i class="fa-solid fa-truck text-success me-2"></i>Home Delivery</h6>
                                        <p class="text-muted small mb-0"><strong>Address:</strong> {{ $deliveryAddress }}</p>
                                    @else
                                        <h6 class="fw-bold mb-1 text-white"><i class="fa-solid fa-store text-success me-2"></i>Self Pickup</h6>
                                        <p class="text-muted small mb-2">Please pick up from the store address on the left.</p>
                                        @if($order->business->latitude && $order->business->longitude)
                                            <a href="{{ route('customer.orders.pickup-map', $order->id) }}"
                                               class="btn btn-success btn-sm rounded-pill px-3">
                                                <i class="fa fa-route me-2"></i>Get Directions &amp; Route Map
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Itemized List --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted text-uppercase small mb-2">Reserved Items</h6>
                            <div class="table-responsive border rounded-3" style="border-color: var(--border) !important;">
                                <table class="table mb-0 align-middle" style="background: transparent !important; --bs-table-bg: transparent; --bs-table-color: var(--text);">
                                    <thead style="background: var(--bg-surface-2); border-color: var(--border) !important;">
                                        <tr style="border-color: var(--border) !important;">
                                            <th class="ps-3 text-white">Item</th>
                                            <th class="text-white">Original Price</th>
                                            <th class="text-white">Rescue Price</th>
                                            <th class="text-white">Qty</th>
                                            <th class="text-end pe-3 text-white">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr style="border-color: var(--border) !important;">
                                            <td class="ps-3 fw-semibold text-white">{{ $item->food->name ?? 'Surplus Item' }}</td>
                                            <td class="text-muted text-decoration-line-through">Rs. {{ number_format($item->price * 1.5) }}</td>
                                            <td class="text-success fw-bold">Rs. {{ number_format($item->price) }}</td>
                                            <td class="text-white">{{ $item->quantity }}</td>
                                            <td class="text-end pe-3 fw-bold text-white">Rs. {{ number_format($item->price * $item->quantity) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Total Calculation --}}
                        <div class="d-flex justify-content-end pt-3 border-top">
                            <div class="text-end" style="min-width: 250px;">
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Items Subtotal:</span>
                                    <span class="fw-semibold text-white">Rs. {{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->loyalty_discount > 0)
                                    <div class="d-flex justify-content-between mb-2 small text-muted">
                                        <span>Loyalty Discount:</span>
                                        <span class="fw-semibold text-danger">- Rs. {{ number_format($order->loyalty_discount, 2) }}</span>
                                    </div>
                                @endif
                                @if($isDelivery)
                                    <div class="d-flex justify-content-between mb-2 small text-muted">
                                        <span>Delivery Fee:</span>
                                        <span class="fw-semibold text-white">Rs. {{ number_format($order->delivery_fee, 2) }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between border-top pt-2 fs-5 fw-bold" style="border-color: var(--border) !important;">
                                    <span class="text-white">Total Paid:</span>
                                    <span class="text-success">Rs. {{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Live Route Map with real-time tracking HUD --}}
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-map-location-dot text-success me-2"></i>
                                @if($isDelivery)
                                    Live Delivery Route Map (Home Delivery)
                                @else
                                    Store Pickup Location
                                @endif
                            </h5>
                            @if($isDelivery)
                                <span class="badge bg-success status-badge active">Live Tracking</span>
                            @else
                                <span class="badge bg-success status-badge active">Store Location</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-4 text-start">
                            @if($isDelivery)
                                Track the real-time location of the delivery rider bringing your food from the vendor.
                            @else
                                Below is the pickup location for the store. You can collect your order during store operating hours.
                            @endif
                        </p>
                        
                        <div class="position-relative overflow-hidden rounded-3 border" style="border-color: var(--border) !important;">
                            @if(!$isDelivery)
                            {{-- Start Navigation Overlay for Self-Pickup --}}
                            <div id="start-nav-overlay" class="d-flex flex-column align-items-center justify-content-center position-absolute w-100 h-100 bg-dark bg-opacity-75 z-3 text-center p-4">
                                <i class="fa fa-route fa-3x text-success mb-3 animate-bounce"></i>
                                <h5 class="fw-bold text-white mb-2">Self-Pickup Navigation</h5>
                                <p class="text-muted small mb-4 px-3" style="max-width: 320px;">Use your device's live GPS location to trace the walking or driving route to the store location.</p>
                                <button id="start-nav-btn" class="btn btn-success px-4 py-2 rounded-pill fw-bold">
                                    <i class="fa fa-location-crosshairs me-2"></i>Start Navigation
                                </button>
                            </div>
                            @endif

                            {{-- Turn-by-Turn Instruction Box --}}
                            <div class="nav-instruction-box shadow">
                                <div class="nav-arrow">{{ $isDelivery ? '🛵' : '🚶' }}</div>
                                <div class="nav-text-container text-start">
                                    <div class="nav-dist-text" id="nav-dist-text">Calculating...</div>
                                    <div class="nav-instruction-text" id="nav-instruction-text">Retrieving route data...</div>
                                </div>
                            </div>

                            <div id="routeMap" style="height: 420px; width: 100%;"></div>

                            {{-- Bottom Live Progress HUD --}}
                            <div class="live-tracking-hud shadow">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="hud-item text-start">
                                        <span class="hud-label" id="hud-arrival-label">EST. ARRIVAL</span>
                                        <span class="hud-value" id="hud-arrival">--:--</span>
                                    </div>
                                    <div class="hud-item text-center">
                                        <span class="hud-label" id="hud-time-label">REMAINING TIME</span>
                                        <span class="hud-value text-success" id="hud-time">-- min</span>
                                    </div>
                                    <div class="hud-item text-end">
                                        <span class="hud-label">DISTANCE</span>
                                        <span class="hud-value" id="hud-distance">-- km</span>
                                    </div>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" id="hud-progress-fill" style="width: 0%;"></div>
                                    <div class="progress-vehicle" id="hud-progress-vehicle" style="left: 0%;">{{ $isDelivery ? '🛵' : '🚶' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- QR Verification & Actions Card --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4 text-center">
                        <h5 class="fw-bold mb-3 text-dark">QR Collection Code</h5>
                        <p class="text-muted small mb-4">Present this QR code to the business merchant at pickup to collect your rescued food items.</p>

                        @if($order->qrCode)
                            <div class="d-inline-block p-3 border rounded-3 bg-light mb-4 shadow-sm">
                                <img src="{{ asset($order->qrCode->qr_image_path) }}" alt="Order QR Code" class="img-fluid" style="max-width: 200px; height: 200px;">
                            </div>
                            <div class="mb-4">
                                <span class="badge {{ $order->qrCode->is_used ? 'bg-danger' : 'bg-success' }} py-2 px-3">
                                    {{ $order->qrCode->is_used ? 'Scanned & Used' : 'Active — Pending Scan' }}
                                </span>
                            </div>
                        @else
                            <div class="alert alert-warning py-3 mb-4 small rounded-3">
                                <i class="fa fa-triangle-exclamation mb-2 fs-4 text-warning"></i>
                                <p class="mb-0">No QR Code generated yet. Please contact support.</p>
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.orders.receipt', $order->id) }}" class="btn btn-outline-success">
                                <i class="fa fa-file-pdf me-2"></i>Download Receipt
                            </a>
                            
                            @if($order->status === 'pending')
                                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fa fa-circle-xmark me-2"></i>Cancel Reservation
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom Leaflet Routing Machine overlay hide */
    .leaflet-routing-container {
        display: none !important;
    }
    
    /* Premium Live Tracking HUD Styles */
    .nav-instruction-box {
        position: absolute;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        width: 90%;
        max-width: 360px;
        background: rgba(15, 23, 42, 0.95);
        color: #fff;
        border-radius: 16px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .nav-arrow {
        font-size: 24px;
        background: rgba(255, 255, 255, 0.1);
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nav-text-container {
        display: flex;
        flex-direction: column;
    }
    .nav-dist-text {
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .nav-instruction-text {
        font-size: 0.8rem;
        color: #94a3b8;
    }
    
    .live-tracking-hud {
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        width: 90%;
        max-width: 360px;
        background: rgba(26, 32, 44, 0.95);
        border-radius: 20px;
        padding: 16px 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        border: 1px solid var(--border);
        backdrop-filter: blur(8px);
    }
    
    .hud-item {
        display: flex;
        flex-direction: column;
    }
    .hud-label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: #a0aec0;
    }
    .hud-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #fff;
    }
    
    .progress-track {
        position: relative;
        height: 6px;
        background: var(--border);
        border-radius: 3px;
        margin-top: 12px;
    }
    .progress-fill {
        height: 100%;
        background: #10b981;
        border-radius: 3px;
    }
    .progress-vehicle {
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.1rem;
        transition: left 0.1s linear;
    }
    
    /* Start Navigation Overlay */
    .start-nav-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1100;
        width: 85%;
        max-width: 320px;
        background: rgba(15, 23, 42, 0.95);
        color: #fff;
        border-radius: 20px;
        padding: 20px 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        border: 1px solid var(--border);
        text-align: center;
        backdrop-filter: blur(8px);
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps_key') }}&libraries=geometry,places"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDelivery = {{ $isDelivery ? 'true' : 'false' }};
    const movingEmoji = isDelivery ? '🛵' : '🚗';

    // Restaurant Location
    let restLat = {{ $order->business?->latitude ?? 'null' }};
    let restLng = {{ $order->business?->longitude ?? 'null' }};
    const restAddress = "{{ addslashes($order->business?->address ?? '') }}";
    const restName = "{{ addslashes($order->business?->business_name ?? 'Restaurant') }}";

    if (restLat === null || restLng === null) {
        restLat = 8.5755;
        restLng = 81.2285;
    }

    // Customer Location
    let deliveryAddress = "{{ addslashes($order->delivery_address ?? '') }}";
    if (!deliveryAddress) {
        deliveryAddress = "{{ addslashes(auth()->user()->home_address ?? '') }}";
    }
    let dbUserLat = {{ $order->delivery_latitude ?? 'null' }};
    let dbUserLng = {{ $order->delivery_longitude ?? 'null' }};
    let initialUserLat = dbUserLat;
    let initialUserLng = dbUserLng;

    let map = null;
    let directionsService = null;
    let directionsRenderer = null;
    let restaurantMarker = null;
    let userMarker = null;
    let movingMarker = null;

    function initMap() {
        var centerLatLng = { lat: parseFloat(restLat), lng: parseFloat(restLng) };
        map = new google.maps.Map(document.getElementById('routeMap'), {
            center: centerLatLng,
            zoom: 14,
            styles: [
                {
                    "featureType": "poi.business",
                    "elementType": "labels",
                    "stylers": [
                        { "visibility": "off" }
                    ]
                }
            ]
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#059669',
                strokeWeight: 5,
                strokeOpacity: 0.75
            }
        });

        restaurantMarker = new google.maps.Marker({
            position: centerLatLng,
            map: map,
            title: restName,
            icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });

        startGeocodingAndTracking();
    }

    async function startGeocodingAndTracking() {
        var isDefaultStore = (restLat === 8.5755 && restLng === 81.2285);
        if (isDefaultStore && restAddress) {
            await geocodeAddressHelper(restAddress, true);
        }
        
        // Only geocode in browser if database coordinates are missing
        if ((initialUserLat === null || initialUserLng === null) && deliveryAddress) {
            await geocodeAddressHelper(deliveryAddress, false);
        }
        
        // Final fallback to restaurant coordinates if geocoding fails or is empty
        if (initialUserLat === null || initialUserLng === null) {
            initialUserLat = restLat;
            initialUserLng = restLng;
        }

        initializeTracking();
    }

    function geocodeAddressHelper(addressText, isRestaurant) {
        return new Promise((resolve) => {
            var query = addressText.trim();
            if (!query.toLowerCase().includes("trincomalee")) query += ", Trincomalee";
            if (!query.toLowerCase().includes("sri lanka")) query += ", Sri Lanka";

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: query }, function(results, status) {
                if (status === 'OK' && results && results.length > 0) {
                    var lat = results[0].geometry.location.lat();
                    var lon = results[0].geometry.location.lng();
                    if (isRestaurant) {
                        restLat = lat;
                        restLng = lon;
                        if (restaurantMarker) {
                            restaurantMarker.setPosition({ lat: lat, lng: lon });
                        }
                    } else {
                        initialUserLat = lat;
                        initialUserLng = lon;
                    }
                }
                resolve();
            });
        });
    }

    function initializeTracking() {
        // Center map to bounds
        var bounds = new google.maps.LatLngBounds();
        bounds.extend({ lat: restLat, lng: restLng });
        bounds.extend({ lat: initialUserLat, lng: initialUserLng });
        map.fitBounds(bounds);

        if (isDelivery) {
            runDeliverySimulation(initialUserLat, initialUserLng);
        } else {
            runSelfPickupPreview(initialUserLat, initialUserLng);
        }
    }

    // --- HOME DELIVERY SIMULATION LOOP ---
    function runDeliverySimulation(userLat, userLng) {
        userMarker = new google.maps.Marker({
            position: { lat: userLat, lng: userLng },
            map: map,
            title: "Your Delivery Address",
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
        });

        directionsService.route({
            origin: { lat: restLat, lng: restLng },
            destination: { lat: userLat, lng: userLng },
            travelMode: google.maps.TravelMode.DRIVING
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);

                const route = response.routes[0];
                const summary = route.legs[0];
                const coordinates = route.overview_path;

                movingMarker = new google.maps.Marker({
                    position: coordinates[0],
                    map: map,
                    title: "Rider",
                    label: movingEmoji
                });

                let currentIndex = 0;
                const totalPoints = coordinates.length;
                const animationSpeed = 250;

                function animateStep() {
                    if (currentIndex >= totalPoints) {
                        currentIndex = 0;
                        setTimeout(animateStep, 3000);
                        return;
                    }

                    const point = coordinates[currentIndex];
                    movingMarker.setPosition(point);

                    const progressFraction = currentIndex / (totalPoints - 1 || 1);
                    const progressPercent = Math.min(100, Math.round(progressFraction * 100));

                    document.getElementById('hud-progress-fill').style.width = progressPercent + '%';
                    document.getElementById('hud-progress-vehicle').style.left = progressPercent + '%';

                    const remainingDistanceVal = (summary.distance.value * (1 - progressFraction) / 1000.0).toFixed(2);
                    const remainingTimeVal = Math.round((summary.duration.value * (1 - progressFraction)) / 60.0);

                    document.getElementById('hud-distance').innerText = remainingDistanceVal + ' km';
                    document.getElementById('hud-time').innerText = remainingTimeVal + ' min';

                    const arrivalDate = new Date();
                    arrivalDate.setMinutes(arrivalDate.getMinutes() + remainingTimeVal);
                    document.getElementById('hud-arrival').innerText = arrivalDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    let instruction = "Delivery rider is heading your way";
                    let arrow = "🛵";
                    let distanceVal = Math.round(remainingDistanceVal * 1000);

                    if (progressPercent >= 95) {
                        instruction = "Rider arrived at your address!";
                        arrow = "🎉";
                        distanceVal = 0;
                    } else if (progressPercent >= 75) {
                        instruction = "Approaching customer drop-off";
                        arrow = "📍";
                    } else if (progressPercent >= 45) {
                        instruction = "Turn right onto Inner Harbour Road";
                        arrow = "➡️";
                    } else if (progressPercent >= 20) {
                        instruction = "Turn left onto Nilaveli Road";
                        arrow = "⬅️";
                    }

                    document.getElementById('nav-dist-text').innerText = distanceVal > 0 ? (distanceVal + ' m') : 'Arrived';
                    document.getElementById('nav-instruction-text').innerText = instruction;
                    document.querySelector('.nav-arrow').innerText = arrow;

                    map.panTo(point);

                    currentIndex++;
                    setTimeout(animateStep, animationSpeed);
                }

                animateStep();
            }
        });
    }

    // --- SELF-PICKUP FLOW ---
    let previewRoutingControl = null;

    function runSelfPickupPreview(userLat, userLng) {
        userMarker = new google.maps.Marker({
            position: { lat: userLat, lng: userLng },
            map: map,
            title: "Your Location",
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
        });

        directionsService.route({
            origin: { lat: userLat, lng: userLng },
            destination: { lat: restLat, lng: restLng },
            travelMode: google.maps.TravelMode.WALKING
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);

                const route = response.routes[0];
                const summary = route.legs[0];

                const distKm = (summary.distance.value / 1000.0).toFixed(2);
                const timeMin = Math.round(summary.duration.value / 60.0);

                document.getElementById('hud-distance').innerText = distKm + ' km';
                document.getElementById('hud-time').innerText = timeMin + ' min';

                const arrivalDate = new Date();
                arrivalDate.setMinutes(arrivalDate.getMinutes() + timeMin);
                document.getElementById('hud-arrival').innerText = arrivalDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                document.getElementById('nav-dist-text').innerText = Math.round(summary.distance.value) + ' m';
                document.getElementById('nav-instruction-text').innerText = "Click 'Start Navigation' to trace route";
                document.querySelector('.nav-arrow').innerText = "🗺️";
            }
        });

        document.getElementById('start-nav-btn').addEventListener('click', function() {
            const overlay = document.getElementById('start-nav-overlay');
            if (overlay) {
                overlay.style.opacity = 0;
                setTimeout(() => overlay.remove(), 300);
            }

            startLiveGpsTracking(userLat, userLng);
        });
    }

    let watchId = null;
    let initialDistance = null;
    let userCoordsCount = 0;
    let lastCoords = null;

    function startLiveGpsTracking(startLat, startLng) {
        if (userMarker) userMarker.setMap(null);

        userMarker = new google.maps.Marker({
            position: { lat: startLat, lng: startLng },
            map: map,
            title: "Your Live Position",
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
        });

        if (navigator.geolocation) {
            watchId = navigator.geolocation.watchPosition(
                function(pos) {
                    let liveLat = pos.coords.latitude;
                    let liveLng = pos.coords.longitude;

                    if (lastCoords && lastCoords.lat === liveLat && lastCoords.lng === liveLng) {
                        userCoordsCount++;
                        const stepFraction = Math.min(1, userCoordsCount * 0.08);
                        liveLat = liveLat + (restLat - liveLat) * stepFraction;
                        liveLng = liveLng + (restLng - liveLng) * stepFraction;
                    } else {
                        lastCoords = { lat: liveLat, lng: liveLng };
                        userCoordsCount = 0;
                    }

                    var userLatLng = { lat: liveLat, lng: liveLng };
                    userMarker.setPosition(userLatLng);

                    directionsService.route({
                        origin: userLatLng,
                        destination: { lat: restLat, lng: restLng },
                        travelMode: google.maps.TravelMode.WALKING
                    }, function(response, status) {
                        if (status === 'OK') {
                            directionsRenderer.setDirections(response);

                            const route = response.routes[0];
                            const summary = route.legs[0];

                            if (!initialDistance) {
                                initialDistance = summary.distance.value;
                            }

                            const progressFraction = Math.max(0, Math.min(1, 1 - (summary.distance.value / initialDistance)));
                            const progressPercent = Math.round(progressFraction * 100);

                            document.getElementById('hud-progress-fill').style.width = progressPercent + '%';
                            document.getElementById('hud-progress-vehicle').style.left = progressPercent + '%';

                            const remainingDistanceVal = (summary.distance.value / 1000.0).toFixed(2);
                            const remainingTimeVal = Math.round(summary.duration.value / 60.0);

                            document.getElementById('hud-distance').innerText = remainingDistanceVal + ' km';
                            document.getElementById('hud-time').innerText = remainingTimeVal + ' min';

                            const arrivalDate = new Date();
                            arrivalDate.setMinutes(arrivalDate.getMinutes() + remainingTimeVal);
                            document.getElementById('hud-arrival').innerText = arrivalDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                            let instruction = "Continue toward merchant store";
                            let arrow = "⬆️";
                            let distanceVal = Math.round(summary.distance.value);

                            if (summary.distance.value < 20) {
                                instruction = "Arrived at restaurant! Pick up items.";
                                arrow = "🎉";
                                distanceVal = 0;
                                if (watchId) {
                                    navigator.geolocation.clearWatch(watchId);
                                    watchId = null;
                                }
                            }

                            document.getElementById('nav-dist-text').innerText = distanceVal > 0 ? (distanceVal + ' m') : 'Arrived';
                            document.getElementById('nav-instruction-text').innerText = instruction;
                            document.querySelector('.nav-arrow').innerText = arrow;
                        }
                    });

                    map.panTo(userLatLng);
                },
                function(err) {
                    console.warn("watchPosition failed: ", err);
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        }
    }

    initMap();
});
</script>
@endpush
