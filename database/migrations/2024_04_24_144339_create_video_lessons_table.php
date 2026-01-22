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
        Schema::create('video_lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('youtube_link');
            $table->integer('lesson_type');
            $table->softDeletes();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->string('slug')->unique();

        });
    }

    public function down()
    {
        Schema::dropIfExists('video_lessons');
    }
};
