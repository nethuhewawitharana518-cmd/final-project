<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique()->comment('SHA-256 secure random hash');
            $table->string('qr_image_path', 255)->nullable();
            $table->boolean('is_used')->default(false);
            $table->unsignedBigInteger('scanned_by')->nullable()->comment('business user_id');
            $table->timestamp('scanned_at')->nullable();
            $table->datetime('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index('token');
            $table->index('is_used');
            $table->foreign('scanned_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->integer('balance')->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->enum('transaction_type', ['earn', 'redeem', 'bonus', 'expire']);
            $table->string('description', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('message');
            $table->enum('type', ['reservation', 'payment', 'qr', 'subscription', 'approval', 'loyalty', 'system', 'new_food']);
            $table->string('action_url', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'is_read']);
        });

        Schema::create('featured_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('promotion_type', ['homepage', 'search_top', 'featured_badge']);
            $table->decimal('fee_paid', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_promotions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('qr_codes');
    }
};
