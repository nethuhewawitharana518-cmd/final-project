<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show business dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('home')->with('error', 'Business record not found.');
        }

        $subscription = $business->activeSubscription();

        // Calculate statistics
        $todayEarnings = (float) $business->commissions()
            ->whereDate('created_at', now()->toDateString())
            ->sum('business_earnings');

        $activeListingsCount = $business->foods()
            ->where('status', 'active')
            ->count();

        $pendingReservationsCount = $business->reservations()
            ->where('status', 'pending')
            ->count();

        $totalCommissionPaid = (float) $business->commissions()
            ->sum('commission_amount');

        // Monthly Income Chart Data (Current Year)
        $monthlyEarningsData = $business->commissions()
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(business_earnings) as earnings')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('earnings', 'month')
            ->all();

        $monthlyEarnings = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyEarnings[] = (float) ($monthlyEarningsData[$m] ?? 0);
        }

        // Daily Earnings Trend Chart Data (Last 15 days)
        $dailyEarningsData = $business->commissions()
            ->where('created_at', '>=', now()->subDays(14)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(business_earnings) as earnings')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('earnings', 'date')
            ->all();

        $dailyEarnings = [];
        $dailyLabels = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $label = now()->subDays($i)->format('M d');
            $dailyEarnings[] = (float) ($dailyEarningsData[$date] ?? 0);
            $dailyLabels[] = $label;
        }

        // Summary widgets calculations
        $totalRevenueThisMonth = (float) $business->commissions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('business_earnings');

        $totalSalesAmount = (float) $business->commissions()->sum('sale_amount');
        $totalOrdersCount = $business->commissions()->count();
        $averageOrderValue = $totalOrdersCount > 0 ? $totalSalesAmount / $totalOrdersCount : 0.0;

        $pendingPayouts = (float) $business->commissions()
            ->where('status', 'pending')
            ->sum('business_earnings');

        // Fetch AI risk alerts (Medium or High expiry risk foods)
        $aiInsights = $business->foods()
            ->where('status', 'active')
            ->where('expiry_datetime', '>', now())
            ->whereIn('ai_risk_level', ['medium', 'high'])
            ->orderBy('expiry_datetime', 'asc')
            ->take(5)
            ->get();

        // Recent bookings
        $recentReservations = $business->reservations()
            ->with(['customer', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('business.dashboard', compact(
            'business',
            'subscription',
            'todayEarnings',
            'activeListingsCount',
            'pendingReservationsCount',
            'totalCommissionPaid',
            'aiInsights',
            'recentReservations',
            'monthlyEarnings',
            'dailyEarnings',
            'dailyLabels',
            'totalRevenueThisMonth',
            'averageOrderValue',
            'pendingPayouts'
        ));
    }
}
