<?php

namespace App\Http\Controllers\Api;

use App\Enums\CountryTypeEnum;
use App\Enums\UserTypeEnum;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Models\CampaignSubscription;
use App\Models\Donation;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;
class UserController extends Controller
{
    use HttpResponses;

    public function index()
    {
        try {
            $user = Auth::user();

            if ($user) {
                // Prepare the user information
                $userInfo = [
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'birthday' => $user->date_of_birth,
                    'phone_number' => $user->mobile_no,
                    'address' => CountryTypeEnum::Bangladesh->getTitle(),
                ];

                return $this->success('User information', $userInfo);
            } else {
                return $this->error('Unauthorized', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::user()->id],
            'mobile_no' => ['nullable', 'numeric'],
            'whatsapp_no' => ['nullable', 'numeric'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:255'], // Changed 'integer' to 'string'
            'profession' => ['nullable', 'string', 'max:255'],
            'user_type' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]*$/'],
            'contact_person_mobile' => ['nullable', 'string', 'max:255'],
            'contact_person_designation' => ['nullable', 'string', 'max:255'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'thana' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:255'],
            'current_password' => ['required_with:password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'], // Added 'confirmed' for password confirmation
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors(), 422);
        }
        try {
            $user = Auth::user();

            if (!$user->active) {
                Auth::user()->currentAccessToken()->delete();
                return $this->error('You need to be active to update the profile', [], 401);
            }

            $excludeFields = ['password', 'password_confirmation', 'current_password'];
            $userData = $request->except($excludeFields);
            $userData['first_name'] = $request->get('name');

            if ($request->password !== null) {
                if (Hash::check($request->current_password, $user->password)) {
                    $userData['password'] = Hash::make($request->password);
                } else {
                    return $this->error('Failed to update the user: Incorrect current password', [], 401);
                }
            }


            if (array_key_exists('country', $userData) && $userData['country'] != CountryTypeEnum::Bangladesh->value) {
                $userData['district'] = null;
                $userData['thana'] = null;
                $userData['post_code'] = null;
            }

            if (array_key_exists('user_type', $userData) && $userData['user_type'] == UserTypeEnum::Individual->value) {
                $userData['contact_person_name'] = null;
                $userData['contact_person_mobile'] = null;
                $userData['contact_person_designation'] = null;
            }

            $user->update($userData);

            return $this->success('User updated successfully', []);
        } catch (\Exception $e) {
            return $this->error('Failed to update the user', [$e->getMessage()], 401);
        }
    }

    public function exportPaymentStatement(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $payments = $currentUser->successfulDonations();

            if($payments==null){
                return $this->success('No payments avaiable');
            }

            if ($request->filled('payment_start_date')) {
                $start_date = Carbon::createFromFormat('Y-m-d', $request->input('payment_start_date'))->startOfDay();
                $payments->where('created_at', '>=', $start_date);
            }

            if ($request->filled('payment_end_date')) {
                $end_date = Carbon::createFromFormat('Y-m-d', $request->input('payment_end_date'))->endOfDay();
                $payments->where('created_at', '<=', $end_date);
            }

            $payments = $payments?->orderBy('created_at')->get();
            $totalAmount = $payments?->sum('amount');

            $pdfFileName = 'user-payment-statement-' . time() . '.pdf';
            $pdfFilePath = 'public/pdf/' . $pdfFileName;

            $pdf = PDF::loadView('pdf.user-payment-statement', [
                'payments' => $payments,
                'totalAmount' => $totalAmount,
                'currentUser' => $currentUser,
                'start_date' => $request->input('payment_start_date'),
                'end_date' => $request->input('payment_end_date')
            ]);

            Storage::put($pdfFilePath, $pdf->output());

            $pdfUrl = Storage::url($pdfFilePath);

            return $this->success('Pdf url', url($pdfUrl));
        } catch (\Exception $e) {
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    public function userDonations($fetchArray = false): JsonResponse|array
    {
        try {
            $currentUser = Auth::user();
            $donationCollection = $currentUser->successfulDonations();

            $data['user-fullname'] = $currentUser->getFullNameAttribute();
            $data['total-zakat-amount'] = $currentUser->getTotalZakatAmount();
            $data['total-sadakah-amount'] = $currentUser->getTotalSadakahAmount();
            $data['total-waqf-amount'] = $currentUser->getTotalWaqfAmount();
            $data['total-donation-amount'] = $currentUser->getTotalDonationAmount() . "";
            $data['total-past-donation-count'] = $donationCollection ? $donationCollection->get()->count() : 0;

            if (is_null($donationCollection)){
                if($fetchArray){
                    $data['all-past-donation-count'] = [];
                    return $data;
                }
                return $this->success("No donation made by {$currentUser->email}");
            }

            foreach ($donationCollection->get() as $donation) {
                if(!is_null($donation))
                    $data['all-past-donations'] []=
                        $this->getDonationDetailsInfo($donation);
            }

            // Sort by latest 'donation-date'
            usort($data['all-past-donations'], function($a, $b) {
                return strtotime($b['donation-date']) - strtotime($a['donation-date']);
            });

            if($fetchArray) return $data;

            return $this->success("Donations made by {$currentUser->email}", $data);
        } catch (\Exception $e) {
            return $this->error('Error occurred: ' . $e->getMessage(), [], 401);
        }
    }

    public function pendingDonations($fetchArray = false): JsonResponse|array
    {
        try {
            $currentUser = Auth::user();
            $pendingDonations = $currentUser->getUpcomingDonations();

            $data['user-fullname'] = $currentUser->getFullNameAttribute();
            $data['total-pending-donation-count'] = $pendingDonations ? $pendingDonations->count() : 0;

            if (is_null($pendingDonations) || $pendingDonations->isEmpty())  {
                if($fetchArray){
                    $data['all-pending-donations'] = [];
                    return $data;
                }
                return $this->success("No pending donations for {$currentUser->email}");
            }

            foreach ($pendingDonations as $donationSubscription) {
                if(!is_null($donationSubscription))
                    $data['all-pending-donations'] []=
                        $this->getDonationDetailsInfo($donationSubscription);
            }
            if($fetchArray) return $data;

            return $this->success("Pending Donations for {$currentUser->email}", $data);
        } catch (\Exception $e) {
            return $this->error('Error occurred: ' . $e->getMessage(), [], 401);
        }
    }

    protected function getDonationDetailsInfo($donation): array
    {
        $donationData = [];

        if($donation instanceof Donation) {
            $donationData['donation-amount'] = $donation->amount;
            $donationData['donation-date'] = $donation->created_at;
            $donationData['donation-type'] = $donation->donation_type->getTitle();
            $donationData['donation-via'] = $donation->payment_via;

            if ($donation->isGeneralDonation()) {
                $donationData['program-title'] = "General Purpose";
                $donationData['campaign-title'] = "General Purpose";
                $donationData['campaign-slug'] = null;
            }

            else {
                $campaign = $donation->getCampaign();
                $donationData['program-title'] = $campaign->program->title;
                $donationData['campaign-title'] = $campaign->title;
                $donationData['campaign-slug'] = $campaign->slug;

                $donationData['campaign-allocated-amount'] = (int)$campaign->allocated_amount;
                $donationData['campaign-raised-amount'] = (int)$campaign->getFundCount();
                $donationData['campaign-raised-percent'] = (int)$campaign->getFundPercentage();

                $donationData['campaign-status'] = $campaign->campaign_status->getTitle();
                $donationData['campaign-urgent'] = (bool)$campaign->urgency_status;
                $donationData['campaign-visitable'] = $campaign->isAvailable();
                $donationData['campaign-banner'] = $campaign->getThumbnailImage();
            }
        }
        elseif ($donation instanceof CampaignSubscription)
        {
            $donationData['due-donation-amount'] = (string)abs($donation->due_amount);
            $donationData['subscribed-amount'] = $donation->subscribed_amount;
            $donationData['last-donation-date'] = $donation->last_donated;
            $donationData['next-donation-date'] = $donation->next_donation_date;

            $donationData['program-title'] = $donation->campaign->program->title;
            $donationData['campaign-title'] = $donation->campaign->title;
            $donationData['campaign-slug'] = $donation->campaign->slug;
            $donationData['campaign-banner'] = $donation->campaign->getThumbnailImage();

            $donationData['campaign-allocated-amount'] = (int)$donation->campaign->allocated_amount;
            $donationData['campaign-raised-amount'] = (int)$donation->campaign->getFundCount();
            $donationData['campaign-raised-percent'] = (int)$donation->campaign->getFundPercentage();
        }

        return $donationData;
    }

    public function allDonations(): JsonResponse
    {
        $responseArray = array_merge(
            $this->userDonations(true),
            $this->pendingDonations(true)
        );

        return $this->success('All donation data', $responseArray);
    }

    public function archivedZakatCalculations()
    {
        try {
            $user = Auth::user();

            if ($user && $user->active) {
                $calculationRecords = $user->zakatCalculations()
                                            ->where('archived', true)
                                            ->orderBy('date', 'desc')
                                            ->get(['id', 'payable_zakat', 'date']);
                $data = [
                    'total_calculation_records' => count($calculationRecords),
                    'calculation_records' => $calculationRecords
                ];
                return $this->success('Archived Zakat Calculations', $data);
            } else {
                return $this->error('You need to login first to proceed', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }
}
