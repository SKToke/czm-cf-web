<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('parentable');
            $table->string('title')->nullable();
            $table->integer('download_count')->nullable();
            $table->timestamps();

            $table->index(['parentable_type', 'parentable_id'], 'index_attachments_on_parentable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attachments');
    }
};
