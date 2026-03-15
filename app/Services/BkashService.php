<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BkashService
{
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $appKey;
    protected $appSecret;

    public function __construct()
    {
        $this->baseUrl = config('bkash.base_url');
        $this->username = config('bkash.username');
        $this->password = config('bkash.password');
        $this->appKey = config('bkash.app_key');
        $this->appSecret = config('bkash.app_secret');
    }

    public function createAgreement($payerReference)
    {
        $token = $this->getToken();

        $callback = "https://b7e3-202-4-115-14.ngrok-free.app/bkash/callback";

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $this->baseUrl . 'tokenized-checkout/agreement/create',
                [
                    "payerReference" => (string)$payerReference,
                    "callbackURL" => $callback,
                ]
            );

        return $response->json();
    }

    public function getToken()
    {
        // check cache first
        if (Cache::has('bkash_token')) {
            return Cache::get('bkash_token');
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'username' => $this->username,
                'password' => $this->password,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->baseUrl . 'tokenized-checkout/auth/grant-token', [
                "app_key" => $this->appKey,
                "app_secret" => $this->appSecret,
            ]);

        $data = $response->json();

        if (!isset($data['id_token'])) {
            throw new \Exception('Bkash token error');
        }

        // cache for 55 min
        Cache::put('bkash_token', $data['id_token'], now()->addMinutes(55));

        return $data['id_token'];
    }

    public function queryPayment($paymentId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/query/payment';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'username' => $this->username,
                'password' => $this->password,
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->post($url, [
                "paymentId" => $paymentId,
            ]);

        return $response->json();
    }

    public function executePaymentWithAgreement($paymentId, $agreementId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/payment-with-agreement/execute';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'username' => $this->username,
                'password' => $this->password,
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->post($url, [
                "paymentId" => $paymentId,
                "agreementId" => $agreementId,
            ]);

        return $response->json();
    }

    public function createPaymentWithAgreement(
        $agreementId,
        $payerReference,
        $amount,
        $invoice
    )
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/payment-with-agreement/create';

        $callback = "https://b7e3-202-4-115-14.ngrok-free.app/bkash/payment/callback";

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'username' => $this->username,
                'password' => $this->password,
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->post($url, [

                "agreementId" => $agreementId,
                "payerReference" => (string)$payerReference,
                "callbackURL" => $callback,

                "amount" => (string)$amount,
                "currency" => "BDT",
                "intent" => "sale",

                "merchantInvoiceNumber" => $invoice,
            ]);

        return $response->json();
    }

    public function executeAgreement($agreementId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/agreement/execute';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'username' => $this->username,
                'password' => $this->password,
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->post($url, [
                "agreementId" => $agreementId,
            ]);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }

    public function refund(
        $paymentId,
        $trxId,
        $amount,
        $reason = 'refund',
        $sku = 'sku1'
    )
    {

        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/refund';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->post($url, [
                "paymentId" => $paymentId,
                "trxId" => $trxId,
                "amount" => (string)$amount,
                "reason" => $reason,
                "sku" => $sku,
            ]);

        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }

    public function refundStatus($paymentId, $trxId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/refund/status';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'username' => $this->username,
                'password' => $this->password,
                'Accept' => 'application/json',
            ])
            ->asJson()
            ->post($url, [
                "paymentId" => $paymentId,
                "trxId" => $trxId,
            ]);

        return $response->json();
    }

    public function searchTransaction($trxId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/search/transaction';

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $token,
                'X-App-Key' => $this->appKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->asJson()
            ->post($url, [
                "trxId" => $trxId
            ]);

        return $response->json();
    }
}
