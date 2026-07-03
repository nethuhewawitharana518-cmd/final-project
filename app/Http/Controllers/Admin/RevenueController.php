<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Commission;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    /**
     * Display breakdown of system revenue sources.
     */
    public function index()
    {
        $payments = Payment::where('status', 'success')
            ->latest()
            ->paginate(15);

        $subscriptionRevenue = (float) Payment::where('status', 'success')
            ->where('gateway', 'subscription')
            ->sum('amount');

        $commissionsCollected = (float) Commission::sum('commission_amount');

        $registrationRevenue = (float) Payment::where('status', 'success')
            ->where('gateway', 'registration')
            ->sum('amount');

        return view('admin.revenue', compact(
            'payments',
            'subscriptionRevenue',
            'commissionsCollected',
            'registrationRevenue'
        ));
    }
}
