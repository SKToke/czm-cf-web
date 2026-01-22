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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('default')->default(false);
            $table->text('objective')->nullable();
            $table->text('activities_description')->nullable();
            $table->text('strategy')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('slogan')->nullable();
            $table->string('program_logo')->nullable();
            $table->string('counter_1_label')->nullable();
            $table->decimal('counter_1_value', 10)->nullable();
            $table->string('counter_2_label')->nullable();
            $table->decimal('counter_2_value', 10)->nullable();
            $table->string('counter_3_label')->nullable();
            $table->decimal('counter_3_value', 10)->nullable();
            $table->string('counter_4_label')->nullable();
            $table->decimal('counter_4_value', 10)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
