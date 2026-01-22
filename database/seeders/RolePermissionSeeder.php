<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesData = [
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
            ],
            [
                'name' => 'Resource Mobilizer',
                'slug' => 'resource_mobilizer',
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
            ],
            [
                'name' => 'Board Secretary',
                'slug' => 'board_secretary',
            ],
            [
                'name' => 'Digital Marketer',
                'slug' => 'digital_marketer',
            ],
            [
                'name' => 'Campaign Manager',
                'slug' => 'campaign_manager',
            ],
            [
                'name' => 'Donor',
                'slug' => 'donor',
            ],
        ];

        foreach ($rolesData as $roleData) {
            DB::table('admin_roles')->updateOrInsert(['slug' => $roleData['slug']], $roleData);
        }

        $permissionsData = [
            [
                'name'        => 'All permission',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => 'Dashboard',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => 'Login',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => 'User setting',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => 'Auth management',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
            [
                'name'        => 'Program CRUD',
                'slug'        => 'program.crud',
                'http_method' => '',
                'http_path'   => "/programs\r\n/programs/create\r\n/programs/*/edit\r\n/programs/*",
            ],
            [
                'name'        => 'Campaign Index-Create-Update-Show',
                'slug'        => 'campaign.index.create.update.show',
                'http_method' => '',
                'http_path'   => "/campaigns\r\n/campaigns/create\r\n/campaigns/*/edit\r\n/campaigns/*",
            ],
            [
                'name'        => 'Campaign Index-Update-Show',
                'slug'        => 'campaign.index.update.show',
                'http_method' => '',
                'http_path'   => "/campaigns\r\n/campaigns/*/edit\r\n/campaigns/*",
            ],
            [
                'name'        => 'Campaign-Updates CRUD',
                'slug'        => 'campaign-updates.crud',
                'http_method' => '',
                'http_path'   => "/campaign-updates\r\n/campaign-updates/create\r\n/campaign-updates/*/edit\r\n/campaign-updates/*",
            ],
            [
                'name'        => 'Campaign-Updates Without Create',
                'slug'        => 'campaign-updates.index.update.show',
                'http_method' => '',
                'http_path'   => "/campaign-updates\r\n/campaign-updates/*/edit\r\n/campaign-updates/*",
            ],
            [
                'name'        => 'Campaign-Subscriptions Index-Show',
                'slug'        => 'campaign-subscriptions.index.show',
                'http_method' => '',
                'http_path'   => "/campaign-subscriptions\r\n/campaign-subscriptions/*",
            ],
            [
                'name'        => 'User Index-Update-Show',
                'slug'        => 'user.index.update.show',
                'http_method' => '',
                'http_path'   => "/users\r\n/users/*/edit\r\n/users/*",
            ],
            [
                'name'        => 'Donation Index-Show',
                'slug'        => 'donation.index.show',
                'http_method' => '',
                'http_path'   => "/donations\r\n/donations/*",
            ],
            [
                'name'        => 'Donor Index-Show',
                'slug'        => 'donor.index.show',
                'http_method' => '',
                'http_path'   => "/donors\r\n/donors/*",
            ],
            [
                'name'        => 'User Index-Show',
                'slug'        => 'user.index.show',
                'http_method' => '',
                'http_path'   => "/users\r\n/users/*",
            ],
            [
                'name'        => 'User Update',
                'slug'        => 'user.update',
                'http_method' => '',
                'http_path'   => "/users/*/edit",
            ],
            [
                'name'        => 'NewsLetter Index-Show',
                'slug'        => 'newsletter.index.show',
                'http_method' => '',
                'http_path'   => "/newsletter_subscriptions\r\n/newsletter_subscriptions/*",
            ],
            [
                'name'        => 'Content CRUD',
                'slug'        => 'content',
                'http_method' => '',
                'http_path'   => "/contents\r\n/contents/*/edit\r\n/contents/*\r\n/contents/create",
            ],
            [
                'name'        => 'ContentSection CRUD',
                'slug'        => 'content-section',
                'http_method' => '',
                'http_path'   => "/contentSections\r\n/contentSections/*/edit\r\n/contentSections/*\r\n/contentSections/create\r\n/contentSections/*/remove",
            ],
            [
                'name'        => 'ZakatCalculationPDF',
                'slug'        => 'zakat-calculation-pdf',
                'http_method' => '',
                'http_path'   => "/generate-zakat-calculation-pdf/*",
            ],
            [
                'name'        => 'Category CRUD',
                'slug'        => 'category.crud',
                'http_method' => '',
                'http_path'   => "/categories\r\n/categories/*/edit\r\n/categories/*\r\n/categories/create",
            ],
            [
                'name'        => 'Committee CRUD',
                'slug'        => 'committee.crud',
                'http_method' => '',
                'http_path'   => "/committees\r\n/committees/*/edit\r\n/committees/*\r\n/committees/create",
            ],
            [
                'name'        => 'Member CRUD',
                'slug'        => 'member.crud',
                'http_method' => '',
                'http_path'   => "/members\r\n/members/*/edit\r\n/members/*\r\n/members/create",
            ],
            [
                'name'        => 'CommitteeMember CRUD',
                'slug'        => 'committeeMember.crud',
                'http_method' => '',
                'http_path'   => "/committee-members\r\n/committee-members/*/edit\r\n/committee-members/*\r\n/committee-members/create",
            ],
            [
                'name'        => 'GovernancePage CRUD',
                'slug'        => 'governancePage.crud',
                'http_method' => '',
                'http_path'   => "/governance-pages\r\n/governance-pages/*/edit\r\n/governance-pages/*\r\n/governance-pages/create",
            ],
            [
                'name'        => 'Publications CRUD',
                'slug'        => 'publications.crud',
                'http_method' => '',
                'http_path'   => "/publications\r\n/publications/*/edit\r\n/publications/*\r\n/publications/create",
            ],
            [
                'name'        => 'ContactUs Create',
                'slug'        => 'contact-us-create',
                'http_method' => '',
                'http_path'   => "/contact-us-queries/create",
            ],
            [
                'name'        => 'ContactUs Index-Show-Edit-Delete',
                'slug'        => 'contact-us',
                'http_method' => '',
                'http_path'   => "/contact-us-queries\r\n/contact-us-queries/*/edit\r\n/contact-us-queries/*\r\n/contact-us-queries/*/delete",
            ],
            [
                'name'        => 'Banner CRUD',
                'slug'        => 'banner',
                'http_method' => '',
                'http_path'   => "/banners\r\n/banners/*/edit\r\n/banners/*\r\n/banners/*/delete",
            ],
            [
                'name'        => 'JobPost CRUD',
                'slug'        => 'jobPost.crud',
                'http_method' => '',
                'http_path'   => "/job_postings\r\n/job_postings/*/create\r\n/job_postings/*/edit\r\n/job_postings/*\r\n/job_postings/*/delete",
            ],
            [
                'name'        => 'JobApplication Index-Show',
                'slug'        => 'jobApplication.index.show',
                'http_method' => '',
                'http_path'   => "/job_applications\r\n/job_applications/*",
            ],
            [
                'name'        => 'HeroSection Index-Create-Show',
                'slug'        => 'heroSection.index.create.show',
                'http_method' => '',
                'http_path'   => "/sliders\r\n/sliders/*/create\r\n/sliders/*",
            ],
            [
                'name'        => 'HeroSection Update',
                'slug'        => 'heroSection.update',
                'http_method' => '',
                'http_path'   => "/sliders/*/edit",
            ],
            [
                'name'        => 'Nisab Index-Create-Show',
                'slug'        => 'nisab.index.create.show',
                'http_method' => '',
                'http_path'   => "/nisabs\r\n/nisabs/*/create\r\n/nisabs/*",
            ],
            [
                'name'        => 'Nisab Update',
                'slug'        => 'nisab.update',
                'http_method' => '',
                'http_path'   => "/nisabs/*/edit",
            ],
            [
                'name'        => 'ZakatCalculation Index-Show',
                'slug'        => 'zakat-calculation.index.show',
                'http_method' => '',
                'http_path'   => "/user-zakat-calculations\r\n/user-zakat-calculations/*",
            ],
            [
                'name'        => 'Notice Index-Create-Show',
                'slug'        => 'notice.index.create.show',
                'http_method' => '',
                'http_path'   => "/notices\r\n/notices/*/create\r\n/notices/*",
            ],
            [
                'name'        => 'Notice Update',
                'slug'        => 'notice.update',
                'http_method' => '',
                'http_path'   => "/notices/*/edit",
            ],
            [
                'name'        => 'VideoLesson CRUD',
                'slug'        => 'video-lesson.crud',
                'http_method' => '',
                'http_path'   => "/video-lessons\r\n/video-lessons/*/create\r\n/video-lessons/*/edit\r\n/video-lessons/*\r\n/video-lessons/*/delete",
            ],
            [
                'name'        => 'VideoGallery CRUD',
                'slug'        => 'video-gallery',
                'http_method' => '',
                'http_path'   => "/video-galleries\r\n/video-galleries/*/create\r\n/video-galleries/*/edit\r\n/video-galleries/*\r\n/video-galleries/*/delete",
            ],
            [
                'name'        => 'PhotoGallery CRUD',
                'slug'        => 'photo-gallery.crud',
                'http_method' => '',
                'http_path'   => "/photo-galleries\r\n/photo-galleries/*/create\r\n/photo-galleries/*/edit\r\n/photo-galleries/*\r\n/photo-galleries/*/delete",
            ],
            [
                'name'        => 'Report Index',
                'slug'        => 'report.index',
                'http_method' => '',
                'http_path'   => "/reports\r\n/reports/*",
            ],
            [
                'name'        => 'Notification Index-Show',
                'slug'        => 'notification.index.show',
                'http_method' => '',
                'http_path'   => "/notifications\r\n/notifications/*",
            ],
            [
                'name'        => 'Notification Create',
                'slug'        => 'notification.create',
                'http_method' => '',
                'http_path'   => "/notifications/*/create",
            ],
            [
                'name'        => 'CzmSupportCounter CRUD',
                'slug'        => 'czm-support-counter.crud',
                'http_method' => '',
                'http_path'   => "/czm-support-counters\r\n/czm-support-counters/*/create\r\n/czm-support-counters/*/edit\r\n/czm-support-counters/*\r\n/czm-support-counters/*/delete",
            ],
            [
                'name'        => 'AppQuranicVerse CRUD',
                'slug'        => 'app-quranic-verse.crud',
                'http_method' => '',
                'http_path'   => "/app-quranic-verses\r\n/app-quranic-verses/*/edit\r\n/app-quranic-verses/*\r\n/app-quranic-verses/create",
            ]
        ];

        DB::table('admin_permissions')->truncate();
        DB::table('admin_role_permissions')->truncate();

        foreach ($permissionsData as $permissionData) {
            DB::table('admin_permissions')->updateOrInsert(['slug' => $permissionData['slug']], $permissionData);
        }

        //roles
        $superAdmin = DB::table('admin_roles')->where('slug', 'administrator')->first();
        $admin = DB::table('admin_roles')->where('slug', 'admin')->first();
        $campaignManager = DB::table('admin_roles')->where('slug', 'campaign_manager')->first();
        $accountant = DB::table('admin_roles')->where('slug', 'accountant')->first();
        $boardSecretary = DB::table('admin_roles')->where('slug', 'board_secretary')->first();
        $digitalMarketer = DB::table('admin_roles')->where('slug', 'digital_marketer')->first();
        $resourceMobilizer = DB::table('admin_roles')->where('slug', 'resource_mobilizer')->first();

        //permissions
        $allPermissions = DB::table('admin_permissions')->where('slug', '*')->first();
        $loginPermission = DB::table('admin_permissions')->where('slug', 'auth.login')->first();
        $dashboardPermission = DB::table('admin_permissions')->where('slug', 'dashboard')->first();
        $programPermission = DB::table('admin_permissions')->where('slug', 'program.crud')->first();
        $campaignPermission = DB::table('admin_permissions')->where('slug', 'campaign.index.create.update.show')->first();
        $campaignPermissionWithoutCreate = DB::table('admin_permissions')->where('slug', 'campaign.index.update.show')->first();
        $campaignUpdatesPermission = DB::table('admin_permissions')->where('slug', 'campaign-updates.crud')->first();
        $campaignUpdatesPermissionWithoutCreate = DB::table('admin_permissions')->where('slug', 'campaign-updates.index.update.show')->first();
        $campaignSubscriptionsPermission = DB::table('admin_permissions')->where('slug', 'campaign-subscriptions.index.show')->first();
        $userPermission = DB::table('admin_permissions')->where('slug', 'user.index.update.show')->first();
        $zakatCalculationPdfPermission = DB::table('admin_permissions')->where('slug', 'zakat-calculation-pdf')->first();
        $czmSupportCounterPermission = DB::table('admin_permissions')->where('slug', 'czm-support-counter.crud')->first();
        $newsletterIndexShowPermission = DB::table('admin_permissions')->where('slug', 'newsletter.index.show')->first();
        $contentPermission = DB::table('admin_permissions')->where('slug', 'content')->first();
        $contentSectionPermission = DB::table('admin_permissions')->where('slug', 'content-section')->first();
        $contactUsPermission = DB::table('admin_permissions')->where('slug', 'contact-us')->first();
        $contactUsCreatePermission = DB::table('admin_permissions')->where('slug', 'contact-us-create')->first();
        $bannerPermission = DB::table('admin_permissions')->where('slug', 'banner')->first();
        $videoGalleryPermission = DB::table('admin_permissions')->where('slug', 'video-gallery')->first();
        $photoGalleryPermission = DB::table('admin_permissions')->where('slug', 'photo-gallery.crud')->first();
        $categoryPermission = DB::table('admin_permissions')->where('slug', 'category.crud')->first();
        $committeePermission = DB::table('admin_permissions')->where('slug', 'committee.crud')->first();
        $memberPermission = DB::table('admin_permissions')->where('slug', 'member.crud')->first();
        $committeeMemberPermission = DB::table('admin_permissions')->where('slug', 'committeeMember.crud')->first();
        $donationPermission = DB::table('admin_permissions')->where('slug', 'donation.index.show')->first();
        $donorPermission = DB::table('admin_permissions')->where('slug', 'donor.index.show')->first();
        $governancePagePermission = DB::table('admin_permissions')->where('slug', 'governancePage.crud')->first();
        $HeroSecPermissionWithoutUpdate = DB::table('admin_permissions')->where('slug', 'heroSection.index.create.show')->first();
        $HeroSecUpdatePermission = DB::table('admin_permissions')->where('slug', 'heroSection.update')->first();
        $NisabPermissionWithoutUpdate = DB::table('admin_permissions')->where('slug', 'nisab.index.create.show')->first();
        $NisabUpdatePermission = DB::table('admin_permissions')->where('slug', 'nisab.update')->first();
        $NoticePermissionWithoutUpdate = DB::table('admin_permissions')->where('slug', 'notice.index.create.show')->first();
        $NoticeUpdatePermission = DB::table('admin_permissions')->where('slug', 'notice.update')->first();
        $userIndexShowPermission = DB::table('admin_permissions')->where('slug', 'user.index.show')->first();
        $userUpdatePermission = DB::table('admin_permissions')->where('slug', 'user.update')->first();
        $publicationsPermission = DB::table('admin_permissions')->where('slug', 'publications.crud')->first();
        $reportPermission = DB::table('admin_permissions')->where('slug', 'report.index')->first();
        $videoLessonPermission = DB::table('admin_permissions')->where('slug', 'video-lesson.crud')->first();
        $jobPostPermission = DB::table('admin_permissions')->where('slug', 'jobPost.crud')->first();
        $jobApplicationPermission = DB::table('admin_permissions')->where('slug', 'jobApplication.index.show')->first();
        $zakatCalculationPermission = DB::table('admin_permissions')->where('slug', 'zakat-calculation.index.show')->first();
        $notificationIndexShowPermission = DB::table('admin_permissions')->where('slug', 'notification.index.show')->first();
        $notificationCreatePermission = DB::table('admin_permissions')->where('slug', 'notification.create')->first();
        $appQuranicVersePermission = DB::table('admin_permissions')->where('slug', 'app-quranic-verse.crud')->first();

        //super admin permission
        if ($superAdmin && $allPermissions) {
            $superAdminRolePermission = DB::table('admin_role_permissions')
                ->where('role_id', $superAdmin->id)
                ->where('permission_id', $allPermissions->id)
                ->first();
            if (! $superAdminRolePermission) {
                DB::table('admin_role_permissions')->insert(['role_id' => $superAdmin->id, 'permission_id' => $allPermissions->id]);
            }
        }

        //campaign manager permissions
        $campaignManagerPermissions = [];
        $campaignManagerPermissions[] = $loginPermission->id;
        $campaignManagerPermissions[] = $dashboardPermission->id;
        $campaignManagerPermissions[] = $campaignPermission->id;
        $campaignManagerPermissions[] = $campaignUpdatesPermission->id;
        $campaignManagerPermissions[] = $NoticePermissionWithoutUpdate->id;
        $campaignManagerPermissions[] = $notificationIndexShowPermission->id;
        $campaignManagerPermissions[] = $notificationCreatePermission->id;

        if ($campaignManager) {
            foreach ($campaignManagerPermissions as $permissionId) {
                $campaignManagerRolePermission = DB::table('admin_role_permissions')
                    ->where('role_id', $campaignManager->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                if (! $campaignManagerRolePermission) {
                    DB::table('admin_role_permissions')->insert(['role_id' => $campaignManager->id, 'permission_id' => $permissionId]);
                }
            }
        }

        //accountant permissions
        $accountantPermissions = [];
        $accountantPermissions[] = $loginPermission->id;
        $accountantPermissions[] = $dashboardPermission->id;
        $accountantPermissions[] = $campaignSubscriptionsPermission->id;
        $accountantPermissions[] = $donationPermission->id;
        $accountantPermissions[] = $zakatCalculationPermission->id;
        $accountantPermissions[] = $zakatCalculationPdfPermission->id;
        // TODO: Add other permissions for Accountant

        if ($accountant) {
            foreach ($accountantPermissions as $permissionId) {
                $accountantRolePermission = DB::table('admin_role_permissions')
                    ->where('role_id', $accountant->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                if (! $accountantRolePermission) {
                    DB::table('admin_role_permissions')->insert(['role_id' => $accountant->id, 'permission_id' => $permissionId]);
                }
            }
        }

        //boardSecretary permissions
        $boardSecretaryPermissions = [];
        $boardSecretaryPermissions[] = $loginPermission->id;
        $boardSecretaryPermissions[] = $dashboardPermission->id;
        $boardSecretaryPermissions[] = $bannerPermission->id;
        $boardSecretaryPermissions[] = $videoGalleryPermission->id;
        $boardSecretaryPermissions[] = $contactUsPermission->id;
        $boardSecretaryPermissions[] = $contentPermission->id;
        $boardSecretaryPermissions[] = $contentSectionPermission->id;
        $boardSecretaryPermissions[] = $campaignPermissionWithoutCreate->id;
        $boardSecretaryPermissions[] = $campaignUpdatesPermissionWithoutCreate->id;
        $boardSecretaryPermissions[] = $categoryPermission->id;
        $boardSecretaryPermissions[] = $committeePermission->id;
        $boardSecretaryPermissions[] = $memberPermission->id;
        $boardSecretaryPermissions[] = $committeeMemberPermission->id;
        $boardSecretaryPermissions[] = $donorPermission->id;
        $boardSecretaryPermissions[] = $governancePagePermission->id;
        $boardSecretaryPermissions[] = $HeroSecPermissionWithoutUpdate->id;
        $boardSecretaryPermissions[] = $HeroSecUpdatePermission->id;
        $boardSecretaryPermissions[] = $NisabPermissionWithoutUpdate->id;
        $boardSecretaryPermissions[] = $NisabUpdatePermission->id;
        $boardSecretaryPermissions[] = $NoticePermissionWithoutUpdate->id;
        $boardSecretaryPermissions[] = $NoticeUpdatePermission->id;
        $boardSecretaryPermissions[] = $programPermission->id;
        $boardSecretaryPermissions[] = $userIndexShowPermission->id;
        $boardSecretaryPermissions[] = $publicationsPermission->id;
        $boardSecretaryPermissions[] = $newsletterIndexShowPermission->id;
        $boardSecretaryPermissions[] = $zakatCalculationPermission->id;
        $boardSecretaryPermissions[] = $zakatCalculationPdfPermission->id;
        $boardSecretaryPermissions[] = $notificationIndexShowPermission->id;
        $boardSecretaryPermissions[] = $notificationCreatePermission->id;
        // TODO: Add other permissions for this BoardSecretary

        if ($boardSecretary) {
            foreach ($boardSecretaryPermissions as $permissionId) {
                $boardSecretaryRolePermission = DB::table('admin_role_permissions')
                    ->where('role_id', $boardSecretary->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                if (! $boardSecretaryRolePermission) {
                    DB::table('admin_role_permissions')->insert(['role_id' => $boardSecretary->id, 'permission_id' => $permissionId]);
                }
            }
        }

        //digitalMarketer permissions
        $digitalMarketerPermissions = [];
        $digitalMarketerPermissions[] = $loginPermission->id;
        $digitalMarketerPermissions[] = $dashboardPermission->id;
        $digitalMarketerPermissions[] = $bannerPermission->id;
        $digitalMarketerPermissions[] = $videoGalleryPermission->id;
        $digitalMarketerPermissions[] = $contactUsPermission->id;
        $digitalMarketerPermissions[] = $contentPermission->id;
        $digitalMarketerPermissions[] = $contentSectionPermission->id;
        $digitalMarketerPermissions[] = $campaignPermission->id;
        $digitalMarketerPermissions[] = $campaignUpdatesPermission->id;
        $digitalMarketerPermissions[] = $committeePermission->id;
        $digitalMarketerPermissions[] = $memberPermission->id;
        $digitalMarketerPermissions[] = $committeeMemberPermission->id;
        $digitalMarketerPermissions[] = $governancePagePermission->id;
        $digitalMarketerPermissions[] = $HeroSecPermissionWithoutUpdate->id;
        $digitalMarketerPermissions[] = $NisabPermissionWithoutUpdate->id;
        $digitalMarketerPermissions[] = $NoticePermissionWithoutUpdate->id;
        $digitalMarketerPermissions[] = $publicationsPermission->id;
        $digitalMarketerPermissions[] = $newsletterIndexShowPermission->id;
        $digitalMarketerPermissions[] = $notificationCreatePermission->id;
        // TODO: Add other permissions for DigitalMarketer

        if ($digitalMarketer) {
            foreach ($digitalMarketerPermissions as $permissionId) {
                $digitalMarketerRolePermission = DB::table('admin_role_permissions')
                    ->where('role_id', $digitalMarketer->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                if (! $digitalMarketerRolePermission) {
                    DB::table('admin_role_permissions')->insert(['role_id' => $digitalMarketer->id, 'permission_id' => $permissionId]);
                }
            }
        }

        //resourceMobilizer permissions
        $resourceMobilizerPermissions = [];
        $resourceMobilizerPermissions[] = $loginPermission->id;
        $resourceMobilizerPermissions[] = $dashboardPermission->id;
        $resourceMobilizerPermissions[] = $contactUsCreatePermission->id;
        $resourceMobilizerPermissions[] = $contactUsPermission->id;
        $resourceMobilizerPermissions[] = $newsletterIndexShowPermission->id;
        $resourceMobilizerPermissions[] = $campaignPermissionWithoutCreate->id;
        $resourceMobilizerPermissions[] = $campaignUpdatesPermissionWithoutCreate->id;
        $resourceMobilizerPermissions[] = $campaignSubscriptionsPermission->id;
        $resourceMobilizerPermissions[] = $categoryPermission->id;
        $resourceMobilizerPermissions[] = $donationPermission->id;
        $resourceMobilizerPermissions[] = $donorPermission->id;
        $resourceMobilizerPermissions[] = $NisabPermissionWithoutUpdate->id;
        $resourceMobilizerPermissions[] = $NisabUpdatePermission->id;
        $resourceMobilizerPermissions[] = $NoticePermissionWithoutUpdate->id;
        $resourceMobilizerPermissions[] = $NoticeUpdatePermission->id;
        $resourceMobilizerPermissions[] = $programPermission->id;
        $resourceMobilizerPermissions[] = $userIndexShowPermission->id;
        $resourceMobilizerPermissions[] = $zakatCalculationPermission->id;
        $resourceMobilizerPermissions[] = $zakatCalculationPdfPermission->id;
        $resourceMobilizerPermissions[] = $notificationIndexShowPermission->id;
        $resourceMobilizerPermissions[] = $notificationCreatePermission->id;
        // TODO: Add other permissions for ResourceMobilizer

        if ($resourceMobilizer) {
            foreach ($resourceMobilizerPermissions as $permissionId) {
                $resourceMobilizerRolePermission = DB::table('admin_role_permissions')
                    ->where('role_id', $resourceMobilizer->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                if (! $resourceMobilizerRolePermission) {
                    DB::table('admin_role_permissions')->insert(['role_id' => $resourceMobilizer->id, 'permission_id' => $permissionId]);
                }
            }
        }

        //admin permissions
        $adminPermissions = [];
        $adminPermissions[] = $loginPermission->id;
        $adminPermissions[] = $dashboardPermission->id;
        $adminPermissions[] = $campaignPermission->id;
        $adminPermissions[] = $campaignUpdatesPermission->id;
        $adminPermissions[] = $userPermission->id;
        $adminPermissions[] = $campaignSubscriptionsPermission->id;
        $adminPermissions[] = $newsletterIndexShowPermission->id;
        $adminPermissions[] = $contentPermission->id;
        $adminPermissions[] = $contentSectionPermission->id;
        $adminPermissions[] = $contactUsPermission->id;
        $adminPermissions[] = $bannerPermission->id;
        $adminPermissions[] = $videoGalleryPermission->id;
        $adminPermissions[] = $photoGalleryPermission->id;
        $adminPermissions[] = $categoryPermission->id;
        $adminPermissions[] = $committeePermission->id;
        $adminPermissions[] = $memberPermission->id;
        $adminPermissions[] = $committeeMemberPermission->id;
        $adminPermissions[] = $donationPermission->id;
        $adminPermissions[] = $donorPermission->id;
        $adminPermissions[] = $governancePagePermission->id;
        $adminPermissions[] = $HeroSecPermissionWithoutUpdate->id;
        $adminPermissions[] = $HeroSecUpdatePermission->id;
        $adminPermissions[] = $NisabPermissionWithoutUpdate->id;
        $adminPermissions[] = $NisabUpdatePermission->id;
        $adminPermissions[] = $NoticePermissionWithoutUpdate->id;
        $adminPermissions[] = $NoticeUpdatePermission->id;
        $adminPermissions[] = $programPermission->id;
        $adminPermissions[] = $userIndexShowPermission->id;
        $adminPermissions[] = $userUpdatePermission->id;
        $adminPermissions[] = $publicationsPermission->id;
        $adminPermissions[] = $czmSupportCounterPermission->id;
        $adminPermissions[] = $reportPermission->id;
        $adminPermissions[] = $zakatCalculationPermission->id;
        $adminPermissions[] = $zakatCalculationPdfPermission->id;
        $adminPermissions[] = $videoLessonPermission->id;
        $adminPermissions[] = $jobPostPermission->id;
        $adminPermissions[] = $jobApplicationPermission->id;
        $adminPermissions[] = $notificationIndexShowPermission->id;
        $adminPermissions[] = $notificationCreatePermission->id;
        $adminPermissions[] = $appQuranicVersePermission->id;
        // TODO: Add other permissions for Admin

        if ($admin) {
            foreach ($adminPermissions as $permissionId) {
                $adminRolePermission = DB::table('admin_role_permissions')
                    ->where('role_id', $admin->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                if (! $adminRolePermission) {
                    DB::table('admin_role_permissions')->insert(['role_id' => $admin->id, 'permission_id' => $permissionId]);
                }
            }
        }
    }
}
