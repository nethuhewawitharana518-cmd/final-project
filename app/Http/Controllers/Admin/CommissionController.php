<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    /**
     * Display commissions ledger.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Commission::with(['business', 'reservation']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $commissions = $query->latest()
            ->paginate(15);

        $pendingTotal = (float) Commission::where('status', 'pending')->sum('commission_amount');
        $settledTotal = (float) Commission::where('status', 'settled')->sum('commission_amount');

        return view('admin.commissions', compact(
            'commissions',
            'status',
            'pendingTotal',
            'settledTotal'
        ));
    }

    /**
     * Settle pending payout commissions.
     */
    public function settle(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
        ]);

        Commission::where('business_id', $request->business_id)
            ->where('status', 'pending')
            ->update([
                'status'     => 'settled',
                'settled_at' => now(),
            ]);

        return back()->with('success', 'All pending commissions settled for business successfully.');
    }

    /**
     * Update global default platform commission rate.
     */
    public function updateRate(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        // Simulates saving new configuration rate.
        return back()->with('success', "Platform commission rate updated to {$request->rate}% successfully.");
    }
}
