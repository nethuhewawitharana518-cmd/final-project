@extends('layouts.app')

@section('title', 'Business Profile Settings')

@push('styles')
<style>
    #locationMap {
        height: 380px;
        width: 100%;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        z-index: 1;
    }
    .profile-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.05);
    }
    .map-helper-badge {
        font-size: 0.78rem;
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .img-logo-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }
    .locate-btn {
        background-color: #059669;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 0.85rem;
        transition: background-color 0.2s;
    }
    .locate-btn:hover {
        background-color: #047857;
    }
</style>
@endpush

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Business Profile</h4>
            <span class="badge bg-success status-badge active">Configure Storefront & GPS Location</span>
        </div>

        <div class="content-area">
            <form action="{{ route('business.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Left Column: General & Store Details --}}
                    <div class="col-lg-6">
                        <div class="card profile-card p-4 h-100">
                            <h5 class="fw-bold text-dark mb-4"><i class="fa fa-store text-success me-2"></i>Business Details</h5>

                            <div class="mb-3">
                                <label for="business_name" class="form-label fw-semibold text-secondary">Business / Hotel Name</label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $business->business_name) }}" required>
                                @error('business_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_type" class="form-label fw-semibold text-secondary">Business Type</label>
                                    <select class="form-select @error('business_type') is-invalid @enderror" id="business_type" name="business_type" required>
                                        <option value="hotel" {{ old('business_type', $business->business_type) == 'hotel' ? 'selected' : '' }}>🏨 Hotel / Resort</option>
                                        <option value="restaurant" {{ old('business_type', $business->business_type) == 'restaurant' ? 'selected' : '' }}>🍽️ Restaurant</option>
                                        <option value="bakery" {{ old('business_type', $business->business_type) == 'bakery' ? 'selected' : '' }}>🥐 Bakery</option>
                                        <option value="cafe" {{ old('business_type', $business->business_type) == 'cafe' ? 'selected' : '' }}>☕ Cafe</option>
                                        <option value="supermarket" {{ old('business_type', $business->business_type) == 'supermarket' ? 'selected' : '' }}>🛒 Supermarket / Grocery</option>
                                    </select>
                                    @error('business_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold text-secondary">Contact Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $business->phone) }}" maxlength="10" placeholder="e.g., 0771234567" required pattern="^07[0-9]{8}$"
                                        oninvalid="this.setCustomValidity('Please enter a valid 10-digit Sri Lankan phone number starting with 07.')"
                                        oninput="this.setCustomValidity('')">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger small mt-1 d-none" id="phone-warning">⚠️ Please enter exactly 10 digits starting with 07 (e.g., 0771234567). Only numbers are allowed.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-secondary">Contact Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $business->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold text-secondary">Short Description / Bio</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Tell customers about your brand or food rescue initiatives...">{{ old('description', $business->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary d-block">Store Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    @if($business->logo)
                                        <img src="{{ asset('storage/' . $business->logo) }}" alt="Logo preview" class="img-logo-preview">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light text-muted fw-bold" style="width: 80px; height: 80px; border-radius: 12px; border: 2px dashed #cbd5e1;">NO LOGO</div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/jpeg,image/png">
                                        <div class="form-text small text-muted">Mimes: jpeg, png. Max size: 2MB.</div>
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Address & Location Picker Map --}}
                    <div class="col-lg-6">
                        <div class="card profile-card p-4 h-100">
                            <h5 class="fw-bold text-dark mb-4"><i class="fa fa-map-location-dot text-success me-2"></i>Physical Location & Map</h5>

                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold text-secondary">Street Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $business->address) }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="latitude" class="form-label fw-semibold text-secondary small">Latitude (GPS Coords)</label>
                                    <input type="text" class="form-control bg-light" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}" readonly placeholder="Click map to pick">
                                </div>
                                <div class="col-6">
                                    <label for="longitude" class="form-label fw-semibold text-secondary small">Longitude (GPS Coords)</label>
                                    <input type="text" class="form-control bg-light" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}" readonly placeholder="Click map to pick">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold text-secondary small">Click or drag the marker to set position:</span>
                                    <button type="button" class="locate-btn shadow-sm btn-sm" id="locateBtn">
                                        <i class="fa fa-location-crosshairs me-1"></i> Get My GPS
                                    </button>
                                </div>
                                <div id="locationMap"></div>
                            </div>

                            <div class="map-helper-badge mt-2">
                                <i class="fa fa-info-circle text-success" style="font-size: 1.1rem;"></i>
                                <span>Drag the red pin or click anywhere on the map to pinpoint your exact shop entrance. This ensures accurate distance calculation for customers!</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm rounded-pill"><i class="fa fa-save me-2"></i>Save Business Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps_key') }}&libraries=geometry,places"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const defaultLat = 8.5811;
        const defaultLng = 81.2341;

        let latVal = document.getElementById('latitude').value;
        let lngVal = document.getElementById('longitude').value;

        const initialLat = latVal ? parseFloat(latVal) : defaultLat;
        const initialLng = lngVal ? parseFloat(lngVal) : defaultLng;

        let map = new google.maps.Map(document.getElementById('locationMap'), {
            center: { lat: initialLat, lng: initialLng },
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

        let marker = null;
        if (latVal && lngVal) {
            marker = new google.maps.Marker({
                position: { lat: initialLat, lng: initialLng },
                map: map,
                title: "Business Location",
                draggable: false
            });
        }

        const addrInp = document.getElementById('address');
        if (addrInp) {
            addrInp.addEventListener('change', function() {
                geocodeAddress(this.value);
            });
        }

        const phoneInp = document.getElementById('phone');
        if (phoneInp) {
            const warning = document.getElementById('phone-warning');
            phoneInp.addEventListener('input', function () {
                let val = this.value.replace(/[^0-9]/g, '');
                if (this.value !== val) {
                    if (warning) warning.classList.remove('d-none');
                } else {
                    if (warning && val.length === 10 && val.startsWith('07')) warning.classList.add('d-none');
                }
                if (val.length > 0 && val[0] !== '0') {
                    val = '';
                    if (warning) warning.classList.remove('d-none');
                }
                if (val.length > 1 && val[1] !== '7') {
                    val = '0';
                    if (warning) warning.classList.remove('d-none');
                }
                this.value = val;
            });
            phoneInp.addEventListener('blur', function () {
                if (this.value.length < 10 || !this.value.startsWith('07')) {
                    if (warning) warning.classList.remove('d-none');
                } else {
                    if (warning) warning.classList.add('d-none');
                }
            });
        }

        function geocodeAddress(addressText) {
            if (!addressText || addressText.trim().length < 3) return;
            var query = addressText.trim();
            
            if (!query.toLowerCase().includes("trincomalee")) {
                query += ", Trincomalee";
            }
            if (!query.toLowerCase().includes("sri lanka")) {
                query += ", Sri Lanka";
            }

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: query }, function(results, status) {
                if (status === 'OK' && results && results.length > 0) {
                    var lat = results[0].geometry.location.lat();
                    var lng = results[0].geometry.location.lng();
                    var pos = { lat: lat, lng: lng };
                    map.panTo(pos);
                    map.setZoom(16);
                    if (marker) {
                        marker.setPosition(pos);
                    } else {
                        marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: "Business Location",
                            draggable: false
                        });
                    }
                    updateCoords(lat, lng);
                } else {
                    console.warn("Geocoding failed: " + status);
                }
            });
        }

        function updateCoords(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }

        document.getElementById('locateBtn').addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Locating...';
            btn.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const pos = { lat: lat, lng: lng };

                        map.panTo(pos);
                        map.setZoom(16);

                        if (marker) {
                            marker.setPosition(pos);
                        } else {
                            marker = new google.maps.Marker({
                                position: pos,
                                map: map,
                                title: "Business Location",
                                draggable: false
                            });
                        }

                        updateCoords(lat, lng);
                        btn.innerHTML = '<i class="fa fa-check me-1"></i> Location Set';
                        btn.disabled = false;
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                        }, 2000);
                    },
                    function(error) {
                        alert("Could not retrieve current GPS coordinates: " + error.message);
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            } else {
                alert("Geolocation is not supported by your browser.");
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });
    });
</script>
@endpush
