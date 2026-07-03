<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show customer panel dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Active orders
        $activeOrdersCount = $user->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->count();

        // Order history
        $reservations = $user->reservations()
            ->with(['business', 'items'])
            ->latest()
            ->take(5)
            ->get();

        // Calculate total savings
        $savedAmount = $user->reservations()
            ->where('status', 'collected')
            ->get()
            ->sum(function($res) {
                return $res->items->sum(function($item) {
                    $food = $item->food;
                    if ($food) {
                        return ($food->original_price - $food->discount_price) * $item->quantity;
                    }
                    return 0;
                });
            });

        // Loyalty points
        $loyaltyPoints = $user->getTotalLoyaltyPoints();

        // CO2 emissions saved: assuming 1 food item ~ 0.5kg saved ~ 1.25kg CO2 offset
        $totalItemsCollected = $user->reservations()
            ->where('status', 'collected')
            ->get()
            ->sum(fn($res) => $res->items->sum('quantity'));
            
        $co2Saved = $totalItemsCollected * 0.5 * 2.5;

        return view('customer.dashboard', compact(
            'activeOrdersCount',
            'savedAmount',
            'loyaltyPoints',
            'co2Saved',
            'reservations'
        ));
    }
}
