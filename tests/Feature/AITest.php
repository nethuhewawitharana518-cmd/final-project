<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AITest extends TestCase
{
    use RefreshDatabase;

    private AIService $aiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aiService = $this->app->make(AIService::class);
    }

    public function test_ai_expiry_risk_prediction(): void
    {
        $user = User::create([
            'name' => 'Business Owner',
            'email' => 'business@test.com',
            'password' => bcrypt('password'),
            'role' => 'business_owner',
        ]);

        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Test Cafe',
            'business_type' => 'cafe',
            'address' => '456 Road',
            'phone' => '987654321',
            'email' => 'cafe@test.com',
            'status' => 'approved',
            'reg_number' => 'REG-987654',
        ]);

        $category = FoodCategory::create([
            'name' => 'Bakery Items',
            'slug' => 'bakery',
            'icon' => 'fa-bread-slice',
            'is_active' => true,
        ]);

        $food = Food::create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'name' => 'Croissant',
            'original_price' => 250.00,
            'discount_price' => 150.00,
            'discount_percentage' => 40,
            'quantity' => 10,
            'available_quantity' => 10,
            'expiry_datetime' => now()->addHours(3),
            'status' => 'active',
        ]);

        $risk = $this->aiService->getExpiryRisk($food);

        $this->assertArrayHasKey('risk', $risk);
        $this->assertArrayHasKey('confidence', $risk);
        $this->assertContains($risk['risk'], ['low', 'medium', 'high']);
    }

    public function test_ai_discount_recommendation(): void
    {
        $user = User::create([
            'name' => 'Business Owner',
            'email' => 'business@test.com',
            'password' => bcrypt('password'),
            'role' => 'business_owner',
        ]);

        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Test Cafe',
            'business_type' => 'cafe',
            'address' => '456 Road',
            'phone' => '987654321',
            'email' => 'cafe@test.com',
            'status' => 'approved',
            'reg_number' => 'REG-987654',
        ]);

        $category = FoodCategory::create([
            'name' => 'Bakery Items',
            'slug' => 'bakery',
            'icon' => 'fa-bread-slice',
            'is_active' => true,
        ]);

        $food = Food::create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'name' => 'Croissant',
            'original_price' => 250.00,
            'discount_price' => 150.00,
            'discount_percentage' => 40,
            'quantity' => 10,
            'available_quantity' => 10,
            'expiry_datetime' => now()->addHours(3),
            'status' => 'active',
        ]);

        $recommendation = $this->aiService->getDiscountRecommendation($food);

        $this->assertArrayHasKey('recommended_discount_percent', $recommendation);
        $this->assertArrayHasKey('reasoning', $recommendation);
        $this->assertGreaterThanOrEqual(5, $recommendation['recommended_discount_percent']);
        $this->assertLessThanOrEqual(80, $recommendation['recommended_discount_percent']);
    }

    public function test_ai_demand_forecasting(): void
    {
        $forecast = $this->aiService->getDemandForecast(1);

        $this->assertArrayHasKey('forecast', $forecast);
        $this->assertArrayHasKey('peak_hours', $forecast);
    }

    public function test_update_food_status_command(): void
    {
        $user = User::create([
            'name' => 'Business Owner',
            'email' => 'business@test.com',
            'password' => bcrypt('password'),
            'role' => 'business_owner',
        ]);

        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Test Cafe',
            'business_type' => 'cafe',
            'address' => '456 Road',
            'phone' => '987654321',
            'email' => 'cafe@test.com',
            'status' => 'approved',
            'reg_number' => 'REG-987654',
        ]);

        $category = FoodCategory::create([
            'name' => 'Bakery Items',
            'slug' => 'bakery',
            'icon' => 'fa-bread-slice',
            'is_active' => true,
        ]);

        $foodActive = Food::create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'name' => 'Croissant',
            'original_price' => 250.00,
            'discount_price' => 150.00,
            'quantity' => 10,
            'available_quantity' => 10,
            'expiry_datetime' => now()->addHours(3),
            'status' => 'active',
        ]);

        $foodExpired = Food::create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'name' => 'Expired Cake',
            'original_price' => 500.00,
            'discount_price' => 300.00,
            'quantity' => 5,
            'available_quantity' => 5,
            'expiry_datetime' => now()->subHours(1),
            'status' => 'active',
        ]);

        $this->artisan('foods:update-status')
            ->assertExitCode(0);

        $this->assertEquals('expired', $foodExpired->fresh()->status);

        $this->assertContains($foodActive->fresh()->ai_risk_level, ['low', 'medium', 'high']);
        $this->assertGreaterThan(0, $foodActive->fresh()->ai_recommended_discount);
    }
}
