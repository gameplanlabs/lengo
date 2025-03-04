<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('iso_code');
            $table->string('symbol');
            $table->decimal('rate', 6, 2)->default(1.00);
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('paypal_transaction_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('paypal_payer_id')->nullable();
            $table->text('paypal_facilitator_access_token_id')->nullable();
            $table->decimal('total_paid', 6, 2)->nullable();
            $table->string('user_name')->nullable();
            $table->string('payer_country_code')->nullable();
            $table->string('transaction_status')->nullable();
            $table->text('transaction_debug_id')->nullable();
            $table->text('transaction_message')->nullable();
            $table->text('transaction_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('currencies');
    }
};
