<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('newsletter_subscriptions', 'deleted')) {
                $table->dropColumn('deleted');
            }
            if (!Schema::hasColumn('newsletter_subscriptions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('newsletter_subscriptions', 'deleted_at')) {
                $table->boolean('deleted')->default(false);
                $table->dropSoftDeletes();
            }
        });
    }
};
