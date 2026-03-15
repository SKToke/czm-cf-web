<?php

namespace App\Http\Controllers;

use App\Models\BkashAgreement;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    public function createAgreement(BkashService $bkash)
    {
        $payerReference = auth()->id(); // user id
        $payerReference = "USER001";

        $data = $bkash->createAgreement($payerReference);

        if (!isset($data['bkashURL'])) {
            return $data;
        }

        return redirect()->away($data['bkashURL']);
    }

    public function pay(BkashService $bkash)
    {
        $agreement = BkashAgreement::first();
        $invoice = 'Inv_' . Str::uuid() . '_' . now()->format('YmdHisv');

        $data = $bkash->createPaymentWithAgreement(
            $agreement->agreement_id,
            $agreement->payer_reference,
            10,
            $invoice
        );

        if (!isset($data['bkashURL'])) {
            return $data;
        }

        return redirect()->away($data['bkashURL']);
    }

    public function callback(Request $request, BkashService $bkash)
    {
        if ($request->status !== 'success') {
            return redirect('/agreement/fail');
        }

        $agreementId = $request->agreementId;

        $result = $bkash->executeAgreement($agreementId);

        $body = $result['body'] ?? $result;

        if (($body['agreementStatus'] ?? '') !== 'Completed') {
            return redirect('/agreement/fail');
        }

        // save DB
        BkashAgreement::updateOrCreate(
            ['agreement_id' => $body['agreementId']],
            [
                'payer_reference' => $body['payerReference'] ?? null,
                'wallet' => $body['payerAccount'] ?? null,
                'status' => $body['agreementStatus'],
                'user_id' => auth()->id() ?? 1,
            ]
        );

        return redirect('/agreement/success');
    }

    public function paymentCallback(
        Request      $request,
        BkashService $bkash
    )
    {

        if ($request->status !== 'success') {
            return redirect('/payment/fail');
        }

        // Step 1 — execute
        $execute = $bkash->executePaymentWithAgreement(
            $request->paymentID,
            $request->agreementId
        );

        $final = $execute;

        // Step 2 — fallback query if needed
        if (($execute['transactionStatus'] ?? null) !== 'Completed') {

            $query = $bkash->queryPayment(
                $request->paymentID
            );

            if (($query['transactionStatus'] ?? null) !== 'Completed') {
                return redirect('/payment/fail');
            }

            $final = $query;
        }

        // Step 3 — save payment
        \App\Models\BkashPayment::create([
            'payment_id' => $final['paymentId'] ?? null,
            'trx_id' => $final['trxId'] ?? null,
            'agreement_id' => $final['agreementId'] ?? null,
            'invoice' => $final['merchantInvoiceNumber'] ?? null,
            'amount' => $final['amount'] ?? null,
            'status' => $final['transactionStatus'] ?? null,
        ]);

        return redirect('/payment/success');
    }

}
