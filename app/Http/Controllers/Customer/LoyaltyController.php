<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function __construct(private LoyaltyService $loyaltyService) {}

    /**
     * Display customer loyalty program details.
     */
    public function index()
    {
        $user = Auth::user();
        $balance = $this->loyaltyService->getBalance($user->id);
        $tier = $this->loyaltyService->calculateTier($balance);
        $redemptionOptions = $this->loyaltyService->getRedemptionOptions();
        
        $history = $user->loyaltyPoints()
            ->latest()
            ->paginate(10);

        return view('customer.loyalty', compact('balance', 'tier', 'redemptionOptions', 'history'));
    }

    /**
     * Redeem customer loyalty points.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|in:100,500',
        ]);

        $user = Auth::user();
        
        try {
            $discount = $this->loyaltyService->redeem($user, (int) $request->points);
            return back()->with('success', "Successfully redeemed {$request->points} points for Rs. " . number_format($discount, 2) . " voucher!");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
