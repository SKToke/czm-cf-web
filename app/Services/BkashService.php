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
}
