<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionActiveMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isBusinessOwner()) {
            $business = $user->business;
            if (!$business || !$business->hasActiveSubscription()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'An active subscription is required to manage food listings.'], 403);
                }
                return redirect()->route('business.subscription')->with('error', 'An active subscription plan is required to upload and manage food listings. Please select and activate a plan.');
            }
        }

        return $next($request);
    }
}
