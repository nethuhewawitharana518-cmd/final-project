<?php

namespace App\Console\Commands;

use App\Models\Food;
use App\Models\Subscription;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckSubscriptionExpiry extends Command
{
    protected $signature   = 'subscriptions:check-expiry';
    protected $description = 'Check and process expired subscriptions, send warnings.';

    public function handle(): void
    {
        $this->info('Checking subscriptions...');

        // 1. Expire overdue subscriptions
        $expired = Subscription::where('status', 'active')
            ->where('end_date', '<', now()->toDateString())
            ->with('business.user')
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);

            // Hide all food listings
            Food::where('business_id', $subscription->business_id)
                ->where('status', 'active')
                ->update(['status' => 'hidden']);

            NotificationService::subscriptionExpired($subscription->business->user);

            $this->line("✗ Expired: Business #{$subscription->business_id}");
        }

        // 2. Send 7-day warning
        $warnSeven = Subscription::where('status', 'active')
            ->whereDate('end_date', Carbon::today()->addDays(7))
            ->with('business.user')
            ->get();

        foreach ($warnSeven as $sub) {
            NotificationService::subscriptionExpiring($sub->business->user, 7);
            $this->line("⚠ 7-day warning: Business #{$sub->business_id}");
        }

        // 3. Send 1-day warning
        $warnOne = Subscription::where('status', 'active')
            ->whereDate('end_date', Carbon::today()->addDay())
            ->with('business.user')
            ->get();

        foreach ($warnOne as $sub) {
            NotificationService::subscriptionExpiring($sub->business->user, 1);
            $this->line("⚠ 1-day warning: Business #{$sub->business_id}");
        }

        $this->info('Done. Expired: ' . $expired->count() . ' | Warned: ' . ($warnSeven->count() + $warnOne->count()));
    }
}
