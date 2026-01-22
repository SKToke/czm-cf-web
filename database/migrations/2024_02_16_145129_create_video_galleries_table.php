<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoGalleriesTable extends Migration
{
    public function up()
    {
        Schema::create('video_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('youtube_link');
            $table->softDeletes();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->string('slug')->unique();

        });
    }

    public function down()
    {
        Schema::dropIfExists('video_galleries');
    }

}
