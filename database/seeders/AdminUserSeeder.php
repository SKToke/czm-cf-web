<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminData = [
            'email' => 'super_admin@czm.com',
            'password' => Hash::make('czm2024'),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'active' => true,
            'admin' => true,
        ];

        DB::table('users')->updateOrInsert(['email' => $superAdminData['email']], $superAdminData);
        $superAdmin = DB::table('users')->where('email', $superAdminData['email'])->first();

        $superAdminRoleData = [
            'name' => 'Administrator',
            'slug' => 'administrator',
        ];

        DB::table('admin_roles')->updateOrInsert(['slug' => $superAdminRoleData['slug']], $superAdminRoleData);
        $superAdminRole = DB::table('admin_roles')->where('slug', $superAdminRoleData['slug'])->first();

        if ($superAdmin && $superAdminRole) {
            DB::table('admin_role_users')
                ->where('role_id', 1)
                ->where('user_id', 1)
                ->delete();
            $superAdminRoleUser = DB::table('admin_role_users')
                ->where('role_id', $superAdminRole->id)
                ->where('user_id', $superAdmin->id)
                ->first();
            if (! $superAdminRoleUser) {
                DB::table('admin_role_users')->insert(['user_id' => $superAdmin->id, 'role_id' => $superAdminRole->id]);
            }
        }
    }
}
