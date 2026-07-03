<?php $__env->startSection('title', 'Secure Checkout | Food Rescue Marketplace Trincomalee'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Fulfillment tabs ── */
.pay-method-tab {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    cursor: pointer;
    transition: all .2s;
    user-select: none;
    background: #fff;
}
.pay-method-tab:hover        { border-color: #059669; background: #f0fdf4; }
.pay-method-tab.selected     { border-color: #059669; background: #f0fdf4; }
.pay-method-tab .tab-label   { font-size: .85rem; font-weight: 700; color: #111827; }
.pay-method-tab .tab-desc    { font-size: .75rem; color: #6b7280; margin-top: 2px; }

/* ── Pay button loading state ── */
#pay-btn { position: relative; overflow: hidden; font-size: 1.05rem; letter-spacing: .3px; }
#pay-btn .btn-text    { transition: opacity .2s; }
#pay-btn .btn-spinner { position: absolute; left:50%; top:50%; transform:translate(-50%,-50%); display:none; }
#pay-btn.loading .btn-text    { opacity: 0; }
#pay-btn.loading .btn-spinner { display: block; }

/* ── Demo card hint ── */
.test-card-hint {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1px solid #fcd34d;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: .78rem;
    color: #78350f;
}

/* ── Security strip ── */
.security-strip {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border: 1px solid #a7f3d0;
    border-radius: 10px;
    padding: 12px 16px;
}

/* ── Step circles ── */
.checkout-step {
    width: 28px; height: 28px; border-radius: 50%;
    background: #059669; color: #fff; font-size: .75rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}

/* ── Stripe card element wrapper ── */
#stripe-card-wrapper {
    background: #fff;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    padding: 14px 16px;
    transition: border-color .2s, box-shadow .2s;
    min-height: 52px;
}
#stripe-card-wrapper.StripeElement--focus   { border-color: #059669; box-shadow: 0 0 0 3px rgba(5,150,105,.12); }
#stripe-card-wrapper.StripeElement--invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.10); }

/* ── Delivery engine panel ── */
#delivery-engine-panel {
    border-radius: 12px;
    padding: 16px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    transition: border-color .3s, background .3s;
}
#delivery-engine-panel.status-ok      { border-color: #059669; background: #f0fdf4; }
#delivery-engine-panel.status-error   { border-color: #ef4444; background: #fef2f2; }
#delivery-engine-panel.status-loading { border-color: #d97706; background: #fffbeb; }

/* ── Zone badges ── */
.zone-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .72rem; font-weight: 700; border-radius: 20px;
    padding: 3px 12px;
}
.zone-badge.zone-base     { background: #d1fae5; color: #065f46; }
.zone-badge.zone-extended { background: #fef3c7; color: #92400e; }
.zone-badge.zone-far      { background: #fee2e2; color: #991b1b; }
.zone-badge.zone-blocked  { background: #f3f4f6; color: #6b7280; }

/* ── Distance slider ── */
.dist-slider {
    -webkit-appearance: none; appearance: none;
    width: 100%; height: 6px; border-radius: 3px;
    background: linear-gradient(to right,
        #059669 0%, #059669 var(--fill, 10%),
        #e2e8f0 var(--fill, 10%), #e2e8f0 100%);
    outline: none; cursor: pointer;
}
.dist-slider::-webkit-slider-thumb {
    -webkit-appearance: none; appearance: none;
    width: 20px; height: 20px; border-radius: 50%;
    background: #059669; cursor: pointer;
    box-shadow: 0 2px 6px rgba(5,150,105,.4);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">

    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3">
                <i class="fa fa-shield-halved text-success fa-2x"></i>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Secure Checkout</h4>
                    <p class="text-muted small mb-0">Reserve your surplus food deal. Pay securely and pick up at your scheduled time.</p>
                </div>
            </div>
        </div>
    </div>

    
    <div id="checkout-alert" class="alert alert-danger d-none shadow-sm rounded-3 mb-3" role="alert">
        <i class="fa fa-triangle-exclamation me-2"></i><span id="checkout-alert-msg"></span>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger shadow-sm rounded-3 mb-3">
            <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <div class="row g-4 text-start">

        
        <div class="col-lg-7">

            
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <span class="checkout-step me-2">1</span>Fulfillment Method
                </h5>

                
                <div class="row g-3 mb-3">
                    
                    <div class="col-md-6">
                        <div class="pay-method-tab selected" id="btn-select-pickup"
                             onclick="toggleFulfillment('pickup')">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-store text-success fs-5"></i>
                                <div>
                                    <div class="tab-label">Self Pickup</div>
                                    <div class="tab-desc">Free — collect at store</div>
                                </div>
                                <i class="fa fa-circle-check text-success ms-auto" id="pickup-check"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="pay-method-tab" id="btn-select-delivery"
                             onclick="toggleFulfillment('delivery')">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-truck text-success fs-5"></i>
                                <div>
                                    <div class="tab-label">Home Delivery</div>
                                    <div class="tab-desc" id="delivery-tab-desc">Rs. 100 base + Rs. 80/km above 1km</div>
                                </div>
                                <i class="fa fa-circle text-muted ms-auto" id="delivery-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="checkout_type" value="pickup">

                
                <div id="delivery-engine-panel" class="mb-3 d-none">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-route text-success"></i>
                        <span class="fw-bold small text-dark">Delivery location mapping</span>
                    </div>

                    
                    <div class="p-3 mb-3 bg-light rounded-3 border d-flex justify-content-around text-center" style="border-color: var(--border) !important;">
                        <div>
                            <div class="text-muted small fw-semibold mb-1">📏 Distance</div>
                            <div class="fs-6 fw-bold text-success" id="distance-display">-- km</div>
                        </div>
                        <div class="border-start animate-pulse" style="width: 1px; height: 40px; background-color: var(--border);"></div>
                        <div>
                            <div class="text-muted small fw-semibold mb-1">⏱️ Est. Travel Time</div>
                            <div class="fs-6 fw-bold text-warning" id="hud-time-val">-- min</div>
                        </div>
                    </div>
                    
                    
                    <div class="position-relative mb-3">
                        <div id="delivery-map" style="height: 300px; width: 100%; border-radius: 12px; border: 1.5px solid var(--border);" class="shadow-sm"></div>
                        <div class="position-absolute top-0 start-0 m-2 p-2 bg-white rounded-3 shadow border" style="z-index: 1000; width: 280px; max-width: calc(100% - 16px);">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="fa fa-magnifying-glass text-muted"></i></span>
                                <input type="text" id="map-search-input" class="form-control border-0 bg-light text-dark small" placeholder="Search address or landmark..." style="font-size: 0.78rem; box-shadow: none;">
                                <button type="button" class="btn btn-warning btn-sm rounded-end-3 text-white" onclick="triggerGeocodeSearch()"><i class="fa fa-location-arrow"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-0 mb-3">
                    <div class="d-flex align-items-start gap-3">
                        <div id="delivery-result-icon" style="font-size:1.4rem;">📍</div>
                        <div class="flex-grow-1">
                            <div id="delivery-result-msg" class="small fw-semibold text-muted">
                                Drag the blue marker to calculate distance and delivery fee...
                            </div>
                            <div id="delivery-zone-badge" class="mt-1"></div>
                            <div id="delivery-fee-preview" class="mt-1 small fw-bold text-dark"></div>
                        </div>
                        <div id="delivery-spinner"
                             class="spinner-border spinner-border-sm text-success d-none"
                             role="status"></div>
                    </div>
                </div>

                
                <div class="mb-3">
                    <label for="pickup_time" class="form-label fw-semibold small" id="time-label">
                        <i class="fa fa-calendar-days me-1 text-success"></i>Pickup Date &amp; Time
                    </label>
                    <input type="datetime-local" id="pickup_time" class="form-control"
                           value="<?php echo e(old('pickup_time', now()->addHours(1)->format('Y-m-d\TH:i'))); ?>" required>
                    <small class="text-muted" id="time-help">Pick up within store operating hours.</small>
                </div>

                
                <div class="mb-1 d-none" id="delivery-address-container">
                    <label for="delivery_address" class="form-label fw-semibold small">
                        <i class="fa fa-map-marker-alt me-1 text-success"></i>Delivery Address
                    </label>
                    <textarea id="delivery_address" class="form-control" rows="3"
                              placeholder="Enter your full street address in Trincomalee"><?php echo e(old('delivery_address', Auth::user()->home_address)); ?></textarea>
                    <small class="text-muted">Delivery within Trincomalee town limits only.</small>
                </div>

            </div>

            
            <?php if($loyaltyBal > 0): ?>
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <span class="checkout-step me-2">2</span>Loyalty Points Rewards
                </h5>
                <p class="text-muted small mb-3">
                    Your balance: <span class="fw-bold text-success"><?php echo e($loyaltyBal); ?> points</span>
                    &nbsp;(1 pt = Rs. 1.00 discount)
                </p>
                <div class="d-flex align-items-center gap-2" style="max-width:300px;">
                    <input type="number" id="loyalty_points" class="form-control"
                           placeholder="0" min="0" max="<?php echo e($maxRedeemable); ?>" value="0">
                    <button type="button" class="btn btn-outline-success px-3"
                            onclick="applyLoyalty()">Apply</button>
                </div>
                <small class="text-muted d-block mt-1">
                    Max redeemable this order: <strong><?php echo e($maxRedeemable); ?> pts</strong>
                </small>
            </div>
            <?php endif; ?>

            
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-1 text-dark">
                    <span class="checkout-step me-2"><?php echo e($loyaltyBal > 0 ? '3' : '2'); ?></span>Card Payment
                </h5>
                <p class="text-muted small mb-4">
                    Visa or Mastercard. Your card number is handled exclusively by Stripe.
                </p>

                
                <div class="test-card-hint mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <i class="fa fa-flask text-warning me-1"></i>
                        <strong>Test card:</strong>
                        <span class="font-monospace ms-1">4242 4242 4242 4242 &nbsp; 12/27 &nbsp; 123</span>
                    </div>
                    <button type="button" id="autofill-btn" onclick="fillDemoCard()"
                            style="background:linear-gradient(135deg,#d97706,#f59e0b);border:none;color:#fff;border-radius:8px;padding:5px 14px;font-size:.78rem;font-weight:700;cursor:pointer;">
                        <i class="fa fa-wand-magic-sparkles me-1"></i>Auto-fill
                    </button>
                </div>

                
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Card Details</label>
                    <div id="stripe-card-wrapper">
                        <div id="stripe-card-element"></div>
                        <div id="mock-card-element" class="d-none">
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="mock-card-number" class="form-control border-0 p-0 fs-6 text-dark" style="box-shadow:none; background:transparent; width: 60%;" placeholder="Card number" value="">
                                <input type="text" id="mock-card-expiry" class="form-control border-0 p-0 fs-6 text-dark text-center" style="box-shadow:none; background:transparent; width: 20%;" placeholder="MM/YY" value="">
                                <input type="text" id="mock-card-cvc" class="form-control border-0 p-0 fs-6 text-dark text-center" style="box-shadow:none; background:transparent; width: 20%;" placeholder="CVC" value="">
                            </div>
                        </div>
                    </div>
                    <div id="stripe-card-errors" class="text-danger small mt-1"></div>
                </div>

                
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Cardholder Name</label>
                    <input type="text" id="cardholder-name" class="form-control"
                           placeholder="Name as on card"
                           value="<?php echo e(Auth::user()->name); ?>"
                           autocomplete="cc-name" required>
                </div>

                
                <button id="pay-btn" type="button"
                        class="btn btn-success w-100 py-3 rounded-pill shadow-lg fw-bold"
                        onclick="submitPayment()">
                    <span class="btn-text">
                        <i class="fa fa-lock me-2"></i>Pay Securely — Rs.&nbsp;<span id="btn-total"><?php echo e(number_format($subtotal, 2)); ?></span>
                    </span>
                    <span class="btn-spinner">
                        <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                        &nbsp;&nbsp;Processing…
                    </span>
                </button>

                
                <div class="security-strip mt-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-brands fa-stripe text-primary fs-2"></i>
                        <div>
                            <div class="fw-bold small text-dark">Powered by Stripe</div>
                            <div class="text-muted" style="font-size:.72rem;">
                                256-bit TLS · PCI DSS compliant · Card data never stored on our servers
                            </div>
                        </div>
                        <div class="ms-auto d-flex gap-2">
                            <i class="fa-brands fa-cc-visa text-primary fs-4"></i>
                            <i class="fa-brands fa-cc-mastercard text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 sticky-top" style="top:90px;">
                <h5 class="fw-bold text-dark mb-4">Order Summary</h5>

                
                <div class="d-flex flex-column gap-3 mb-4">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center gap-3">
                        <?php if($item['image']): ?>
                            <img src="<?php echo e(str_starts_with($item['image'], 'assets/') ? asset($item['image']) : asset('storage/' . $item['image'])); ?>" alt=""
                                 class="rounded-3" style="width:50px;height:50px;object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-success"
                                 style="width:50px;height:50px;font-size:1.2rem;">🍲</div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-0 small"><?php echo e($item['name']); ?></h6>
                            <span class="text-muted small">Qty: <?php echo e($item['quantity']); ?></span>
                        </div>
                        <div class="fw-bold text-dark text-end small">
                            Rs. <?php echo e(number_format($item['discount_price'] * $item['quantity'], 2)); ?>

                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2 text-muted small">
                    <span>Items Subtotal</span>
                    <span class="fw-bold text-dark">Rs. <?php echo e(number_format($subtotal, 2)); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted small d-none" id="loyalty-discount-row">
                    <span>Loyalty Discount</span>
                    <span class="fw-bold text-success">− Rs. <span id="loyalty-discount-val">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted small d-none" id="delivery-fee-row">
                    <span>Delivery Fee</span>
                    <span class="fw-bold text-dark">Rs. <span id="delivery-fee-val">0.00</span></span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold fs-5">Total Due</span>
                    <span class="fw-bold fs-5 text-success">Rs. <span id="summary-total"><?php echo e(number_format($subtotal, 2)); ?></span></span>
                </div>

                <div class="alert alert-success border-0 small mb-0 d-flex align-items-center gap-2">
                    <i class="fa fa-leaf text-success fs-5"></i>
                    <div>
                        <strong>Food Rescue Purchase!</strong>
                        <p class="mb-0 text-muted" style="font-size:.75rem;">
                            You're preventing food waste and saving money. 🌱
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(env('GOOGLE_MAPS_API_KEY')); ?>&libraries=geometry,places"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
// Vendor Location
var vendorLat = <?php echo e($business?->latitude ?? 8.5755); ?>;
var vendorLng = <?php echo e($business?->longitude ?? 81.2285); ?>;
var vendorAddress = "<?php echo e(addslashes($business?->address ?? '')); ?>";
var vendorName = "<?php echo e(addslashes($business?->business_name ?? 'Vendor')); ?>";
// ═══════════════════════════════════════════════════════════════════
//  CONSTANTS  (PHP → JS)
// ═══════════════════════════════════════════════════════════════════
var STRIPE_PK            = <?php echo json_encode($stripeKey, 15, 512) ?>;
var SUBTOTAL             = <?php echo e((float) $subtotal); ?>;
var MAX_REDEEM           = <?php echo e((int) ($maxRedeemable ?? 0)); ?>;
// CSRF_TOKEN is already defined globally in app.js
var INTENT_URL           = '<?php echo e(route("customer.checkout.intent")); ?>';
var CONFIRM_URL          = '<?php echo e(route("customer.checkout.confirm")); ?>';
var DELIVERY_OPTIONS_URL = '<?php echo e(route("customer.checkout.delivery-options")); ?>';

// ═══════════════════════════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════════════════════════
var isDelivery            = false;
var loyaltyApplied        = 0;
var deliveryFeeFromServer = 0;
var deliveryAvailable     = true;
var stripeObj             = null;
var stripeCard            = null;     // Stripe CardElement instance
var _debounceTimer        = null;
var customerLat           = null;
var customerLng           = null;

// ═══════════════════════════════════════════════════════════════════
//  STRIPE INIT  (runs immediately — Stripe.js is loaded above)
// ═══════════════════════════════════════════════════════════════════
(function initStripe() {
    if (typeof Stripe === 'undefined') {
        showAlert('Stripe could not load. Please check your connection and refresh.');
        return;
    }
    try {
        stripeObj = Stripe(STRIPE_PK);
        var elements = stripeObj.elements();
        stripeCard = elements.create('card', {
            style: {
                base: {
                    fontSize: '15px',
                    color: '#111827',
                    fontFamily: 'system-ui, sans-serif',
                    '::placeholder': { color: '#9ca3af' },
                },
                invalid: { color: '#ef4444' },
            },
            hidePostalCode: true,
        });
        stripeCard.mount('#stripe-card-element');

        // Show inline validation errors
        stripeCard.on('change', function(event) {
            var errBox = document.getElementById('stripe-card-errors');
            if (errBox) errBox.textContent = event.error ? event.error.message : '';
            // Apply focus/invalid classes to wrapper
            var wrapper = document.getElementById('stripe-card-wrapper');
            if (wrapper) {
                wrapper.classList.toggle('StripeElement--invalid', !!event.error);
            }
        });
        stripeCard.on('focus', function() {
            var w = document.getElementById('stripe-card-wrapper');
            if (w) w.classList.add('StripeElement--focus');
        });
        stripeCard.on('blur', function() {
            var w = document.getElementById('stripe-card-wrapper');
            if (w) w.classList.remove('StripeElement--focus');
        });

        console.log('✅ Stripe CardElement mounted.');
    } catch (err) {
        console.error('Stripe init error:', err);
        showAlert('Failed to load card input. Please refresh the page.');
    }
})();

// ═══════════════════════════════════════════════════════════════════
//  DEMO AUTO-FILL  (fills Stripe test card via clipboard trick)
// ═══════════════════════════════════════════════════════════════════
function fillDemoCard() {
    // Hide the real Stripe element container and show the styled mock inputs
    var realEl = document.getElementById('stripe-card-element');
    var mockEl = document.getElementById('mock-card-element');
    if (realEl && mockEl) {
        realEl.classList.add('d-none');
        mockEl.classList.remove('d-none');
    }

    // Populate mock fields programmatically
    var numInp = document.getElementById('mock-card-number');
    var expInp = document.getElementById('mock-card-expiry');
    var cvcInp = document.getElementById('mock-card-cvc');
    if (numInp) numInp.value = '4242 4242 4242 4242';
    if (expInp) expInp.value = '12/27';
    if (cvcInp) cvcInp.value = '123';

    // Set mock payment flag
    window.useMockPayment = true;

    // Provide instant visual success feedback on the autofill helper button
    var btn = document.getElementById('autofill-btn');
    if (btn) {
        btn.innerHTML = '<i class="fa fa-circle-check me-1"></i>Auto-filled!';
        btn.style.background = '#059669';
    }
}

// ═══════════════════════════════════════════════════════════════════
//  TOTAL RECALCULATION
// ═══════════════════════════════════════════════════════════════════
function fmt(n) {
    return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function recalcTotal() {
    var fee   = isDelivery ? deliveryFeeFromServer : 0;
    var total = Math.max(1, SUBTOTAL - loyaltyApplied + fee);

    var btnTotal     = document.getElementById('btn-total');
    var summaryTotal = document.getElementById('summary-total');
    var loyaltyRow   = document.getElementById('loyalty-discount-row');
    var loyaltyVal   = document.getElementById('loyalty-discount-val');
    var deliveryRow  = document.getElementById('delivery-fee-row');
    var deliveryVal  = document.getElementById('delivery-fee-val');

    if (btnTotal)     btnTotal.textContent     = fmt(total);
    if (summaryTotal) summaryTotal.textContent  = fmt(total);
    if (loyaltyVal)   loyaltyVal.textContent   = fmt(loyaltyApplied);
    if (loyaltyRow)   loyaltyRow.classList.toggle('d-none', loyaltyApplied <= 0);
    if (deliveryVal)  deliveryVal.textContent  = fmt(fee);
    if (deliveryRow)  deliveryRow.classList.toggle('d-none', !isDelivery || fee <= 0);
}

// ═══════════════════════════════════════════════════════════════════
//  LOYALTY POINTS
// ═══════════════════════════════════════════════════════════════════
function applyLoyalty() {
    var input = document.getElementById('loyalty_points');
    var pts   = parseInt((input && input.value) ? input.value : 0) || 0;
    loyaltyApplied = Math.min(pts, MAX_REDEEM, SUBTOTAL);
    recalcTotal();
}

// ═══════════════════════════════════════════════════════════════════
//  FULFILLMENT TOGGLE  — the core fix
// ═══════════════════════════════════════════════════════════════════
//  FULFILLMENT TOGGLE  — the core fix
// ═══════════════════════════════════════════════════════════════════
var deliveryMap = null;
var vendorMarker = null;
var customerMarker = null;
var directionsRenderer = null;
var ignoreAddressInputEvent = false;

function initDeliveryMap() {
    if (deliveryMap) return;

    var centerLatLng = { lat: parseFloat(vendorLat), lng: parseFloat(vendorLng) };
    deliveryMap = new google.maps.Map(document.getElementById('delivery-map'), {
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

    directionsRenderer = new google.maps.DirectionsRenderer({
        map: deliveryMap,
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: '#059669',
            strokeWeight: 5,
            strokeOpacity: 0.75
        }
    });

    vendorMarker = new google.maps.Marker({
        position: centerLatLng,
        map: deliveryMap,
        title: vendorName,
        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
    });

    var isDefaultVendor = (vendorLat === 8.5755 && vendorLng === 81.2285);
    if (isDefaultVendor && vendorAddress) {
        geocodeVendorAddress(vendorAddress);
    }

    var addrTa = document.getElementById('delivery_address');
    var initialAddr = addrTa ? addrTa.value.trim() : '';
    if (initialAddr) {
        setupCustomerMarker(vendorLat, vendorLng);
        geocodeAddress(initialAddr);
    } else if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
            setupCustomerMarker(pos.lat, pos.lng);
        }, function(error) {
            console.warn("Geolocation access denied/failed, placing fallback customer pin.", error);
            setupCustomerMarker(vendorLat, vendorLng);
        }, { timeout: 4000, enableHighAccuracy: true });
    } else {
        setupCustomerMarker(vendorLat, vendorLng);
    }

    var searchInp = document.getElementById('map-search-input');
    if (searchInp) {
        // Initialize Google Places Autocomplete on the search input box
        var autocomplete = new google.maps.places.Autocomplete(searchInp, {
            fields: ["geometry", "formatted_address", "name"],
            componentRestrictions: { country: "lk" } // Restrict to Sri Lanka
        });

        // Set bounds to bias suggestions towards the map's current viewport
        if (deliveryMap) {
            autocomplete.bindTo("bounds", deliveryMap);
        }

        autocomplete.addListener("place_changed", function() {
            var place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) {
                console.warn("Autocomplete returned place with no geometry. Falling back to text search: " + searchInp.value);
                geocodeAddress(searchInp.value);
                return;
            }

            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            console.log("Places Autocomplete selection success: " + lat + ", " + lng);

            var addrTa = document.getElementById('delivery_address');
            if (addrTa) {
                ignoreAddressInputEvent = true;
                addrTa.value = place.formatted_address || place.name;
                ignoreAddressInputEvent = false;
            }

            setupCustomerMarker(lat, lng);
        });

        searchInp.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerGeocodeSearch();
            }
        });
    }

    if (addrTa) {
        addrTa.addEventListener('input', function(e) {
            if (ignoreAddressInputEvent) return;
            var val = e.target.value;
            if (searchInp) searchInp.value = val;
            debounceGeocode(val);
        });
    }
}

// ═══════════════════════════════════════════════════════════════════
//  GEOCODING (ADDRESS SEARCH) INTEGRATION
// ═══════════════════════════════════════════════════════════════════
function geocodeVendorAddress(addressText) {
    var query = addressText.trim();
    if (!query.toLowerCase().includes("trincomalee")) query += ", Trincomalee";
    if (!query.toLowerCase().includes("sri lanka")) query += ", Sri Lanka";

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: query }, function(results, status) {
        if (status === 'OK' && results && results.length > 0) {
            vendorLat = results[0].geometry.location.lat();
            vendorLng = results[0].geometry.location.lng();
            var pos = { lat: vendorLat, lng: vendorLng };
            vendorMarker.setPosition(pos);
            console.log(`Google Vendor Geocoding success: ${vendorLat}, ${vendorLng}`);
            if (customerMarker) {
                var bounds = new google.maps.LatLngBounds();
                bounds.extend(vendorMarker.getPosition());
                bounds.extend(customerMarker.getPosition());
                deliveryMap.fitBounds(bounds);
            }
        }
    });
}

async function geocodeAddress(addressText) {
    if (!addressText || addressText.trim().length < 3) return;

    var query = addressText.trim();
    if (!query.toLowerCase().includes("trincomalee")) query += ", Trincomalee";
    if (!query.toLowerCase().includes("sri lanka")) query += ", Sri Lanka";

    console.log(`Google Geocoding search: "${query}"`);
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: query }, function(results, status) {
        if (status === 'OK' && results && results.length > 0) {
            setupCustomerMarker(results[0].geometry.location.lat(), results[0].geometry.location.lng());
        } else {
            console.warn("Google Geocoding failed: " + status + ". Trying Nominatim fallback...");
            tryNominatimGeocode(query, addressText);
        }
    });

    function tryNominatimGeocode(fullQuery, originalText) {
        var url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(fullQuery) + '&limit=1';
        fetch(url, { headers: { 'User-Agent': 'FoodRescueApp/1.0' } })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.length > 0) {
                    console.log("Nominatim geocode success (full query): " + data[0].lat + ", " + data[0].lon);
                    setupCustomerMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
                } else {
                    console.warn("Nominatim full query failed. Retrying without house number...");
                    var fallbackQuery = originalText.replace(/^(no[:\.\s]*)?\d+([\/\-]\d+)?\s*,?\s*/i, '').trim();
                    if (!fallbackQuery.toLowerCase().includes("trincomalee")) fallbackQuery += ", Trincomalee";
                    if (!fallbackQuery.toLowerCase().includes("sri lanka")) fallbackQuery += ", Sri Lanka";

                    var fallbackUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(fallbackQuery) + '&limit=1';
                    fetch(fallbackUrl, { headers: { 'User-Agent': 'FoodRescueApp/1.0' } })
                        .then(function(res) { return res.json(); })
                        .then(function(fallbackData) {
                            if (fallbackData && fallbackData.length > 0) {
                                console.log("Nominatim geocode success (fallback query): " + fallbackData[0].lat + ", " + fallbackData[0].lon);
                                setupCustomerMarker(parseFloat(fallbackData[0].lat), parseFloat(fallbackData[0].lon));
                            } else {
                                console.warn("All geocoding attempts failed.");
                            }
                        })
                        .catch(function(err) { console.error("Nominatim fallback error:", err); });
                }
            })
            .catch(function(err) {
                console.error("Nominatim geocode error:", err);
            });
    }
}

var geocodeDebounceTimer = null;
function debounceGeocode(val) {
    clearTimeout(geocodeDebounceTimer);
    geocodeDebounceTimer = setTimeout(function() {
        geocodeAddress(val);
    }, 1000);
}

function triggerGeocodeSearch() {
    var searchInp = document.getElementById('map-search-input');
    var val = searchInp ? searchInp.value.trim() : '';
    if (val) {
        var addrTa = document.getElementById('delivery_address');
        if (addrTa) addrTa.value = val;
        geocodeAddress(val);
    }
}

function setupCustomerMarker(lat, lng) {
    var pos = { lat: parseFloat(lat), lng: parseFloat(lng) };
    customerLat = pos.lat;
    customerLng = pos.lng;
    if (customerMarker) {
        customerMarker.setPosition(pos);
    } else {
        customerMarker = new google.maps.Marker({
            position: pos,
            map: deliveryMap,
            title: "Your Delivery Pin",
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
            draggable: false
        });
    }

    deliveryMap.setCenter(pos);
    deliveryMap.setZoom(15);

    var bounds = new google.maps.LatLngBounds();
    if (vendorMarker) bounds.extend(vendorMarker.getPosition());
    if (customerMarker) bounds.extend(customerMarker.getPosition());
    if (vendorMarker && customerMarker) deliveryMap.fitBounds(bounds);

    calculateRouteDistance(lat, lng);
}

function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
    var R = 6371; // km
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2)
            + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180)
            * Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Calculate route using DirectionsService and DistanceMatrixService
async function calculateRouteDistance(custLat, custLng) {
    var origin = { lat: parseFloat(vendorLat), lng: parseFloat(vendorLng) };
    var destination = { lat: parseFloat(custLat), lng: parseFloat(custLng) };

    var spinner = document.getElementById('delivery-spinner');
    if (spinner) spinner.classList.remove('d-none');

    var directionsService = new google.maps.DirectionsService();
    directionsService.route({
        origin: origin,
        destination: destination,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(response);
        } else {
            console.warn("Google Directions route failed: " + status);
        }
    });

    var service = new google.maps.DistanceMatrixService();
    service.getDistanceMatrix({
        origins: [origin],
        destinations: [destination],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC
    }, function(response, status) {
        if (spinner) spinner.classList.add('d-none');

        try {
            if (status === 'OK' && 
                response && 
                response.rows && 
                response.rows.length > 0 && 
                response.rows[0].elements && 
                response.rows[0].elements.length > 0 && 
                response.rows[0].elements[0].status === 'OK') {
                
                var element = response.rows[0].elements[0];
                var distanceValue = element.distance.value / 1000.0;
                var durationValueMins = Math.round(element.duration.value / 60.0);

                var timeEl = document.getElementById('hud-time-val');
                if (timeEl) timeEl.textContent = durationValueMins + ' min';

                var disp = document.getElementById('distance-display');
                if (disp) disp.textContent = distanceValue.toFixed(2) + ' km';

                checkDeliveryOptions(distanceValue);
            } else {
                console.warn("Google Distance Matrix failed or returned empty elements. Status: " + status);
                runFallback();
            }
        } catch (e) {
            console.error("Error in Google Distance Matrix callback: ", e);
            runFallback();
        }

        function runFallback() {
            var distanceKm = calculateHaversineDistance(vendorLat, vendorLng, custLat, custLng);
            var durationMins = Math.round(distanceKm * 2 + 5);

            var timeEl = document.getElementById('hud-time-val');
            if (timeEl) timeEl.textContent = durationMins + ' min';

            var disp = document.getElementById('distance-display');
            if (disp) disp.textContent = distanceKm.toFixed(2) + ' km';

            checkDeliveryOptions(distanceKm);
        }
    });
}

function toggleFulfillment(type) {
    isDelivery = (type === 'delivery');

    // Tab active states
    document.getElementById('btn-select-pickup').classList.toggle('selected', !isDelivery);
    document.getElementById('btn-select-delivery').classList.toggle('selected', isDelivery);
    document.getElementById('pickup-check').className =
        isDelivery ? 'fa fa-circle text-muted ms-auto' : 'fa fa-circle-check text-success ms-auto';
    document.getElementById('delivery-check').className =
        isDelivery ? 'fa fa-circle-check text-success ms-auto' : 'fa fa-circle text-muted ms-auto';

    // Hidden input
    document.getElementById('checkout_type').value = type;

    // Show / hide delivery engine panel
    var panel = document.getElementById('delivery-engine-panel');
    if (panel) panel.classList.toggle('d-none', !isDelivery);

    // Show / hide address block
    var addrBox = document.getElementById('delivery-address-container');
    if (addrBox) addrBox.classList.toggle('d-none', !isDelivery);

    // Update time label text
    var timeLabel = document.getElementById('time-label');
    if (timeLabel) timeLabel.innerHTML = isDelivery
        ? '<i class="fa fa-calendar-days me-1 text-success"></i>Preferred Delivery Time'
        : '<i class="fa fa-calendar-days me-1 text-success"></i>Pickup Date &amp; Time';

    if (isDelivery) {
        // Initialize interactive map and fetch dynamic routing distance
        setTimeout(initDeliveryMap, 100);

        // Focus address textarea
        if (addrBox) {
            var ta = addrBox.querySelector('textarea');
            if (ta) setTimeout(function() { ta.focus(); }, 150);
        }
    } else {
        deliveryFeeFromServer = 0;
        deliveryAvailable     = true;
        // Re-enable pay button
        var payBtn = document.getElementById('pay-btn');
        if (payBtn) {
            payBtn.disabled = false;
            payBtn.classList.remove('btn-secondary');
            payBtn.classList.add('btn-success');
        }
    }

    recalcTotal();
}

async function checkDeliveryOptions(distanceKm) {
    var spinner  = document.getElementById('delivery-spinner');
    var panel    = document.getElementById('delivery-engine-panel');
    var msgEl    = document.getElementById('delivery-result-msg');
    var iconEl   = document.getElementById('delivery-result-icon');
    var badgeEl  = document.getElementById('delivery-zone-badge');
    var feeEl    = document.getElementById('delivery-fee-preview');
    var tabDesc  = document.getElementById('delivery-tab-desc');
    var payBtn   = document.getElementById('pay-btn');
    var disp     = document.getElementById('distance-display');

    if (disp) disp.textContent = distanceKm.toFixed(2) + ' km';

    if (spinner) spinner.classList.remove('d-none');
    if (panel)   panel.className = 'mb-3 status-loading';
    panel.style.borderRadius = '12px';
    panel.style.padding      = '16px';
    panel.style.border       = '1.5px solid #d97706';
    panel.style.background   = '#fffbeb';

    try {
        var res  = await fetch(DELIVERY_OPTIONS_URL, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ distance_km: distanceKm }),
        });
        var data = await res.json();

        if (spinner) spinner.classList.add('d-none');

        if (data.available) {
            deliveryFeeFromServer = data.fee;
            deliveryAvailable     = true;

            if (panel) { panel.style.border = '1.5px solid #059669'; panel.style.background = '#f0fdf4'; }
            if (iconEl)  iconEl.textContent = '✅';
            if (msgEl)   msgEl.textContent  = data.message;
            if (feeEl)   feeEl.textContent  = 'Delivery fee: Rs. ' + Number(data.fee).toFixed(2);
            if (tabDesc) tabDesc.textContent = '+Rs. ' + Number(data.fee).toFixed(2) + ' delivery';

            var zoneClass = { base: 'zone-base', extended: 'zone-extended', far: 'zone-far', dynamic: 'zone-base' }[data.zone] || 'zone-base';
            if (badgeEl) badgeEl.innerHTML =
                '<span class="zone-badge ' + zoneClass + '"><i class="fa fa-location-dot me-1"></i>' +
                (data.zone_label || data.zone) + '</span>';

            if (payBtn) { payBtn.disabled = false; payBtn.classList.remove('btn-secondary'); payBtn.classList.add('btn-success'); }

        } else {
            deliveryFeeFromServer = 0;
            deliveryAvailable     = false;

            if (panel) { panel.style.border = '1.5px solid #ef4444'; panel.style.background = '#fef2f2'; }
            if (iconEl)  iconEl.textContent = data.reason === 'out_of_range' ? '🚫' : '⚠️';
            if (msgEl)   msgEl.textContent  = data.message;
            if (feeEl)   feeEl.textContent  = '';
            if (tabDesc) tabDesc.textContent = 'Not available';

            var label = data.reason === 'out_of_range' ? 'Out of Range' : 'Min. Order Not Met';
            if (badgeEl) badgeEl.innerHTML =
                '<span class="zone-badge zone-blocked"><i class="fa fa-ban me-1"></i>' + label + '</span>';

            if (payBtn) { payBtn.disabled = true; payBtn.classList.remove('btn-success'); payBtn.classList.add('btn-secondary'); }
        }

        recalcTotal();

    } catch (err) {
        if (spinner) spinner.classList.add('d-none');
        if (msgEl) msgEl.textContent = 'Could not check delivery options. Please try again.';
        console.error('Delivery check error:', err);
    }
}

// ═══════════════════════════════════════════════════════════════════
//  ERROR HELPER
// ═══════════════════════════════════════════════════════════════════
function showAlert(msg) {
    var box = document.getElementById('checkout-alert');
    if (box) {
        document.getElementById('checkout-alert-msg').textContent = msg;
        box.classList.remove('d-none');
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        alert(msg);
    }
}

// ═══════════════════════════════════════════════════════════════════
//  PAYMENT SUBMISSION
// ═══════════════════════════════════════════════════════════════════
async function submitPayment() {
    var btn = document.getElementById('pay-btn');

    // ── Guard: delivery blocked ──
    if (isDelivery && !deliveryAvailable) {
        showAlert('Home Delivery is not available for your location or order value. Please switch to Self-Pickup or adjust your cart.');
        return;
    }

    // ── Guard: pickup/delivery time ──
    var pickupTime = document.getElementById('pickup_time') ? document.getElementById('pickup_time').value : '';
    if (!pickupTime) {
        showAlert('Please select a pickup / delivery time.');
        return;
    }

    // ── Guard: delivery address ──
    var addrEl       = document.getElementById('delivery_address');
    var deliveryAddr = addrEl ? addrEl.value.trim() : '';
    if (isDelivery && !deliveryAddr) {
        showAlert('Please enter your delivery address.');
        if (addrEl) addrEl.focus();
        return;
    }

    // ── Guard: Stripe must be loaded ──
    if (!window.useMockPayment && (!stripeObj || !stripeCard)) {
        showAlert('Payment system is not ready. Please refresh the page and try again.');
        return;
    }

    // ── Hide previous alerts, set loading ──
    var alertBox = document.getElementById('checkout-alert');
    if (alertBox) alertBox.classList.add('d-none');
    if (btn) { btn.classList.add('loading'); btn.disabled = true; }

    try {
        var loyaltyEl = document.getElementById('loyalty_points');
        var typeEl    = document.getElementById('checkout_type');

        // ── STEP A: Create PaymentIntent on server ──
        var intentRes = await fetch(INTENT_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({
                pickup_time:        pickupTime,
                loyalty_points:     parseInt((loyaltyEl && loyaltyEl.value) ? loyaltyEl.value : 0) || 0,
                checkout_type:      typeEl ? typeEl.value : 'pickup',
                delivery_address:   deliveryAddr,
                delivery_latitude:  customerLat,
                delivery_longitude: customerLng,
            }),
        });

        var intentData = await intentRes.json();
        if (!intentRes.ok || intentData.error) {
            throw new Error(intentData.error || intentData.message || 'Could not create payment. Please try again.');
        }

        // ── STEP B: Confirm card via Stripe.js (CardElement) ──
        var nameEl    = document.getElementById('cardholder-name');
        var cardName  = nameEl ? nameEl.value.trim() : '';

        var result;
        if (intentData.client_secret.startsWith('pi_mock_') || window.useMockPayment) {
            result = {
                paymentIntent: {
                    id: intentData.intent_id,
                    status: 'succeeded'
                }
            };
        } else {
            result = await stripeObj.confirmCardPayment(intentData.client_secret, {
                payment_method: {
                    card: stripeCard,
                    billing_details: { name: cardName },
                },
            });
        }

        if (result.error) {
            throw new Error(result.error.message);
        }

        if (result.paymentIntent.status !== 'succeeded') {
            throw new Error('Payment did not complete. Status: ' + result.paymentIntent.status);
        }

        // ── STEP C: Record order on server ──
        var confirmRes = await fetch(CONFIRM_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ payment_intent_id: result.paymentIntent.id }),
        });

        var confirmData = await confirmRes.json();
        if (!confirmRes.ok || confirmData.error) {
            throw new Error(confirmData.error || 'Order recording failed. Please contact support. Ref: ' + result.paymentIntent.id);
        }

        // ── STEP D: Redirect to success / receipt ──
        window.location.href = confirmData.redirect;

    } catch (err) {
        showAlert(err.message || 'An unexpected error occurred. Please try again.');
        if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
    }
}

// ── Boot ──
recalcTotal();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/customer/checkout.blade.php ENDPATH**/ ?>