@extends('layouts.app')

@section('title', 'Secure Checkout | Food Rescue Marketplace Trincomalee')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
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
@endpush

@section('content')
<div class="container py-5">

    {{-- ── Page header ── --}}
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

    {{-- ── Global error alert (filled by JS) ── --}}
    <div id="checkout-alert" class="alert alert-danger d-none shadow-sm rounded-3 mb-3" role="alert">
        <i class="fa fa-triangle-exclamation me-2"></i><span id="checkout-alert-msg"></span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3 mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4 text-start">

        {{-- ══════════════════════════════════════════════════════
             LEFT COLUMN
        ══════════════════════════════════════════════════════ --}}
        <div class="col-lg-7">

            {{-- ── STEP 1: Fulfillment Method ── --}}
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <span class="checkout-step me-2">1</span>Fulfillment Method
                </h5>

                {{-- Tab row --}}
                <div class="row g-3 mb-3">
                    {{-- Self Pickup --}}
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
                    {{-- Home Delivery --}}
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
                </div>{{-- /tab row --}}

                <input type="hidden" id="checkout_type" value="pickup">

                {{-- ── Delivery Engine Panel (hidden until delivery selected) ── --}}
                <div id="delivery-engine-panel" class="mb-3 d-none">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-route text-success"></i>
                        <span class="fw-bold small text-dark">Delivery route map</span>
                    </div>

                    {{-- Address input row --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold small mb-1">
                            <i class="fa fa-map-marker-alt me-1 text-primary"></i>Your Delivery Address
                        </label>
                        <div class="input-group">
                            <textarea id="delivery_address" class="form-control form-control-sm" rows="2"
                                      placeholder="Type your delivery address in Trincomalee…">{{ old('delivery_address', Auth::user()->home_address) }}</textarea>
                            <button type="button" class="btn btn-outline-secondary px-2" title="Use my GPS location"
                                    onclick="useGPSLocation()">
                                <i class="fa fa-location-crosshairs"></i>
                            </button>
                        </div>
                        <small class="text-muted" style="font-size:0.72rem;">
                            Type address and press <strong>Enter</strong>, or click <i class="fa fa-location-crosshairs"></i> for GPS.
                        </small>
                    </div>

                    {{-- Dynamic Metrics Row --}}
                    <div class="p-2 mb-2 bg-light rounded-3 border d-flex justify-content-around text-center" style="border-color: var(--border) !important;">
                        <div>
                            <div class="text-muted" style="font-size:0.72rem;font-weight:600;">📏 Distance</div>
                            <div class="fw-bold text-success small" id="distance-display">-- km</div>
                        </div>
                        <div class="border-start" style="width:1px;height:36px;background:var(--border);"></div>
                        <div>
                            <div class="text-muted" style="font-size:0.72rem;font-weight:600;">⏱️ Est. Travel</div>
                            <div class="fw-bold text-warning small" id="hud-time-val">-- min</div>
                        </div>
                        <div class="border-start" style="width:1px;height:36px;background:var(--border);"></div>
                        <div>
                            <div class="text-muted" style="font-size:0.72rem;font-weight:600;">💳 Delivery Fee</div>
                            <div class="fw-bold text-dark small" id="delivery-fee-inline">Rs. --</div>
                        </div>
                    </div>

                    {{-- Map Container --}}
                    <div class="position-relative mb-2">
                        <div id="delivery-map" style="height:280px;width:100%;border-radius:10px;border:1.5px solid var(--border);" class="shadow-sm"></div>

                        {{-- Map legend overlay --}}
                        <div class="position-absolute bottom-0 start-0 m-2 p-2 bg-white rounded-3 shadow" style="z-index:999;font-size:0.72rem;border:1px solid #e5e7eb;">
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <img src="http://maps.google.com/mapfiles/ms/icons/red-dot.png" style="width:14px;height:14px;object-fit:contain;"> <span>Restaurant</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <img src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png" style="width:14px;height:14px;object-fit:contain;"> <span>Your Address</span>
                            </div>
                        </div>

                        {{-- Spinner overlay --}}
                        <div id="delivery-map-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center d-none"
                             style="background:rgba(255,255,255,0.75);border-radius:10px;z-index:998;">
                            <div class="text-center">
                                <div class="spinner-border text-success mb-2" role="status"></div>
                                <div class="small text-muted fw-semibold" id="overlay-msg">Locating address…</div>
                            </div>
                        </div>
                    </div>

                    <a id="open-google-maps-link" href="#" target="_blank" rel="noopener"
                       class="btn btn-sm btn-outline-secondary d-none mb-2">
                        <i class="fa fa-arrow-up-right-from-square me-1"></i> Open route in Google Maps
                    </a>

                    {{-- Status bar --}}
                    <div class="d-flex align-items-start gap-2">
                        <div id="delivery-result-icon" style="font-size:1.2rem;">📍</div>
                        <div class="flex-grow-1">
                            <div id="delivery-result-msg" class="small fw-semibold text-muted">
                                Type your address above, or tap/click directly on the map to set your exact delivery spot.
                            </div>
                            <div id="delivery-zone-badge" class="mt-1"></div>
                            <div id="delivery-fee-preview" class="mt-1 small fw-bold text-dark"></div>
                        </div>
                        <div id="delivery-spinner" class="spinner-border spinner-border-sm text-success d-none" role="status"></div>
                    </div>
                </div>{{-- /engine panel --}}

                {{-- Pickup / Delivery time — required by the backend, was missing from the UI --}}
                <div class="mt-3 pt-3 border-top">
                    <label for="pickup_time" class="form-label fw-semibold text-dark small" id="time-label">
                        <i class="fa fa-calendar-days me-1 text-success"></i>Pickup Date &amp; Time
                    </label>
                    <input type="datetime-local" id="pickup_time" class="form-control" required>
                    <div class="form-text small text-muted">Must be at least 15 minutes from now.</div>
                </div>

            </div>{{-- /fulfillment card --}}

            {{-- ── STEP 2: Loyalty Points (only if balance > 0) ── --}}
            @if($loyaltyBal > 0)
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <span class="checkout-step me-2">2</span>Loyalty Points Rewards
                </h5>
                <p class="text-muted small mb-3">
                    Your balance: <span class="fw-bold text-success">{{ $loyaltyBal }} points</span>
                    &nbsp;(1 pt = Rs. 1.00 discount)
                </p>
                <div class="d-flex align-items-center gap-2" style="max-width:300px;">
                    <input type="number" id="loyalty_points" class="form-control"
                           placeholder="0" min="0" max="{{ $maxRedeemable }}" value="0">
                    <button type="button" class="btn btn-outline-success px-3"
                            onclick="applyLoyalty()">Apply</button>
                </div>
                <small class="text-muted d-block mt-1">
                    Max redeemable this order: <strong>{{ $maxRedeemable }} pts</strong>
                </small>
            </div>
            @endif

            {{-- ── STEP 3 (or 2): Card Payment ── --}}
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-1 text-dark">
                    <span class="checkout-step me-2">{{ $loyaltyBal > 0 ? '3' : '2' }}</span>Card Payment
                </h5>
                <p class="text-muted small mb-4">
                    Visa or Mastercard. Your card number is handled exclusively by Stripe.
                </p>

                {{-- Demo card hint --}}
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

                {{-- Stripe Card Element --}}
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

                {{-- Cardholder name --}}
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Cardholder Name</label>
                    <input type="text" id="cardholder-name" class="form-control"
                           placeholder="Name as on card"
                           value="{{ Auth::user()->name }}"
                           autocomplete="cc-name" required>
                </div>

                {{-- Pay button --}}
                <button id="pay-btn" type="button"
                        class="btn btn-success w-100 py-3 rounded-pill shadow-lg fw-bold"
                        onclick="submitPayment()">
                    <span class="btn-text">
                        <i class="fa fa-lock me-2"></i>Pay Securely — Rs.&nbsp;<span id="btn-total">{{ number_format($subtotal, 2) }}</span>
                    </span>
                    <span class="btn-spinner">
                        <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                        &nbsp;&nbsp;Processing…
                    </span>
                </button>

                {{-- Powered by Stripe strip --}}
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
            </div>{{-- /card payment card --}}

        </div>{{-- /col-lg-7 --}}

        {{-- ══════════════════════════════════════════════════════
             RIGHT COLUMN: Order Summary
        ══════════════════════════════════════════════════════ --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-white rounded-3 p-4 sticky-top" style="top:90px;">
                <h5 class="fw-bold text-dark mb-4">Order Summary</h5>

                {{-- Item list --}}
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($items as $item)
                    <div class="d-flex align-items-center gap-3">
                        @if($item['image'])
                            <img src="{{ str_starts_with($item['image'], 'assets/') ? asset($item['image']) : asset('storage/' . $item['image']) }}" alt=""
                                 class="rounded-3" style="width:50px;height:50px;object-fit:cover;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-success"
                                 style="width:50px;height:50px;font-size:1.2rem;">🍲</div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-0 small">{{ $item['name'] }}</h6>
                            <span class="text-muted small">Qty: {{ $item['quantity'] }}</span>
                        </div>
                        <div class="fw-bold text-dark text-end small">
                            Rs. {{ number_format($item['discount_price'] * $item['quantity'], 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2 text-muted small">
                    <span>Items Subtotal</span>
                    <span class="fw-bold text-dark">Rs. {{ number_format($subtotal, 2) }}</span>
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
                    <span class="fw-bold fs-5 text-success">Rs. <span id="summary-total">{{ number_format($subtotal, 2) }}</span></span>
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
        </div>{{-- /col-lg-5 --}}

    </div>{{-- /row --}}
</div>{{-- /container --}}
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
// Vendor Location
var vendorLat = {{ $business?->latitude ?? 8.5755 }};
var vendorLng = {{ $business?->longitude ?? 81.2285 }};
var vendorAddress = "{{ addslashes($business?->address ?? '') }}";
var vendorName = "{{ addslashes($business?->business_name ?? 'Vendor') }}";
// ═══════════════════════════════════════════════════════════════════
//  CONSTANTS  (PHP → JS)
// ═══════════════════════════════════════════════════════════════════
var STRIPE_PK            = @json($stripeKey);
var SUBTOTAL             = {{ (float) $subtotal }};
var MAX_REDEEM           = {{ (int) ($maxRedeemable ?? 0) }};
// CSRF_TOKEN is already defined globally in app.js
var INTENT_URL           = '{{ route("customer.checkout.intent") }}';
var CONFIRM_URL          = '{{ route("customer.checkout.confirm") }}';
var DELIVERY_OPTIONS_URL = '{{ route("customer.checkout.delivery-options") }}';

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
var deliveryMap           = null;   // Leaflet map instance for the delivery panel
var vendorMarker          = null;
var customerMarker        = null;
var routeLine             = null;   // Leaflet polyline for the OSRM route

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
// ═══════════════════════════════════════════════════════════════════
//  FULFILLMENT TOGGLE  — the core fix
// ═══════════════════════════════════════════════════════════════════
function initDeliveryMap() {
    var mapDiv = document.getElementById('delivery-map');
    if (!mapDiv) return;

    if (!deliveryMap) {
        deliveryMap = L.map(mapDiv).setView([vendorLat, vendorLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(deliveryMap);

        vendorMarker = L.marker([vendorLat, vendorLng], {
            icon: L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41]
            })
        }).addTo(deliveryMap).bindPopup('🏪 ' + vendorName + ' — pickup point');

        // Click anywhere on the map to drop/move the delivery pin there directly —
        // this is faster than typing when the customer can recognise their own
        // neighbourhood visually (free geocoding won't know every small lane).
        deliveryMap.on('click', function (e) {
            setupCustomerMarker(e.latlng.lat, e.latlng.lng);
            setDeliveryStatus('ok', '📍', 'Location set from map click — drag the pin to adjust further.');
        });

        // If the vendor's own coordinates are still the platform-wide default
        // (never geocoded correctly at registration time), try a one-time
        // correction using their stored address text.
        if (Math.abs(vendorLat - 8.5755) < 0.0001 && Math.abs(vendorLng - 81.2285) < 0.0001 && vendorAddress) {
            geocodeVendorAddress(vendorAddress);
        }
    } else {
        setTimeout(function () { deliveryMap.invalidateSize(); }, 50);
        deliveryMap.setView([vendorLat, vendorLng], 13);
    }

    var addrTa = document.getElementById('delivery_address');
    if (addrTa) {
        // If pre-filled, geocode after map tiles load
        var existing = addrTa.value.trim();
        if (existing.length > 3) {
            setTimeout(function() { geocodeAddress(existing); }, 800);
        }
    }
}

// ── GPS button ────────────────────────────────────────────────────────────────
function useGPSLocation() {
    if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }
    showMapOverlay('Detecting GPS location…');
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude, lng = pos.coords.longitude;
            setupCustomerMarker(lat, lng);

            // Reverse-geocode via Nominatim (free, no key) to fill the address box
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng)
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var ta = document.getElementById('delivery_address');
                    if (ta && data && data.display_name) ta.value = data.display_name;
                })
                .catch(function(err) { console.warn('Reverse geocode failed:', err); });
        },
        function(err) {
            hideMapOverlay();
            // Only overwrite the status message if we don't already have a
            // valid pin placed — otherwise a flaky retry shouldn't hide a
            // result that already worked.
            if (!customerMarker) {
                var reasons = {
                    1: 'Location permission denied. Please allow location access and try again.',
                    2: 'Could not detect your location. Try moving near a window, or type your address.',
                    3: 'GPS timed out — your signal may be weak indoors. Try again or type your address.',
                };
                setDeliveryStatus('error', '❌', reasons[err.code] || 'GPS failed. Please type your address.');
            }
        },
        { enableHighAccuracy: true, timeout: 20000, maximumAge: 60000 }
    );
}

function showMapOverlay(msg) {
    var ov = document.getElementById('delivery-map-overlay');
    var lb = document.getElementById('overlay-msg');
    if (ov) ov.classList.remove('d-none');
    if (lb) lb.textContent = msg || 'Loading…';
}
function hideMapOverlay() {
    var ov = document.getElementById('delivery-map-overlay');
    if (ov) ov.classList.add('d-none');
}
function setDeliveryStatus(type, icon, msg) {
    var ic = document.getElementById('delivery-result-icon');
    var mg = document.getElementById('delivery-result-msg');
    if (ic) ic.textContent = icon;
    if (mg) mg.textContent = msg;
}

// ── Vendor address geocoder (only for default fallback coords) ────────────────
function geocodeVendorAddress(addressText) {
    var q = addressText.trim();
    if (!q.toLowerCase().includes('trincomalee')) q += ', Trincomalee, Sri Lanka';

    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1')
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.length > 0) {
                vendorLat = parseFloat(data[0].lat);
                vendorLng = parseFloat(data[0].lon);
                if (vendorMarker) {
                    vendorMarker.setLatLng([vendorLat, vendorLng]);
                    deliveryMap.setView([vendorLat, vendorLng], deliveryMap.getZoom());
                }
            } else {
                console.warn('Vendor geocode: no results for "' + q + '"');
            }
        })
        .catch(function(err) { console.warn('Vendor geocode failed: ', err); });
}

// ── Customer delivery-address geocoder (typed / debounced) ────────────────────
function geocodeAddress(addressText) {
    var query = addressText.trim();
    if (!query.toLowerCase().includes('trincomalee')) query += ', Trincomalee, Sri Lanka';

    showMapOverlay('Locating address…');

    var url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=1';
    fetch(url, { headers: { 'Accept-Language': 'en' } })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.length > 0) {
                console.log("Nominatim geocode success (full query): " + data[0].lat + ", " + data[0].lon);
                setupCustomerMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } else {
                console.warn("Nominatim full query failed. Retrying without house number...");
                var fallbackQuery = addressText.replace(/^(no[:\.\s]*)?\d+([\/\-]\d+)?\s*,?\s*/i, '').trim();
                if (!fallbackQuery.toLowerCase().includes("trincomalee")) fallbackQuery += ", Trincomalee";
                if (!fallbackQuery.toLowerCase().includes("sri lanka")) fallbackQuery += ", Sri Lanka";

                var fallbackUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(fallbackQuery) + '&limit=1';
                fetch(fallbackUrl, { headers: { 'Accept-Language': 'en' } })
                    .then(function(res) { return res.json(); })
                    .then(function(fallbackData) {
                        if (fallbackData && fallbackData.length > 0) {
                            console.log("Nominatim geocode success (fallback query): " + fallbackData[0].lat + ", " + fallbackData[0].lon);
                            setupCustomerMarker(parseFloat(fallbackData[0].lat), parseFloat(fallbackData[0].lon));
                        } else {
                            console.warn("Fallback query failed too. Trying broader area (last 2 segments)...");
                            tryBroadAreaThenGiveUp();
                        }
                    })
                    .catch(function(err) {
                        console.error("Nominatim fallback error:", err);
                        tryBroadAreaThenGiveUp();
                    });
            }
        })
        .catch(function(err) {
            console.error("Nominatim geocode error:", err);
            tryBroadAreaThenGiveUp();
        });

    // Small streets/lanes are often missing from free OSM data in this area.
    // Last resort: geocode just the broad area (e.g. "Abhayapura, Trincomalee")
    // so the pin at least lands in the right neighbourhood, then let the
    // customer drag it to the exact spot themselves.
    function tryBroadAreaThenGiveUp() {
        var parts = addressText.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
        var broadQuery = parts.slice(-2).join(', ');
        if (!broadQuery || broadQuery.length < 3) { giveUp(); return; }
        if (!broadQuery.toLowerCase().includes('trincomalee')) broadQuery += ', Trincomalee';
        broadQuery += ', Sri Lanka';

        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(broadQuery) + '&limit=1',
              { headers: { 'Accept-Language': 'en' } })
            .then(function(res) { return res.json(); })
            .then(function(broadData) {
                if (broadData && broadData.length > 0) {
                    console.log("Nominatim geocode success (broad area): " + broadData[0].lat + ", " + broadData[0].lon);
                    setupCustomerMarker(parseFloat(broadData[0].lat), parseFloat(broadData[0].lon));
                    setDeliveryStatus('ok', '📍', 'Pinned to the general area — drag the marker to your exact address.');
                } else {
                    giveUp();
                }
            })
            .catch(function() { giveUp(); });
    }

    function giveUp() {
        console.warn("All geocoding attempts failed — dropping pin at vendor location as a starting point.");
        hideMapOverlay();
        // Drop a draggable pin near the vendor so the customer can position it
        // themselves rather than being stuck with nothing on the map.
        setupCustomerMarker(vendorLat, vendorLng);
        setDeliveryStatus('error', '⚠️', "Couldn't find that address automatically — drag the blue pin to your exact location.");
    }
}

function setupCustomerMarker(lat, lng) {
    customerLat = parseFloat(lat);
    customerLng = parseFloat(lng);

    hideMapOverlay();

    if (customerMarker) {
        customerMarker.setLatLng([customerLat, customerLng]);
    } else {
        customerMarker = L.marker([customerLat, customerLng], {
            draggable: true,
            icon: L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41]
            })
        }).addTo(deliveryMap).bindPopup('📦 Your delivery location — drag to fine-tune');

        // Free geocoding (Nominatim) is sometimes imprecise for small streets —
        // letting the customer drag the pin to the exact spot fixes that
        // without depending on geocoding accuracy.
        customerMarker.on('dragend', function (e) {
            var pos = e.target.getLatLng();
            customerLat = pos.lat;
            customerLng = pos.lng;
            setDeliveryStatus('ok', '📍', 'Custom location set — recalculating route and fee…');
            calculateRouteDistance(customerLat, customerLng);
        });
    }

    setDeliveryStatus('ok', '📍', 'Delivery pin placed — calculating route and fee…');

    // "Open in Google Maps" — a plain link needs no API key or billing at all,
    // unlike embedding the Google Maps JS API. This just opens the customer's
    // own Google Maps app/site with turn-by-turn directions already filled in.
    var gmapsLink = document.getElementById('open-google-maps-link');
    if (gmapsLink) {
        gmapsLink.href = 'https://www.google.com/maps/dir/?api=1'
            + '&origin=' + vendorLat + ',' + vendorLng
            + '&destination=' + customerLat + ',' + customerLng
            + '&travelmode=driving';
        gmapsLink.classList.remove('d-none');
    }

    // Fit map to show both vendor + customer pins
    if (vendorMarker) {
        var bounds = L.latLngBounds([vendorMarker.getLatLng(), [customerLat, customerLng]]);
        deliveryMap.fitBounds(bounds, { padding: [50, 50] });
    } else {
        deliveryMap.setView([customerLat, customerLng], 15);
    }

    calculateRouteDistance(customerLat, customerLng);
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

// Calculate route using OSRM (free, no key needed) — one call gives distance,
// duration, and the route geometry to draw on the map.
async function calculateRouteDistance(custLat, custLng) {
    var spinner = document.getElementById('delivery-spinner');
    if (spinner) spinner.classList.remove('d-none');

    // OSRM expects coordinates as lng,lat (not lat,lng)
    var url = 'https://router.project-osrm.org/route/v1/driving/'
            + vendorLng + ',' + vendorLat + ';' + custLng + ',' + custLat
            + '?overview=full&geometries=geojson';

    try {
        var res  = await fetch(url);
        var data = await res.json();

        if (spinner) spinner.classList.add('d-none');

        if (data && data.code === 'Ok' && data.routes && data.routes.length > 0) {
            var route         = data.routes[0];
            var distanceValue = route.distance / 1000.0;          // km
            var durationMins  = Math.round(route.duration / 60.0); // minutes

            var timeEl = document.getElementById('hud-time-val');
            if (timeEl) timeEl.textContent = durationMins + ' min';

            var disp = document.getElementById('distance-display');
            if (disp) disp.textContent = distanceValue.toFixed(2) + ' km';

            // Draw the route line on the map
            var coords = route.geometry.coordinates.map(function(c) { return [c[1], c[0]]; });
            if (routeLine) deliveryMap.removeLayer(routeLine);
            routeLine = L.polyline(coords, { color: '#059669', weight: 4, opacity: 0.8 }).addTo(deliveryMap);

            checkDeliveryOptions(distanceValue);
        } else {
            console.warn("OSRM route failed. Response: ", data);
            runFallback();
        }
    } catch (e) {
        if (spinner) spinner.classList.add('d-none');
        console.error("Error fetching OSRM route: ", e);
        runFallback();
    }

    function runFallback() {
        var distanceKm   = calculateHaversineDistance(vendorLat, vendorLng, custLat, custLng);
        var durationMins = Math.round(distanceKm * 2 + 5); // rough estimate when OSRM is unreachable

        var timeEl = document.getElementById('hud-time-val');
        if (timeEl) timeEl.textContent = durationMins + ' min';

        var disp = document.getElementById('distance-display');
        if (disp) disp.textContent = distanceKm.toFixed(2) + ' km';

        checkDeliveryOptions(distanceKm);
    }
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
    var feeHud   = document.getElementById('delivery-fee-inline');
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
            if (feeHud)  feeHud.textContent = 'Rs. ' + Number(data.fee).toFixed(2);
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
            if (feeHud)  feeHud.textContent = 'Rs. --';
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
// Wire up the delivery-address textarea: typing (debounced) and pressing
// Enter both trigger a geocode + map pin update. Previously these functions
// existed but were never actually connected to the input.
var deliveryAddrInput = document.getElementById('delivery_address');
if (deliveryAddrInput) {
    deliveryAddrInput.addEventListener('input', function () {
        debounceGeocode(this.value);
    });
    deliveryAddrInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // don't insert a newline in the textarea
            clearTimeout(geocodeDebounceTimer);
            triggerGeocodeSearch();
        }
    });
}

// Pickup/delivery time input — set a sensible minimum (15 mins from now) and
// a default value (30 mins from now) since the browser won't do this for us.
var pickupTimeInput = document.getElementById('pickup_time');
if (pickupTimeInput) {
    function toLocalDatetimeValue(d) {
        var pad = function(n) { return String(n).padStart(2, '0'); };
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())
             + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }
    var now = new Date();
    var minTime = new Date(now.getTime() + 15 * 60000);
    var defaultTime = new Date(now.getTime() + 30 * 60000);
    pickupTimeInput.min = toLocalDatetimeValue(minTime);
    pickupTimeInput.value = toLocalDatetimeValue(defaultTime);
}

recalcTotal();
</script>
@endpush
