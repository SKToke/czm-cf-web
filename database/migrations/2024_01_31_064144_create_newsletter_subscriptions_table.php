<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsletterSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->timestamps();
            $table->boolean('deleted')->default(false);
            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('newsletter_subscriptions');
    }
}
