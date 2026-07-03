<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_checkout_successfully(): void
    {
        // 1. Create a customer user
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        // 2. Create a business owner
        $businessUser = User::create([
            'name'     => 'Business Owner',
            'email'    => 'business@test.com',
            'password' => bcrypt('password'),
            'role'     => 'business_owner',
            'status'   => 'active',
        ]);

        $business = Business::create([
            'user_id'       => $businessUser->id,
            'business_name' => 'Test Restaurant',
            'business_type' => 'restaurant',
            'address'       => '123 Galle Road, Trincomalee',
            'phone'         => '0771234567',
            'email'         => 'restaurant@test.com',
            'status'        => 'approved',
            'reg_number'    => 'REG-999999',
        ]);

        // 3. Create a category and food item
        $category = FoodCategory::create([
            'name' => 'Meals',
            'icon' => 'fa-utensils',
            'slug' => 'meals'
        ]);

        $food = Food::create([
            'business_id'        => $business->id,
            'category_id'        => $category->id,
            'name'               => 'Chicken Biryani',
            'description'        => 'Spicy chicken biryani portion',
            'original_price'     => 1000.00,
            'discount_price'     => 500.00,
            'discount_percentage'=> 50,
            'quantity'           => 10,
            'available_quantity' => 10,
            'expiry_datetime'    => now()->addHours(6),
            'status'             => 'active',
        ]);

        // 4. Put item into session cart
        $cart = [
            $food->id => [
                'food_id'        => $food->id,
                'business_id'    => $food->business_id,
                'business_name'  => $business->business_name,
                'name'           => $food->name,
                'image'          => $food->image,
                'discount_price' => $food->discount_price,
                'original_price' => $food->original_price,
                'quantity'       => 2,
            ]
        ];

        // 5. Mock Stripe Service
        $this->mock(\App\Services\StripePaymentService::class, function ($mock) {
            $mock->shouldReceive('createPaymentIntent')
                ->once()
                ->andReturn([
                    'success' => true,
                    'client_secret' => 'pi_test_secret_123',
                    'intent_id' => 'pi_test_123',
                ]);

            $mockIntent = new \Stripe\PaymentIntent('pi_test_123');
            $mockIntent->status = 'succeeded';
            $mockIntent->currency = 'lkr';
            $mockIntent->amount = 1000;
            $mockIntent->payment_method = 'pm_test_123';

            $mock->shouldReceive('retrievePaymentIntent')
                ->with('pi_test_123')
                ->andReturn($mockIntent);

            $mock->shouldReceive('recordPayment')
                ->andReturn(new \App\Models\Payment());
        });

        // 6. Submit checkout process - Step 1: create intent
        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('customer.checkout.intent'), [
                'pickup_time'    => now()->addHours(2)->format('Y-m-d\TH:i'),
                'checkout_type'  => 'pickup',
                'loyalty_points' => 0,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['client_secret', 'intent_id', 'amount']);

        // Step 2: confirm order
        $response = $this->actingAs($customer)
            ->post(route('customer.checkout.confirm'), [
                'payment_intent_id' => 'pi_test_123',
            ]);

        // 7. Assert redirect response
        $reservation = Reservation::first();
        $this->assertNotNull($reservation);
        $response->assertJson(['redirect' => route('customer.checkout.success', $reservation->id)]);

        // 8. Verify reservation record
        $this->assertEquals($customer->id, $reservation->customer_id);
        $this->assertEquals($business->id, $reservation->business_id);
        $this->assertEquals(1000.00, $reservation->subtotal); // 500 * 2
        $this->assertEquals(1000.00, $reservation->total_amount);
        $this->assertEquals('paid', $reservation->status);

        // 9. Verify order items were created
        $this->assertDatabaseHas('order_items', [
            'reservation_id' => $reservation->id,
            'food_id'        => $food->id,
            'quantity'       => 2,
            'unit_price'     => 500.00,
        ]);

        // 10. Verify food stock was reduced
        $this->assertEquals(8, $food->fresh()->available_quantity);

        // 11. Verify session cart is cleared
        $this->assertNull(session('cart'));
    }

    public function test_customer_can_checkout_with_delivery(): void
    {
        // 1. Create a customer user
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer_delivery@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        // 2. Create a business owner
        $businessUser = User::create([
            'name'     => 'Business Owner',
            'email'    => 'business_delivery@test.com',
            'password' => bcrypt('password'),
            'role'     => 'business_owner',
            'status'   => 'active',
        ]);

        $business = Business::create([
            'user_id'       => $businessUser->id,
            'business_name' => 'Delivery Restaurant',
            'business_type' => 'restaurant',
            'address'       => '123 Galle Road, Trincomalee',
            'phone'         => '0771234567',
            'email'         => 'delivery_rest@test.com',
            'status'        => 'approved',
            'reg_number'    => 'REG-888888',
        ]);

        // 3. Create a category and food item
        $category = FoodCategory::create([
            'name' => 'Meals',
            'icon' => 'fa-utensils',
            'slug' => 'meals'
        ]);

        $food = Food::create([
            'business_id'        => $business->id,
            'category_id'        => $category->id,
            'name'               => 'Chicken Biryani',
            'description'        => 'Spicy chicken biryani portion',
            'original_price'     => 1000.00,
            'discount_price'     => 500.00,
            'discount_percentage'=> 50,
            'quantity'           => 10,
            'available_quantity' => 10,
            'expiry_datetime'    => now()->addHours(6),
            'status'             => 'active',
        ]);

        // 4. Put item into session cart
        $cart = [
            $food->id => [
                'food_id'        => $food->id,
                'business_id'    => $food->business_id,
                'business_name'  => $business->business_name,
                'name'           => $food->name,
                'image'          => $food->image,
                'discount_price' => $food->discount_price,
                'original_price' => $food->original_price,
                'quantity'       => 2,
            ]
        ];

        // 5. Simulate setting delivery options (5.0 km -> Rs. 420 fee)
        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('customer.checkout.delivery-options'), [
                'distance_km' => 5.0,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'available'   => true,
            'fee'         => 420.00,
            'zone'        => 'dynamic',
            'distance_km' => 5.0,
        ]);

        // 6. Mock Stripe Service for delivery (subtotal 1000 + delivery 420 = 1420)
        $this->mock(\App\Services\StripePaymentService::class, function ($mock) {
            $mock->shouldReceive('createPaymentIntent')
                ->once()
                ->andReturn([
                    'success' => true,
                    'client_secret' => 'pi_test_secret_456',
                    'intent_id' => 'pi_test_456',
                ]);

            $mockIntent = new \Stripe\PaymentIntent('pi_test_456');
            $mockIntent->status = 'succeeded';
            $mockIntent->currency = 'lkr';
            $mockIntent->amount = 1420;
            $mockIntent->payment_method = 'pm_test_456';

            $mock->shouldReceive('retrievePaymentIntent')
                ->with('pi_test_456')
                ->andReturn($mockIntent);

            $mock->shouldReceive('recordPayment')
                ->andReturn(new \App\Models\Payment());
        });

        // 7. Submit checkout process with delivery - Step 1: create intent
        $response = $this->actingAs($customer)
            ->post(route('customer.checkout.intent'), [
                'pickup_time'      => now()->addHours(2)->format('Y-m-d\TH:i'),
                'checkout_type'    => 'delivery',
                'delivery_address' => '456 Main St, Trincomalee',
                'loyalty_points'   => 0,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['client_secret', 'intent_id', 'amount']);

        // Step 2: confirm order
        $response = $this->actingAs($customer)
            ->post(route('customer.checkout.confirm'), [
                'payment_intent_id' => 'pi_test_456',
            ]);

        // 8. Assert redirect response
        $reservation = Reservation::where('customer_id', $customer->id)->first();
        $this->assertNotNull($reservation);
        $response->assertJson(['redirect' => route('customer.checkout.success', $reservation->id)]);

        // 9. Verify reservation record includes delivery fee in total_amount
        $this->assertEquals(1000.00, $reservation->subtotal); // 500 * 2
        $this->assertEquals(1420.00, $reservation->total_amount); // 1000 + 420 delivery fee
        $this->assertEquals('delivery', $reservation->delivery_method);
        $this->assertEquals('456 Main St, Trincomalee', $reservation->delivery_address);
        $this->assertEquals(420.00, $reservation->delivery_fee);
        $this->assertStringContainsString('Method: Delivery', $reservation->notes);
        $this->assertStringContainsString('Address: 456 Main St, Trincomalee', $reservation->notes);
    }

    public function test_delivery_restricted_if_cart_under_500(): void
    {
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer_under500@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $cart = [
            99 => [
                'food_id'        => 99,
                'business_id'    => 1,
                'business_name'  => 'Test Restaurant',
                'name'           => 'Small Snack',
                'image'          => null,
                'discount_price' => 250.00,
                'original_price' => 300.00,
                'quantity'       => 1, // Total is 250 < 500
            ]
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('customer.checkout.delivery-options'), [
                'distance_km' => 2.0,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'available' => false,
            'reason'    => 'min_cart',
        ]);
    }

    public function test_delivery_restricted_if_out_of_range(): void
    {
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer_outofrange@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $cart = [
            99 => [
                'food_id'        => 99,
                'business_id'    => 1,
                'business_name'  => 'Test Restaurant',
                'name'           => 'Large Deal',
                'image'          => null,
                'discount_price' => 600.00,
                'original_price' => 800.00,
                'quantity'       => 1, // Total is 600 >= 500
            ]
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('customer.checkout.delivery-options'), [
                'distance_km' => 12.5, // > 10 km
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'available' => false,
            'reason'    => 'out_of_range',
        ]);
    }

    public function test_customer_can_download_receipt(): void
    {
        // 1. Create a customer user
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer_receipt@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        // 2. Create a business owner
        $businessUser = User::create([
            'name'     => 'Business Owner',
            'email'    => 'business_receipt@test.com',
            'password' => bcrypt('password'),
            'role'     => 'business_owner',
            'status'   => 'active',
        ]);

        $business = Business::create([
            'user_id'       => $businessUser->id,
            'business_name' => 'Receipt Restaurant',
            'business_type' => 'restaurant',
            'address'       => '123 Galle Road, Trincomalee',
            'phone'         => '0771234567',
            'email'         => 'receipt_rest@test.com',
            'status'        => 'approved',
            'reg_number'    => 'REG-777777',
        ]);

        // 3. Create a reservation
        $reservation = Reservation::create([
            'customer_id'         => $customer->id,
            'business_id'         => $business->id,
            'reservation_code'    => 'FR-RCPT123',
            'pickup_time'         => now()->addHours(2),
            'subtotal'            => 500.00,
            'platform_commission' => 50.00,
            'business_earnings'   => 450.00,
            'loyalty_points_used' => 0,
            'loyalty_discount'    => 0.00,
            'total_amount'        => 500.00,
            'status'              => 'paid',
            'notes'               => 'Method: Store Pickup',
        ]);

        // 4. Download receipt
        $response = $this->actingAs($customer)
            ->get(route('customer.orders.receipt', $reservation->id));

        // 5. Assert receipt was downloaded successfully
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="receipt-FR-RCPT123.pdf"');
    }
}
