<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);

        // Ensure the reservation belongs to the user and is collected
        if ($reservation->customer_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($reservation->status !== 'collected') {
            return back()->with('error', 'You can only review completed orders.');
        }

        if ($reservation->review) {
            return back()->with('error', 'You have already reviewed this order.');
        }

        Review::create([
            'customer_id' => Auth::id(),
            'business_id' => $reservation->business_id,
            'reservation_id' => $reservation->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Clear the cached top businesses just in case
        \Illuminate\Support\Facades\Cache::forget('top_3_businesses');

        return back()->with('success', 'Thank you for your review!');
    }
}
