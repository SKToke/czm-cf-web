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
        Schema::table('contact_us_queries', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('mobile_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_us_queries', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('mobile_no')->nullable(false)->change();
        });
    }
};
