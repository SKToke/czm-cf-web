<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsQueriesTable extends Migration
{
    public function up()
    {
        Schema::create('contact_us_queries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile_no');
            $table->boolean('responded')->default(false);
            $table->text('message')->nullable();
            $table->integer('contact_type');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_us_queries');
    }
}
