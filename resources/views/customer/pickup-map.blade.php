@extends('layouts.app')

@section('title', 'Self Pick-up Route — ' . $vendorName)
@section('meta_description', 'Navigate to ' . $vendorName . ' for your self pick-up order. Live routing and turn-by-turn directions powered by OpenStreetMap.')

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
{{-- Leaflet Routing Machine CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<style>
    /* ── Page Shell ───────────────────────────────────────── */
    html, body { height: 100%; }
    .pickup-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        overflow: hidden;
    }

    /* ── Top Hero Bar ─────────────────────────────────────── */
    .pickup-hero {
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        border-bottom: 1px solid #2d2d2d;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }
    .pickup-hero h1 { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0; }
    .pickup-hero p  { color: rgba(255,255,255,0.55); font-size: 0.8rem; margin: 3px 0 0; }

    .pup-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16,185,129,0.12);
        border: 1px solid rgba(16,185,129,0.25);
        border-radius: 50px;
        padding: 5px 14px;
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .pup-chip .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 6px #10b981;
        animation: blink 1.4s ease-in-out infinite;
    }
    @keyframes blink {
        0%,100% { opacity: 1; transform: scale(1); }
        50%      { opacity: 0.4; transform: scale(0.8); }
    }

    /* ── Body Split: Map + Panel ──────────────────────────── */
    .pickup-body {
        display: flex;
        flex: 1 1 0;
        min-height: 0;
        overflow: hidden;
    }

    /* ── Side Panel ───────────────────────────────────────── */
    .pickup-panel {
        width: 340px;
        min-width: 280px;
        background: #111;
        border-right: 1px solid #222;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        flex-shrink: 0;
    }
    .panel-section {
        padding: 18px 20px;
        border-bottom: 1px solid #1f1f1f;
    }
    .panel-section h3 {
        color: #10b981;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0 0 12px;
        font-weight: 700;
    }
    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
    }
    .info-row i { color: #10b981; width: 16px; margin-top: 2px; flex-shrink: 0; }
    .info-row span { color: #ccc; font-size: 0.85rem; line-height: 1.45; }
    .info-row span strong { color: #fff; display: block; font-size: 0.9rem; }

    /* Stats Cards */
    .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat-card {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 10px;
        padding: 12px 14px;
        text-align: center;
    }
    .stat-card .val {
        font-size: 1.3rem;
        font-weight: 700;
        color: #10b981;
        display: block;
        line-height: 1.1;
    }
    .stat-card .lbl {
        font-size: 0.72rem;
        color: rgba(255,255,255,0.45);
        margin-top: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .stat-card.loading .val { color: #444; font-size: 0.85rem; }

    /* Status Banner */
    .status-banner {
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
    }
    .status-banner.locating {
        background: rgba(234,179,8,0.12);
        border: 1px solid rgba(234,179,8,0.25);
        color: #fbbf24;
    }
    .status-banner.routing {
        background: rgba(59,130,246,0.12);
        border: 1px solid rgba(59,130,246,0.25);
        color: #60a5fa;
    }
    .status-banner.ready {
        background: rgba(16,185,129,0.12);
        border: 1px solid rgba(16,185,129,0.25);
        color: #6ee7b7;
    }
    .status-banner.error {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.25);
        color: #fca5a5;
    }

    /* Directions List */
    #directionsList {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    #directionsList li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px solid #1e1e1e;
        font-size: 0.81rem;
        color: #bbb;
        line-height: 1.4;
    }
    #directionsList li:last-child { border-bottom: none; }
    #directionsList li .step-num {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #10b981;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .no-directions {
        color: rgba(255,255,255,0.3);
        font-size: 0.8rem;
        text-align: center;
        padding: 16px 0;
    }

    /* ── Map Canvas ───────────────────────────────────────── */
    .pickup-map-container {
        flex: 1 1 0;
        position: relative;
        min-width: 0;
        overflow: hidden;
    }
    #pickupMap {
        position: absolute;
        inset: 0;          /* top:0; right:0; bottom:0; left:0 */
        width: 100%;
        height: 100%;
    }

    /* Map Overlay Cards */
    .map-fab {
        position: absolute;
        z-index: 999;
        background: rgba(10,10,10,0.92);
        backdrop-filter: blur(8px);
        border: 1px solid #2d2d2d;
        border-radius: 12px;
        padding: 10px 14px;
        color: #fff;
        font-size: 0.8rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    }
    .map-fab.top-right  { top: 14px; right: 14px; }
    .map-fab.bot-left   { bottom: 26px; left: 14px; }
    .map-fab span { color: #10b981; font-weight: 700; font-size: 0.88rem; }

    /* ── Leaflet Routing Machine Overrides ────────────────── */
    .leaflet-routing-container { display: none !important; } /* We render steps ourselves */
    .leaflet-routing-alt { display: none !important; }

    /* Custom marker labels */
    .custom-marker-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.45);
    }

    /* ── Responsive ───────────────────────────────────────── */
    @media (max-width: 767px) {
        .pickup-wrapper { height: 100svh; }
        .pickup-body { flex-direction: column; overflow: auto; }
        .pickup-panel {
            width: 100%;
            min-width: unset;
            height: 38vh;
            flex-shrink: 0;
            border-right: none;
            border-bottom: 1px solid #222;
        }
        .pickup-map-container { height: 62vh; flex: none; }
        .map-fab.top-right { top: 8px; right: 8px; }
    }
</style>
@endpush

@section('content')
<div class="pickup-wrapper">

    {{-- ── Hero Bar ────────────────────────────────────────── --}}
    <div class="pickup-hero">
        <div>
            <h1><i class="fa fa-route me-2 text-success"></i>Self Pick-up Route</h1>
            <p>Order #{{ $order->reservation_code }} &bull; {{ $vendorName }}</p>
        </div>
        <div class="d-flex gap-2 ms-auto flex-wrap">
            <span class="pup-chip"><span class="dot"></span>Live GPS</span>
            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                <i class="fa fa-arrow-left me-1"></i>Order Details
            </a>
        </div>
    </div>

    {{-- ── Split Layout ─────────────────────────────────────── --}}
    <div class="pickup-body">

        {{-- ── Side Panel ───────────────────────────────────── --}}
        <div class="pickup-panel">

            {{-- Status --}}
            <div class="panel-section">
                <div id="statusBanner" class="status-banner locating">
                    <i class="fa fa-circle-notch fa-spin"></i>
                    <span id="statusText">Acquiring your location…</span>
                </div>
            </div>

            {{-- Travel Stats --}}
            <div class="panel-section">
                <h3>Route Summary</h3>
                <div class="stat-grid">
                    <div class="stat-card loading" id="distCard">
                        <span class="val" id="statDist">—</span>
                        <span class="lbl">Distance</span>
                    </div>
                    <div class="stat-card loading" id="timeCard">
                        <span class="val" id="statTime">—</span>
                        <span class="lbl">Est. Time</span>
                    </div>
                </div>
            </div>

            {{-- Vendor Info --}}
            <div class="panel-section">
                <h3>Vendor / Pick-up Point</h3>
                <div class="info-row">
                    <i class="fa fa-store"></i>
                    <span><strong>{{ $vendorName }}</strong>{{ $vendorAddress }}</span>
                </div>
                @if($vendorPhone)
                <div class="info-row">
                    <i class="fa fa-phone"></i>
                    <span><a href="tel:{{ $vendorPhone }}" style="color:#10b981;">{{ $vendorPhone }}</a></span>
                </div>
                @endif
                @if($pickupTime)
                <div class="info-row">
                    <i class="fa fa-clock"></i>
                    <span><strong>Pick-up Time</strong>{{ $pickupTime }}</span>
                </div>
                @endif
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $vendorLat }},{{ $vendorLng }}"
                   target="_blank" rel="noopener"
                   class="btn btn-success btn-sm w-100 mt-2 rounded-pill">
                    <i class="fa fa-diamond-turn-right me-2"></i>Open in Google Maps
                </a>
            </div>

            {{-- Turn-by-turn Directions --}}
            <div class="panel-section" style="flex: 1;">
                <h3>Turn-by-turn Directions</h3>
                <ul id="directionsList">
                    <li>
                        <span class="no-directions" style="width:100%">
                            Directions will appear once your location is confirmed.
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ── Map Canvas ─────────────────────────────────────── --}}
        <div class="pickup-map-container">
            <div id="pickupMap"></div>

            {{-- Floating Distance Badge --}}
            <div class="map-fab top-right" id="distBadge" style="display:none;">
                <i class="fa fa-road me-2 text-success"></i>
                <span id="badgeDist">—</span> &nbsp;·&nbsp; <span id="badgeTime">—</span>
            </div>

            {{-- You Are Here Badge --}}
            <div class="map-fab bot-left">
                <i class="fa fa-location-crosshairs me-2 text-success"></i>
                <span id="coordDisplay">Detecting GPS…</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
{{-- Leaflet Routing Machine --}}
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
// ── Constants ──────────────────────────────────────────────────────────────────
const VENDOR_LAT  = {{ $vendorLat }};
const VENDOR_LNG  = {{ $vendorLng }};
const VENDOR_NAME = @json($vendorName);

// ── Map Initialisation ─────────────────────────────────────────────────────────
const map = L.map('pickupMap', {
    zoomControl:         true,
    attributionControl:  true,
}).setView([VENDOR_LAT, VENDOR_LNG], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom:     19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

// Force Leaflet to recalculate container size after CSS flex layout settles
setTimeout(() => map.invalidateSize(), 200);

// ── Custom Icons ───────────────────────────────────────────────────────────────
function makeIcon(emoji, bg, size) {
    size = size || 42;
    return L.divIcon({
        html: `<div style="
            width:${size}px;height:${size}px;border-radius:50%;
            background:${bg};border:3px solid #fff;
            display:flex;align-items:center;justify-content:center;
            font-size:${Math.round(size*0.45)}px;
            box-shadow:0 3px 12px rgba(0,0,0,0.5);">
            ${emoji}
        </div>`,
        className: '',
        iconSize:   [size, size],
        iconAnchor: [size/2, size/2],
        popupAnchor:[0, -(size/2+4)],
    });
}

const customerIcon = makeIcon('📍', '#3b82f6', 44);
const vendorIcon   = makeIcon('🍽️', '#10b981', 44);

// ── Vendor Marker (always shown first) ────────────────────────────────────────
const vendorMarker = L.marker([VENDOR_LAT, VENDOR_LNG], { icon: vendorIcon })
    .addTo(map)
    .bindPopup(`
        <div style="font-family:sans-serif;min-width:160px;">
            <p style="font-weight:700;margin:0 0 4px;font-size:13px;">${VENDOR_NAME}</p>
            <span style="font-size:11px;color:#059669;font-weight:600;">🟢 Pick-up Point</span>
            <p style="margin:4px 0 0;font-size:11px;color:#555;">{{ $vendorAddress }}</p>
        </div>
    `, { maxWidth: 220 })
    .openPopup();

// ── State ──────────────────────────────────────────────────────────────────────
let customerMarker = null;
let routeControl   = null;

// ── UI Helpers ─────────────────────────────────────────────────────────────────
function setStatus(type, icon, msg) {
    const el = document.getElementById('statusBanner');
    el.className = `status-banner ${type}`;
    el.innerHTML = `<i class="fa ${icon}"></i><span>${msg}</span>`;
}

function setStats(distKm, durationSec) {
    const km   = distKm >= 1
        ? distKm.toFixed(1) + ' km'
        : Math.round(distKm * 1000) + ' m';
    const mins = Math.ceil(durationSec / 60);
    const time = mins >= 60
        ? `${Math.floor(mins/60)}h ${mins%60}m`
        : `${mins} min`;

    document.getElementById('statDist').textContent  = km;
    document.getElementById('statTime').textContent  = time;
    document.getElementById('badgeDist').textContent = km;
    document.getElementById('badgeTime').textContent = time;
    document.getElementById('distCard').classList.remove('loading');
    document.getElementById('timeCard').classList.remove('loading');
    document.getElementById('distBadge').style.display = '';
}

function renderDirections(steps) {
    const list = document.getElementById('directionsList');
    list.innerHTML = '';
    steps.forEach((step, i) => {
        const li   = document.createElement('li');
        const text = step.text || step.name || 'Continue';
        li.innerHTML = `
            <span class="step-num">${i + 1}</span>
            <span>${text}</span>
        `;
        list.appendChild(li);
    });
    if (steps.length === 0) {
        list.innerHTML = '<li><span class="no-directions" style="width:100%">No directions available.</span></li>';
    }
}

// ── Geolocation + Routing ──────────────────────────────────────────────────────
function startRouting(customerLat, customerLng) {
    setStatus('routing', 'fa-spinner fa-spin', 'Calculating shortest route…');
    document.getElementById('coordDisplay').textContent =
        `${customerLat.toFixed(5)}, ${customerLng.toFixed(5)}`;

    // Place / move customer marker
    if (customerMarker) {
        customerMarker.setLatLng([customerLat, customerLng]);
    } else {
        customerMarker = L.marker([customerLat, customerLng], { icon: customerIcon })
            .addTo(map)
            .bindPopup('<b>📍 You are here</b>', { maxWidth: 160 });
    }

    // Fit both markers in view
    map.fitBounds(
        L.latLngBounds(
            [customerLat, customerLng],
            [VENDOR_LAT,  VENDOR_LNG]
        ),
        { padding: [50, 50], maxZoom: 16 }
    );

    // Remove old route
    if (routeControl) {
        routeControl.remove();
        routeControl = null;
    }

    // Build new route
    routeControl = L.Routing.control({
        waypoints: [
            L.latLng(customerLat, customerLng),
            L.latLng(VENDOR_LAT,  VENDOR_LNG),
        ],
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1',
            profile:    'driving',
        }),
        lineOptions: {
            styles: [
                { color: '#10b981', weight: 5, opacity: 0.9 },
                { color: '#065f46', weight: 8, opacity: 0.25 },
            ],
            extendToWaypoints:  true,
            missingRouteTolerance: 0,
        },
        fitSelectedRoutes: false,
        showAlternatives:  false,
        addWaypoints:      false,
        draggableWaypoints:false,
        createMarker:      () => null,   // We handle markers ourselves
    }).addTo(map);

    routeControl.on('routesfound', function (e) {
        const route = e.routes[0];
        setStats(route.summary.totalDistance / 1000, route.summary.totalTime);
        renderDirections(route.instructions);
        setStatus('ready', 'fa-check-circle', 'Route ready — Head to the pick-up point');
    });

    routeControl.on('routingerror', function (e) {
        setStatus('error', 'fa-triangle-exclamation',
            'Could not calculate route. Check internet connection.');
        console.error('Routing error:', e);
    });
}

// ── Main: Continuous live tracking (not just a one-time fix) ───────────────────
// We re-run the OSRM route calculation as the customer moves, but throttled so we
// don't spam the free public OSRM server (fair-use ~1 req/sec) or Leaflet Routing
// Machine while GPS jitters by a few metres.
let lastRoutedAt  = 0;
let lastRoutedPos = null;
const REROUTE_MIN_INTERVAL_MS = 15000; // re-fetch a fresh route at most every 15s
const REROUTE_MIN_DISTANCE_M  = 40;    // ...and only if the customer actually moved

function haversineMeters(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const toRad = d => d * Math.PI / 180;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a = Math.sin(dLat / 2) ** 2 +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(a));
}

function onLocationUpdate(pos) {
    const { latitude: lat, longitude: lng } = pos.coords;
    const now = Date.now();

    const movedEnough = !lastRoutedPos ||
        haversineMeters(lastRoutedPos.lat, lastRoutedPos.lng, lat, lng) >= REROUTE_MIN_DISTANCE_M;
    const timeElapsed = (now - lastRoutedAt) >= REROUTE_MIN_INTERVAL_MS;

    if (!lastRoutedPos || (movedEnough && timeElapsed)) {
        lastRoutedAt  = now;
        lastRoutedPos = { lat, lng };
        startRouting(lat, lng);
    } else if (customerMarker) {
        // Between full re-routes, still slide the marker itself so it feels live
        customerMarker.setLatLng([lat, lng]);
        document.getElementById('coordDisplay').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    }
}

let geoWatchId = null;

if (!navigator.geolocation) {
    setStatus('error', 'fa-triangle-exclamation',
        'Geolocation is not supported by your browser.');
} else {
    setStatus('locating', 'fa-circle-notch fa-spin', 'Acquiring your live location…');

    geoWatchId = navigator.geolocation.watchPosition(
        onLocationUpdate,
        function onError(err) {
            const msgs = {
                1: 'Location access denied. Please allow location in your browser settings.',
                2: 'Location unavailable. Try moving to an open area.',
                3: 'Location request timed out. Please retry.',
            };
            setStatus('error', 'fa-triangle-exclamation',
                msgs[err.code] || 'Unknown location error.');
        },
        {
            enableHighAccuracy: true,
            timeout:            15000,
            maximumAge:         5000,
        }
    );
}

// Stop watching when the customer navigates away, to save battery/data
window.addEventListener('beforeunload', function () {
    if (geoWatchId !== null) navigator.geolocation.clearWatch(geoWatchId);
});
</script>
@endpush
