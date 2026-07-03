<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name', 150);
            $table->enum('business_type', ['hotel', 'restaurant', 'bakery', 'cafe', 'supermarket']);
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('reg_number', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->json('documents')->nullable()->comment('Array of document file paths');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->decimal('featured_fee', 10, 2)->default(0.00);
            $table->boolean('reg_fee_paid')->default(false);
            $table->timestamp('reg_fee_paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('business_type');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
