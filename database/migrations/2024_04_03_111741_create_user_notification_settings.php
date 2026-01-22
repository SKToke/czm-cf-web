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
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->boolean('allow_general_type')->default(true);
            $table->boolean('allow_campaign_launch')->default(true);
            $table->boolean('allow_campaign_milestone')->default(true);
            $table->boolean('allow_campaign_countdown')->default(true);
            $table->boolean('allow_campaign_progress')->default(true);
            $table->boolean('allow_campaign_reminder')->default(true);
            $table->boolean('allow_gratitude')->default(true);
            $table->integer('frequency');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_settings');
    }
};
