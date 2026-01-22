<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('position')->nullable();
            $table->unsignedBigInteger('content_id');
            $table->softDeletes();
            $table->timestamps();
            $table->string('image')->nullable();
            $table->index('content_id', 'index_content_sections_on_content_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('content_sections');
    }
}
