<?php

namespace App\Services;

class SslEncryptionHelper
{
    public static function encrypt(string $plainText, string $key): string
    {
        $cipher = 'aes-256-cbc';

        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt(
            $plainText,
            $cipher,
            $key,
            0,
            $iv
        );

        return base64_encode($iv . '|||' . $encrypted);
    }

    public static function decrypt(string $cipherText, string $key): string|false
    {
        $decoded = base64_decode($cipherText);

        if ($decoded === false) {
            return false;
        }

        [$iv, $encrypted] = explode('|||', $decoded, 2);

        return openssl_decrypt(
            $encrypted,
            'aes-256-cbc',
            $key,
            0,
            $iv
        );
    }
}
