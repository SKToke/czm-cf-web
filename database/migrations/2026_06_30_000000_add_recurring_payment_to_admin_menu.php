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
        // Clean up old menu if exists
        $oldMenu = DB::table('admin_menu')->where('title', 'Recurring Subscriptions')->first();
        if ($oldMenu) {
            DB::table('admin_role_menu')->where('menu_id', $oldMenu->id)->delete();
            DB::table('admin_menu')->where('id', $oldMenu->id)->delete();
        }

        // Insert or update new "Recurring Payment" menu item
        $menuId = DB::table('admin_menu')->where('title', 'Recurring Payment')->value('id');
        if (!$menuId) {
            $menuId = DB::table('admin_menu')->insertGetId([
                'parent_id' => 0,
                'order' => 6,
                'title' => 'Recurring Payment',
                'icon' => 'icon-sync',
                'uri' => 'recurring-subscriptions',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('admin_menu')->where('id', $menuId)->update([
                'uri' => 'recurring-subscriptions',
                'updated_at' => now(),
            ]);
        }

        // Clean existing role menu associations for this menu item to avoid duplicates
        DB::table('admin_role_menu')->where('menu_id', $menuId)->delete();

        // Map roles: superadmin (slug: administrator), admin, resource_mobilizer, board_secretary, accountant
        $rolesToMap = ['administrator', 'admin', 'resource_mobilizer', 'board_secretary', 'accountant'];
        $roleIds = DB::table('admin_roles')->whereIn('slug', $rolesToMap)->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('admin_role_menu')->insert([
                'role_id' => $roleId,
                'menu_id' => $menuId,
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
        $menuItem = DB::table('admin_menu')->where('title', 'Recurring Payment')->first();
        if ($menuItem) {
            DB::table('admin_role_menu')->where('menu_id', $menuItem->id)->delete();
            DB::table('admin_menu')->where('id', $menuItem->id)->delete();
        }
    }
};
