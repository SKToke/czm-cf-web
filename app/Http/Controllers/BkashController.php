<?php

namespace App\Http\Controllers;

use App\Models\BkashAgreement;
use App\Models\BkashPayment;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    public function checkout(BkashService $bkash)
    {
        $agreement = BkashAgreement::query()
            ->where('user_id', auth()->id())
            ->where('status', 'Completed')
            ->latest()
            ->first();

        if (!$agreement) {
            return $this->createAgreement($bkash);
        }

        return $this->pay($bkash);
    }

    public function createAgreement(BkashService $bkash)
    {
        $payerReference = auth()->id(); // user id

        $data = $bkash->createAgreement($payerReference);

        if (!isset($data['bkashURL'])) {
            return $data;
        }

        return redirect()->away($data['bkashURL']);
    }

    public function pay(BkashService $bkash)
    {
        $agreement = BkashAgreement::where('user_id', auth()->id())->where('status', 'Completed')->latest()->first();

        if (!$agreement) {
            return redirect('/bkash/agreement');
        }

        $invoice = 'Inv_' . Str::uuid() . '_' . now()->format('YmdHisv');

        $data = $bkash->createPaymentWithAgreement(
            $agreement->agreement_id,
            $agreement->payer_reference,
            session('subscription_amount'),
            $invoice
        );

        if (!isset($data['bkashURL'])) {
            return $data;
        }

        return redirect()->away($data['bkashURL']);
    }

    /**
     * @throws \Exception
     */
    public function callback(Request $request, BkashService $bkash)
    {
        if ($request->status === 'cancel') {
            return redirect('/bkash/payment/cancel')->with('message', 'Payment Cancelled');
        }

        if ($request->status === 'failure') {
            \Log::channel('bkash')->error('callback - Payment Failed', [
                'status' => $request->status,
                'errorCode' => $request->errorCode ?? null,
                'request' => $request->all(),
            ]);

            $errorCode = $request->errorCode
                ?? $request->statusCode
                ?? $request->externalCode
                ?? null;
            $message = bkashErrorMessage($errorCode);
            return redirect('/bkash/payment/fail')->with('message', $message);
        }

        if ($request->status !== 'success') {
            return redirect('/bkash/agreement/fail');
        }

        $agreementId = $request->agreementId;

        $result = $bkash->executeAgreement($agreementId);
        $body = $result['body'] ?? $result;

        if (($body['agreementStatus'] ?? '') !== 'Completed') {
            return redirect('/bkash/agreement/fail');
        }

        BkashAgreement::updateOrCreate(
            ['agreement_id' => $body['agreementId']],
            [
                'payer_reference' => $body['payerReference'] ?? null,
                'wallet' => $body['payerAccount'] ?? null,
                'status' => $body['agreementStatus'],
                'user_id' => auth()->id() ?? 1,
            ]
        );

        return redirect('/checkout/bkash');
    }

    /**
     * @throws \Exception
     */
    public function paymentCallback(Request $request, BkashService $bkash)
    {
        if ($request->status === 'cancel') {
            return redirect('/bkash/payment/cancel')->with('message', 'Payment Cancelled');
        }

        if ($request->status === 'failure') {
            \Log::channel('bkash')->error('paymentCallback - Payment Failed', [
                'status' => $request->status,
                'errorCode' => $request->errorCode ?? null,
                'request' => $request->all(),
            ]);
            $errorCode = $request->errorCode
                ?? $request->statusCode
                ?? $request->externalCode
                ?? null;
            $message = bkashErrorMessage($errorCode);
            return redirect('/bkash/payment/fail')->with('message', $message);
        }

        if ($request->status !== 'success') {
            return redirect('/bkash/payment/fail');
        }

        $existingPayment = BkashPayment::where('payment_id', $request->paymentID)->first();
        if ($existingPayment) {
            return redirect('/bkash/payment/fail')->with('message', 'Duplicate transaction detected');
        }

        $execute = $bkash->executePaymentWithAgreement(
            $request->paymentID,
            $request->agreementId
        );

        $final = $execute;

        // fallback query
        if (($execute['transactionStatus'] ?? null) !== 'Completed') {
            $errorCode = $execute['externalCode'] ?? null;
            // handle duplicate
            if ($errorCode === '2029') {
                return redirect('/bkash/payment/fail')->with('message', bkashErrorMessage('2029'));
            }
            $query = $bkash->queryPayment($request->paymentID);
            if (($query['transactionStatus'] ?? null) !== 'Completed') {
                $errorCode = $query['externalCode'] ?? $errorCode;
                return redirect('/bkash/payment/fail')->with('message', bkashErrorMessage($errorCode));
            }
            $final = $query;
        }

        BkashPayment::create([
            'payment_id' => $final['paymentId'] ?? null,
            'trx_id' => $final['trxId'] ?? null,
            'agreement_id' => $final['agreementId'] ?? null,
            'invoice' => $final['merchantInvoiceNumber'] ?? null,
            'amount' => $final['amount'] ?? null,
            'status' => $final['transactionStatus'] ?? null,
        ]);

        return redirect('/bkash/payment/success');
    }
}
