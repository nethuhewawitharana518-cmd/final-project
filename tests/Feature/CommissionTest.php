<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Commission;
use App\Models\Reservation;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionTest extends TestCase
{
    use RefreshDatabase;

    private CommissionService $commissionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commissionService = $this->app->make(CommissionService::class);
    }

    public function test_commission_calculation(): void
    {
        config(['commission.rate' => 5.0]);

        $result = $this->commissionService->calculate(100.00);

        $this->assertEquals(100.00, $result['sale_amount']);
        $this->assertEquals(5.0, $result['commission_rate']);
        $this->assertEquals(5.00, $result['commission_amount']);
        $this->assertEquals(95.00, $result['business_earnings']);
    }

    public function test_commission_recording(): void
    {
        config(['commission.rate' => 5.0]);

        $user = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $businessUser = User::create([
            'name' => 'Business Owner',
            'email' => 'business@test.com',
            'password' => bcrypt('password'),
            'role' => 'business_owner',
        ]);

        $business = Business::create([
            'user_id' => $businessUser->id,
            'business_name' => 'Test Restaurant',
            'business_type' => 'restaurant',
            'address' => '123 Street',
            'phone' => '123456789',
            'email' => 'restaurant@test.com',
            'status' => 'approved',
            'reg_number' => 'REG-123456',
        ]);

        $reservation = Reservation::create([
            'customer_id' => $user->id,
            'business_id' => $business->id,
            'reservation_code' => 'FR-TEST1234',
            'pickup_time' => now()->addHours(2),
            'subtotal' => 1000.00,
            'total_amount' => 1000.00,
            'status' => 'pending',
        ]);

        $commission = $this->commissionService->record($reservation);

        $this->assertDatabaseHas('commissions', [
            'reservation_id' => $reservation->id,
            'business_id' => $business->id,
            'sale_amount' => 1000.00,
            'commission_amount' => 50.00,
            'business_earnings' => 950.00,
            'status' => 'pending',
        ]);

        $this->assertEquals(50.00, $reservation->fresh()->platform_commission);
        $this->assertEquals(950.00, $reservation->fresh()->business_earnings);
    }

    public function test_payment_webhook_updates_status_and_records_commission(): void
    {
        config(['commission.rate' => 5.0]);

        $user = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $businessUser = User::create([
            'name' => 'Business Owner',
            'email' => 'business@test.com',
            'password' => bcrypt('password'),
            'role' => 'business_owner',
        ]);

        $business = Business::create([
            'user_id' => $businessUser->id,
            'business_name' => 'Test Restaurant',
            'business_type' => 'restaurant',
            'address' => '123 Street',
            'phone' => '123456789',
            'email' => 'restaurant@test.com',
            'status' => 'approved',
            'reg_number' => 'REG-123456',
        ]);

        $reservation = Reservation::create([
            'customer_id' => $user->id,
            'business_id' => $business->id,
            'reservation_code' => 'FR-TEST5678',
            'pickup_time' => now()->addHours(2),
            'subtotal' => 1000.00,
            'total_amount' => 1000.00,
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('webhook.payment'), [
            'order_id' => 'FR-TEST5678',
            'status_code' => 2,
            'payment_id' => 'PAY-123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Payment processed successfully.']);

        $this->assertEquals('paid', $reservation->fresh()->status);

        $this->assertDatabaseHas('payments', [
            'reservation_id' => $reservation->id,
            'amount' => 1000.00,
            'transaction_id' => 'PAY-123456',
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('commissions', [
            'reservation_id' => $reservation->id,
            'business_id' => $business->id,
            'sale_amount' => 1000.00,
            'commission_amount' => 50.00,
            'business_earnings' => 950.00,
        ]);
    }
}
