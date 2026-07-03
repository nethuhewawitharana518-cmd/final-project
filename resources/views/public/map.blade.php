@extends('layouts.app')

@section('title', 'Food Rescue Map — Trincomalee')
@section('meta_description', 'Find surplus food deals near you in Trincomalee. Interactive map showing all partner hotels, restaurants, bakeries and cafes with live food deals.')

@push('styles')
<style>
    .map-page-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        min-height: 600px;
    }
    .map-hero-bar {
        background: linear-gradient(135deg, #1e1e1e 0%, #121212 100%);
        border-bottom: 1px solid #2d2d2d;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .map-hero-bar h1 {
        color: #fff;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .map-hero-bar p {
        color: rgba(255,255,255,0.6);
        font-size: 0.82rem;
        margin: 2px 0 0;
    }
    .map-stat-chip {
        background: rgba(255,107,0,0.1);
        border: 1px solid rgba(255,107,0,0.2);
        border-radius: 50px;
        padding: 6px 16px;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .map-stat-chip .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #ff6b00;
        box-shadow: 0 0 6px #ff6b00;
        animation: pulse-dot 1.5s infinite;
    }
    @keyframes pulse-dot {
        0%,100% { box-shadow: 0 0 4px #ff6b00; transform: scale(1); }
        50%      { box-shadow: 0 0 10px #ff6b00; transform: scale(1.25); }
    }

    .map-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    .map-sidebar {
        width: 320px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        background: #1e1e1e;
        border-right: 1px solid #2d2d2d;
        overflow: hidden;
        z-index: 10;
    }
    .sidebar-search-wrap {
        padding: 14px;
        border-bottom: 1px solid #2d2d2d;
        background: #181818;
    }
    .sidebar-search-wrap input {
        width: 100%;
        border: 1px solid #2d2d2d;
        background-color: #252525;
        color: #fff;
        border-radius: 8px;
        padding: 9px 14px 9px 36px;
        font-size: 0.85rem;
        outline: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23ff6b00' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 11px center;
        transition: border-color .2s;
    }
    .sidebar-search-wrap input:focus { border-color: #ff6b00; }
    
    .sidebar-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 0;
    }
    .biz-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid #2d2d2d;
        transition: background .15s;
    }
    .biz-item:hover, .biz-item.active-item {
        background: rgba(255, 107, 0, 0.05);
    }
    .biz-item.active-item { border-left: 3px solid #ff6b00; }
    
    .biz-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        background-color: #252525;
    }
    .biz-name { font-size: 0.85rem; font-weight: 700; color: #FFF; margin: 0 0 2px; }
    .biz-addr { font-size: 0.75rem; color: #888; margin: 0; line-height: 1.4; }
    
    .deals-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: 700;
        margin-top: 4px;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    #map {
        flex: 1;
        z-index: 1;
        background-color: #121212;
        height: 450px !important;
        min-height: 450px !important;
    }

    /* ── Custom Dark Leaflet popup ── */
    .leaflet-popup-content-wrapper {
        background: #1e1e1e !important;
        border: 1px solid #2d2d2d !important;
        color: #e0e0e0 !important;
        border-radius: 14px !important;
        box-shadow: 0 8px 30px rgba(0,0,0,0.5) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 0 !important; width: 240px !important; }
    .leaflet-popup-tip {
        background: #1e1e1e !important;
        border: 1px solid #2d2d2d !important;
    }
    .popup-card { padding: 12px; text-align: center; }
    .popup-title { font-size: 14px; font-weight: 700; color: #FFF; margin-bottom: 5px; }
    .popup-status { font-size: 11px; color: #10b981; font-weight: 800; text-transform: uppercase; display: inline-block; margin-bottom: 8px; }
    .popup-address { font-size: 11px; color: #aaa; margin: 0; }

    @media (max-width: 768px) {
        .map-body { flex-direction: column; }
        .map-sidebar {
            width: 100%;
            max-height: 220px;
            flex-direction: row;
            overflow: hidden;
        }
        .sidebar-list { flex: 1; overflow-x: auto; display: flex; flex-direction: row; padding: 4px; }
        .biz-item { min-width: 200px; border-bottom: none; border-right: 1px solid #2d2d2d; }
        #map { min-height: 450px; }
    }

    .sidebar-empty { text-align: center; padding: 40px 20px; color: #666; }
    .fr-footer { display: none !important; }
</style>
@endpush

@section('content')
@php
    $merchants = \App\Models\Business::approved()->get();
@endphp
<div class="map-page-wrapper">
    {{-- Top hero bar --}}
    <div class="map-hero-bar">
        <div style="flex:1; min-width: 200px;">
            <h1><i class="fa fa-map-location-dot me-2" style="color: var(--dash-orange);"></i>FoodRescue Live Map</h1>
            <p>Real-time partner business locations in Trincomalee</p>
        </div>
        <div class="map-stat-chip">
            <span class="dot"></span>
            {{ $merchants->count() }} Partner{{ $merchants->count() != 1 ? 's' : '' }} on Map
        </div>
        <a href="{{ route('food.browse') }}" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size:.8rem; border-color:rgba(255,255,255,.3);">
            <i class="fa fa-list me-1"></i>Browse Deals
        </a>
    </div>

    <div class="map-body">
        {{-- Sidebar --}}
        <div class="map-sidebar" id="mapSidebar">
            <div class="sidebar-search-wrap">
                <input type="text" id="bizSearch" placeholder="Search businesses..." />
            </div>
            <div class="sidebar-list" id="bizList">
                @forelse($merchants as $merchant)
                    <div class="biz-item" id="item-{{ $merchant->id }}" onclick="focusBusiness({{ $merchant->id }}, {{ $merchant->latitude }}, {{ $merchant->longitude }})">
                        <div class="biz-icon">
                            @if($merchant->business_type == 'hotel') 🏨
                            @elseif($merchant->business_type == 'restaurant') 🍽️
                            @elseif($merchant->business_type == 'bakery') 🥐
                            @elseif($merchant->business_type == 'cafe') ☕
                            @else 🛒 @endif
                        </div>
                        <div style="min-width:0;">
                            <p class="biz-name">{{ $merchant->business_name }}</p>
                            <p class="biz-addr">{{ $merchant->address }}</p>
                            <span class="deals-pill">🟢 Surplus Food Available</span>
                        </div>
                    </div>
                @empty
                    <div class="sidebar-empty">
                        <i class="fa fa-store-slash fa-2x mb-2"></i>
                        <p class="mb-0">No active businesses found</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Google Map --}}
        <div id="map"></div>
@endsection

@push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry,places&callback=initMap" async defer></script>

<script>
let map;
let markerMap = {};
let activeInfoWindow = null;

const TRINCO = { lat: 8.5874, lng: 81.2152 };

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: TRINCO,
        zoom: 13,
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

    const TYPE_CONFIG = {
        hotel:       { emoji: '🏨', color: '#f59e0b' },
        restaurant:  { emoji: '🍽️', color: '#ef4444' },
        bakery:      { emoji: '🥐', color: '#f97316' },
        cafe:        { emoji: '☕', color: '#8b5cf6' },
        supermarket: { emoji: '🛒', color: '#10b981' },
    };

    const merchants = @json($merchants);

    merchants.forEach((merchant) => {
        if (merchant.latitude && merchant.longitude) {
            const pos = { lat: parseFloat(merchant.latitude), lng: parseFloat(merchant.longitude) };
            const cfg = TYPE_CONFIG[merchant.business_type] || { emoji: '📍', color: '#ff6b00' };

            const marker = new google.maps.Marker({
                position: pos,
                map: map,
                title: merchant.business_name,
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                label: {
                    text: cfg.emoji,
                    fontSize: '16px'
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="color: #000; font-family: sans-serif; padding: 4px; max-width: 200px;">
                        <h6 style="margin: 0 0 4px; font-weight: bold; font-size: 13px;">${escHtml(merchant.business_name)}</h6>
                        <span style="font-size: 10px; color: #059669; font-weight: bold; text-transform: uppercase;">🟢 Surplus Food Available</span>
                        <p style="margin: 4px 0 0; font-size: 11px; color: #666; line-height: 1.3;">${escHtml(merchant.address)}</p>
                    </div>
                `
            });

            marker.addListener('click', () => {
                if (activeInfoWindow) activeInfoWindow.close();
                infoWindow.open(map, marker);
                activeInfoWindow = infoWindow;
                highlightItem(merchant.id);
            });

            markerMap[merchant.id] = { marker, infoWindow, pos };
        }
    });

    const bizSearch = document.getElementById('bizSearch');
    if (bizSearch) {
        bizSearch.addEventListener('input', e => {
            const search = e.target.value.toLowerCase();
            merchants.forEach(m => {
                const el = document.getElementById('item-' + m.id);
                const match = m.business_name.toLowerCase().includes(search) || m.address.toLowerCase().includes(search);
                if (el) el.style.display = match ? 'flex' : 'none';
                if (markerMap[m.id]) {
                    markerMap[m.id].marker.setMap(match ? map : null);
                }
            });
        });
    }
}

function focusBusiness(id, lat, lng) {
    if (lat && lng) {
        const pos = { lat: parseFloat(lat), lng: parseFloat(lng) };
        map.panTo(pos);
        map.setZoom(16);
        if (markerMap[id]) {
            if (activeInfoWindow) activeInfoWindow.close();
            markerMap[id].infoWindow.open(map, markerMap[id].marker);
            activeInfoWindow = markerMap[id].infoWindow;
        }
        highlightItem(id);
    }
}

function highlightItem(id) {
    document.querySelectorAll('.biz-item').forEach(el => el.classList.remove('active-item'));
    const el = document.getElementById('item-' + id);
    if (el) {
        el.classList.add('active-item');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
// ─── HTML escape helper ─────────────────────────────────────────────────────
function escHtml(s) {
    return String(s || '').replace(/[&<>"']/g, c =>
        ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[c]);
}

</script>
@endpush
