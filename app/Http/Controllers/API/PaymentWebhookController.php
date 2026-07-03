<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(private StripePaymentService $stripeService) {}

    /**
     * Handle Stripe webhook events (async backup for payment confirmation).
     * This runs even if the customer closed their browser mid-payment.
     */
    public function handleStripe(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        $event = $this->stripeService->parseWebhookEvent($payload, $signature);

        if (!$event) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        match ($event->type) {
            'payment_intent.succeeded'       => $this->handleSucceeded($event->data->object),
            'payment_intent.payment_failed'  => $this->handleFailed($event->data->object),
            'charge.refunded'                => $this->handleRefunded($event->data->object),
            default                          => null, // Ignore unhandled events
        };

        return response()->json(['received' => true]);
    }

    /**
     * Legacy handler (keep for backward compat with existing route & integration tests).
     */
    public function handle(Request $request)
    {
        // If it is a Stripe signature event or contains standard Stripe fields, forward to Stripe handler
        if ($request->hasHeader('Stripe-Signature') || ($request->has('type') && $request->has('data'))) {
            return $this->handleStripe($request);
        }

        // Handle legacy format (used in CommissionTest.php)
        $request->validate([
            'order_id'    => 'required|string',
            'status_code' => 'required|integer',
            'payment_id'  => 'required|string',
        ]);

        $reservation = Reservation::where('reservation_code', $request->order_id)->first();
        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        // status_code = 2 represents paid/success in legacy flow
        if ($request->status_code == 2) {
            $reservation->update(['status' => 'paid']);

            // Create Payment record
            Payment::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $reservation->customer_id,
                'amount'         => $reservation->total_amount,
                'gateway'        => 'visa',
                'transaction_id' => $request->payment_id,
                'status'         => 'success',
                'paid_at'        => now(),
            ]);

            // Record Commission
            $commissionService = app(\App\Services\CommissionService::class);
            $commissionService->record($reservation);
        }

        return response()->json(['message' => 'Payment processed successfully.']);
    }

    // ─── Event handlers ───────────────────────────────────────────────

    private function handleSucceeded(\Stripe\PaymentIntent $intent): void
    {
        // Check if already processed (idempotency)
        $existing = Payment::where('payment_intent_id', $intent->id)->first();
        if ($existing && $existing->status === 'success') {
            Log::info('Webhook: PaymentIntent already processed', ['id' => $intent->id]);
            return;
        }

        // If payment was recorded during browser flow, just ensure status is correct
        if ($existing) {
            $existing->update(['status' => 'success', 'paid_at' => now()]);
            // Ensure reservation is paid
            if ($existing->reservation) {
                $existing->reservation->update(['status' => 'paid']);
            }
            Log::info('Webhook: Updated existing payment to success', ['id' => $intent->id]);
            return;
        }

        // Edge case: payment succeeded but browser confirm endpoint was never called
        // This shouldn't happen normally but is a safety net
        Log::warning('Webhook: Succeeded PaymentIntent has no Payment record — manual review needed', [
            'intent_id' => $intent->id,
            'metadata'  => $intent->metadata,
        ]);
    }

    private function handleFailed(\Stripe\PaymentIntent $intent): void
    {
        $existing = Payment::where('payment_intent_id', $intent->id)->first();

        if ($existing) {
            $existing->update([
                'status'          => 'failed',
                'failure_code'    => $intent->last_payment_error?->code,
                'failure_message' => $intent->last_payment_error?->message,
            ]);

            // If reservation was somehow already created, revert to pending
            if ($existing->reservation && $existing->reservation->status === 'paid') {
                $existing->reservation->update(['status' => 'pending']);
            }
        }

        Log::warning('Stripe payment failed', [
            'intent_id' => $intent->id,
            'code'      => $intent->last_payment_error?->code,
            'message'   => $intent->last_payment_error?->message,
        ]);
    }

    private function handleRefunded(\Stripe\Charge $charge): void
    {
        $payment = Payment::where('transaction_id', $charge->payment_intent)->first();
        if ($payment) {
            $payment->update(['status' => 'refunded']);
            Log::info('Payment marked as refunded', ['payment_id' => $payment->id]);
        }
    }
}
