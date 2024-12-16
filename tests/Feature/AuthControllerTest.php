<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/auth/register', $payload)
        ->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => true ])
        ->assertJson([ 'message' => 'User registered successfully.' ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_when_password_diferent()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password111',
        ];

       $this->postJson('/api/auth/register', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The password field confirmation does not match.' ]);
    }

    public function test_register_when_email()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'testexample.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/auth/register', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The email field must be a valid email address.' ]);
    }

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $payload)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'token'
        ])
        ->assertJson([ 'success' => true ])
        ->assertJson([ 'message' => 'Login successful.' ]);

        $this->assertNotNull($response->json('token'));
    }

    public function test_login_when_error_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'test@example.com',
            'password' => 'password111',
        ];

        $this->postJson('/api/auth/login', $payload)
        ->assertStatus(401)
        ->assertJsonStructure([
            'success',
            'message',
        ])->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'Invalid credentials' ]);
    }

    public function test_forgot_password()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $payload = [
            'email' => 'test@example.com',
        ];

        $this->postJson('/api/auth/password/forgot', $payload)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => true ])
        ->assertJson([ 'message' => 'Password reset link sent to your email.']);

        Mail::assertSent(ForgotPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_reset_password_when_error_token()
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $payload = [
            'token' => '72df8a37203514b491f771195bbe8204dae58b0dd08e179bf1c2b59de513ae52',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $this->postJson('/api/auth/password/reset', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'error'
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'Failed to reset password.' ])
        ->assertJson([ 'error' => 'This password reset token is invalid.' ]);

    }

    public function test_reset_password_when_error_password_confirmation()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = Password::createToken($user);

        $payload = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'errorPassord',
        ];

        $this->postJson('/api/auth/password/reset', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The password field confirmation does not match.' ]);

    }

    public function test_reset_password_when_error_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = Password::createToken($user);

        $payload = [
            'token' => $token,
            'email' => 'test.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'errorPassord',
        ];

        $this->postJson('/api/auth/password/reset', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The email field must be a valid email address.' ]);

    }

    public function test_reset_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = Password::createToken($user);

        $payload = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $this->postJson('/api/auth/password/reset', $payload)
        ->assertStatus(200)
        ->assertJson([ 'success' => true ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
