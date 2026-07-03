<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\FoodCategory;
use App\Models\User;
use App\Mail\NewFoodAlertMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FoodMailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_blasts_emails_to_all_registered_customers_when_new_food_is_created()
    {
        Mail::fake();

        // 1. Create a business owner and business
        $owner = User::create([
            'name'     => 'Merchant Owner',
            'email'    => 'owner@example.com',
            'password' => bcrypt('password'),
            'role'     => 'business_owner',
            'status'   => 'active',
        ]);

        $business = Business::create([
            'user_id'       => $owner->id,
            'business_name' => 'Kandy Road Restaurant',
            'business_type' => 'restaurant',
            'address'       => 'No.140, Abayapura Junction, Kandy Road, Trincomalee',
            'latitude'      => 8.5811454,
            'longitude'     => 81.2226177,
            'status'        => 'approved',
            'reg_number'    => 'REG-12345',
            'phone'         => '0771234567',
            'email'         => 'restaurant@example.com',
        ]);

        // 2. Create customer users
        $customer1 = User::create([
            'name'     => 'Customer One',
            'email'    => 'cust1@example.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $customer2 = User::create([
            'name'     => 'Customer Two',
            'email'    => 'cust2@example.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        // 3. Create a category
        $category = FoodCategory::create([
            'name'   => 'Meals',
            'slug'   => 'meals',
            'status' => 'active',
        ]);

        // 4. Authenticate as merchant and post a new food listing
        $response = $this->actingAs($owner)->post(route('business.food.store'), [
            'title'            => 'Special Rice Pack',
            'category_id'      => $category->id,
            'description'      => 'A delicious Sri Lankan rice pack deal',
            'original_price'   => 800,
            'discounted_price' => 500,
            'quantity'         => 10,
            'expiry_time'      => now()->addHours(3)->toDateTimeString(),
        ]);

        // 5. Verify redirect and database insert
        $response->assertRedirect(route('business.food.index'));
        $this->assertDatabaseHas('foods', [
            'name' => 'Special Rice Pack',
        ]);

        // 6. Verify that the email was sent to all registered customer emails individually
        Mail::assertSent(NewFoodAlertMail::class, function ($mail) use ($customer1) {
            return $mail->hasTo($customer1->email);
        });
        Mail::assertSent(NewFoodAlertMail::class, function ($mail) use ($customer2) {
            return $mail->hasTo($customer2->email);
        });
    }
}
