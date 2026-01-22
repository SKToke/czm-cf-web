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
        Schema::create('download_histories', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name')->nullable();
            $table->unsignedBigInteger('publication_id');
            $table->boolean('registered_user')->default(false);
            $table->boolean('newsletter_subscribed')->default(false);
            $table->timestamps();
            $table->string('mobile_no')->nullable();
            $table->index('publication_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_histories');
    }
};
