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
        Schema::table('members', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('email_address', 255)->nullable();
            $table->string('facebook_link', 255)->nullable();
            $table->string('linkedin_link', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('contact_number');
            $table->dropColumn('email_address');
            $table->dropColumn('facebook_link');
            $table->dropColumn('linkedin_link');
        });
    }
};
