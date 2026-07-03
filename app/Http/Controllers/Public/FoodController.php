<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display food items with optional filters.
     */
    public function index(Request $request)
    {
        $query = Food::with('business', 'category')
            ->where('status', 'active')
            ->where('available_quantity', '>', 0)
            ->where('expiry_datetime', '>', now());

        // Search Query
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $categories = (array) $request->category;
            $query->whereIn('category_id', $categories);
        }

        // Business Type Filter
        if ($request->filled('business_type')) {
            $types = (array) $request->business_type;
            $query->whereHas('business', function($sub) use ($types) {
                $sub->whereIn('business_type', $types);
            });
        }

        // Price Filter
        if ($request->filled('max_price')) {
            $query->where('discount_price', '<=', $request->max_price);
        }

        // Expiry hours filter
        if ($request->filled('expiry_hours')) {
            $hours = (int) $request->expiry_hours;
            $query->where('expiry_datetime', '<=', now()->addHours($hours));
        }

        // AI Risk Filter
        if ($request->filled('ai_risk')) {
            $query->where('ai_risk_level', $request->ai_risk);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('discount_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('discount_price', 'desc');
                break;
            case 'expiring_soon':
                $query->orderBy('expiry_datetime', 'asc');
                break;
            case 'discount_high':
                $query->orderBy('discount_percentage', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $foods = $query->paginate(12)->withQueryString();
        $categories = FoodCategory::all();

        return view('public.browse', compact('foods', 'categories'));
    }

    /**
     * Show food detail page.
     */
    public function show($id)
    {
        $food = Food::with('business', 'category')->findOrFail($id);

        // Increment view count
        $food->increment('views_count');

        // Related foods from the same business
        $relatedFoods = Food::where('business_id', $food->business_id)
            ->where('id', '!=', $food->id)
            ->where('status', 'active')
            ->where('available_quantity', '>', 0)
            ->where('expiry_datetime', '>', now())
            ->take(4)
            ->get();

        return view('public.food-detail', compact('food', 'relatedFoods'));
    }

    /**
     * API: List active food items.
     */
    public function apiIndex(Request $request)
    {
        $query = Food::with('business', 'category')
            ->where('status', 'active')
            ->where('available_quantity', '>', 0)
            ->where('expiry_datetime', '>', now());

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->get());
    }

    /**
     * API: Get single food details.
     */
    public function apiShow($id)
    {
        $food = Food::with('business', 'category')->find($id);

        if (!$food) {
            return response()->json(['message' => 'Food item not found.'], 404);
        }

        return response()->json($food);
    }
}
