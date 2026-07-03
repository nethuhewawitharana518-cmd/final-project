<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Services\CommissionService;
use App\Services\LoyaltyService;
use App\Services\NotificationService;
use App\Services\QRCodeService;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private CommissionService    $commissionService,
        private QRCodeService        $qrCodeService,
        private LoyaltyService       $loyaltyService,
        private StripePaymentService $stripeService,
    ) {}

    // ─────────────────────────────────────────────────────────────────
    //  STEP 1: Show checkout page
    // ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty.');
        }

        $items         = collect($cart);
        $subtotal      = $items->sum(fn($i) => $i['discount_price'] * $i['quantity']);
        $loyaltyBal    = $this->loyaltyService->getBalance(Auth::id());
        $maxRedeemable = min(floor($subtotal), $loyaltyBal);
        $stripeKey     = config('services.stripe.key');

        // Fetch business details for the interactive delivery map
        $businessId = $items->first()['business_id'] ?? null;
        $business   = \App\Models\Business::find($businessId);

        return view('customer.checkout', compact('items', 'subtotal', 'loyaltyBal', 'maxRedeemable', 'stripeKey', 'business'));
    }

    // ─────────────────────────────────────────────────────────────────
    //  STEP 2: Create Stripe PaymentIntent (AJAX / JSON)
    // ─────────────────────────────────────────────────────────────────
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'pickup_time'        => 'required|date|after:now',
            'loyalty_points'     => 'nullable|integer|min:0',
            'checkout_type'      => 'nullable|in:pickup,delivery',
            'delivery_address'   => 'required_if:checkout_type,delivery|nullable|string|max:500',
            'delivery_latitude'  => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        $cartItems = collect($cart);
        $subtotal  = $cartItems->sum(fn($i) => $i['discount_price'] * $i['quantity']);

        // Loyalty discount preview (don't redeem yet — only after payment confirmed)
        $loyaltyPointsUsed = (int) $request->loyalty_points;
        $loyaltyBal        = $this->loyaltyService->getBalance(Auth::id());
        $loyaltyDiscount   = min($loyaltyPointsUsed, $subtotal, $loyaltyBal);

        $checkoutType = $request->input('checkout_type', 'pickup');

        // Use the dynamic delivery fee computed by calculateDelivery() and stored in session.
        // Falls back to 0 for pickup or if no delivery check was run.
        $dynamicFee  = session('delivery_option.fee', 0.00);
        $deliveryFee = $checkoutType === 'delivery' ? (float) $dynamicFee : 0.00;
        $totalAmount = max(1, $subtotal - $loyaltyDiscount + $deliveryFee); // Min 1 LKR

        // Store order metadata in session for use after payment confirmation
        session()->put('pending_checkout', [
            'pickup_time'        => $request->pickup_time,
            'checkout_type'      => $checkoutType,
            'delivery_address'   => $request->delivery_address,
            'delivery_latitude'  => $request->delivery_latitude ? (float) $request->delivery_latitude : null,
            'delivery_longitude' => $request->delivery_longitude ? (float) $request->delivery_longitude : null,
            'loyalty_points'     => $loyaltyPointsUsed,
            'loyalty_discount'   => $loyaltyDiscount,
            'subtotal'           => $subtotal,
            'delivery_fee'       => $deliveryFee,
            'total_amount'       => $totalAmount,
            'cart'               => $cart,
            'business_id'        => $cartItems->first()['business_id'],
        ]);

        // Create Stripe PaymentIntent
        $result = $this->stripeService->createPaymentIntent($totalAmount, [
            'customer_user_id' => (string) Auth::id(),
            'checkout_type'    => $checkoutType,
        ]);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 500);
        }

        return response()->json([
            'client_secret' => $result['client_secret'],
            'intent_id'     => $result['intent_id'],
            'amount'        => $totalAmount,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  STEP 3: Confirm payment after Stripe.js success (AJAX / JSON)
    // ─────────────────────────────────────────────────────────────────
    public function confirm(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string|starts_with:pi_',
        ]);

        $intentId = $request->payment_intent_id;

        // Verify with Stripe that the payment actually succeeded
        $intent = $this->stripeService->retrievePaymentIntent($intentId);

        if (!$intent) {
            return response()->json(['error' => 'Payment verification failed. Please contact support.'], 422);
        }

        // Retrieve pending checkout data
        $checkout = session()->get('pending_checkout');
        if (!$checkout) {
            return response()->json(['error' => 'Session expired. Please try again.'], 422);
        }

        // Check PaymentIntent not already used (idempotency)
        if (\App\Models\Payment::where('payment_intent_id', $intentId)->exists()) {
            $existingPayment = \App\Models\Payment::where('payment_intent_id', $intentId)->first();
            session()->forget(['pending_checkout', 'cart']);
            return response()->json(['redirect' => route('customer.checkout.success', $existingPayment->reservation_id)]);
        }

        try {
            $reservationId = DB::transaction(function () use ($checkout, $intent, $intentId) {
                $cartItems = collect($checkout['cart']);

                // 1. Redeem loyalty points NOW (payment confirmed)
                $loyaltyDiscount = 0.00;
                if ($checkout['loyalty_points'] > 0) {
                    $loyaltyDiscount = $this->loyaltyService->redeem(Auth::user(), $checkout['loyalty_points']);
                }

                $commission = $this->commissionService->calculate($checkout['total_amount']);

                // 2. Create Reservation
                $notes = $checkout['checkout_type'] === 'delivery'
                    ? "Method: Delivery\nAddress: " . $checkout['delivery_address']
                    : "Method: Store Pickup";

                $reservation = Reservation::create([
                    'customer_id'         => Auth::id(),
                    'business_id'         => $checkout['business_id'],
                    'reservation_code'    => 'FR-' . strtoupper(Str::random(8)),
                    'pickup_time'         => $checkout['pickup_time'],
                    'subtotal'            => $checkout['subtotal'],
                    'platform_commission' => $commission['commission_amount'],
                    'business_earnings'   => $commission['business_earnings'],
                    'loyalty_points_used' => $checkout['loyalty_points'],
                    'loyalty_discount'    => $loyaltyDiscount,
                    'total_amount'        => $checkout['total_amount'],
                    'status'              => 'paid',
                    'notes'               => $notes,
                    'delivery_method'     => $checkout['checkout_type'],
                    'delivery_address'    => $checkout['checkout_type'] === 'delivery' ? $checkout['delivery_address'] : null,
                    'delivery_fee'        => $checkout['delivery_fee'],
                    'delivery_latitude'   => $checkout['checkout_type'] === 'delivery' ? ($checkout['delivery_latitude'] ?? null) : null,
                    'delivery_longitude'  => $checkout['checkout_type'] === 'delivery' ? ($checkout['delivery_longitude'] ?? null) : null,
                ]);

                // 3. Create Order Items & reduce food quantities
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'reservation_id' => $reservation->id,
                        'food_id'        => $item['food_id'],
                        'food_name'      => $item['name'],
                        'quantity'       => $item['quantity'],
                        'unit_price'     => $item['discount_price'],
                        'total_price'    => $item['discount_price'] * $item['quantity'],
                    ]);

                    $food = Food::find($item['food_id']);
                    if ($food) {
                        $newQty = max(0, $food->available_quantity - $item['quantity']);
                        $food->update([
                            'available_quantity' => $newQty,
                            'status'             => $newQty === 0 ? 'sold_out' : 'active',
                        ]);
                    }
                }

                // 4. Record Payment (with Stripe details)
                $this->stripeService->recordPayment($reservation, $intent);

                // 5. Record Commission
                $this->commissionService->record($reservation);

                // 6. Generate QR Code
                $this->qrCodeService->generate($reservation);

                // 7. Award Loyalty Points
                $loyaltyEntry = $this->loyaltyService->award($reservation);

                // 8. Send Notifications
                NotificationService::orderConfirmed(Auth::user(), $reservation->reservation_code, $reservation->id);
                NotificationService::loyaltyPointsEarned(Auth::user(), $loyaltyEntry->points_earned, $loyaltyEntry->balance);

                $businessUser = $reservation->business->user;
                NotificationService::newReservation($businessUser, $reservation->reservation_code);

                return $reservation->id;
            });

            // Clear cart and pending checkout
            session()->forget(['cart', 'pending_checkout']);

            return response()->json(['redirect' => route('customer.checkout.success', $reservationId)]);

        } catch (\Throwable $e) {
            Log::error('Checkout confirm failed', [
                'intent_id' => $intentId,
                'user_id'   => Auth::id(),
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Order creation failed: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  DELIVERY ENGINE: AI-driven dynamic fee & eligibility check
    //  POST /customer/checkout/delivery-options
    //  Body: { distance_km: float }
    //  Response JSON:
    //    { available, reason, message, fee, zone, cart_total, distance_km }
    // ─────────────────────────────────────────────────────────────────
    public function calculateDelivery(Request $request)
    {
        $request->validate([
            'distance_km' => 'required|numeric|min:0|max:200',
        ]);

        $cart      = session()->get('cart', []);
        $cartTotal = collect($cart)->sum(fn($i) => $i['discount_price'] * $i['quantity']);
        $distance  = (float) $request->distance_km;

        // ── Rule 1: Minimum cart value ──────────────────────────────
        if ($cartTotal < 500.00) {
            $payload = [
                'available'  => false,
                'reason'     => 'min_cart',
                'message'    => 'Home Delivery requires a minimum order of Rs. 500.00. Please add more items or use Self-Pickup.',
                'fee'        => 0,
                'zone'       => null,
                'cart_total' => round($cartTotal, 2),
                'distance_km'=> $distance,
            ];
            session()->put('delivery_option', $payload);
            return response()->json($payload);
        }

        // ── Rule 2: Out-of-range (> 10 km) ─────────────────────────
        if ($distance > 10) {
            $payload = [
                'available'  => false,
                'reason'     => 'out_of_range',
                'message'    => 'Your location is outside our delivery range (max 10 km). Please choose Self-Pickup.',
                'fee'        => 0,
                'zone'       => 'out_of_range',
                'cart_total' => round($cartTotal, 2),
                'distance_km'=> $distance,
            ];
            session()->put('delivery_option', $payload);
            return response()->json($payload);
        }

        // ── Rule 3: Dynamic fee ──────────────────────
        if ($distance <= 1.00) {
            $fee = 100.00;
        } else {
            $fee = 100.00 + (($distance - 1.00) * 80.00);
        }
        $fee = round($fee, 2);
        $zoneLabel = 'Rs. 100.00 base + Rs. 80.00/km above 1km';

        $payload = [
            'available'   => true,
            'reason'      => 'ok',
            'message'     => 'Delivery available — ' . $zoneLabel,
            'fee'         => $fee,
            'zone'        => 'dynamic',
            'zone_label'  => $zoneLabel,
            'cart_total'  => round($cartTotal, 2),
            'distance_km' => $distance,
        ];

        // Persist fee so createPaymentIntent() can read it
        session()->put('delivery_option', $payload);

        Log::info('Delivery fee calculated', [
            'user_id'     => Auth::id(),
            'distance_km' => $distance,
            'fee'         => $fee,
            'zone'        => 'dynamic',
            'cart_total'  => $cartTotal,
        ]);

        return response()->json($payload);
    }

    // ─────────────────────────────────────────────────────────────────
    //  STEP 4: Payment Failed page
    // ─────────────────────────────────────────────────────────────────
    public function paymentFailed(Request $request)
    {
        $reason = $request->query('reason', 'Your payment was not completed.');
        return view('customer.payment-failed', compact('reason'));
    }

    // ─────────────────────────────────────────────────────────────────
    //  SUCCESS: Receipt page (unchanged)
    // ─────────────────────────────────────────────────────────────────
    public function success(int $id)
    {
        $reservation = Reservation::with(['items', 'business', 'qrCode', 'payment'])
            ->where('customer_id', Auth::id())
            ->findOrFail($id);

        return view('customer.checkout-success', compact('reservation'));
    }
}
