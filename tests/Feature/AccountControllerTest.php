<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function user(){
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs(User::find($user->id));

        return $user;
    }

    public function test_me()
    {
        $this->user();

        $this->getJson('/api/me')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'user',
        ]);
    }

    public function test_deleteAccount()
    {
        $this->user();

        $payload = [
            'password' => 'password123',
        ];

        $this->deleteJson('/api/delete-account', $payload)
        ->assertStatus(200)
        ->assertJson([ 'success' => true ])
        ->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    public function test_deleteAccount_whit_another_password()
    {
        $this->user();

        $payload = [
            'password' => 'anotherPassord',
        ];

        $this->deleteJson('/api/delete-account', $payload)
        ->assertStatus(422)
        ->assertJson([ 'success' => false ])
        ->assertJsonStructure([
            'success',
            'message',
        ]);
    }
}
