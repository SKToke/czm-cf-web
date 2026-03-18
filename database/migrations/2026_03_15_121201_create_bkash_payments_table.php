<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bkash_payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_id')->nullable();
            $table->string('trx_id')->nullable();

            $table->string('agreement_id')->nullable();

            $table->string('invoice')->nullable();

            $table->decimal('amount', 10, 2)->nullable();

            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkash_payments');
    }
};
