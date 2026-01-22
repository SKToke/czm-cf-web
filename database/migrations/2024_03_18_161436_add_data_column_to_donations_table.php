<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            // Add a JSON column named 'data'
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            // Remove the column if the migration is rolled back
            $table->dropColumn('data');
        });
    }
};
