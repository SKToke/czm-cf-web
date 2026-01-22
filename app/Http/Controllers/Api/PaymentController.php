<?php

namespace App\Http\Controllers\Api;

use App\Enums\DonationTypeEnum;
use App\Enums\DonorTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Campaign;
use App\Models\ContactUsQuery;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class PaymentController extends Controller
{
    use HttpResponses;

    const validation_rules = [
        'payment-amount' => 'required|numeric|min:10',
        'payment-type' => 'required|in:1,2,3',
        'payment-agree' => 'accepted',
        'donor-type' =>'required|in:1,2,3',
    ];
    const guest_validation_rules = [
        'payment-name' => 'required|string|max:255',
        'payment-email' => 'required|email|max:255',
        'payment-phone' => 'nullable|max:15',
    ];

    const validation_messages = [
        'payment-amount.required' => 'Amount is required',
        'payment-amount.numeric' => 'Amount must be a number',
        'payment-amount.min' => 'Amount must be at least :min (BDT)',
        'payment-name.required' => 'Name is required',
        'payment-name.max' => 'Name may not be greater than :max characters',
        'payment-email.required' => 'Email is required',
        'payment-email.email' => 'Please enter a valid email address',
        'payment-email.max' => 'The email may not be greater than :max characters',
        'payment-phone.max' => 'Phone number may not be greater than :max characters',
        'payment-type.required' => 'The donation type is required',
        'payment-type.in' => 'Invalid donation type',
        'payment-agree.accepted' => 'You must agree to the terms and conditions',
        'donor-type.required' =>'The donor type is required',
        'donor-type.in' =>'Invalid donor type',
    ];

    public function payViaAjax(Request $request)
    {
        $phone = "01";
        $campaignId = $request->get('campaign-id');
        $userId = null;
        $reqUser = null;
        $rules = self::validation_rules;
        if($request->get('donor-type') === Donor::DONOR_REQUEST_TYPE['guest']){
            $rules = array_merge($rules, self::guest_validation_rules);
        }

        $validator = Validator::make(
            $request->all(),
            $rules,
            self::validation_messages
        );
        $paymentFlag = false;
        $pending_donation=null;

        if($validator->fails()){
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error)
                $errorMessages []= $error;

            return $this->error(
                'Submission failed',
                $errorMessages,
                401
            );
        }
        else{
            if($campaignId && !Campaign::isDonatable($campaignId)){
                return $this->error(
                    'Cannot donate to campaign. Kindly check the campaign validity and try again.',
                    [],
                    401
                );
            }
            $paymentRequest = Arr::except($validator->getData(), ['_token']);

            if($paymentRequest['donor-type'] === Donor::DONOR_REQUEST_TYPE['guest']){

                if(User::where('email', $paymentRequest['payment-email'])->exists()){
                    return $this->error(
                        'Email already exists as a registered user. Kindly login to proceed.',
                        [],
                        401
                    );
                }

                /** Currently, if guest uses an email that is previously recorded as an
                 * unregistered donor, name & phone will be over-written in database.
                 */
                $donor = Donor::firstOrNew([
                    'email' => $paymentRequest['payment-email'],
                ]);
                $donor->name = $paymentRequest['payment-name'] ;
                $donor->phone = $paymentRequest['payment-phone'];
                $donor->donor_type = DonorTypeEnum::UNREGISTERED;
                $donor->save();

                $donation = Donation::create([
                    'amount' => $paymentRequest['payment-amount'],
                    'donor_id' => $donor->id,
                    'transaction_id' => Uuid::uuid4()->toString(),
                    'transaction_status' => TransactionTypeEnum::Pending,
                    'donation_type' => DonationTypeEnum::from($paymentRequest['payment-type'])->value
                ]);

                $donation->save();

                if($campaignId) $donation->setCampaign($campaignId);

                $pending_donation=$donation;

                if($campaignId) $donation->setCampaign($campaignId);

                $paymentFlag = true;
            }

            else if($paymentRequest['donor-type'] === Donor::DONOR_REQUEST_TYPE['self']){
                // Logged-in user option for donation

                $userId = $request->get('user-id');
                if ($userId) {
                    $reqUser = User::find($userId);
                }
                $donor = $reqUser->findOrCreateDonor();
                $donation = Donation::create([
                    'amount' => $paymentRequest['payment-amount'],
                    'donor_id' => $donor->id,
                    'transaction_id' => Uuid::uuid4()->toString(),
                    'transaction_status' => TransactionTypeEnum::Pending,
                    'donation_type' => DonationTypeEnum::from($paymentRequest['payment-type'])->value,
                ]);
                $donation->save();
                if ($reqUser->mobile_no) {
                    $phone = $reqUser->mobile_no;
                }

                if($campaignId) $donation->setCampaign($campaignId);

                $pending_donation=$donation;
                if($campaignId) $donation->setCampaign($campaignId);

                $paymentFlag = true;
            }

            else if($paymentRequest['donor-type'] === Donor::DONOR_REQUEST_TYPE['anonymous']){
                // Anonymous option for donation
                $donation = Donation::create([
                    'amount' => $paymentRequest['payment-amount'],
                    'donor_id' => null,
                    'transaction_id' => Uuid::uuid4()->toString(),
                    'transaction_status' => TransactionTypeEnum::Pending,
                    'donation_type' => DonationTypeEnum::from($paymentRequest['payment-type'])->value
                ]);
                $donation->save();
                $pending_donation=$donation;
                if($campaignId) $donation->setCampaign($campaignId);

                $paymentFlag = true;
            }

            else{
                return $this->error(
                    'Encountered an error. Payment did not initiate',
                    [],
                    401
                );
            }
        }

        $post_data = array();
        $post_data['total_amount'] = $request->input('payment-amount'); # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = $pending_donation->transaction_id; // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $request->input('payment-name') ? $request->input('payment-name') : "customer name";
        $post_data['cus_email'] = $request->input('payment-email') ? $request->input('payment-email') : "customer email";
        $post_data['cus_add1'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_country'] = "Bangladesh";

        if ($request->input('payment-phone')) {
            $post_data['cus_phone'] = $request->input('payment-phone');
        } else {
            $post_data['cus_phone'] = $phone;
        }

        # SHIPMENT INFORMATION

        $post_data['shipping_method'] = "NO";

        # Product INFORMATION
        $post_data['product_name'] = "Zakat or Sadaqah";
        $post_data['product_category'] = "Donation";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['donor-type'] = $request->input('donor-type');
        $post_data['payment-type'] = $request->input('payment-type');
        $post_data['payment-agree'] = $request->input('payment-agree');
        $post_data['campaign-id'] = $campaignId;

        # Before  going to initiate the payment order status need to update as Pending.

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            $payment_options = json_decode($payment_options, true); // Decode to associative array

            if ($payment_options && $payment_options['status'] == 'success') {
                return $this->success('Proceed to the payment gateway',$payment_options['data']);
            } else {

                return $this->error('Unable to proceed', [], 401);
            }
        } else {
            return $this->error('Unexpected response format', [], 401);
        }
    }

}
