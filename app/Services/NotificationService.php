<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send in-app notification.
     */
    public static function send(
        User   $user,
        string $title,
        string $message,
        string $type = 'system',
        string $actionUrl = ''
    ): Notification {
        return Notification::create([
            'user_id'    => $user->id,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'action_url' => $actionUrl,
            'is_read'    => false,
        ]);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllRead(int $userId): void
    {
        Notification::where('user_id', $userId)->update(['is_read' => true]);
    }

    /**
     * Notify customer: Order confirmed + QR ready.
     */
    public static function orderConfirmed(User $customer, string $reservationCode, int $reservationId): void
    {
        self::send(
            $customer,
            '✅ Order Confirmed!',
            "Your reservation #{$reservationCode} is confirmed. Your QR pickup code is ready.",
            'payment',
            route('customer.orders.show', $reservationId)
        );
    }

    /**
     * Notify business owner: New reservation received.
     */
    public static function newReservation(User $businessUser, string $reservationCode): void
    {
        self::send(
            $businessUser,
            '🛒 New Reservation',
            "A new reservation #{$reservationCode} has been placed for your business.",
            'reservation',
            route('business.reservations')
        );
    }

    /**
     * Notify business: QR successfully scanned.
     */
    public static function qrScanned(User $businessUser, string $reservationCode): void
    {
        self::send(
            $businessUser,
            '📱 QR Scanned',
            "Order #{$reservationCode} has been picked up successfully.",
            'qr',
            route('business.reservations')
        );
    }

    /**
     * Notify business: Subscription expiring soon.
     */
    public static function subscriptionExpiring(User $businessUser, int $daysLeft): void
    {
        self::send(
            $businessUser,
            '⚠️ Subscription Expiring',
            "Your subscription expires in {$daysLeft} day(s). Renew now to keep your listings active.",
            'subscription',
            route('business.subscription')
        );
    }

    /**
     * Notify business: Subscription expired + listings locked.
     */
    public static function subscriptionExpired(User $businessUser): void
    {
        self::send(
            $businessUser,
            '🔒 Subscription Expired',
            'Your subscription has expired. Food listings are now hidden. Please renew to re-activate.',
            'subscription',
            route('business.subscription')
        );
    }

    /**
     * Notify business owner: Account approved by admin.
     */
    public static function businessApproved(User $businessUser): void
    {
        self::send(
            $businessUser,
            '🎉 Business Approved!',
            'Congratulations! Your business has been approved. Please select a subscription plan to get started.',
            'approval',
            route('business.subscription')
        );
    }

    /**
     * Notify business owner: Account rejected.
     */
    public static function businessRejected(User $businessUser, string $reason): void
    {
        self::send(
            $businessUser,
            '❌ Business Application Rejected',
            "Your application was rejected. Reason: {$reason}. Contact support for assistance.",
            'approval'
        );
    }

    /**
     * Notify customer: Loyalty points earned.
     */
    public static function loyaltyPointsEarned(User $customer, int $points, int $balance): void
    {
        self::send(
            $customer,
            '⭐ Loyalty Points Earned!',
            "You earned {$points} loyalty points! Your new balance is {$balance} points.",
            'loyalty',
            route('customer.loyalty')
        );
    }
}
