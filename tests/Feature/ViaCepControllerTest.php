<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ViaCepControllerTest extends TestCase
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

    public function test_get_addrees_when_not_cep(): void
    {
        $this->user();

        $this->get('/api/addresses')->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The cep field is required.' ]);
    }

    public function test_get_addrees(): void
    {
        $this->user();

        $cep = '01414000';

        $this->getJson('/api/addresses?cep=' . $cep)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'cep',
                'logradouro',
                'complemento',
                'unidade',
                'bairro',
                'localidade',
                'uf',
                'estado',
                'regiao',
                'ibge',
                'gia',
                'ddd',
                'siafi'
            ]
        ])
        ->assertJson([ 'success' => true ]);

    }
}
