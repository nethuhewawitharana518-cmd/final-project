<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('business_id')->constrained('businesses');
            $table->string('reservation_code', 20)->unique();
            $table->datetime('pickup_time');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('platform_commission', 10, 2)->default(0.00);
            $table->decimal('business_earnings', 10, 2)->default(0.00);
            $table->integer('loyalty_points_used')->default(0);
            $table->decimal('loyalty_discount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'paid', 'collected', 'cancelled', 'expired'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('customer_id');
            $table->index('business_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods');
            $table->string('food_name', 200)->comment('Snapshot at time of order');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('reservations');
    }
};
