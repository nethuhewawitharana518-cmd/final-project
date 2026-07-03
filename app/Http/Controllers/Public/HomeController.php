<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display public home page.
     */
    public function index()
    {
        $categories = collect();
        $expiringSoonFoods = collect();
        $featuredBusinesses = collect();
        $stats = [
            'kg_saved'   => 1250,
            'co2_saved'  => 3125,
            'meals_saved'=> 2500,
            'businesses' => 48,
        ];

        return view('public.home', compact('categories', 'expiringSoonFoods', 'featuredBusinesses', 'stats'));
    }

    /**
     * Dedicated interactive map page showing all business locations.
     */
    public function map()
    {
        $businesses = Business::whereIn('status', ['approved', 'pending'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount(['foods as active_deals_count' => function ($q) {
                $q->where('status', 'available')->where('expiry_datetime', '>', now());
            }])
            ->get();

        $totalDeals  = Food::where('status', 'available')->where('expiry_datetime', '>', now())->count();
        $totalBizMap = $businesses->count();

        return view('public.map', compact('businesses', 'totalDeals', 'totalBizMap'));
    }

    /**
     * Return approved businesses with coordinates as JSON (for Leaflet map).
     */
    public function businessesMapData()
    {
        $businesses = Business::whereIn('status', ['approved', 'pending'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount(['foods as active_deals_count' => function ($q) {
                $q->where('status', 'available')->where('expiry_datetime', '>', now());
            }])
            ->get(['id', 'business_name', 'business_type', 'address', 'phone', 'latitude', 'longitude', 'description']);

        $features = $businesses->map(function ($b) {
            return [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [(float)$b->longitude, (float)$b->latitude],
                ],
                'properties' => [
                    'id'           => $b->id,
                    'name'         => $b->business_name,
                    'type'         => $b->business_type,
                    'address'      => $b->address,
                    'phone'        => $b->phone,
                    'active_deals' => $b->active_deals_count,
                    'url'          => route('food.browse', ['business' => $b->id]),
                    'description'  => $b->description,
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * Show about us page.
     */
    public function about()
    {
        return view('public.about');
    }

    /**
     * Show contact page.
     */
    public function contact()
    {
        return view('public.contact');
    }

    /**
     * Handle contact form submit.
     */
    public function sendContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        return back()->with('success', 'Thank you for reaching out! We will contact you soon.');
    }

    /**
     * Return all categories (JSON).
     */
    public function categories()
    {
        return response()->json(FoodCategory::all());
    }
}
