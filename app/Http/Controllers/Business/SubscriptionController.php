<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display current subscription info and plan options.
     */
    public function index()
    {
        $business = Auth::user()->business;
        $activeSubscription = $business->activeSubscription();
        $plans = config('subscription.plans');
        
        $history = $business->subscriptions()
            ->latest()
            ->get();

        return view('business.subscription', compact('business', 'activeSubscription', 'plans', 'history'));
    }

    /**
     * Process mock subscription activation.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|in:starter,professional,enterprise',
        ]);

        $business = Auth::user()->business;
        $plans = config('subscription.plans');
        $plan = $plans[$request->plan_type];

        // Create mock payment entry
        $payment = Payment::create([
            'reservation_id'   => null, // Null for subscription payment
            'user_id'          => Auth::id(),
            'amount'           => $plan['price'],
            'gateway'          => 'subscription',
            'transaction_id'   => 'SUB-' . strtoupper(uniqid()),
            'status'           => 'success',
            'gateway_response' => ['plan' => $request->plan_type],
            'paid_at'          => now(),
        ]);

        // Cancel previous active plans
        $business->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Create new active subscription
        Subscription::create([
            'business_id'  => $business->id,
            'plan_type'    => $request->plan_type,
            'price'        => $plan['price'],
            'upload_limit' => $plan['upload_limit'],
            'start_date'   => now(),
            'end_date'     => now()->addDays($plan['duration']),
            'status'       => 'active',
            'payment_id'   => $payment->id,
        ]);

        return redirect()->route('business.subscription')->with('success', 'Subscription activated successfully!');
    }

    /**
     * Display checkout screen for a subscription plan payment.
     */
    public function paymentPage(Request $request)
    {
        $plan = $request->get('plan', 'starter');
        $plans = config('subscription.plans');
        
        if (!isset($plans[$plan])) {
            return redirect()->route('business.subscription')->with('error', 'Invalid plan type.');
        }

        $planDetails = $plans[$plan];

        return view('business.payment', compact('plan', 'planDetails'));
    }
}
