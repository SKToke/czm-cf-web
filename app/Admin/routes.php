<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UserController::class);
    $router->resource('roles', RoleController::class);
    $router->resource('permissions', PermissionController::class);
    $router->resource('programs', ProgramController::class);
    $router->resource('categories', CategoryController::class);
    $router->resource('attachments', AttachmentController::class);
    $router->resource('notices', NoticeController::class);
    $router->resource('contents', ContentController::class);
    $router->resource('contentSections', ContentSectionController::class);
    $router->resource('campaigns', CampaignController::class);
    $router->resource('sliders', HeroSectionController::class);
    $router->resource('newsletter_subscriptions', NewsletterController::class);
    $router->resource('contact-us-queries', ContactUsController::class);
    $router->resource('nisabs', NisabController::class);
    $router->resource('donations', DonationController::class);
    $router->resource('donors', DonorController::class);
    $router->resource('job_postings', JobPostController::class);
    $router->resource('job_applications', JobApplicationController::class);
    $router->resource('publications', PublicationController::class);
    $router->resource('campaign-updates', CampaignUpdateController::class);
    $router->resource('video-galleries', VideoGalleryController::class);
    $router->resource('photo-galleries', PhotoGalleryController::class);
    $router->resource('video-lessons', VideoLessonsController::class);
    $router->resource('campaign-subscriptions', CampaignSubscriptionController::class);
    $router->resource('banners', BannerController::class);
    $router->resource('members', MemberController::class);
    $router->resource('committees', CommitteeController::class);
    $router->resource('committee-members', CommitteeMemberController::class);
    $router->resource('governance-pages', GovernancePageController::class);
    $router->resource('czm-support-counters', CzmSupportCounterController::class);
    $router->resource('app-quranic-verses', AppQuranicVerseController::class);
    $router->resource('user-zakat-calculations', UserZakatCalculationController::class);
    $router->post('/generate-zakat-calculation-pdf/{id}', 'UserZakatCalculationController@generatePDF')->name('zakat-calculation.pdf');

    $router->resource('reports', ReportsController::class);
    $router->resource('notifications', NotificationController::class);
    $router->get('/notifications/create', 'NotificationController@create')->name('notifications.create');

    $router->get('/contents/{content}', 'ContentController@show')->name('contents.show');
    $router->get('/campaigns/{campaign}', 'CampaignController@show')->name('campaigns.show');
    $router->get('/campaign-updates/create', 'CampaignUpdateController@create')->name('campaign-updates.create');

    $router->get('/contentSections/create', 'ContentSectionController@create')->name('contentSections.create');
    $router->match(['get', 'post'], '/contentSections/{contentSection}/edit', 'ContentSectionController@edit')->name('contentSections.edit');
    $router->post('/contentSections/{contentSectionId}/remove', 'ContentSectionController@customDestroy')->name('contentSections.customDestroy');

});
