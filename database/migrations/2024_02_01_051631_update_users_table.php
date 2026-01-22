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
        //
        Schema::table('users', function (Blueprint $table) {
            // Rename 'name' column to 'first_name'
            $table->renameColumn('name', 'first_name');
            
            // Add new columns
            $table->string('last_name')->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('post_code')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->integer('profession')->nullable();
            $table->string('thana')->nullable();
            $table->integer('district')->nullable();
            $table->integer('country')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('user_type')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_mobile')->nullable();
            $table->string('contact_person_designation')->nullable();
            $table->boolean('active')->default(true)->nullable(false);
            $table->boolean('admin')->default(false)->nullable();
            $table->json('provider')->nullable();
            $table->json('uid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {
            // Reverse the above changes
            $table->renameColumn('first_name', 'name');
            
            $table->dropColumn([
                'last_name', 'address_line_1', 'address_line_2', 'post_code', 
                'date_of_birth', 'mobile_no', 'whatsapp_no', 'profession', 
                'thana', 'district', 'country', 'gender', 'user_type', 
                'contact_person_name', 'contact_person_mobile', 
                'contact_person_designation', 'active', 'admin', 
                'provider', 'uid'
            ]);
        });

    }
};
