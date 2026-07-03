<?php

namespace App\Console\Commands;

use App\Models\Food;
use App\Services\AIService;
use Illuminate\Console\Command;

class UpdateFoodStatus extends Command
{
    protected $signature   = 'foods:update-status';
    protected $description = 'Auto-expire foods past their expiry datetime and refresh AI predictions.';

    public function __construct(private AIService $aiService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Updating food statuses...');

        // Mark expired foods
        $expired = Food::where('status', 'active')
            ->where('expiry_datetime', '<', now())
            ->update(['status' => 'expired']);

        $this->line("✗ Expired: {$expired} food items");

        // Refresh AI predictions for active foods
        $this->line('Refreshing AI predictions...');
        $this->aiService->updateAllFoodPredictions();

        $this->info('Done.');
    }
}
