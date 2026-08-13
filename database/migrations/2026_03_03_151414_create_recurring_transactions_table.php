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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recurring_subscription_id');

            $table->string('tran_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BDT');

            $table->string('payment_status'); // valid / failed / cancelled
            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->foreign('recurring_subscription_id')
                ->references('id')
                ->on('recurring_subscriptions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
