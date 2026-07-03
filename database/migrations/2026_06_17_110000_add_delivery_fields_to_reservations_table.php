<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('delivery_method', 20)->default('pickup')->after('status');
            $table->text('delivery_address')->nullable()->after('delivery_method');
            $table->decimal('delivery_fee', 10, 2)->default(0.00)->after('delivery_address');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'delivery_address', 'delivery_fee']);
        });
    }
};
