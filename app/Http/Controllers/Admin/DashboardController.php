<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard overview.
     */
    public function index()
    {
        $totalRevenue = (float) Payment::where('status', 'success')->sum('amount');
        $activeBusinessesCount = Business::approved()->count();
        $totalOrdersCount = Reservation::count();

        // Food waste saved in kilograms
        $totalItemsCollected = Reservation::where('status', 'collected')
            ->get()
            ->sum(fn($res) => $res->items->sum('quantity'));

        $foodSavedKg = max(1250, $totalItemsCollected * 0.5 + 1250);

        // Fetch businesses pending reviews
        $pendingApprovals = Business::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $usersCount = User::count();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'activeBusinessesCount',
            'totalOrdersCount',
            'foodSavedKg',
            'pendingApprovals',
            'usersCount'
        ));
    }
}
