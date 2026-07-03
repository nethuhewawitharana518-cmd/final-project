<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('icon', 100)->nullable();
            $table->string('slug', 100)->unique();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('food_categories');
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_price', 10, 2);
            $table->integer('discount_percentage')->default(0);
            $table->integer('quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->datetime('expiry_datetime');
            $table->enum('status', ['active', 'sold_out', 'expired', 'hidden'])->default('active');
            $table->enum('ai_risk_level', ['low', 'medium', 'high'])->default('low');
            $table->integer('ai_recommended_discount')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('expiry_datetime');
            $table->index('business_id');
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['name', 'description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
        Schema::dropIfExists('food_categories');
    }
};
