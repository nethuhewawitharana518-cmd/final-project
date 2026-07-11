<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\Reservation;
use App\Models\User;

class LoyaltyService
{
    /** Points earned per Rs.500 spent */
    const POINTS_PER_500 = 1;

    /** Redemption: Rs. per point */
    const VALUE_PER_POINT = 1.00;

    /**
     * Award loyalty points for a completed reservation.
     */
    public function award(Reservation $reservation): LoyaltyPoint
    {
        $pointsEarned = (int) floor($reservation->total_amount / 500) * self::POINTS_PER_500;
        $currentTotal = $this->getBalance($reservation->customer_id);
        $newBalance   = $currentTotal + $pointsEarned;

        return LoyaltyPoint::create([
            'user_id'          => $reservation->customer_id,
            'reservation_id'   => $reservation->id,
            'points_earned'    => $pointsEarned,
            'points_redeemed'  => 0,
            'balance'          => $newBalance,
            'tier'             => $this->calculateTier($newBalance),
            'transaction_type' => 'earn',
            'description'      => "Earned for Order #{$reservation->reservation_code}",
        ]);
    }

    /**
     * Redeem loyalty points for a discount.
     *
     * @return float Discount amount in LKR
     */
    public function redeem(User $user, int $points): float
    {
        $balance = $this->getBalance($user->id);

        if ($points > $balance) {
            throw new \InvalidArgumentException("Insufficient points. Balance: {$balance}");
        }

        $discountAmount = $points * self::VALUE_PER_POINT;
        $newBalance     = $balance - $points;

        LoyaltyPoint::create([
            'user_id'          => $user->id,
            'reservation_id'   => null,
            'points_earned'    => 0,
            'points_redeemed'  => $points,
            'balance'          => $newBalance,
            'tier'             => $this->calculateTier($newBalance),
            'transaction_type' => 'redeem',
            'description'      => "Redeemed {$points} points for Rs.{$discountAmount} discount",
        ]);

        return $discountAmount;
    }

    /**
     * Get current loyalty points balance for a user.
     */
    public function getBalance(int $userId): int
    {
        $last = LoyaltyPoint::where('user_id', $userId)->latest('id')->first();
        return $last ? $last->balance : 0;
    }

    /**
     * Calculate tier based on total balance.
     */
    public function calculateTier(int $balance): string
    {
        if ($balance >= 500) return 'gold';
        if ($balance >= 100) return 'silver';
        return 'bronze';
    }

    /**
     * Get redemption value options.
     */
    public function getRedemptionOptions(): array
    {
        return [
            ['points' => 100, 'value' => 100,  'label' => 'Rs.100 Voucher'],
            ['points' => 500, 'value' => 500,  'label' => 'Rs.500 Premium Coupon'],
        ];
    }
}
