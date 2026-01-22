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
        Schema::table('content_sections', function (Blueprint $table) {
            if (Schema::hasIndex('content_sections', 'index_content_sections_on_content_id')) {
                $table->dropIndex('index_content_sections_on_content_id');
            }
        });
        Schema::table('content_sections', function (Blueprint $table) {
            if (Schema::hasColumn('content_sections', 'content_id')) {
                $table->dropColumn('content_id');
            }
        });
        Schema::table('content_sections', function (Blueprint $table) {
            $table->bigInteger('content_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_sections', function (Blueprint $table) {
            if (Schema::hasColumn('content_sections', 'content_id')) {
                $table->dropColumn('content_id');
            }
        });
        Schema::table('content_sections', function (Blueprint $table) {
            $table->bigInteger('content_id');
        });
    }
};
