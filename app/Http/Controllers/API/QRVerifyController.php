<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRVerifyController extends Controller
{
    public function __construct(private QRCodeService $qrService) {}

    /**
     * POST /api/qr/verify
     * Verifies a QR token scanned by a business owner.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string|size:64',
        ]);

        $result = $this->qrService->verify(
            $request->qr_token,
            Auth::id()
        );

        if (!$result['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $result['message'],
            ], 400);
        }

        $reservation = $result['reservation'];

        // Notify business owner
        NotificationService::qrScanned(
            Auth::user(),
            $reservation->reservation_code
        );

        return response()->json([
            'status'  => 'success',
            'message' => $result['message'],
            'order'   => [
                'reservation_code' => $reservation->reservation_code,
                'customer_name'    => $reservation->customer->name,
                'customer_phone'   => $reservation->customer->phone,
                'pickup_time'      => $reservation->pickup_time->format('H:i d M Y'),
                'total_amount'     => 'Rs. ' . number_format($reservation->total_amount, 2),
                'items'            => $reservation->items->map(fn($item) => [
                    'name'     => $item->food_name,
                    'quantity' => $item->quantity,
                    'price'    => 'Rs. ' . number_format($item->total_price, 2),
                ]),
            ],
        ]);
    }
}
