<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\Reservation;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QRCodeService
{
    /**
     * Generate and store a QR code for a reservation.
     */
    public function generate(Reservation $reservation): QrCode
    {
        // Create unique secure token
        $token = hash('sha256', $reservation->id . Str::random(32) . time());

        // Generate QR image
        $filename   = 'qr_' . $reservation->reservation_code . '.svg';
        $folderPath = public_path('uploads/qr_codes');
        $filePath   = $folderPath . '/' . $filename;

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        QrCodeGenerator::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($token, $filePath);

        // Store in DB — QR expires 1 hour after pickup time
        $expiresAt = $reservation->pickup_time->addHours(1);

        return QrCode::create([
            'reservation_id' => $reservation->id,
            'token'          => $token,
            'qr_image_path'  => 'uploads/qr_codes/' . $filename,
            'is_used'        => false,
            'expires_at'     => $expiresAt,
        ]);
    }

    /**
     * Verify a QR token scanned by a business owner.
     *
     * @return array{success: bool, message: string, reservation?: Reservation}
     */
    public function verify(string $token, int $scannedByUserId): array
    {
        $qr = QrCode::where('token', $token)->first();

        if (!$qr) {
            return ['success' => false, 'message' => 'Invalid QR code. Not found in system.'];
        }

        if ($qr->is_used) {
            return [
                'success' => false,
                'message' => 'QR code already scanned at ' . $qr->scanned_at?->format('H:i d M Y'),
            ];
        }

        if ($qr->isExpired()) {
            return ['success' => false, 'message' => 'QR code has expired.'];
        }

        $reservation = $qr->reservation()->with(['items', 'customer'])->first();

        if ($reservation->status !== 'paid') {
            return ['success' => false, 'message' => 'Order is not in a paid state.'];
        }

        // Mark QR as used and update order status
        $qr->update([
            'is_used'    => true,
            'scanned_by' => $scannedByUserId,
            'scanned_at' => now(),
        ]);

        $reservation->update(['status' => 'collected']);

        return [
            'success'     => true,
            'message'     => 'Pickup verified successfully!',
            'reservation' => $reservation,
        ];
    }

    /**
     * Get QR code public URL for display.
     */
    public function getUrl(QrCode $qr): string
    {
        return asset($qr->qr_image_path);
    }
}
