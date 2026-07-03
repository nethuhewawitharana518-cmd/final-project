<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Stripe PaymentIntent fields
            $table->string('payment_intent_id', 255)->nullable()->unique()->after('transaction_id')
                  ->comment('Stripe pi_xxx PaymentIntent ID');
            $table->string('card_brand', 30)->nullable()->after('payment_intent_id')
                  ->comment('visa, mastercard, amex, etc.');
            $table->string('card_last4', 4)->nullable()->after('card_brand');
            $table->string('card_country', 5)->nullable()->after('card_last4');
            $table->string('card_funding', 20)->nullable()->after('card_country')
                  ->comment('credit, debit, prepaid');
            $table->string('currency', 3)->default('lkr')->after('card_funding');
            $table->string('failure_code', 100)->nullable()->after('currency');
            $table->text('failure_message')->nullable()->after('failure_code');

            // Extend gateway enum to include 'stripe'
            // MySQL: modify enum column
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                \DB::statement("ALTER TABLE payments MODIFY COLUMN gateway ENUM('visa','mastercard','online_banking','digital_wallet','stripe','subscription','registration') NOT NULL");
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_intent_id', 'card_brand', 'card_last4',
                'card_country', 'card_funding', 'currency',
                'failure_code', 'failure_message',
            ]);
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                \DB::statement("ALTER TABLE payments MODIFY COLUMN gateway ENUM('visa','mastercard','online_banking','digital_wallet','subscription','registration') NOT NULL");
            }
        });
    }
};
