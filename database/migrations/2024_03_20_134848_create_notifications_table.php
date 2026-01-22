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
        if (!Schema::hasTable(config('notifications'))) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('campaign_id')->nullable();
                $table->text('notification_title');
                $table->integer('type');
                $table->text('notification_description')->nullable();
                $table->boolean('send_mail')->default(false);
                $table->text('mail_subject')->nullable();
                $table->text('mail_body')->nullable();
                $table->integer('user_type')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        };

        if (!Schema::hasTable(config('user_notifications'))) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('notification_id')->constrained()->onDelete('cascade');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('notifications');
    }
};
