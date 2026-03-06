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
        Schema::create('recurring_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id')->nullable();
            $table->unsignedBigInteger('donor_id');
            $table->string('refer');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BDT');

            $table->enum('frequency_type', ['daily', 'monthly']);
            $table->integer('billing_day')->nullable(); // for monthly only

            $table->enum('status', [
                'initiated',
                'active',
                'paused',
                'cancelled',
                'expired',
                'failed'
            ])->default('initiated');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resume_at')->nullable();
            $table->timestamp('cancel_requested_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->string('last_tran_id')->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->string('last_payment_status')->nullable();

            $table->timestamps();

            $table->foreign('donor_id')->references('id')->on('donors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_subscriptions');
    }
};
