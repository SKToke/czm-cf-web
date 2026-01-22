<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use OpenAdmin\Admin\Facades\Admin;

class AdminRoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_role_menu')->truncate();

        //roles
        $superAdmin = DB::table('admin_roles')->where('slug', 'administrator')->first();
        $admin = DB::table('admin_roles')->where('slug', 'admin')->first();
        $campaignManager = DB::table('admin_roles')->where('slug', 'campaign_manager')->first();
        $accountant = DB::table('admin_roles')->where('slug', 'accountant')->first();
        $boardSecretary = DB::table('admin_roles')->where('slug', 'board_secretary')->first();
        $digitalMarketer = DB::table('admin_roles')->where('slug', 'digital_marketer')->first();
        $resourceMobilizer = DB::table('admin_roles')->where('slug', 'resource_mobilizer')->first();

        $menuItems = DB::table('admin_menu')->get();

        foreach ($menuItems as $menuItem) {
            if ($menuItem->title == "Dashboard") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $campaignManager->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $accountant->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Contents") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Programs") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Campaigns") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $campaignManager->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $accountant->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Gallery") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Notice Board") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $campaignManager->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Contact Us Queries") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "CZM Governance") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Publications") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Nisab Value") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Home") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Video Lessons") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Career") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Admin") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "All Reports") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $accountant->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
            }

            //submenus
            if ($menuItem->title == "User") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Role") {
                DB::table('admin_role_menu')->insert(['role_id' => $superAdmin->id, 'menu_id' => $menuItem->id]);
            }

            if ($menuItem->title == "CZM Contents") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Quranic Verse for App") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
            }

            if ($menuItem->title == "Video Gallery") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Photo Gallery") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
            }

            if ($menuItem->title == "All Campaigns" || $menuItem->title == "Campaign Updates") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $campaignManager->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Campaign Subscriptions") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $accountant->id, 'menu_id' => $menuItem->id]);
            }

            if ($menuItem->title == "Slider" || $menuItem->title == "Banner") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "CZM Support Counter") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
            }

            if ($menuItem->title == "Donor") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Donation") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $accountant->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Newsletter Subscription") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $digitalMarketer->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Zakat Calculator User") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $resourceMobilizer->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $boardSecretary->id, 'menu_id' => $menuItem->id]);
                DB::table('admin_role_menu')->insert(['role_id' => $accountant->id, 'menu_id' => $menuItem->id]);
            }
            if ($menuItem->title == "Report") {
                DB::table('admin_role_menu')->insert(['role_id' => $admin->id, 'menu_id' => $menuItem->id]);
            }
        }
    }
}
