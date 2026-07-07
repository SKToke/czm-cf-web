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
        $exists = DB::table('reports')->where('report_type', '12')->exists();
        if (!$exists) {
            DB::table('reports')->insert([
                'report_type' => '12',
                'data' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reports')->where('report_type', '12')->delete();
    }
};
