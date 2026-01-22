<?php

namespace App\Http\Controllers;

use App\Enums\DonationTypeEnum;
use App\Enums\DonorTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Helpers\FlashHelper;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Campaign;
use App\Models\CampaignSubscription;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use App\Models\UserZakatCalculation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class PaymentController extends Controller
{
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

    public function index(Request $request): View
    {
        $selectedCampaignId = $request->query('campaign-id');
        $campaign = Campaign::findByCustomId($selectedCampaignId);
        $payableZakat = $request->query('payableZakat');

        return view('payment.index')->with(['campaign' => $campaign, 'payableZakat' => $payableZakat]);
    }

    public function payViaAjax(Request $request)
    {
        $phone = "01";
        $campaignId = $request->get('campaign-id');
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

            FlashHelper::trigger(
                implode('<br>', $errorMessages),
                'danger'
            );

            return redirect()->route('payment.index', [
                'confirmation' => $paymentFlag ? 'success' : null,
                'campaign-id' => $campaignId ?? null,
            ]);
        }
        else{
            if($campaignId && !Campaign::isDonatable($campaignId)){
                FlashHelper::trigger('Cannot donate to campaign. Kindly check the campaign validity and try again.', 'danger');
                return redirect()->route('payment.index');
            }
            $paymentRequest = Arr::except($validator->getData(), ['_token']);

            if($paymentRequest['donor-type'] === Donor::DONOR_REQUEST_TYPE['guest']){

                if(User::where('email', $paymentRequest['payment-email'])->exists()){
                    FlashHelper::trigger(
                        'Email already exists as a registered user. Kindly login to proceed.',
                        'danger'
                    );
                    return redirect()->route('payment.index');
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

                $donor = auth()->user()->findOrCreateDonor();
                $donation = Donation::create([
                    'amount' => $paymentRequest['payment-amount'],
                    'donor_id' => $donor->id,
                    'transaction_id' => Uuid::uuid4()->toString(),
                    'transaction_status' => TransactionTypeEnum::Pending,
                    'donation_type' => DonationTypeEnum::from($paymentRequest['payment-type'])->value,
                ]);
                $donation->save();
                if (auth()->user()->mobile_no) {
                    $phone = auth()->user()->mobile_no;
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
                FlashHelper::trigger('Encountered an error. Payment did not initiate', 'danger');
                return redirect()->route('payment.index', [
                    'campaign-id' => $campaignId ?? null,
                ]);
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
                return redirect()->to($payment_options['data']);
            } else {
                FlashHelper::trigger(
                    'Unable to proceed',
                    'danger'
                );
                return redirect()->route('payment.index', [
                    'campaign-id' => $campaignId ?? null,
                ]);
            }
        } else {
            FlashHelper::trigger(
                'Unexpected response format',
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $campaignId ?? null,
            ]);
        }

    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        // Retrieve the donation record using Eloquent
        $donation = Donation::where('transaction_id', $tran_id)->first();

        if (!$donation) {
            FlashHelper::trigger('No transaction found.', 'danger');
            return redirect()->route('payment.index', ['campaign-id' => null]);
        }

        if ($donation->transaction_status == TransactionTypeEnum::Pending->value) {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation) {
                // Update the donation record
                $donation->transaction_status = TransactionTypeEnum::Complete->value;
                $donation->payment_via = $request->card_type;
                $donation->data = json_encode($request->all());
                $donation->save();

                // Call the updateOrCreateReport method on the Donation model instance
                $donation->updateOrCreateReport();
                $donation->updateOrCreateTransactionReport();

                if ($donation->campaign_id && $donation->transaction_status==TransactionTypeEnum::Complete->value) {
                    $campaign = Campaign::find($donation->campaign_id);
                    $campaign->updateOrCreateReport();
                }

                if ($donation->campaign_id && $donation->donor_id) {
                    $this->updateCampaignSubscription($donation->id, $donation->campaign_id, $donation->donor_id);
                }

                if ($donation->donation_type == DonationTypeEnum::ZAKAT->value) {
                    $this->updateUserZakatCalculation($donation->id);
                }

                FlashHelper::trigger('Transaction is successfully completed', 'success');
                return redirect()->route('payment.index', [
                    'confirmation' => 'success',
                    'campaign-id' => $donation->campaign_id ?? null,
                ]);
            } else {
                FlashHelper::trigger('Transaction validation failed', 'danger');
                return redirect()->route('payment.index', ['campaign-id' => $donation->campaign_id ?? null]);
            }
        } else if ($donation->transaction_status == TransactionTypeEnum::Complete->value) {
            FlashHelper::trigger('Transaction is already completed', 'info');
            return redirect()->route('payment.index', [
                'confirmation' => 'success',
                'campaign-id' => $donation->campaign_id ?? null,
            ]);
        } else {
            FlashHelper::trigger('Invalid transaction', 'danger');
            return redirect()->route('payment.index', ['campaign-id' => $donation->campaign_id ?? null]);
        }
    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $donation_details = DB::table('donations')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'transaction_status', 'amount')->first();


        if ($donation_details->transaction_status == TransactionTypeEnum::Pending->value) {
            $update_product = DB::table('donations')
                ->where('transaction_id', $tran_id)
                ->update([
                    'transaction_status' => TransactionTypeEnum::Failed,
                    'payment_via' => request()->card_type,
                    'data' => json_encode(request()->all()),
                ]);

            $donation = Donation::where('transaction_id', $tran_id)->first();
            $donation->updateOrCreateTransactionReport();

            FlashHelper::trigger(
                $request->input('error'),
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $donation_details->campaign_id ?? null,
            ]);
        } else if ($donation_details->transaction_status == TransactionTypeEnum::Complete->value) {
            FlashHelper::trigger(
                'Transaction is already Successful',
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $donation_details->campaign_id ?? null,
            ]);
        } else {
            FlashHelper::trigger(
                'Invalid transcation',
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $donation_details->campaign_id ?? null,
            ]);
        }
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $donation_details = DB::table('donations')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'transaction_status', 'amount')->first();
        if ($donation_details->transaction_status == TransactionTypeEnum::Pending->value) {
            $update_product = DB::table('donations')
                ->where('transaction_id', $tran_id)
                ->update([
                    'transaction_status' => TransactionTypeEnum::Canceled,
                    'data' => json_encode(request()->all()),
                ]);
            $donation = Donation::where('transaction_id', $tran_id)->first();
            $donation->updateOrCreateTransactionReport();
            FlashHelper::trigger(
                'Transaction is Canceled',
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $donation_details->campaign_id ?? null,
            ]);
        } else if ($donation_details->transaction_status == TransactionTypeEnum::Complete->value) {
            FlashHelper::trigger(
                'Transaction is already Successful',
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $donation_details->campaign_id ?? null,
            ]);
        } else {
            FlashHelper::trigger(
                'Invalid transcation',
                'danger'
            );
            return redirect()->route('payment.index', [
                'campaign-id' => $donation_details->campaign_id ?? null,
            ]);
        }
    }

    public function ipn(Request $request)
    {
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {
            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $donation_details = DB::table('donations')
                ->where('transaction_id', $tran_id)
                ->select('transaction_id', 'transaction_status', 'amount')->first();

            if ($donation_details->transaction_status == TransactionTypeEnum::Pending->value) {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $donation_details->amount, $donation_details->currency);
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */

                    if($request->input('status')=='VALID'){
                        $update_product = DB::table('donations')
                            ->where('transaction_id', $tran_id)
                            ->update([
                                'transaction_status' => TransactionTypeEnum::Complete,
                                'payment_via' => request()->card_type,
                                'data' => json_encode(request()->all()),
                            ]);
                    }elseif ($request->input('status')=='FAILED'){
                        $update_product = DB::table('donations')
                            ->where('transaction_id', $tran_id)
                            ->update([
                                'transaction_status' => TransactionTypeEnum::Failed,
                                'payment_via' => request()->card_type,
                                'data' => json_encode(request()->all()),
                            ]);
                    }elseif ($request->input('status')=='CANCELLED'){
                        $update_product = DB::table('donations')
                            ->where('transaction_id', $tran_id)
                            ->update([
                                'transaction_status' => TransactionTypeEnum::Canceled,
                                'data' => json_encode(request()->all()),
                            ]);
                    }
                }
            }
        }
    }

    public function updateCampaignSubscription($donationId, $campaignId, $donorId)
    {
        $donation = Donation::find($donationId);
        $subscription = CampaignSubscription::where('campaign_id', $campaignId)
            ->where('donor_id', $donorId)
            ->where('active', true)
            ->first();
        if($subscription && $donation->transaction_status==TransactionTypeEnum::Complete->value){
            $subscription->due_amount = $subscription->due_amount + $donation->amount;
            $subscription->last_donated = $donation->created_at;
            $subscription->save();
        }
    }

    public function updateUserZakatCalculation($donationId)
    {
        $donation = Donation::find($donationId);
        if($donation && $donation->transaction_status==TransactionTypeEnum::Complete->value && $donation->donor_id) {
            $donor = Donor::find($donation->donor_id);
            if ($donor) {
                $userZakatCalculation = UserZakatCalculation::where('email', $donor->email)->orderBy('created_at', 'asc')->first();
                if ($userZakatCalculation && $userZakatCalculation->paid_to_czm == null) {
                    $userZakatCalculation->paid_to_czm = $donation->amount;
                    $userZakatCalculation->save();
                }
            }
        }

    }
}
