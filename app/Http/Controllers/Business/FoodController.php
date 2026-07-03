<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    public function __construct(private AIService $aiService) {}

    /**
     * GET /business/food — Manage food listings.
     */
    public function index(Request $request)
    {
        $business = Auth::user()->business;

        $foods = Food::where('business_id', $business->id)
            ->with('category')
            ->when($request->category, fn($q, $cat) => $q->where('category_id', $cat))
            ->when($request->status,   fn($q, $s)   => $q->where('status', $s))
            ->when($request->risk,     fn($q, $r)   => $q->where('ai_risk_level', $r))
            ->latest()
            ->paginate(20);

        $categories = FoodCategory::active()->get();
        $stats      = [
            'total'    => Food::where('business_id', $business->id)->count(),
            'active'   => Food::where('business_id', $business->id)->where('status', 'active')->count(),
            'sold_out' => Food::where('business_id', $business->id)->where('status', 'sold_out')->count(),
            'expired'  => Food::where('business_id', $business->id)->where('status', 'expired')->count(),
            'high_risk'=> Food::where('business_id', $business->id)->where('ai_risk_level', 'high')->count(),
        ];

        return view('business.food.index', compact('foods', 'categories', 'stats', 'business'));
    }

    /**
     * GET /business/food/add — Show add food form.
     */
    public function create()
    {
        $business   = Auth::user()->business;
        $categories = FoodCategory::active()->get();

        if (!$business->canUploadFood()) {
            return redirect()->route('business.food.index')->with('error',
                'You have reached your monthly upload limit. Please upgrade your subscription.');
        }

        $templates = [
            [
                'name' => 'Rice & Curry',
                'category_id' => $categories->firstWhere('slug', 'rice-curry')->id ?? ($categories->first()->id ?? null),
                'original_price' => 450,
                'discount_price' => 200,
                'image_url' => 'assets/images/templates/rice-curry.png',
                'description' => 'Delicious, hot Sri Lankan rice and curry with chicken, dhal, and sambol.',
            ],
            [
                'name' => 'Chicken Kottu',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 750,
                'discount_price' => 350,
                'image_url' => 'assets/images/templates/kottu.png',
                'description' => 'Freshly chopped chicken kottu roti with rich gravy and aromatic spices.',
            ],
            [
                'name' => 'Cheese Burger',
                'category_id' => $categories->firstWhere('slug', 'fast-food')->id ?? ($categories->first()->id ?? null),
                'original_price' => 850,
                'discount_price' => 400,
                'image_url' => 'assets/images/templates/burger.png',
                'description' => 'Juicy beef/chicken patty cheese burger with crispy lettuce and tomato.',
            ],
            [
                'name' => 'Margherita Pizza',
                'category_id' => $categories->firstWhere('slug', 'fast-food')->id ?? ($categories->first()->id ?? null),
                'original_price' => 1800,
                'discount_price' => 850,
                'image_url' => 'assets/images/templates/pizza.png',
                'description' => 'Classic Margherita pizza with fresh mozzarella cheese, tomatoes, and basil.',
            ],
            [
                'name' => 'Egg Hoppers (4 pcs)',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 300,
                'discount_price' => 150,
                'image_url' => 'assets/images/templates/hoppers.png',
                'description' => 'Crispy egg hoppers served hot with lunu miris.',
            ],
            [
                'name' => 'Ghee Roast Dosa',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 350,
                'discount_price' => 180,
                'image_url' => 'assets/images/templates/dosa.png',
                'description' => 'Crispy golden Ghee Roast Dosa served with traditional sambar and coconut chutney.',
            ],
            [
                'name' => 'Steamed Idli (4 pcs)',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 250,
                'discount_price' => 120,
                'image_url' => 'assets/images/templates/idli.png',
                'description' => 'Soft, fluffy steamed white idli cakes served hot with spicy coconut chutney.',
            ],
            [
                'name' => 'Traditional Pittu',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 300,
                'discount_price' => 150,
                'image_url' => 'assets/images/templates/pittu.png',
                'description' => 'Freshly steamed red/white pittu layered with grated coconut, served with coconut milk.',
            ],
            [
                'name' => 'String Hoppers (15 pcs)',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 350,
                'discount_price' => 180,
                'image_url' => 'assets/images/templates/string-hoppers.png',
                'description' => 'Neat nests of steamed rice flour string hoppers served with coconut sambol and sothi.',
            ],
            [
                'name' => 'Flaky Parotta (3 pcs)',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 250,
                'discount_price' => 130,
                'image_url' => 'assets/images/templates/parotta.png',
                'description' => 'Soft, multi-layered golden flaky parottas served with a side of spicy vegetable/chicken gravy.',
            ],
            [
                'name' => 'Stir-Fried Noodles',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 600,
                'discount_price' => 300,
                'image_url' => 'assets/images/templates/noodles.png',
                'description' => 'Aromatic stir-fried noodles tossed with fresh vegetables, egg, and chicken.',
            ],
            [
                'name' => 'Tomato Penne Pasta',
                'category_id' => $categories->firstWhere('slug', 'meals')->id ?? ($categories->first()->id ?? null),
                'original_price' => 900,
                'discount_price' => 450,
                'image_url' => 'assets/images/templates/pasta.png',
                'description' => 'Al dente penne pasta tossed in rich tomato marinara sauce, fresh basil, and olive oil.',
            ],
        ];

        return view('business.food.create', compact('categories', 'business', 'templates'));
    }

    /**
     * POST /business/food — Store a new food listing.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:200',
            'category_id'      => 'required|exists:food_categories,id',
            'description'      => 'nullable|string|max:1000',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'template_image'   => 'nullable|string',
            'original_price'   => 'required|numeric|min:0',
            'discounted_price' => 'required|numeric|min:0|lt:original_price',
            'quantity'         => 'required|integer|min:1|max:999',
            'expiry_time'      => 'required|date|after:now',
        ]);

        $business = Auth::user()->business;

        // Handle image upload or template image copying
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/foods', 'public');
        } elseif ($request->template_image) {
            $templateFile = public_path($request->template_image);
            if (file_exists($templateFile)) {
                $filename = basename($templateFile);
                $newFilename = uniqid() . '_' . $filename;
                $destDir = storage_path('app/public/uploads/foods');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                copy($templateFile, $destDir . '/' . $newFilename);
                $imagePath = 'uploads/foods/' . $newFilename;
            }
        }

        // Calculate discount percentage
        $discountPct = (int) round(
            (($request->original_price - $request->discounted_price) / $request->original_price) * 100
        );

        // Create food record
        $food = Food::create([
            'business_id'        => $business->id,
            'category_id'        => $request->category_id,
            'name'               => $request->title,
            'description'        => $request->description,
            'image'              => $imagePath,
            'original_price'     => $request->original_price,
            'discount_price'     => $request->discounted_price,
            'discount_percentage'=> $discountPct,
            'quantity'           => $request->quantity,
            'available_quantity' => $request->quantity,
            'expiry_datetime'    => $request->expiry_time,
            'status'             => 'active',
        ]);

        // Run AI predictions
        $risk     = $this->aiService->getExpiryRisk($food->load('category'));
        $discount = $this->aiService->getDiscountRecommendation($food);

        $food->update([
            'ai_risk_level'           => $risk['risk'],
            'ai_recommended_discount' => $discount['recommended_discount_percent'],
        ]);

        // Notify all registered customers about the new deal
        $customers = \App\Models\User::where('role', 'customer')->get();
        foreach ($customers as $customer) {
            \App\Models\Notification::create([
                'user_id'    => $customer->id,
                'title'      => 'New Deal in Trincomalee!',
                'message'    => "{$business->business_name} just listed {$food->name} for Rs.{$food->discount_price} ({$food->discount_percentage}% OFF!).",
                'type'       => 'new_food',
                'action_url' => route('food.browse', ['category' => $food->category->slug]),
                'is_read'    => false,
            ]);
        }

        // Blast email notifications to all customers
        try {
            foreach ($customers as $customer) {
                if ($customer->email) {
                    \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\NewFoodAlertMail($food));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Could not send new food alert emails: ' . $e->getMessage());
        }

        return redirect()->route('business.food.index')
            ->with('success', 'Food item added successfully!');
    }

    /**
     * GET /business/food/{id}/edit — Show edit form.
     */
    public function edit(int $id)
    {
        $business = Auth::user()->business;
        $food     = Food::where('business_id', $business->id)->findOrFail($id);
        $categories = FoodCategory::active()->get();

        return view('business.food.edit', compact('food', 'categories', 'business'));
    }

    /**
     * PUT /business/food/{id} — Update food listing.
     */
    public function update(Request $request, int $id)
    {
        $business = Auth::user()->business;
        $food     = Food::where('business_id', $business->id)->findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:200',
            'category_id'     => 'required|exists:food_categories,id',
            'original_price'  => 'required|numeric|min:0',
            'discount_price'  => 'required|numeric|min:0|lt:original_price',
            'quantity'        => 'required|integer|min:0',
            'expiry_datetime' => 'required|date|after:now',
        ]);

        if ($request->hasFile('image')) {
            if ($food->image) Storage::disk('public')->delete($food->image);
            $food->image = $request->file('image')->store('uploads/foods', 'public');
        }

        $discountPct = (int) round(
            (($request->original_price - $request->discount_price) / $request->original_price) * 100
        );

        $food->update([
            'category_id'        => $request->category_id,
            'name'               => $request->name,
            'description'        => $request->description,
            'original_price'     => $request->original_price,
            'discount_price'     => $request->discount_price,
            'discount_percentage'=> $discountPct,
            'quantity'           => $request->quantity,
            'expiry_datetime'    => $request->expiry_datetime,
        ]);

        // Refresh AI predictions
        $risk     = $this->aiService->getExpiryRisk($food->load('category'));
        $discount = $this->aiService->getDiscountRecommendation($food);
        $food->update([
            'ai_risk_level'           => $risk['risk'],
            'ai_recommended_discount' => $discount['recommended_discount_percent'],
        ]);

        return redirect()->route('business.food.index')
            ->with('success', "Food listing updated successfully!");
    }

    /**
     * DELETE /business/food/{id} — Delete food listing.
     */
    public function destroy(int $id)
    {
        $business = Auth::user()->business;
        $food     = Food::where('business_id', $business->id)->findOrFail($id);

        if ($food->image) Storage::disk('public')->delete($food->image);
        $food->delete();

        return redirect()->route('business.food.index')
            ->with('success', 'Food listing deleted.');
    }

    /**
     * POST /business/food/{id}/toggle-featured — Toggle featured status.
     */
    public function toggleFeatured(int $id)
    {
        $business = Auth::user()->business;
        $food     = Food::where('business_id', $business->id)->findOrFail($id);
        $food->update(['is_featured' => !$food->is_featured]);

        return back()->with('success', 'Featured status updated.');
    }
}
