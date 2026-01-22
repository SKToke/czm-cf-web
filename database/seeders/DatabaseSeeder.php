<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the components's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(AdminMenuSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(ContentSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(BannerSeeder::class);
        $this->call(ReportSeeder::class);
        $this->call(PublicationSeeder::class);
        $this->call(AdminRoleMenuSeeder::class);
    }
}
