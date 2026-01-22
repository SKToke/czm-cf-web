<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_post_id');
            $table->string('applicant_name');
            $table->string('mobile_no');
            $table->string('email');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('cv');
            $table->index('job_post_id', 'index_job_applications_on_job_post_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_applications');
    }
}
