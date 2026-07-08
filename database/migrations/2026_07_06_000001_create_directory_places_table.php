<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directory_places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 50);      // hotel, resort, guest_house, restaurant, fast_food, cafe, bakery, other
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('osm_id')->unique();   // e.g. "node_123456" — prevents duplicate rows on re-import
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directory_places');
    }
};
