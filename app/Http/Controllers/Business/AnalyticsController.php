<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(private AIService $aiService) {}

    /**
     * Display business owner analytics and AI forecasts.
     */
    public function index()
    {
        $business = Auth::user()->business;

        // Fetch weekly demand forecast from Flask AI service
        $forecast = $this->aiService->getDemandForecast($business->id);

        // Fetch all active categories and join with the business's active foods
        $foodStats = \App\Models\FoodCategory::active()
            ->leftJoin('foods', function($join) use ($business) {
                $join->on('food_categories.id', '=', 'foods.category_id')
                     ->where('foods.business_id', $business->id)
                     ->where('foods.status', 'active')
                     ->where('foods.expiry_datetime', '>', now())
                     ->where('foods.available_quantity', '>', 0);
            })
            ->selectRaw('food_categories.name, COALESCE(sum(foods.available_quantity), 0) as total_qty, count(foods.id) as count')
            ->groupBy('food_categories.id', 'food_categories.name')
            ->get();

        return view('business.analytics', compact('business', 'forecast', 'foodStats'));
    }
}
