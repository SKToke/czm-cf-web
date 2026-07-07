<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    public function test_registration_validation_blocks_honeypot_spam(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'mobile_no' => '01712345678',
            'website' => 'http://spammer.com', // Honeypot filled
            'captcha_answer' => '8',
            'captcha_hash' => encrypt(8)
        ]);

        $response->assertSessionHasErrors(['website']);
    }

    public function test_registration_validation_blocks_invalid_mobile(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'mobile_no' => 'hzklkrwmve', // Invalid mobile
            'captcha_answer' => '8',
            'captcha_hash' => encrypt(8)
        ]);

        $response->assertSessionHasErrors(['mobile_no']);
    }

    public function test_registration_validation_blocks_invalid_captcha_answer(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'mobile_no' => '01712345678',
            'captcha_answer' => '9', // Wrong answer
            'captcha_hash' => encrypt(8)
        ]);

        $response->assertSessionHasErrors(['captcha_answer']);
    }

    public function test_registration_validation_passes_with_valid_data(): void
    {
        $email = 'john.doe.' . uniqid() . '@example.com';
        $answer = 8;
        $hash = encrypt($answer);

        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $email,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'mobile_no' => '01712345678',
            'captcha_answer' => strval($answer),
            'captcha_hash' => $hash
        ]);

        // It should redirect to verification notice upon successful registration
        $response->assertRedirect(route('verification.notice'));

        // Clean up created user to keep DB clean (since we aren't using RefreshDatabase)
        User::where('email', $email)->delete();
    }
}
