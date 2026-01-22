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
        Schema::create('user_zakat_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->integer('zakat_type');
            $table->integer('nisab_standard');
            $table->date('date');
            $table->decimal('zakat_amount', 10, 2);
            $table->boolean('paid_to_czm')->default(false);
            $table->boolean('registered_user')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_zakat_calculations');
    }
};
