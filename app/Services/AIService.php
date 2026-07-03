<?php

namespace App\Services;

use App\Models\Food;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $baseUrl;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = config('ai_service.base_url', 'http://127.0.0.1:5000');
        $this->timeout = (int) config('ai_service.timeout', 30);
    }

    /**
     * Predict expiry risk level for a food item.
     *
     * @return array{risk: string, confidence: float}
     */
    public function getExpiryRisk(Food $food): array
    {
        try {
            $response = Http::timeout($this->timeout)->post(
                $this->baseUrl . config('ai_service.endpoints.expiry_risk'),
                [
                    'food_category'    => $food->category->slug ?? 'other',
                    'hours_remaining'  => $food->hours_remaining,
                    'original_quantity'=> $food->quantity,
                    'qty_remaining'    => $food->available_quantity,
                    'time_of_day'      => now()->format('H'),
                    'day_of_week'      => now()->dayOfWeek,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data['risk'])) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI Service unavailable: ' . $e->getMessage());
        }

        // Fallback: rule-based calculation
        return $this->fallbackExpiryRisk($food->hours_remaining);
    }

    /**
     * Predict expiry risk level for raw parameters.
     *
     * @return array{risk: string, confidence: float}
     */
    public function predictExpiryRisk(float $hoursRemaining, float $originalPrice): array
    {
        try {
            $response = Http::timeout($this->timeout)->post(
                $this->baseUrl . config('ai_service.endpoints.expiry_risk'),
                [
                    'food_category'    => 'other',
                    'hours_remaining'  => $hoursRemaining,
                    'original_quantity'=> 10,
                    'qty_remaining'    => 5,
                    'time_of_day'      => now()->format('H'),
                    'day_of_week'      => now()->dayOfWeek,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data['risk'])) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI Service unavailable: ' . $e->getMessage());
        }

        // Fallback: rule-based calculation
        return $this->fallbackExpiryRisk($hoursRemaining);
    }

    /**
     * Get AI discount recommendation for a food item or raw parameters.
     *
     * @return array{recommended_discount_percent: int, reasoning: str}
     */
    public function getDiscountRecommendation(
        mixed $foodOrCategory,
        ?float $hoursRemaining = null,
        ?float $originalPrice = null,
        ?int $quantityRemaining = null,
        ?float $historicalSellRate = null
    ): array {
        if ($foodOrCategory instanceof Food) {
            $food = $foodOrCategory;
            $category = $food->category->slug ?? 'other';
            $hoursRemaining = $food->hours_remaining;
            $originalPrice = $food->original_price;
            $quantityRemaining = $food->available_quantity;
        } else {
            $category = $foodOrCategory;
        }

        try {
            $response = Http::timeout($this->timeout)->post(
                $this->baseUrl . config('ai_service.endpoints.discount_recommend'),
                [
                    'food_category'       => $category,
                    'hours_remaining'     => $hoursRemaining,
                    'original_price'      => $originalPrice,
                    'quantity_remaining'  => $quantityRemaining,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data['recommended_discount_percent'])) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI Service unavailable: ' . $e->getMessage());
        }

        // Fallback: rule-based discount
        return $this->fallbackDiscountRecommendation($hoursRemaining);
    }

    /**
     * Get demand forecast for a business.
     *
     * @return array{forecast: array, peak_hours: array}
     */
    public function getDemandForecast(int $businessId): array
    {
        try {
            $response = Http::timeout($this->timeout)->post(
                $this->baseUrl . config('ai_service.endpoints.demand_forecast'),
                ['business_id' => $businessId]
            );

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::warning('AI Service unavailable: ' . $e->getMessage());
        }

        return ['forecast' => [], 'peak_hours' => [12, 18], 'message' => 'AI service temporarily unavailable.'];
    }

    // ─── Fallback Rule-Based Methods ─────────────────────────────────

    private function fallbackExpiryRisk(float $hoursRemaining): array
    {
        $thresholds = config('ai_service.risk_thresholds');

        if ($hoursRemaining <= $thresholds['high']) {
            return ['risk' => 'high', 'confidence' => 0.95, 'source' => 'fallback'];
        }
        if ($hoursRemaining <= $thresholds['medium']) {
            return ['risk' => 'medium', 'confidence' => 0.90, 'source' => 'fallback'];
        }
        return ['risk' => 'low', 'confidence' => 0.85, 'source' => 'fallback'];
    }

    private function fallbackDiscountRecommendation(float $hoursRemaining): array
    {
        $rules   = config('ai_service.discount_rules');
        $discount = 5; // default

        foreach ($rules as $rule) {
            if ($hoursRemaining <= $rule['hours']) {
                $discount = $rule['discount'];
                break;
            }
        }

        return [
            'recommended_discount_percent' => $discount,
            'reasoning'   => "Based on {$hoursRemaining} hours remaining",
            'confidence'  => 0.80,
            'source'      => 'fallback',
        ];
    }

    /**
     * Update AI predictions for all active foods (called by cron job).
     */
    public function updateAllFoodPredictions(): void
    {
        Food::active()->with('category')->chunk(50, function ($foods) {
            foreach ($foods as $food) {
                $risk     = $this->getExpiryRisk($food);
                $discount = $this->getDiscountRecommendation($food);

                $food->update([
                    'ai_risk_level'           => $risk['risk'],
                    'ai_recommended_discount' => $discount['recommended_discount_percent'],
                ]);
            }
        });
    }
}
