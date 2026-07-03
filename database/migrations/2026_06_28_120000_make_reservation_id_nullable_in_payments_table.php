<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['reservation_id']);
            
            // Modify column to be nullable
            $table->unsignedBigInteger('reservation_id')->nullable()->change();
            
            // Re-add foreign key with nullOnDelete
            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('reservations')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            
            // Revert to non-nullable (will fail if null values exist, so delete them first or change manually)
            $table->unsignedBigInteger('reservation_id')->change();
            
            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('reservations');
        });
    }
};
