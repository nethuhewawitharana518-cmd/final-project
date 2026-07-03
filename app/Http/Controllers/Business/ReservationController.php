<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display reservations list for business.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $business = Auth::user()->business;
        
        $query = $business->reservations()
            ->with(['customer', 'items']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reservations = $query->latest()
            ->paginate(15);

        return view('business.reservations', compact('reservations', 'status'));
    }

    /**
     * Show detailed view of a customer reservation.
     */
    public function show($id)
    {
        $business = Auth::user()->business;
        
        $reservation = $business->reservations()
            ->with(['customer', 'items.food', 'qrCode', 'payment'])
            ->findOrFail($id);

        return view('business.reservation-detail', compact('reservation'));
    }
}
