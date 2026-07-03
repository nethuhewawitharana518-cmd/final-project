<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LoyaltyPoint;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_loyalty_dashboard(): void
    {
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        LoyaltyPoint::create([
            'user_id'          => $customer->id,
            'reservation_id'   => null,
            'points_earned'    => 150,
            'points_redeemed'  => 0,
            'balance'          => 150,
            'tier'             => 'silver',
            'transaction_type' => 'earn',
            'description'      => 'Initial points',
        ]);

        $response = $this->actingAs($customer)
            ->get(route('customer.loyalty'));

        $response->assertStatus(200);
        $response->assertSee('150'); // Balance
        $response->assertSee('silver'); // Tier
        $response->assertSee('Rs. 100 Voucher'); // Redemption option label or value
    }

    public function test_customer_can_redeem_loyalty_points(): void
    {
        $customer = User::create([
            'name'     => 'Test Customer',
            'email'    => 'customer@test.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        LoyaltyPoint::create([
            'user_id'          => $customer->id,
            'reservation_id'   => null,
            'points_earned'    => 200,
            'points_redeemed'  => 0,
            'balance'          => 200,
            'tier'             => 'silver',
            'transaction_type' => 'earn',
            'description'      => 'Initial points',
        ]);

        $response = $this->actingAs($customer)
            ->post(route('customer.loyalty.redeem'), [
                'points' => 100,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loyalty_points', [
            'user_id'         => $customer->id,
            'points_redeemed' => 100,
            'balance'         => 100,
        ]);
    }
}
