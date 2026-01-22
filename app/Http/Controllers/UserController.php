<?php

namespace App\Http\Controllers;

use App\Enums\TransactionTypeEnum;
use App\Helpers\FlashHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Enums\CountryTypeEnum;
use App\Enums\DistrictTypeEnum;
use App\Enums\GenderTypeEnum;
use App\Enums\ProfessionTypeEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use PDF;

class UserController extends Controller
{
    public function verifyEmail()
    {
        return view('auth.verify');
    }

    public function verifyVerification(EmailVerificationRequest $request)
    {
        $request->fulfill();
        FlashHelper::trigger('Your email address has been verified successfully!', 'success');
        return redirect()->route('home');
    }

    public function sendVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        FlashHelper::trigger('Verification link sent!', 'success');
        return back();
    }

    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function providerCallback(string $provider)
    {
        /**
         * Points: one user can have multiple social accounts.
         * if social account exists then user also exists and so redirect to login
         * if social acc doesn't exist then 3 possible cases:
         * 1. user doesn't exist 2. user exists with own acc 3. user exists with another social acc.
         */
        try {
            $socialUser = Socialite::driver($provider)->user();

            //set userEmail for null (here fb) mail
            $userEmail = $socialUser->getEmail();
            if (null === $userEmail) {
                $userId = $socialUser->getId();
                $userEmail = "${userId}@facebook.com";
            }

            //find user
            $user = User::where([
                'email' => $userEmail,
            ])->first();

            //if user exits check social_info
            if ($user) {
                $key = "${provider}_id";
                $val = $socialUser->getId();

                $infoArray = json_decode($user->social_info, true);
                if (null === $infoArray || false === array_key_exists($key, $infoArray)) {
                    $infoArray[$key] = $val;
                    $user->social_info = json_encode($infoArray);
                    $user->save();
                }
            }

            //if user not found then create new user and add info
            if (! $user) {
                $key = "${provider}_id";
                $val = $socialUser->getId();

                $infoArray = [
                    $key => $val,
                ];

                $user = User::create([
                    'email' => $userEmail,
                    'first_name' => $socialUser->getName(),
                    'last_name' => '',
                    'password' => Str::uuid()->toString(),
                    'social_info' => json_encode($infoArray),
                    'email_verified_at' => now(),
                ]);
            }

            if(!$user->removed) {
                Auth::login($user, true);
                if ($user->active && $user->hasValidRoles()) {
                    auth()->guard('admin')->login($user);
                    return redirect('/admin');
                }
                FlashHelper::trigger('You have Successfully logged in!', 'success');
            }else{
              FlashHelper::trigger('Account not found', 'danger');
            }
            return redirect()->route('home');

        } catch (Exception $e) {
            FlashHelper::trigger('Opps! Something went wrong. Please try again later.', 'danger');
            return redirect()->route('home');
        }
    }

    public function userDonations()
    {
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        $upcomingDonations = $currentUser->getUpcomingDonations();

        $currentDonor = $currentUser->donor;
        if($currentDonor === null){
            return view('user_donations.user-donations',
                ['pastDonations' => null, 'upcomingDonations' => $upcomingDonations, 'currentUser' => $currentUser]);
        }
        $donations = $currentDonor->donations;
        if ($donations) {
            $donations = $donations->filter(function ($donation) {
                return $donation->transaction_status == TransactionTypeEnum::Complete->value;
            })->sortByDesc('created_at');
        }
        return view('user_donations.user-donations',
            ['pastDonations' => $donations, 'upcomingDonations' => $upcomingDonations, 'currentUser' => $currentUser]);
    }

    public function donationHistory()
    {
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }

        $currentDonor = $currentUser->donor;
        if($currentDonor === null){
            return view('user_donations.donation-history',
                ['pastDonations' => null, 'currentUser' => $currentUser]);
        }
        $donations = $currentDonor->donations;
        if ($donations) {
            $donations = $donations->filter(function ($donation) {
                return $donation->transaction_status == TransactionTypeEnum::Complete->value;
            })->sortByDesc('created_at');
        }
        return view('user_donations.donation-history',
            ['pastDonations' => $donations, 'currentUser' => $currentUser]);
    }

    public function upcomingDonations()
    {
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        $upcomingDonations = $currentUser->getUpcomingDonations();

        return view('user_donations.upcoming-donations',
            ['upcomingDonations' => $upcomingDonations]);
    }

    public function archivedZakatCalculations()
    {
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        $calculationRecords = $currentUser->zakatCalculations()->where('archived', true)->orderBy('date', 'desc')->get();

        return view('user.archived_zakat_calculations',
            ['calculationRecords' => $calculationRecords, 'currentUser' => $currentUser]);
    }

    public function userNotifications()
    {
        $currentUser = auth()->user();

        if (!$currentUser || !$currentUser->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        if ($currentUser->notifications) {
            $notifications = $currentUser->getUnreadNotifications();
            $archivedNotifications = $currentUser->getArchivedNotifications();
        }

        return view('user.notifications',
            ['notifications' => $notifications, 'archivedNotifications' => $archivedNotifications, 'currentUser' => $currentUser]);
    }

    public function showNotificationSettingsForm()
    {
        $user = auth()->user();
        $userSettings = $user->notificationSettings()->get();
        return view('user.notification_settings', ['user' => $user, 'userSettings' => $userSettings]);
    }

    public function saveNotificationSettings(Request $request)
    {
        $user = auth()->user();
        $userSettings = $user->notificationSettings()->firstOrNew();

        if ($request->input('allow_general_type')) {
            $userSettings->allow_general_type = true;
        } else {
            $userSettings->allow_general_type = false;
        }
        if ($request->input('allow_campaign_launch')) {
            $userSettings->allow_campaign_launch = true;
        } else {
            $userSettings->allow_campaign_launch = false;
        }
        if ($request->input('allow_campaign_milestone')) {
            $userSettings->allow_campaign_milestone = true;
        } else {
            $userSettings->allow_campaign_milestone = false;
        }
        if ($request->input('allow_campaign_countdown')) {
            $userSettings->allow_campaign_countdown = true;
        } else {
            $userSettings->allow_campaign_countdown = false;
        }
        if ($request->input('allow_campaign_progress')) {
            $userSettings->allow_campaign_progress = true;
        } else {
            $userSettings->allow_campaign_progress = false;
        }
        if ($request->input('allow_campaign_reminder')) {
            $userSettings->allow_campaign_reminder = true;
        } else {
            $userSettings->allow_campaign_reminder = false;
        }
        if ($request->input('allow_gratitude')) {
            $userSettings->allow_gratitude = true;
        } else {
            $userSettings->allow_gratitude = false;
        }
        $userSettings->frequency = $request->input('frequency');
        $userSettings->save();

        FlashHelper::trigger('You notification preferences have been saved successfully', 'success');
        return redirect()->route('notification-settings');
    }

    public function userPayments()
    {
        $currentUser = auth()->user();

        if (!$currentUser || !$currentUser->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        $payments = null;
        if ($currentUser->successfulDonations()) {
            $payments = $currentUser->successfulDonations()->get();
        }
        return view('user.payment_history',
            ['payments' => $payments, 'currentUser' => $currentUser]);
    }

    public function exportPaymentStatement(Request $request)
    {
        if (!auth()->user()) {
            FlashHelper::trigger('Sorry! You need to login first to proceed.', 'danger');
            return redirect()->back();
        }

        $currentUser = auth()->user();
        $payments = $currentUser->successfulDonations();

        if ($request->filled('payment_start_date')) {
            $start_date = Carbon::createFromFormat('Y-m-d', $request->input('payment_start_date'))->startOfDay();
            $payments->where('created_at', '>=', $start_date);
        }

        if ($request->filled('payment_end_date')) {
            $end_date = Carbon::createFromFormat('Y-m-d', $request->input('payment_end_date'))->endOfDay();
            $payments->where('created_at', '<=', $end_date);
        }

        $payments = $payments->orderBy('created_at')->get();
        $totalAmount = $payments->sum('amount');

        $pdf = PDF::loadView('pdf.user-payment-statement', [
            'payments' => $payments,
            'totalAmount' => $totalAmount,
            'currentUser' => $currentUser,
            'start_date' => $request->input('payment_start_date'),
            'end_date' => $request->input('payment_end_date')
        ]);
        return $pdf->download('user-payment-statement.pdf');
    }

    public function filterPayments(Request $request)
    {
        $currentUser = auth()->user();
        $payments = $currentUser->successfulDonations();

        if ($request->filled('start_date')) {
            $start_date = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->startOfDay();
            $payments->where('created_at', '>=', $start_date);
        }

        if ($request->filled('end_date')) {
            $end_date = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))->endOfDay();
            $payments->where('created_at', '<=', $end_date);
        }

        $payments = $payments->orderBy('created_at')->get();

        return view('user.filtered_payments', compact('payments'));
    }

    public function adminDashboard(Request $request)
    {
        return redirect()->intended('admin/auth/login');
    }

    public function show(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        if($user->active){
            return view('user.show', compact('user'));
        }else{
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
    }

    public function edit(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $genders= GenderTypeEnum::toArray();
        $professions= ProfessionTypeEnum::toArray();
        $userTypes= UserTypeEnum::toArray();
        $countries= CountryTypeEnum::toArray();
        $districts= DistrictTypeEnum::toArray();

        if($user->active){
            return view('user.edit', compact('user','genders','professions','userTypes','countries','districts'));
        }else{
            FlashHelper::trigger('You need to be active to edit the profile', 'danger');
            auth()->logout();
            return redirect()->route('home');
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'mobile_no' => 'nullable|numeric',
            'whatsapp_no' => 'nullable|numeric',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|integer|max:255',
            'profession' => 'nullable|string|max:255',
            'user_type' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]*$/',
            'contact_person_mobile' => 'nullable|string|max:255',
            'contact_person_designation' => 'nullable|string|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
            'post_code' => 'nullable|string|max:255',
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);

        if(!$user->active){
            FlashHelper::trigger('You need to be active to update the profile', 'danger');
            auth()->logout();
            return redirect()->route('home');
        }

        $excludeFields = ['password', 'password_confirmation', 'current_password'];
        $userData = Arr::except($validatedData, $excludeFields);

        $user->fill($userData);

        if (!empty($validatedData['password'])) {
            if (Hash::check($validatedData['current_password'], $user->password)) {
                $user->password = Hash::make($validatedData['password']);
            } else {
                FlashHelper::trigger('Incorrect password.', 'danger');
                return redirect()->route('user.edit',$user);
            }
        }

        try {
            if($user->country!=CountryTypeEnum::Bangladesh){
                $user->district=null;
                $user->thana=null;
                $user->post_code=null;
            }

            if($user->user_type==UserTypeEnum::Individual){
                $user->contact_person_name=null;
                $user->contact_person_mobile=null;
                $user->contact_person_designation=null;
            }

            $user->save();
            FlashHelper::trigger('Successfully updated the profile.', 'success');
            return redirect()->route('user.show',$user);
        }catch(\Exception){
            FlashHelper::trigger('there is a problem while submitting the form.', 'danger');
            return redirect()->route('user.edit',$user);
        }
    }
    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->active){
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        $genders= GenderTypeEnum::toArray();
        $userTypes= UserTypeEnum::toArray();

        if($user->active){
            return view('user.delete_account', compact('user','genders','userTypes'));
        }else{
            auth()->logout();
            return redirect()->route('home');
        }
    }


    public function deactivate(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);

        if (!$user->active) {
            FlashHelper::trigger('You need to be active to deactivate the profile', 'danger');
            auth()->logout();
            return redirect()->route('home');
        }

        if (!Hash::check($validatedData['password'], $user->password)) {
            FlashHelper::trigger('Incorrect password.', 'danger');
            return redirect()->route('user.delete-account', $user);
        }

        $user->removed = true;
        try {
            $user->save();
            FlashHelper::trigger('Account deactivated successfully.', 'success');
            auth()->logout();
            return redirect()->route('home');
        } catch (\Exception $e) {
            FlashHelper::trigger('There is a problem while submitting the form.', 'danger');
            return redirect()->route('user.delete-account', $user);
        }
    }


    public function campaignSupscriptionHistory(Request $request)
    {
        $user = $request->attributes->get('user');
        if (!$user || !$user->active) {
            FlashHelper::trigger('You are not authorized to view this page.', 'danger');
            return redirect()->route('home');
        }
        $subscriptionHistory = null;
        if ($user->allCampaignSubscriptions()) {
            $subscriptionHistory = $user->allCampaignSubscriptions()
                ->orderBy('campaign_subscriptions.updated_at', 'desc')
                ->paginate(12);

        }
        return view('user.campaign_supscription_history')
            ->with([
                'subscriptions' => $subscriptionHistory
            ]);
    }
}
