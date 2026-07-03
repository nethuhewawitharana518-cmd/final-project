<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(private AIService $aiService) {}

    /**
     * API: Predict risk of food expiry.
     */
    public function expiryRisk(Request $request)
    {
        $request->validate([
            'hours_remaining' => 'required|numeric',
            'original_price'  => 'required|numeric',
        ]);

        $risk = $this->aiService->predictExpiryRisk(
            (float) $request->hours_remaining,
            (float) $request->original_price
        );

        return response()->json($risk);
    }

    /**
     * API: Predict optimal recommended discount percentage.
     */
    public function discount(Request $request)
    {
        $request->validate([
            'food_category'        => 'required|string',
            'hours_remaining'      => 'required|numeric',
            'original_price'       => 'required|numeric',
            'quantity_remaining'   => 'required|integer',
            'historical_sell_rate' => 'required|numeric',
        ]);

        $recommendation = $this->aiService->getDiscountRecommendation(
            $request->food_category,
            (float) $request->hours_remaining,
            (float) $request->original_price,
            (int) $request->quantity_remaining,
            (float) $request->historical_sell_rate
        );

        return response()->json($recommendation);
    }

    /**
     * API: Retrieve weekly demand forecast counts.
     */
    public function forecast($businessId)
    {
        $forecast = $this->aiService->getDemandForecast((int) $businessId);
        return response()->json($forecast);
    }
}
