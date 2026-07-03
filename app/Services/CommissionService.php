<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calculate commission for a given sale amount.
     */
    public function calculate(float $saleAmount): array
    {
        $rate             = (float) config('commission.rate', 5.0);
        $commissionAmount = round(($saleAmount * $rate) / 100, 2);
        $businessEarnings = round($saleAmount - $commissionAmount, 2);

        return [
            'sale_amount'      => $saleAmount,
            'commission_rate'  => $rate,
            'commission_amount'=> $commissionAmount,
            'business_earnings'=> $businessEarnings,
        ];
    }

    /**
     * Record commission for a completed reservation.
     */
    public function record(Reservation $reservation): Commission
    {
        $calc = $this->calculate($reservation->total_amount);

        return DB::transaction(function () use ($reservation, $calc) {
            $commission = Commission::create([
                'reservation_id'  => $reservation->id,
                'business_id'     => $reservation->business_id,
                'sale_amount'     => $calc['sale_amount'],
                'commission_rate' => $calc['commission_rate'],
                'commission_amount'=> $calc['commission_amount'],
                'business_earnings'=> $calc['business_earnings'],
                'status'          => 'pending',
            ]);

            // Update reservation commission fields
            $reservation->update([
                'platform_commission' => $calc['commission_amount'],
                'business_earnings'   => $calc['business_earnings'],
            ]);

            return $commission;
        });
    }

    /**
     * Get total platform commission for a date range.
     */
    public function getPlatformTotal(?string $startDate = null, ?string $endDate = null): float
    {
        $query = Commission::query();
        if ($startDate) $query->where('created_at', '>=', $startDate);
        if ($endDate)   $query->where('created_at', '<=', $endDate);
        return (float) $query->sum('commission_amount');
    }

    /**
     * Get business earnings for a specific business.
     */
    public function getBusinessEarnings(int $businessId, ?string $startDate = null): float
    {
        $query = Commission::where('business_id', $businessId);
        if ($startDate) $query->where('created_at', '>=', $startDate);
        return (float) $query->sum('business_earnings');
    }
}
