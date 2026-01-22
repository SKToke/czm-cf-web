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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 0);

            $table->unsignedBigInteger('donor_id')->nullable();
            // if null, it is anonymous donation

            $table->unsignedBigInteger('campaign_id')->nullable();
            // if null, it is general donation

            $table->string('transaction_id')->nullable();
            $table->integer('transaction_type')->nullable();
            $table->json('nisab_details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
