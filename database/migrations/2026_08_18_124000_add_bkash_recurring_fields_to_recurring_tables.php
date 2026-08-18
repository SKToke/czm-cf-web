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
        if (Schema::hasTable('recurring_subscriptions')) {
            Schema::table('recurring_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('recurring_subscriptions', 'payer_number')) {
                    $table->string('payer_number')->nullable()->after('currency');
                }
                if (!Schema::hasColumn('recurring_subscriptions', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('next_billing_at');
                }
                if (!Schema::hasColumn('recurring_subscriptions', 'deduction_failure_count')) {
                    $table->unsignedInteger('deduction_failure_count')->default(0)->nullable()->after('expires_at');
                }
                if (!Schema::hasColumn('recurring_subscriptions', 'cancelled_by')) {
                    $table->string('cancelled_by')->nullable()->after('cancelled_at');
                }
            });
        }

        if (Schema::hasTable('recurring_transactions')) {
            Schema::table('recurring_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('recurring_transactions', 'refund_trx_id')) {
                    $table->string('refund_trx_id')->nullable()->after('gateway_response');
                }
                if (!Schema::hasColumn('recurring_transactions', 'refunded_amount')) {
                    $table->decimal('refunded_amount', 10, 2)->nullable()->after('refund_trx_id');
                }
                if (!Schema::hasColumn('recurring_transactions', 'refunded_at')) {
                    $table->timestamp('refunded_at')->nullable()->after('refunded_amount');
                }
                if (!Schema::hasColumn('recurring_transactions', 'refund_reason')) {
                    $table->string('refund_reason')->nullable()->after('refunded_at');
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
                $columns = ['payer_number', 'expires_at', 'deduction_failure_count', 'cancelled_by'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('recurring_subscriptions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('recurring_transactions')) {
            Schema::table('recurring_transactions', function (Blueprint $table) {
                $columns = ['refund_trx_id', 'refunded_amount', 'refunded_at', 'refund_reason'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('recurring_transactions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
