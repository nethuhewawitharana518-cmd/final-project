<?php $__env->startSection('title', 'Register Business'); ?>
<?php $__env->startSection('meta_description', 'Partner with FoodRescue. Register your restaurant, cafe, bakery, or hotel in Trincomalee to list surplus food.'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #regMap { height: 280px; width: 100%; border-radius: 10px; border: 1.5px solid #dee2e6; }
    #reg-map-status {
        font-size: 0.78rem; margin-top: 6px; display: flex; align-items: center; gap: 6px;
    }
    #reg-map-status.pending  { color: #b45309; }
    #reg-map-status.success  { color: #15803d; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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

                    <form action="<?php echo e(route('register.business.post')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="latitude" id="latitude" value="<?php echo e(old('latitude', '8.5755')); ?>">
                        <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude', '81.2285')); ?>">
                        
                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fa fa-user me-2"></i>Owner Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="owner_name" class="form-label text-dark small fw-semibold">Owner's Full Name</label>
                                <input type="text" name="owner_name" id="owner_name" class="form-control <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="Ranasinghe Perera" value="<?php echo e(old('owner_name')); ?>" required autofocus>
                                <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="text-danger small mt-1 d-none" id="name-warning">⚠️ Please enter letters and spaces only. Numbers or special characters are not allowed.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-dark small fw-semibold">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="business@example.com" value="<?php echo e(old('email')); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label text-dark small fw-semibold">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="e.g., 0771234567" value="<?php echo e(old('phone')); ?>" required maxlength="10" pattern="^07[0-9]{8}$"
                                    oninvalid="this.setCustomValidity('Please enter a valid 10-digit Sri Lankan phone number starting with 07.')"
                                    oninput="this.setCustomValidity('')">
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="text-danger small mt-1 d-none" id="phone-warning">⚠️ Please enter exactly 10 digits starting with 07 (e.g., 0771234567). Only numbers are allowed.</div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="password" class="form-label text-dark small fw-semibold">Password</label>
                                        <input type="password" name="password" id="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            placeholder="Min 8 chars" required>
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                <div class="position-relative">
                                    <input type="text" name="business_name" id="business_name" class="form-control <?php $__errorArgs = ['business_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="Ranasinghe Hotels" value="<?php echo e(old('business_name')); ?>" required autocomplete="off">
                                    <div id="business-name-suggestions" class="list-group position-absolute w-100 shadow-sm d-none" style="z-index:1000; top:100%;"></div>
                                </div>
                                <?php $__errorArgs = ['business_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text small text-muted">If your business is already on the map, pick it below to auto-fill the address & location.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="business_type" class="form-label text-dark small fw-semibold">Business Type</label>
                                <select name="business_type" id="business_type" class="form-select <?php $__errorArgs = ['business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="" disabled selected>Select Business Type</option>
                                    <option value="hotel" <?php echo e(old('business_type') == 'hotel' ? 'selected' : ''); ?>>Hotel / Resort</option>
                                    <option value="restaurant" <?php echo e(old('business_type') == 'restaurant' ? 'selected' : ''); ?>>Restaurant</option>
                                    <option value="bakery" <?php echo e(old('business_type') == 'bakery' ? 'selected' : ''); ?>>Bakery</option>
                                    <option value="cafe" <?php echo e(old('business_type') == 'cafe' ? 'selected' : ''); ?>>Cafe / Bistro</option>
                                    <option value="supermarket" <?php echo e(old('business_type') == 'supermarket' ? 'selected' : ''); ?>>Supermarket / Grocery</option>
                                </select>
                                <?php $__errorArgs = ['business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label text-dark small fw-semibold">Full Address</label>
                                <textarea name="address" id="address" rows="2" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="No. 45, Dockyard Road, Trincomalee" required><?php echo e(old('address')); ?></textarea>
                                <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark small fw-semibold mb-1">
                                    Confirm Business Location on Map <span class="text-danger">*</span>
                                </label>
                                <div id="regMap"></div>
                                <div id="reg-map-status" class="pending">
                                    <i class="fa fa-circle-notch fa-spin"></i>
                                    <span>Type your address above, tap directly on the map, or drag the pin — whichever finds your exact spot easiest.</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="reg_number" class="form-label text-dark small fw-semibold">Business Registration Number</label>
                                <input type="text" name="reg_number" id="reg_number" class="form-control <?php $__errorArgs = ['reg_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="e.g. 123456" value="<?php echo e(old('reg_number')); ?>" required maxlength="6" pattern="\d{6}" title="Please enter exactly 6 numbers">
                                <?php $__errorArgs = ['reg_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="logo" class="form-label text-dark small fw-semibold">Business Logo (Optional)</label>
                                <input type="file" name="logo" id="logo" class="form-control <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*">
                                <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fa fa-file-shield me-2"></i>Verification Documents</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="reg_cert" class="form-label text-dark small fw-semibold">Registration Certificate (PDF/Image)</label>
                                <input type="file" name="reg_cert" id="reg_cert" class="form-control <?php $__errorArgs = ['reg_cert'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <div class="form-text text-muted small">Upload a PDF or clear photo of your business registration cert.</div>
                                <?php $__errorArgs = ['reg_cert'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="safety_permit" class="form-label text-dark small fw-semibold">Food Safety/Hygiene Permit (PDF/Image)</label>
                                <input type="file" name="safety_permit" id="safety_permit" class="form-control <?php $__errorArgs = ['safety_permit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <div class="form-text text-muted small">Upload your PHI or food safety inspection certificate.</div>
                                <?php $__errorArgs = ['safety_permit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label text-muted small" for="terms">
                                I hereby certify that I have read and agree to the <a href="<?php echo e(route('terms')); ?>" target="_blank" class="text-success fw-semibold text-decoration-none">Terms and Conditions</a> and guarantee the safety of the food listed.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold py-3 shadow-sm">
                            <i class="fa fa-store-slash me-2"></i>Submit Business Registration
                        </button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <span class="text-muted small">Already have an account? </span>
                        <a href="<?php echo e(route('login')); ?>" class="text-success small fw-semibold text-decoration-none">Sign In Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ownerNameInput = document.getElementById('owner_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const regNumberInput = document.getElementById('reg_number');

    if (regNumberInput) {
        regNumberInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        });
    }

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

    // ── Live Map: mandatory visual confirmation of the pinned business location ──
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const mapStatus = document.getElementById('reg-map-status');

    const DEFAULT_LAT = parseFloat(latInput.value) || 8.5755;
    const DEFAULT_LNG = parseFloat(lngInput.value) || 81.2285;

    const regMap = L.map('regMap').setView([DEFAULT_LAT, DEFAULT_LNG], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(regMap);

    // Force Leaflet to size itself correctly (it's inside a card that may not
    // have its final width yet on first paint)
    setTimeout(() => regMap.invalidateSize(), 200);

    const regMarker = L.marker([DEFAULT_LAT, DEFAULT_LNG], { draggable: true }).addTo(regMap);

    function setMapStatus(state, text) {
        if (!mapStatus) return;
        mapStatus.className = state; // 'pending' | 'success'
        mapStatus.innerHTML = `<i class="fa ${state === 'success' ? 'fa-circle-check' : 'fa-circle-notch fa-spin'}"></i><span>${text}</span>`;
    }

    function placePin(lat, lng, zoom) {
        latInput.value = lat;
        lngInput.value = lng;
        regMarker.setLatLng([lat, lng]);
        regMap.setView([lat, lng], zoom || 16);
        setMapStatus('success', 'Pin set — drag it to fine-tune if needed, then submit.');
    }

    // Manual fine-tuning: dragging the marker overrides geocoding directly
    regMarker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        latInput.value = pos.lat;
        lngInput.value = pos.lng;
        setMapStatus('success', 'Custom location set manually.');
    });

    // Click anywhere on the map to drop the pin there directly — for new/small
    // businesses that free geocoding (and the local directory) won't know about,
    // the owner can just tap their own location visually instead of typing.
    regMap.on('click', function (e) {
        placePin(e.latlng.lat, e.latlng.lng);
        setMapStatus('success', 'Location set from map click — drag to fine-tune if needed.');
    });

    // ── Business-name autocomplete against the local Trincomalee directory ──
    // (imported once from free OpenStreetMap data via `php artisan directory:import-trincomalee`)
    // Selecting a suggestion fills the address AND drops the pin using real
    // coordinates directly — no geocoding needed at all for known places.
    const nameInput    = document.getElementById('business_name');
    const suggestBox   = document.getElementById('business-name-suggestions');
    const addressField = document.getElementById('address');

    if (nameInput && suggestBox) {
        let nameDebounce = null;

        nameInput.addEventListener('input', function () {
            if (nameDebounce) clearTimeout(nameDebounce);
            const q = this.value.trim();
            if (q.length < 2) { suggestBox.classList.add('d-none'); suggestBox.innerHTML = ''; return; }

            nameDebounce = setTimeout(async function () {
                try {
                    const res  = await fetch('/api/directory/search?q=' + encodeURIComponent(q));
                    const list = await res.json();

                    if (!list || list.length === 0) {
                        suggestBox.classList.add('d-none');
                        suggestBox.innerHTML = '';
                        return;
                    }

                    suggestBox.innerHTML = list.map(function (item, idx) {
                        const addr = item.address ? item.address : 'Location on map (address not on file — please fill in above)';
                        return '<button type="button" class="list-group-item list-group-item-action py-2" data-idx="' + idx + '">'
                             + '<div class="fw-semibold small">' + item.name + ' <span class="text-muted fw-normal">(' + item.category + ')</span></div>'
                             + '<div class="text-muted small">' + addr + '</div>'
                             + '</button>';
                    }).join('');
                    suggestBox.classList.remove('d-none');

                    suggestBox.querySelectorAll('button').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const item = list[parseInt(this.dataset.idx, 10)];
                            nameInput.value = item.name;
                            if (item.address && addressField) addressField.value = item.address;
                            placePin(item.latitude, item.longitude, 17);
                            suggestBox.classList.add('d-none');
                            suggestBox.innerHTML = '';
                        });
                    });
                } catch (err) {
                    console.warn('Directory search failed:', err);
                }
            }, 300);
        });

        // Hide suggestions when clicking elsewhere
        document.addEventListener('click', function (e) {
            if (!suggestBox.contains(e.target) && e.target !== nameInput) {
                suggestBox.classList.add('d-none');
            }
        });
    }

    if (addressInput && latInput && lngInput) {
        let debounceTimer = null;
        addressInput.addEventListener('input', function () {
            if (debounceTimer) clearTimeout(debounceTimer);
            const addressText = addressInput.value.trim();
            if (addressText.length < 3) return;

            setMapStatus('pending', 'Looking up address on the map…');

            debounceTimer = setTimeout(async function () {
                var query = addressText;
                if (!query.toLowerCase().includes("trincomalee")) query += ", Trincomalee";
                if (!query.toLowerCase().includes("sri lanka")) query += ", Sri Lanka";

                // NOTE: Google's REST Geocoding API (maps.googleapis.com/maps/api/geocode/json)
                // does not send CORS headers, so a browser-side fetch() to it is always blocked
                // and silently fails — that's why it looked "unreliable" before. Nominatim
                // (OpenStreetMap) supports browser fetch directly and needs no API key, so we
                // use it as the single source of truth here instead of a fetch call that can
                // never actually succeed from client-side JS.
                try {
                    var res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`, {
                        headers: { 'Accept-Language': 'en' }
                    });
                    var data = await res.json();
                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        placePin(lat, lon);
                        console.log(`Geocoded (OSM): ${lat}, ${lon}`);
                        return;
                    }
                } catch (err) {
                    console.warn("OSM geocoding failed: ", err);
                }

                setMapStatus('pending', 'Could not find that address automatically — please drag the pin to your exact location.');
            }, 900);
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/auth/business-register.blade.php ENDPATH**/ ?>