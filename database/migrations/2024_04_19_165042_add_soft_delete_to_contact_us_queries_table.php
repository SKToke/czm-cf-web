<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contact_us_queries', function (Blueprint $table) {
            // Add deleted_at column for soft deletes
            $table->softDeletes();
            // Remove the deleted column
            $table->dropColumn('deleted');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contact_us_queries', function (Blueprint $table) {
            // Add the deleted column back (assuming it's boolean)
            $table->boolean('deleted')->default(false);
            // Remove the soft delete behavior
            $table->dropSoftDeletes();
        });
    }
};
