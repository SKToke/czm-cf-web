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
        Schema::table('donations', function (Blueprint $table) {
            // Add new columns
            $table->string('payment_via')->nullable();
            $table->string('session_key')->nullable();
            $table->integer('transaction_status')->nullable();

            // Drop the transaction_type column
            $table->dropColumn('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Remove the newly added columns
            $table->dropColumn(['payment_via', 'session_key', 'transaction_status']);

            // Add back the transaction_type column
            $table->string('transaction_type')->nullable();
        });
    }
};
