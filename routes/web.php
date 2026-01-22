<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ZakatCalculatorController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your components. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/', [HomeController::class, 'home'])->name('home');

//user verification routes
Route::get('/email/verify', [UserController::class, 'verifyEmail'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyVerification'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::get('/email/verification-notification', [UserController::class, 'sendVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ResetPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

//user social-login
Route::group(['prefix' => 'social-auth/{provider}'], function (): void {
    Route::get(
        '/callback',
        [UserController::class, 'providerCallback']
    )->name('social.callback');
    Route::get(
        '',
        [UserController::class, 'redirectToProvider']
    )->name('social.login');
});

// Static pages
Route::get('/czm-about-us', [HomeController::class, 'aboutUs'])->name('aboutUs');
Route::get('/czm-zakat-in-hadiths', [HomeController::class, 'zakatInHadiths'])->name('zakatInHadiths');
Route::get('/czm-personal-zakat', [HomeController::class, 'personalZakat'])->name('personalZakat');
Route::get('/czm-business-zakat', [HomeController::class, 'businessZakat'])->name('businessZakat');
Route::get('/czm-zakat-on-agriculture', [HomeController::class, 'zakatOnAgriculture'])->name('zakatOnAgriculture');
Route::get('/czm-accountability', [HomeController::class, 'accountability'])->name('accountability');
Route::get('/czm-terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/czm-zakat-faq', [HomeController::class, 'zakatFaq'])->name('zakat-faq');
Route::get('/czm-privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/czm-governance', [HomeController::class, 'czmGovernance'])->name('czm-governance');
Route::get('/czm-member/{id}', [HomeController::class, 'czmMemberDetails'])->name('czm-member-details');

Route::post('/subscribe', [HomeController::class, 'subscribe'])->name('subscribe');
Route::get('/video-galleries', [HomeController::class, 'videoGallery'])->name('video-gallery');
Route::get('/video-lessons', [HomeController::class, 'videoLessons'])->name('video-lessons');
Route::get('/filter-videos', [HomeController::class, 'filterVideos'])->name('filter-video-gallery');
Route::get('/photo-galleries', [HomeController::class, 'photoGallery'])->name('photo-gallery');
Route::get('/filter-photos', [HomeController::class, 'filterPhotos'])->name('filter-photo-gallery');
Route::get('/notices', [NoticeController::class, 'index'])->name('notices');
Route::get('/contact-us', [ContactUsController::class, 'index'])->name('contact-us.index');
Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact-us.store');

// Program routes
Route::get('/czm-program', [ProgramController::class, 'index'])->name('programs');
Route::get('/czm-program/{slug}', [ProgramController::class, 'show'])->name('program-details');

//content routes
Route::get('/czm-blogs', [ContentController::class, 'blogs'])->name('blogs');
Route::get('/czm-news', [ContentController::class, 'news'])->name('news');
Route::get('/czm-sadaqah', [ContentController::class, 'sadaqah'])->name('sadaqah');
Route::get('/czm-cash-waqf', [ContentController::class, 'cashWaqf'])->name('cashWaqf');
Route::get('/czm-success-stories', [ContentController::class, 'successStories'])->name('success_stories');
Route::get('/quranic-verses', [ContentController::class, 'quranicVerses'])->name('quranic_verses');
Route::get('/qard-al-hasan', [ContentController::class, 'qardAlHasan'])->name('qard_al_hasan');
Route::get('/czm-content/{slug}', [ContentController::class, 'contentDetails'])->name('content');
Route::get('/czm-filtered-contents', [ContentController::class, 'filterContent'])->name('filter-contents');

// Payment Routes
Route::get('/czm-payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::post('/pay-via-ajax', [PaymentController::class, 'payViaAjax'])->name('payment.ajax');
Route::post('/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::post('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/ipn', [PaymentController::class, 'ipn'])->name('payment.ipn');

//Job Routes
Route::get('/czm-job-posts', [JobPostController::class, 'index'])->name('jobPost.index');
Route::post('/filter-job-posts', [JobPostController::class, 'filterJobs'])->name('filter-jobs');
Route::get('/czm-job-post/{slug}', [JobPostController::class, 'show'])->name('jobPost.show');
Route::post('/czm-job-post-submit', [JobPostController::class, 'submit'])->name('jobPost.submit');

// Campaign routes
Route::get('/czm-campaign', [CampaignController::class, 'index'])->name('campaigns');
Route::get('/czm-campaign/{slug}', [CampaignController::class, 'show'])->name('campaign-details');
Route::get('/filter-campaigns', [CampaignController::class, 'filterCampaigns'])->name('filter-campaigns');
Route::get('description_campaign_tab/{slug}', [CampaignController::class, 'getDescriptionTab'])->name('description_campaign_tab');
Route::get('documents_campaign_tab/{slug}', [CampaignController::class, 'getDocumentsTab'])->name('documents_campaign_tab');
Route::get('updates_campaign_tab/{slug}', [CampaignController::class, 'getCampaignUpdatesTab'])->name('updates_campaign_tab');
Route::post('subscribe_campaign/{slug}', [CampaignController::class, 'subscribe'])->name('subscribe-campaign');
Route::post('unsubscribe_campaign/{slug}', [CampaignController::class, 'unsubscribe'])->name('unsubscribe-campaign');
Route::post('czm-campaign/{slug}/update-share-count', [CampaignController::class, 'updateShareCount'])->name('update-campaign-share-count');

// User routes
Route::middleware(['ensureUserIsCurrent'])->group(function () {
    Route::get('/user/{id}/show', [UserController::class, 'show'])->name('user.show');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/{id}/deactivate', [UserController::class, 'deactivate'])->name('user.deactivate');
    Route::post('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/campaign-supscription-history', [UserController::class, 'campaignSupscriptionHistory'])->name('user.campaign-supscription-history');
    Route::get('/admin-panel', [UserController::class, 'adminDashboard'])->name('user.admin-dashboard');
});
Route::get('/user/delete-account', [UserController::class, 'deleteAccount'])->name('user.delete-account');
Route::get('/user-donations', [UserController::class, 'userDonations'])->name('user-donations');
Route::get('/donation-history', [UserController::class, 'donationHistory'])->name('donation-history');
Route::get('/user-upcoming-donations', [UserController::class, 'upcomingDonations'])->name('upcoming-donations');
Route::get('/archived-zakat-calculations', [UserController::class, 'archivedZakatCalculations'])->name('archived-zakat-calculations');
Route::get('/user-notifications', [UserController::class, 'userNotifications'])->name('user-notifications');
Route::put('/user-notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
Route::get('/notification-settings', [UserController::class, 'showNotificationSettingsForm'])->name('notification-settings');
Route::post('/save-notification-settings', [UserController::class, 'saveNotificationSettings'])->name('save-notification-settings');
Route::get('/user-payments', [UserController::class, 'userPayments'])->name('user-payments');
Route::get('/filter-payments', [UserController::class, 'filterPayments'])->name('filter-payments');
Route::post('/user-payments/export', [UserController::class, 'exportPaymentStatement'])->name('user.export-payment-statement');

//Publication Routes
Route::get('/publications', [PublicationController::class, 'publications'])->name('publications');
Route::get('/auditReports', [PublicationController::class, 'auditReports'])->name('auditReports');
Route::get('/books', [PublicationController::class, 'books'])->name('books');
Route::get('/reports', [PublicationController::class, 'reports'])->name('reports');
Route::get('/newsletters', [PublicationController::class, 'newsletters'])->name('newsletters');
Route::match(['get', 'post'],'/publications/download/{id}', [PublicationController::class, 'download'])->name('download');

//Reports route
Route::post('/report-download/{id}', [ReportController::class, 'download'])->name('report-download');
Route::post('/report-monthly-payment-download/{id}', [ReportController::class, 'monthlyPaymentDownload'])->name('monthly-payment-report');
Route::post('/report-monthly-payment-by-project-download/{id}', [ReportController::class, 'monthlyReportByProgram'])->name('monthly-payment-report-by-project');
Route::post('/report-disbursement-download/{id}', [ReportController::class, 'disbursementReportDownload'])->name('disbursement-report');
Route::post('/report-disbursement-by-project-download/{id}', [ReportController::class, 'disbursementReportByProgramDownload'])->name('disbursement-report-by-project');

//Zakat Calculator routes
Route::get('/zakat-calculator', [ZakatCalculatorController::class, 'index'])->name('zakat-calculator');
Route::post('/zakat/personal', [ZakatCalculatorController::class, 'personalZakatCalculation'])->name('zakat.personal');
Route::post('/zakat/business', [ZakatCalculatorController::class, 'businessZakatCalculation'])->name('zakat.business');
Route::post('/zakat/pay-calculated-zakat', [ZakatCalculatorController::class, 'payCalculatedZakat'])->name('zakat.payCalculatedZakat');
Route::post('/zakat/save-calculation', [ZakatCalculatorController::class, 'saveCalculationToArchive'])->name('zakat.saveCalculation');
Route::post('/zakat/export', [ZakatCalculatorController::class, 'exportPdf'])->name('zakat.exportPdf');
Route::get('/reset-zakat-form', [ZakatCalculatorController::class, 'resetZakatForm'])->name('zakat.resetForm');

//image routes
Route::get('/images/{imageName}', function ($imageName) {
    $path = public_path('images/' . $imageName);

    return response()->file($path);
})->name('images.logo');
