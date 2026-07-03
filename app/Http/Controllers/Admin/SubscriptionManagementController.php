<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    /**
     * List all platform subscriptions.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Subscription::with('business');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $subscriptions = $query->latest()
            ->paginate(15);

        return view('admin.subscriptions', compact('subscriptions', 'status'));
    }

    /**
     * Extend subscription validity duration.
     */
    public function extend(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $sub = Subscription::findOrFail($id);
        
        $sub->update([
            'end_date' => $sub->end_date->addDays((int) $request->days),
        ]);

        return back()->with('success', "Subscription extended by {$request->days} days successfully.");
    }

    /**
     * Terminate/Cancel subscription validity.
     */
    public function cancel($id)
    {
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => 'cancelled']);

        return back()->with('success', 'Subscription status marked as cancelled.');
    }
}
