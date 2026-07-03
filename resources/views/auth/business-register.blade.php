@extends('layouts.app')

@section('title', 'Register Business')
@section('meta_description', 'Partner with FoodRescue. Register your restaurant, cafe, bakery, or hotel in Trincomalee to list surplus food.')

@section('content')
<section class="py-5 bg-light-gradient min-vh-80 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-lg p-4 p-md-5 bg-white rounded-3">
                    <div class="text-center mb-4">
                        <div class="fs-1 mb-2">💼</div>
                        <h3 class="fw-bold text-dark mb-1">Business Registration</h3>
                        <p class="text-muted small">Register your food business to start selling surplus inventory</p>
                    </div>

                    <form action="{{ route('register.business.post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '8.5755') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '81.2285') }}">
                        
                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fa fa-user me-2"></i>Owner Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="owner_name" class="form-label text-dark small fw-semibold">Owner's Full Name</label>
                                <input type="text" name="owner_name" id="owner_name" class="form-control @error('owner_name') is-invalid @enderror" 
                                    placeholder="Ranasinghe Perera" value="{{ old('owner_name') }}" required autofocus>
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-danger small mt-1 d-none" id="name-warning">⚠️ Please enter letters and spaces only. Numbers or special characters are not allowed.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-dark small fw-semibold">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                    placeholder="business@example.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label text-dark small fw-semibold">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                    placeholder="e.g., 0771234567" value="{{ old('phone') }}" required maxlength="10" pattern="^07[0-9]{8}$"
                                    oninvalid="this.setCustomValidity('Please enter a valid 10-digit Sri Lankan phone number starting with 07.')"
                                    oninput="this.setCustomValidity('')">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-danger small mt-1 d-none" id="phone-warning">⚠️ Please enter exactly 10 digits starting with 07 (e.g., 0771234567). Only numbers are allowed.</div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="password" class="form-label text-dark small fw-semibold">Password</label>
                                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                            placeholder="Min 8 chars" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="password_confirmation" class="form-label text-dark small fw-semibold">Confirm Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                            placeholder="Confirm" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fa fa-store me-2"></i>Business Details</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="business_name" class="form-label text-dark small fw-semibold">Business Name</label>
                                <input type="text" name="business_name" id="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                    placeholder="Ranasinghe Hotels" value="{{ old('business_name') }}" required>
                                @error('business_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="business_type" class="form-label text-dark small fw-semibold">Business Type</label>
                                <select name="business_type" id="business_type" class="form-select @error('business_type') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select Business Type</option>
                                    <option value="hotel" {{ old('business_type') == 'hotel' ? 'selected' : '' }}>Hotel / Resort</option>
                                    <option value="restaurant" {{ old('business_type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                                    <option value="bakery" {{ old('business_type') == 'bakery' ? 'selected' : '' }}>Bakery</option>
                                    <option value="cafe" {{ old('business_type') == 'cafe' ? 'selected' : '' }}>Cafe / Bistro</option>
                                    <option value="supermarket" {{ old('business_type') == 'supermarket' ? 'selected' : '' }}>Supermarket / Grocery</option>
                                </select>
                                @error('business_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label text-dark small fw-semibold">Full Address</label>
                                <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" 
                                    placeholder="No. 45, Dockyard Road, Trincomalee" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="reg_number" class="form-label text-dark small fw-semibold">Business Registration Number</label>
                                <input type="text" name="reg_number" id="reg_number" class="form-control @error('reg_number') is-invalid @enderror" 
                                    placeholder="BR-XXXXXX" value="{{ old('reg_number') }}" required>
                                @error('reg_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="logo" class="form-label text-dark small fw-semibold">Business Logo (Optional)</label>
                                <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fa fa-file-shield me-2"></i>Verification Documents</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="reg_cert" class="form-label text-dark small fw-semibold">Registration Certificate (PDF/Image)</label>
                                <input type="file" name="reg_cert" id="reg_cert" class="form-control @error('reg_cert') is-invalid @enderror" required>
                                <div class="form-text text-muted small">Upload a PDF or clear photo of your business registration cert.</div>
                                @error('reg_cert')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="safety_permit" class="form-label text-dark small fw-semibold">Food Safety/Hygiene Permit (PDF/Image)</label>
                                <input type="file" name="safety_permit" id="safety_permit" class="form-control @error('safety_permit') is-invalid @enderror" required>
                                <div class="form-text text-muted small">Upload your PHI or food safety inspection certificate.</div>
                                @error('safety_permit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label text-muted small" for="terms">
                                I confirm that all submitted documents and business details are valid, and I agree to the platform safety guidelines.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold py-3 shadow-sm">
                            <i class="fa fa-store-slash me-2"></i>Submit Business Registration
                        </button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <span class="text-muted small">Already have an account? </span>
                        <a href="{{ route('login') }}" class="text-success small fw-semibold text-decoration-none">Sign In Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ownerNameInput = document.getElementById('owner_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    if (ownerNameInput) {
        const nameWarning = document.getElementById('name-warning');
        ownerNameInput.addEventListener('input', function () {
            let val = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value !== val) {
                if (nameWarning) nameWarning.classList.remove('d-none');
            } else {
                if (nameWarning && val.trim().length > 0) nameWarning.classList.add('d-none');
            }
            this.value = val;
        });
        ownerNameInput.addEventListener('blur', function () {
            if (this.value.trim().length === 0 || /[^a-zA-Z\s]/.test(this.value)) {
                if (nameWarning) nameWarning.classList.remove('d-none');
            } else {
                if (nameWarning) nameWarning.classList.add('d-none');
            }
        });
    }

    if (emailInput) {
        // Email field: convert to lowercase
        emailInput.addEventListener('input', function () {
            this.value = this.value.toLowerCase();
        });
    }

    if (phoneInput) {
        const warning = document.getElementById('phone-warning');
        phoneInput.addEventListener('input', function () {
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
        phoneInput.addEventListener('blur', function () {
            if (this.value.length < 10 || !this.value.startsWith('07')) {
                if (warning) warning.classList.remove('d-none');
            } else {
                if (warning) warning.classList.add('d-none');
            }
        });
    }

    // Geocode address in browser to overcome backend referer restrictions
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (addressInput && latInput && lngInput) {
        let debounceTimer = null;
        addressInput.addEventListener('input', function () {
            if (debounceTimer) clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async function () {
                const addressText = addressInput.value.trim();
                if (addressText.length < 3) return;
                
                var googleApiKey = "{{ env('GOOGLE_MAPS_API_KEY') }}";
                var query = addressText;
                if (!query.toLowerCase().includes("trincomalee")) query += ", Trincomalee";
                if (!query.toLowerCase().includes("sri lanka")) query += ", Sri Lanka";

                try {
                    // Try Google Geocoding first
                    var res = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(query)}&key=${googleApiKey}`);
                    var data = await res.json();
                    if (data && data.status === 'OK' && data.results && data.results.length > 0) {
                        var lat = data.results[0].geometry.location.lat;
                        var lon = data.results[0].geometry.location.lng;
                        latInput.value = lat;
                        lngInput.value = lon;
                        console.log(`Browser Business Signup Geocoding (Google): ${lat}, ${lon}`);
                        return;
                    }
                } catch (e) {
                    console.warn("Google business registration geocoding failed, trying OSM: ", e);
                }

                // Try OSM fallback
                try {
                    var res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`, {
                        headers: { 'Accept-Language': 'en' }
                    });
                    var data = await res.json();
                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        latInput.value = lat;
                        lngInput.value = lon;
                        console.log(`Browser Business Signup Geocoding (OSM): ${lat}, ${lon}`);
                    }
                } catch (err) {
                    console.warn("OSM fallback business registration geocoding failed: ", err);
                }
            }, 1200);
        });
    }
});
</script>
@endpush
