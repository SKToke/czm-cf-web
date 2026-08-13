<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('recurring_transactions')) {
            Schema::table('recurring_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('recurring_transactions', 'payment_id')) {
                    $table->unsignedBigInteger('payment_id')->nullable()->after('recurring_subscription_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recurring_transactions')) {
            Schema::table('recurring_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('recurring_transactions', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
            });
        }
    }
};
