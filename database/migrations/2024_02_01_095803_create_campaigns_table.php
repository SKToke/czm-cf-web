<?php

use App\Enums\CampaignStatusEnum;
use App\Enums\CampaignTypeEnum;
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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->integer('campaign_id')->unique();
            $table->text('description')->nullable();
            $table->integer('campaign_type')->default(CampaignTypeEnum::ONETIME->value);
            $table->bigInteger('program_id');
            $table->boolean('urgency_status')->default(false);
            $table->datetime('donation_start_time');
            $table->datetime('donation_end_time');
            $table->decimal('allocated_amount', 10);
            $table->integer('share_count')->default(0);
            $table->integer('campaign_status')->default(CampaignStatusEnum::UNPUBLISHED->value);
            $table->string('thumbnail_image')->nullable();
            $table->json('image_paths')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
