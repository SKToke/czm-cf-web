<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('recurring_subscriptions')) {
            Schema::table('recurring_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('recurring_subscriptions', 'payment_gateway')) {
                    $table->string('payment_gateway')->default('sslcommerz')->after('id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recurring_subscriptions')) {
            Schema::table('recurring_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('recurring_subscriptions', 'payment_gateway')) {
                    $table->dropColumn('payment_gateway');
                }
            });
        }
    }
};
