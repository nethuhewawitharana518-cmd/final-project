<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickupRoutingController extends Controller
{
    /**
     * Show the self-pickup routing map for a specific order.
     * Passes vendor coordinates and order metadata to the Blade view.
     */
    public function show($orderId)
    {
        $order = Auth::user()
            ->reservations()
            ->with(['business', 'items'])
            ->findOrFail($orderId);

        $business = $order->business;

        return view('customer.pickup-map', [
            'order'        => $order,
            'vendorLat'    => (float) $business->latitude,
            'vendorLng'    => (float) $business->longitude,
            'vendorName'   => $business->business_name,
            'vendorAddress'=> $business->address,
            'vendorPhone'  => $business->phone,
            'pickupTime'   => $order->pickup_time?->format('M d, Y H:i'),
        ]);
    }

    /**
     * API: Find nearest approved food vendors using the Haversine formula.
     * Accepts: ?lat=&lng=&radius=20&limit=10
     * Returns JSON array of vendors sorted by distance (km).
     */
    public function findNearestShops(Request $request)
    {
        $request->validate([
            'lat'    => 'required|numeric|between:-90,90',
            'lng'    => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'limit'  => 'nullable|integer|min:1|max:50',
        ]);

        $customerLat = (float) $request->lat;
        $customerLng = (float) $request->lng;
        $radiusKm    = (float) ($request->radius ?? 20);
        $limit       = (int)   ($request->limit  ?? 10);

        // Haversine formula inside a raw SQL select, no extra packages needed
        $vendors = Business::selectRaw("
                businesses.*,
                ( 6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance_km
            ", [$customerLat, $customerLng, $customerLat])
            ->where('status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km')
            ->limit($limit)
            ->get();

        return response()->json([
            'success'     => true,
            'customer'    => ['lat' => $customerLat, 'lng' => $customerLng],
            'vendors'     => $vendors->map(fn ($v) => [
                'id'           => $v->id,
                'name'         => $v->business_name,
                'type'         => $v->business_type,
                'address'      => $v->address,
                'phone'        => $v->phone,
                'logo'         => $v->logo ? asset('storage/' . $v->logo) : null,
                'latitude'     => (float) $v->latitude,
                'longitude'    => (float) $v->longitude,
                'distance_km'  => round((float) $v->distance_km, 2),
            ]),
        ]);
    }
}
