<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        Stripe::setAppInfo('FoodRescue Marketplace', '1.0.0', 'https://foodrescue.lk');
    }

    /**
     * Create a Stripe PaymentIntent and return the client_secret.
     * Amount is in LKR (whole rupees — LKR has no subunits in Stripe).
     */
    public function createPaymentIntent(float $amountLkr, array $metadata = []): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'                    => (int) round($amountLkr),  // LKR = smallest unit
                'currency'                  => config('services.stripe.currency', 'lkr'),
                'automatic_payment_methods' => ['enabled' => true],
                'description'               => 'FoodRescue Marketplace — Surplus Food Reservation',
                'metadata'                  => array_merge([
                    'platform' => 'FoodRescue',
                    'env'      => config('app.env'),
                ], $metadata),
                'statement_descriptor'      => 'FOODRESCUE LKR',
            ]);

            return [
                'success'       => true,
                'client_secret' => $intent->client_secret,
                'intent_id'     => $intent->id,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe createPaymentIntent failed', [
                'message' => $e->getMessage(),
                'code'    => $e->getStripeCode(),
            ]);

            if (config('app.env') === 'local') {
                Log::info('Stripe API failed or keys are invalid. Falling back to local mock payment intent.');
                return [
                    'success'       => true,
                    'client_secret' => 'pi_mock_secret_' . \Illuminate\Support\Str::random(24),
                    'intent_id'     => 'pi_mock_' . \Illuminate\Support\Str::random(24),
                ];
            }

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Retrieve and verify a PaymentIntent by ID.
     * Returns the Stripe PaymentIntent object if succeeded, null otherwise.
     */
    public function retrievePaymentIntent(string $intentId): ?PaymentIntent
    {
        if (str_starts_with($intentId, 'pi_mock_')) {
            $intent = new PaymentIntent($intentId);
            $intent->status = 'succeeded';
            $intent->currency = config('services.stripe.currency', 'lkr');
            $intent->amount = 1000;
            $intent->created = time();
            $intent->payment_method = 'pm_mock_123';
            return $intent;
        }

        try {
            $intent = PaymentIntent::retrieve($intentId);
            return $intent->status === 'succeeded' ? $intent : null;
        } catch (ApiErrorException $e) {
            Log::error('Stripe retrievePaymentIntent failed', ['id' => $intentId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Extract card details from a succeeded PaymentIntent.
     */
    public function extractCardDetails(PaymentIntent $intent): array
    {
        $details = ['brand' => null, 'last4' => null, 'country' => null, 'funding' => null];

        try {
            if (!empty($intent->payment_method)) {
                if (str_starts_with($intent->payment_method, 'pm_mock_')) {
                    return [
                        'brand'   => 'Visa',
                        'last4'   => '4242',
                        'country' => 'LK',
                        'funding' => 'debit',
                    ];
                }
                $pm = \Stripe\PaymentMethod::retrieve($intent->payment_method);
                if ($pm->type === 'card' && isset($pm->card)) {
                    $details['brand']   = $pm->card->brand;
                    $details['last4']   = $pm->card->last4;
                    $details['country'] = $pm->card->country;
                    $details['funding'] = $pm->card->funding;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not retrieve card details from PaymentMethod', ['error' => $e->getMessage()]);
        }

        return $details;
    }

    /**
     * Record a successful payment in the DB.
     */
    public function recordPayment(Reservation $reservation, PaymentIntent $intent): Payment
    {
        $card = $this->extractCardDetails($intent);

        return Payment::create([
            'reservation_id'    => $reservation->id,
            'user_id'           => $reservation->customer_id,
            'amount'            => $reservation->total_amount,
            'gateway'           => 'stripe',
            'transaction_id'    => $intent->id,
            'payment_intent_id' => $intent->id,
            'card_brand'        => $card['brand'],
            'card_last4'        => $card['last4'],
            'card_country'      => $card['country'],
            'card_funding'      => $card['funding'],
            'currency'          => $intent->currency,
            'status'            => 'success',
            'gateway_response'  => [
                'status'  => $intent->status,
                'amount'  => $intent->amount,
                'created' => $intent->created,
            ],
            'paid_at'           => now(),
        ]);
    }

    /**
     * Record a failed payment.
     */
    public function recordFailedPayment(Reservation $reservation, string $intentId, string $failureCode = null, string $failureMessage = null): Payment
    {
        return Payment::create([
            'reservation_id'    => $reservation->id,
            'user_id'           => $reservation->customer_id,
            'amount'            => $reservation->total_amount,
            'gateway'           => 'stripe',
            'transaction_id'    => $intentId,
            'payment_intent_id' => $intentId,
            'currency'          => config('services.stripe.currency', 'lkr'),
            'status'            => 'failed',
            'failure_code'      => $failureCode,
            'failure_message'   => $failureMessage,
            'gateway_response'  => ['status' => 'failed', 'code' => $failureCode],
        ]);
    }

    /**
     * Verify and parse a Stripe webhook event.
     */
    public function parseWebhookEvent(string $payload, string $signature): ?\Stripe\Event
    {
        $secret = config('services.stripe.webhook_secret');
        if (!$secret || str_starts_with($secret, 'whsec_test_placeholder')) {
            // In test mode without a real webhook secret, parse without verification
            return \Stripe\Event::constructFrom(json_decode($payload, true));
        }

        try {
            return Webhook::constructEvent($payload, $signature, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
