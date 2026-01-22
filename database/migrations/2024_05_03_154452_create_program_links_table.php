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
        Schema::create('program_links', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('link_label')->nullable();
            $table->text('link');
            $table->bigInteger('program_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_links');
    }
};
