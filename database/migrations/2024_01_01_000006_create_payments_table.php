<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('gateway', ['visa', 'mastercard', 'online_banking', 'digital_wallet', 'subscription', 'registration']);
            $table->string('transaction_id', 255)->unique()->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('transaction_id');
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained();
            $table->foreignId('business_id')->constrained();
            $table->decimal('sale_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('business_earnings', 10, 2);
            $table->enum('status', ['pending', 'settled'])->default('pending');
            $table->timestamp('settled_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('business_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('payments');
    }
};
