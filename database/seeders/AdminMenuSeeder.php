<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_menu')->truncate();
        $menuItems = [
            [
                'parent_id' => 0,
                'order'     => 0,
                'title'     => 'Dashboard',
                'icon'      => 'icon-chart-bar',
                'uri'       => '/',
                'permission' 	=> null,
                'created_at' 	=> now(),
                'updated_at' 	=> now(),
            ],
            [
                'parent_id'	=> 0,
                'order'	=> 1,
                'title'	=> 'Campaigns',
                'icon'		=> 'icon-bookmark',
                'uri'		=> '',
                'permission' 	=> null,
                'created_at' 	=> now(),
                'updated_at' 	=> now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'All Campaigns',
                'icon' => 'icon-bookmark',
                'uri' => 'campaigns',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Campaign Updates',
                'icon' => 'icon-clipboard-check',
                'uri' => 'campaign-updates',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'Campaign Subscriptions',
                'icon' => 'icon-address-book',
                'uri' => 'campaign-subscriptions',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Contents',
                'icon' => 'icon-align-center',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'CZM Contents',
                'icon' => 'icon-align-center',
                'uri' => 'contents',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Quranic Verse for App',
                'icon'      => 'icon-quote-left',
                'uri'       => 'app-quranic-verses',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'Programs',
                'icon' => 'icon-archway',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'CZM Program',
                'icon' => 'icon-archway',
                'uri' => 'programs',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Categories',
                'icon' => 'icon-border-all',
                'uri' => 'categories',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 4,
                'title' => 'Gallery',
                'icon' => 'icon-camera',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Photo Gallery',
                'icon' => 'icon-camera',
                'uri' => 'photo-galleries',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Video Gallery',
                'icon' => 'icon-video',
                'uri' => 'video-galleries',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 5,
                'title' => 'Notice Board',
                'icon' => 'icon-sticky-note',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Announcement',
                'icon' => 'icon-sticky-note',
                'uri' => 'notices',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Notification',
                'icon' => 'icon-bell',
                'uri' => 'notifications',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 6,
                'title' => 'Contact Us Queries',
                'icon' => 'icon-question',
                'uri' => 'contact-us-queries',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 7,
                'title' => 'CZM Governance',
                'icon' => 'icon-list-alt',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Governance Page',
                'icon' => 'icon-list-alt',
                'uri' => 'governance-pages',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Committees',
                'icon' => 'icon-people-arrows',
                'uri' => 'committees',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'Members',
                'icon' => 'icon-address-card',
                'uri' => 'members',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 4,
                'title' => 'Committee Members',
                'icon' => 'icon-list-alt',
                'uri' => 'committee-members',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 8,
                'title' => 'Publications',
                'icon' => 'icon-bars',
                'uri' => 'publications',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 9,
                'title' => 'Nisab Value',
                'icon' => 'icon-chart-bar',
                'uri' => 'nisabs',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 10,
                'title' => 'Home',
                'icon' => 'icon-list-alt',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Slider',
                'icon' => 'icon-star',
                'uri' => 'sliders',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Banner',
                'icon' => 'icon-images',
                'uri' => 'banners',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'CZM Support Counter',
                'icon' => 'icon-clock',
                'uri' => 'czm-support-counters',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 11,
                'title' => 'Video Lessons',
                'icon' => 'icon-video',
                'uri' => 'video-lessons',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 12,
                'title' => 'Career',
                'icon' => 'icon-list-alt',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Vacancy',
                'icon' => 'icon-briefcase',
                'uri' => 'job_postings',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Application',
                'icon' => 'icon-clipboard',
                'uri' => 'job_applications',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order'     => 13,
                'title'     => 'Admin',
                'icon'      => 'icon-server',
                'uri'       => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'User',
                'icon' => 'icon-users',
                'uri' => 'users',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Role',
                'icon' => 'icon-user',
                'uri' => 'roles',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 14,
                'title' => 'All Reports',
                'icon' => 'icon-book',
                'uri' => '',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Report',
                'icon' => 'icon-book',
                'uri' => 'reports',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Donor',
                'icon' => 'icon-users',
                'uri' => 'donors',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'Donation',
                'icon' => 'icon-money-bill',
                'uri' => 'donations',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 4,
                'title' => 'Newsletter Subscription',
                'icon' => 'icon-newspaper',
                'uri' => 'newsletter_subscriptions',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 0,
                'order' => 5,
                'title' => 'Zakat Calculator User',
                'icon' => 'icon-calculator',
                'uri' => 'user-zakat-calculations',
                'permission' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($menuItems as $menuItem) {
            DB::table('admin_menu')->updateOrInsert(['title' => $menuItem['title']], $menuItem);
        }

        $campaignsId = DB::table('admin_menu')->where('title', 'Campaigns')->value('id');
        if ($campaignsId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['All Campaigns', 'Campaign Updates', 'Campaign Subscriptions'])
                ->update(['parent_id' => $campaignsId]);
        }

        $contentId = DB::table('admin_menu')->where('title', 'Contents')->value('id');
        if ($contentId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['CZM Contents', 'Quranic Verse for App'])
                ->update(['parent_id' => $contentId]);
        }

        $programId = DB::table('admin_menu')->where('title', 'Programs')->value('id');
        if ($programId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['CZM Program', 'Categories'])
                ->update(['parent_id' => $programId]);
        }

        $galleryId = DB::table('admin_menu')->where('title', 'Gallery')->value('id');
        if ($galleryId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['Photo Gallery', 'Video Gallery'])
                ->update(['parent_id' => $galleryId]);
        }

        $noticeBoardId = DB::table('admin_menu')->where('title', 'Notice Board')->value('id');
        if ($noticeBoardId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['Announcement', 'Notification'])
                ->update(['parent_id' => $noticeBoardId]);
        }

        $governanceId = DB::table('admin_menu')->where('title', 'CZM Governance')->value('id');
        if ($governanceId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['Governance Page', 'Committees', 'Members', 'Committee Members'])
                ->update(['parent_id' => $governanceId]);
        }

        $homeId = DB::table('admin_menu')->where('title', 'Home')->value('id');
        if ($homeId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['Slider', 'Banner', 'CZM Support Counter'])
                ->update(['parent_id' => $homeId]);
        }

        $careerId = DB::table('admin_menu')->where('title', 'Career')->value('id');
        if ($careerId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['Vacancy', 'Application'])
                ->update(['parent_id' => $careerId]);
        }

        $allReportId = DB::table('admin_menu')->where('title', 'All Reports')->value('id');
        if ($allReportId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['Report', 'Donor', 'Donation', 'Newsletter Subscription', 'Zakat Calculator User'])
                ->update(['parent_id' => $allReportId]);
        }

        $permission = DB::table('admin_menu')->where('title', 'Permissions')->first();
        if ($permission) {
            DB::table('admin_menu')
                ->where('id', $permission->id)
                ->delete();
        }

        $admin = DB::table('admin_menu')->where('title', 'Admin')->first();
        if ($admin) {
            $adminUser = DB::table('admin_menu')->where('title', 'Users')->where('parent_id', $admin->id)->first();
            if ($adminUser) {
                DB::table('admin_menu')
                    ->where('id', $adminUser->id)
                    ->delete();
            }
            $adminRole = DB::table('admin_menu')->where('title', 'Roles')->where('parent_id', $admin->id)->first();
            if ($adminRole) {
                DB::table('admin_menu')
                    ->where('id', $adminRole->id)
                    ->delete();
            }
            $adminPermission = DB::table('admin_menu')->where('title', 'Permission')->where('parent_id', $admin->id)->first();
            if ($adminPermission) {
                DB::table('admin_menu')
                    ->where('id', $adminPermission->id)
                    ->delete();
            }
            $adminMenu = DB::table('admin_menu')->where('title', 'Menu')->where('parent_id', $admin->id)->first();
            if ($adminMenu) {
                DB::table('admin_menu')
                    ->where('id', $adminMenu->id)
                    ->delete();
            }
        }

        $adminId = DB::table('admin_menu')->where('title', 'Admin')->value('id');
        if ($adminId !== null) {
            DB::table('admin_menu')
                ->whereIn('title', ['User', 'Role'])
                ->update(['parent_id' => $adminId]);
        }
    }
}
