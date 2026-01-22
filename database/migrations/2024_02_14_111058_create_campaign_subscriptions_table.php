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
        Schema::create('campaign_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campaign_id');
            $table->integer('subscription_type');
            $table->decimal('subscribed_amount', 10, 2);
            $table->datetime('last_donated')->nullable();
            $table->datetime('last_notified')->nullable();
            $table->datetime('subscription_start_date');
            $table->datetime('next_donation_date');
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->bigInteger('donor_id');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_subscriptions');
    }
};
