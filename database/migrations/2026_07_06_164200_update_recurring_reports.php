<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up old report type 12
        DB::table('reports')->where('report_type', '12')->delete();

        // Insert Recurring_Transactions (12)
        DB::table('reports')->insert([
            'report_type' => '12',
            'data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Recurring_Subscriptions (13)
        DB::table('reports')->insert([
            'report_type' => '13',
            'data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reports')->whereIn('report_type', ['12', '13'])->delete();

        DB::table('reports')->insert([
            'report_type' => '12',
            'data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
