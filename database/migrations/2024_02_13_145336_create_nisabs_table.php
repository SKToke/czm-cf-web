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
        Schema::create('nisabs', function (Blueprint $table) {
            $table->id();
            $table->decimal('gold_value', 10);
            $table->decimal('silver_value', 10);
            $table->date('nisab_update_date')->nullable(false);
            $table->integer('nisab_update_type')->nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nisabs');
    }
};
