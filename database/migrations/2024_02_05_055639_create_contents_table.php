<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('content_type');
            $table->softDeletes();
            $table->timestamps();
            $table->string('slug')->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contents');
    }
}
