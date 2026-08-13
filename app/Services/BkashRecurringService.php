<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashRecurringService
{
    protected $baseUrl;
    protected $apiKey;
    protected $serviceId;
    protected $merchantShortCode;

    public function __construct()
    {
        $mode = config('bkash.mode', 'sandbox');
        $this->baseUrl = rtrim(config("bkash.recurring.{$mode}.base_url"), '/') . '/';
        $this->apiKey = config("bkash.recurring.{$mode}.api_key");
        $this->serviceId = config("bkash.recurring.{$mode}.service_id");
        $this->merchantShortCode = config("bkash.recurring.{$mode}.merchant_short_code");
    }

    /**
     * Get headers required for bKash recurring payment APIs
     */
    protected function getHeaders(): array
    {
        // timeStamp format must be ISO-8601 UTC with milliseconds. e.g. "2021-02-08T09:16:28.603Z"
        $timestamp = now()->setTimezone('UTC')->format('Y-m-d\TH:i:s.v\Z');

        return [
            'version' => 'v1.0',
            'channelId' => 2, // 1: Customer APP, 2: Merchant WEB
            'timeStamp' => $timestamp,
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create subscription request
     */
    public function createSubscription(
        string $subscriptionRequestId,
        string $subscriptionReference,
        float $amount,
        string $frequency,
        ?string $payerWallet,
        string $redirectUrl
    ): array {
        $url = $this->baseUrl . 'gateway/api/subscription';

        // Timezone validation for start date (Note-2: start date cannot be today after 11:30 PM)
        $now = now()->setTimezone('Asia/Dhaka');
        $startDate = $now->copy();
        if ($now->format('H:i') >= '23:30') {
            $startDate = $now->addDay();
        }

        $startDateStr = $startDate->format('Y-m-d');
        // Expiry date is maximum 2 years from start date
        $expiryDateStr = $startDate->copy()->addYears(2)->format('Y-m-d');

        // Map frequency
        $mappedFrequency = match (strtolower($frequency)) {
            'daily' => 'DAILY',
            'weekly' => 'WEEKLY',
            'monthly' => 'CALENDAR_MONTH',
            'yearly' => 'CALENDAR_YEAR',
            default => 'DAILY'
        };

        $body = [
            'amount' => $amount,
            'amountQueryUrl' => null,
            'firstPaymentAmount' => $amount,
            'firstPaymentIncludedInCycle' => true,
            'serviceId' => (int)$this->serviceId,
            'currency' => 'BDT',
            'startDate' => $startDateStr,
            'expiryDate' => $expiryDateStr,
            'frequency' => $mappedFrequency,
            'subscriptionType' => 'WITH_PAYMENT',
            'maxCapAmount' => null,
            'maxCapRequired' => false,
            'merchantShortCode' => $this->merchantShortCode,
            'payer' => null,
            'payerType' => 'CUSTOMER',
            'paymentType' => 'FIXED',
            'redirectUrl' => $redirectUrl,
            'subscriptionRequestId' => $subscriptionRequestId,
            'subscriptionReference' => $subscriptionReference,
            'extraParams' => null,
        ];

        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash-recurring')->info('Bkash Recurring Create Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Query subscription details by subscription ID
     */
    public function querySubscription(int $subscriptionId): array
    {
        $url = $this->baseUrl . "gateway/api/subscriptions/{$subscriptionId}";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->get($url);

        Log::channel('bkash-recurring')->info('Bkash Recurring Query Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Query subscription details by request ID (fallback query)
     */
    public function querySubscriptionByRequestId(string $requestId): array
    {
        $url = $this->baseUrl . "gateway/api/subscriptions/request-id/{$requestId}";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->get($url);

        Log::channel('bkash-recurring')->info('Bkash Recurring Query Subscription By RequestId API', [
            'url' => $url,
            'headers' => $headers,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Cancel subscription using subscription ID
     */
    public function cancelSubscription(int $subscriptionId, string $reason = 'Customer Requested'): array
    {
        $url = $this->baseUrl . "gateway/api/subscriptions/{$subscriptionId}?reason=" . urlencode($reason);
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->delete($url);

        Log::channel('bkash-recurring')->info('Bkash Recurring Cancel Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'reason' => $reason,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Refund a recurring payment
     */
    public function refundSubscription(int $paymentId, float $amount): array
    {
        $url = $this->baseUrl . 'gateway/api/subscription/payment/refund';
        $headers = $this->getHeaders();
        $headers['requestId'] = 'REF' . time() . rand(1000, 9999);

        $body = [
            'paymentId' => $paymentId,
            'amount' => $amount,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash-recurring')->info('Bkash Recurring Refund API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Execute a scheduled or manual recurring payment for a subscription
     */
    public function executePayment(int $subscriptionId, float $amount): array
    {
        $url = $this->baseUrl . 'gateway/api/subscription/payment/execute';
        $headers = $this->getHeaders();

        $body = [
            'subscriptionId' => $subscriptionId,
            'amount' => $amount,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash-recurring')->info('Bkash Recurring Execute Payment API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Query payment details using payment_id
     */
    public function queryPaymentByPaymentId(int $paymentId): array
    {
        $url = $this->baseUrl . "gateway/api/subscription/payment/{$paymentId}";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->get($url);

        Log::channel('bkash-recurring')->info('Bkash Recurring Query Payment By PaymentId API', [
            'url' => $url,
            'headers' => $headers,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Query payments under a recurring subscription by subscription_id
     */
    public function queryPaymentsBySubscriptionId(int $subscriptionId): array
    {
        $url = $this->baseUrl . "gateway/api/subscriptions/{$subscriptionId}/payments";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->get($url);

        Log::channel('bkash-recurring')->info('Bkash Recurring Query Payments By SubscriptionId API', [
            'url' => $url,
            'headers' => $headers,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Extend subscription expiry date using subscription_id
     */
    public function extendSubscription(int $subscriptionId, string $newExpiryDate): array
    {
        $url = $this->baseUrl . "gateway/api/subscriptions/{$subscriptionId}/extend";
        $headers = $this->getHeaders();

        $body = [
            'expiryDate' => $newExpiryDate,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash-recurring')->info('Bkash Recurring Extend Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }

    /**
     * Get billing schedule given start date, expiry date, and frequency
     */
    public function getSchedule(string $startDate, string $expiryDate, string $frequency): array
    {
        $url = $this->baseUrl . 'gateway/api/subscription/schedule';
        $headers = $this->getHeaders();

        $mappedFrequency = match (strtolower($frequency)) {
            'daily' => 'DAILY',
            'weekly' => 'WEEKLY',
            'monthly', 'calendar_month' => 'CALENDAR_MONTH',
            'yearly', 'calendar_year' => 'CALENDAR_YEAR',
            default => strtoupper($frequency)
        };

        $body = [
            'startDate' => $startDate,
            'expiryDate' => $expiryDate,
            'frequency' => $mappedFrequency,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash-recurring')->info('Bkash Recurring Get Schedule API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'rawBody' => $response->body(),
            'response' => $response->json(),
        ]);

        return $response->json() ?? [];
    }
}

