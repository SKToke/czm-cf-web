<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPostsTable extends Migration
{
    public function up()
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('job_nature');
            $table->string('company_name');
            $table->date('opening_date');
            $table->date('closing_date');
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('logo')->nullable();
            $table->string('slug')->nullable()->unique('index_job_posts_on_slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_posts');
    }
}
