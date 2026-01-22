<?php

use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProgramController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZakatCalculatorController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AboutUsController;

/*
|--------------
| API Routes
|--------------
|
| These routes are loaded by the RouteServiceProvider and
| all of them will be assigned to the "api" middleware group.
| To call these routes, add << \api\* >> before every route endpoint.
|
*/

/*
|--------------------
| Public Routes
|--------------------
|
| These APIs do not require any bearer token.
|
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/social-auth/provider-callback', [AuthController::class, 'providerCallback']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
Route::get('/nisab-values', [ZakatCalculatorController::class, 'index']);
Route::post('/personal-zakat', [ZakatCalculatorController::class, 'personalZakatCalculation']);
Route::post('/business-zakat', [ZakatCalculatorController::class, 'businessZakatCalculation']);
Route::post('/store', [ContactUsController::class, 'store']);
Route::get('/aboutUs', [AboutUsController::class, 'aboutUs']);
Route::get('/achievements', [HomeController::class, 'achievements']);
Route::get('/zakat-in-quran', [HomeController::class, 'zakatInQuran']);
Route::get('/zakat-in-hadith', [HomeController::class, 'zakatInHadith']);
Route::get('/zakat-faq', [HomeController::class, 'zakatFaq']);
Route::get('/app-quranic-verse', [HomeController::class, 'appQuranicVerse']);

Route::post('czm-payment', [PaymentController::class, 'payViaAjax']);

Route::get('/czm-default-programs', [ProgramController::class, 'defaultPrograms']);
Route::get('/czm-all-programs', [ProgramController::class, 'allPrograms']);
Route::get('/czm-program/{slug}', [ProgramController::class, 'details']);

Route::get('czm-all-campaigns', [CampaignController::class, 'allCampaigns']);
Route::get('czm-latest-campaigns', [CampaignController::class, 'latestCampaigns']);
Route::get('czm-program-campaigns/{programSlug}', [CampaignController::class, 'programCampaigns']);
Route::get('czm-campaign/{campaignSlug}', [CampaignController::class, 'details']);
Route::post('/filtered-campaigns', [CampaignController::class, 'filteredCampaigns']);
Route::get('/categories', [HomeController::class, 'getCategories']);

Route::post('/campaign-subscription', [CampaignController::class, 'campaignSubscription']);


/*
|--------------------
| Protected Routes
|--------------------
|
| When an api request goes through auth:sanctum middleware, it requires
| a bearer token for a valid, logged-in user << Auth::user() >> .
|
*/
route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/user-info', [UserController::class, 'index']);
    Route::get('/user-payment-history-pdf', [UserController::class, 'exportPaymentStatement']);
    Route::post('/edit-user', [UserController::class, 'update']);
    Route::get('/user-donation-history', [UserController::class, 'userDonations']);
    Route::get('/user-pending-donations', [UserController::class, 'pendingDonations']);
    Route::get('/user-all-donations', [UserController::class, 'allDonations']);
    Route::post('/save-zakat-calculation', [ZakatCalculatorController::class, 'saveZakatCalculation']);
    Route::post('/download-zakat-calculation', [ZakatCalculatorController::class, 'downloadZakatCalculation']);
    Route::post('/download-archived-zakat-calculation', [ZakatCalculatorController::class, 'downloadArchivedZakatCalculation']);
    Route::get('/archived-zakat-calculations', [UserController::class, 'archivedZakatCalculations']);
    Route::get('/user-notifications', [NotificationController::class, 'userNotifications']);
    Route::post('/mark-notification', [NotificationController::class, 'markNotificationAsRead']);
});
