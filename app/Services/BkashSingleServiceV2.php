<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class BkashSingleServiceV2
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

        $callback = env('NGROK_BASE_URL') . "bkash-single/callback";

        $url = $this->baseUrl . 'tokenized-checkout/agreement/create';

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "payerReference" => (string)$payerReference,
            "callbackURL" => $callback,
        ];
        $response = Http::timeout(30)->withHeaders($headers)->post($url, $body);

        //log
        Log::channel('bkash')->info('Create Agreement API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return $response->json();
    }

    public function getToken()
    {
        if (Cache::has('bkash_single_token')) {
            return Cache::get('bkash_single_token');
        }

        $url = $this->baseUrl . 'tokenized-checkout/auth/grant-token';

        $headers = [
            'username' => $this->username,
            'password' => $this->password,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $body = [
            "app_key" => $this->appKey,
            "app_secret" => $this->appSecret,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('Grant Token API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        $data = $response->json();

        if (!isset($data['id_token'])) {
            throw new \Exception('Bkash token error');
        }

        Cache::put('bkash_single_token', $data['id_token'], now()->addMinutes(55));

        return $data['id_token'];
    }

    /**
     * @throws \Exception
     */
    public function queryPayment($paymentId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/query/payment';

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'username' => $this->username,
            'password' => $this->password,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "paymentId" => $paymentId,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('Query Payment API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return $response->json();
    }

    public function executePaymentWithAgreement($paymentId, $agreementId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/payment-with-agreement/execute';

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'username' => $this->username,
            'password' => $this->password,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "paymentId" => $paymentId,
            "agreementId" => $agreementId,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('executePaymentWithAgreement API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
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

        $callback = env('NGROK_BASE_URL') . "bkash-single/payment/callback";

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'username' => $this->username,
            'password' => $this->password,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "agreementId" => $agreementId,
            "payerReference" => (string)$payerReference,
            "callbackURL" => $callback,

            "amount" => (string)$amount,
            "currency" => "BDT",
            "intent" => "sale",

            "merchantInvoiceNumber" => $invoice,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('createPaymentWithAgreement', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return $response->json();
    }

    /**
     * @throws \Exception
     */
    public function executeAgreement($agreementId): array
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/agreement/execute';

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'username' => $this->username,
            'password' => $this->password,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "agreementId" => $agreementId,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('Execute Agreement API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
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

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $body = [
            "paymentId" => $paymentId,
            "trxId" => $trxId,
            "amount" => (string)$amount,
            "reason" => $reason,
            "sku" => $sku,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('Refund API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
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

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'username' => $this->username,
            'password' => $this->password,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "paymentId" => $paymentId,
            "trxId" => $trxId,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('RefundStatus API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return $response->json();
    }

    public function searchTransaction($trxId)
    {
        $token = trim($this->getToken());

        $url = $this->baseUrl . 'tokenized-checkout/search/transaction';

        $headers = [
            'Authorization' => $token,
            'X-App-Key' => $this->appKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = [
            "trxId" => $trxId
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->asJson()
            ->post($url, $body);

        //log
        Log::channel('bkash')->info('Search Transaction API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return $response->json();
    }
}
