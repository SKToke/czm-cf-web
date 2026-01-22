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
        Schema::table('user_zakat_calculations', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('mobile');
            $table->dropColumn('email');
            $table->dropColumn('zakat_type');
            $table->dropColumn('nisab_standard');
            $table->dropColumn('zakat_amount');
            $table->dropColumn('paid_to_czm');
        });
        Schema::table('user_zakat_calculations', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('zakat_type');
            $table->string('nisab_standard');
            $table->json('calculation_form_data');
            $table->decimal('nisab_value', 10, 2);
            $table->decimal('total_assets', 16, 2);
            $table->decimal('total_liabilities', 16, 2);
            $table->decimal('net_zakatable_assets', 16, 2);
            $table->decimal('payable_zakat', 16, 2);
            $table->decimal('paid_to_czm', 16, 2)->nullable();
            $table->boolean('archived')->default(false);
            $table->boolean('exported')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_zakat_calculations', function (Blueprint $table) {
            $table->dropColumn(['name', 'mobile', 'email', 'zakat_type', 'nisab_standard', 'calculation_form_data', 'nisab_value', 'total_assets', 'total_liabilities', 'net_zakatable_assets', 'payable_zakat', 'paid_to_czm', 'archived', 'exported']);
        });
        Schema::table('user_zakat_calculations', function (Blueprint $table) {
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->integer('zakat_type');
            $table->integer('nisab_standard');
            $table->decimal('zakat_amount', 10, 2);
            $table->boolean('paid_to_czm')->default(false);
        });
    }
};
