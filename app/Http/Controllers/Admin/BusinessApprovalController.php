<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class BusinessApprovalController extends Controller
{
    /**
     * Display pending, approved, or rejected business owner submissions.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $businesses = Business::where('status', $status)
            ->latest()
            ->paginate(15);

        return view('admin.businesses', compact('businesses', 'status'));
    }

    /**
     * Show single business owner details and submitted document links.
     */
    public function show($id)
    {
        $business = Business::with('user')->findOrFail($id);
        return view('admin.business-detail', compact('business'));
    }

    /**
     * Approve business application.
     */
    public function approve($id)
    {
        $business = Business::findOrFail($id);
        
        $business->update(['status' => 'approved']);
        $business->user->update(['status' => 'active']);

        // Send Approval Notification
        NotificationService::businessApproved($business->user);

        return redirect()->route('admin.businesses')->with('success', 'Business account approved successfully.');
    }

    /**
     * Reject business application.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $business = Business::findOrFail($id);
        
        $business->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        // Send Rejection Notification
        NotificationService::businessRejected($business->user, $request->reason);

        return redirect()->route('admin.businesses')->with('success', 'Business application rejected successfully.');
    }
}
