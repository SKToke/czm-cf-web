<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashRecurringService
{
    protected $baseUrl;
    protected $apiKey;
    protected $serviceId;

    public function __construct()
    {
        $mode = config('bkash.mode', 'sandbox');
        $this->baseUrl = rtrim(config("bkash.recurring.{$mode}.base_url"), '/') . '/';
        $this->apiKey = config("bkash.recurring.{$mode}.api_key");
        $this->serviceId = config("bkash.recurring.{$mode}.service_id");
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
            'channelId' => '2', // 1: Customer APP, 2: Merchant WEB
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
        string $payerWallet,
        string $redirectUrl
    ): array {
        $url = $this->baseUrl . 'api/subscription';

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
            'subscriptionRequestId' => $subscriptionRequestId,
            'serviceId' => (int)$this->serviceId,
            'subscriptionReference' => $subscriptionReference,
            'paymentType' => 'FIXED',
            'subscriptionType' => 'WITH_PAYMENT', // charge immediately
            'amount' => $amount,
            'firstPaymentAmount' => $amount,
            'maxCapRequired' => false,
            'frequency' => $mappedFrequency,
            'startDate' => $startDateStr,
            'expiryDate' => $expiryDateStr,
            'payerType' => 'CUSTOMER',
            'payer' => $payerWallet,
            'currency' => 'BDT',
            'firstPaymentIncludedInCycle' => true,
            'redirectUrl' => $redirectUrl,
        ];

        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash')->info('Bkash Recurring Create Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Query subscription details by subscription ID
     */
    public function querySubscription(int $subscriptionId): array
    {
        $url = $this->baseUrl . "api/subscriptions/{$subscriptionId}";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->get($url);

        Log::channel('bkash')->info('Bkash Recurring Query Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Query subscription details by request ID (fallback query)
     */
    public function querySubscriptionByRequestId(string $requestId): array
    {
        $url = $this->baseUrl . "api/subscriptions/request-id/{$requestId}";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->get($url);

        Log::channel('bkash')->info('Bkash Recurring Query Subscription By RequestId API', [
            'url' => $url,
            'headers' => $headers,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Cancel subscription using subscription ID
     */
    public function cancelSubscription(int $subscriptionId, string $reason = 'Customer Requested'): array
    {
        $url = $this->baseUrl . "api/subscriptions/{$subscriptionId}";
        $headers = $this->getHeaders();

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->delete($url, [
                'reason' => $reason
            ]);

        Log::channel('bkash')->info('Bkash Recurring Cancel Subscription API', [
            'url' => $url,
            'headers' => $headers,
            'reason' => $reason,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Refund a recurring payment
     */
    public function refundSubscription(int $paymentId, float $amount): array
    {
        $url = $this->baseUrl . 'api/subscription/payment/refund';
        $headers = $this->getHeaders();

        $body = [
            'paymentId' => $paymentId,
            'amount' => $amount,
        ];

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($url, $body);

        Log::channel('bkash')->info('Bkash Recurring Refund API', [
            'url' => $url,
            'headers' => $headers,
            'request' => $body,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return $response->json();
    }
}
