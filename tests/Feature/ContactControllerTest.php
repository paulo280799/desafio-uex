<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Contact;
use App\Models\User;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function contact(){
        $contact = Contact::factory()->create();

        $this->actingAs(User::find($contact->user_id));

        return $contact;
    }

    public function test_list_all_contacts()
    {
        $this->contact();

        $response = $this->getJson('/api/contacts')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'contacts' => [
                'current_page',
                'data',
                'last_page',
                'per_page',
                'total'
            ]
        ]);

        $responseData = $response->json('contacts');
        $this->assertEquals(1, $responseData['current_page']);
        $this->assertNotEmpty($responseData['data']);
        $this->assertTrue($responseData['total'] >= 1);
    }

    public function test_store_a_new_contact()
    {
        $this->contact();

        $cpf = '80694657000';

        $payload = [
            "name" => "New Name 2",
            "cpf" => $cpf,
            "phone" => "(88) 99853-4446",
            "number" => 88,
            "address" => "Av. Francisco Caetano Dantas",
            "cep" => "63430000",
            "district" => "Cidade Nova",
            "city" => "Icó",
            "state" => "CE",
            "country" => "Brasil",
            "complement" => "Suite 202"
        ];

        $response = $this->postJson('/api/contacts', $payload)
        ->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'contact'
        ])
        ->assertJson([ 'success' => true ])
        ->assertJson([ 'message' => 'Contact created successfully.' ])
        ->assertJson(['contact' => ['cpf' => $cpf]]);
    }

    public function test_store_a_new_contact_when_invalid_cpf()
    {
        $this->contact();

        $payload = [
            "name" => "New Name 2",
            "cpf" => "12398736774",
            "phone" => "(88) 99853-4446",
            "number" => 88,
            "address" => "Av. Francisco Caetano Dantas",
            "cep" => "63430000",
            "district" => "Cidade Nova",
            "city" => "Icó",
            "state" => "CE",
            "country" => "Brasil",
            "complement" => "Suite 202"
        ];

        $this->postJson('/api/contacts', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The CPF is invalid.' ]);
    }

    public function test_contact_cpf_must_be_unique_per_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $cpf = '41836329008';

        $payload = [
            "name" => "New Name 2",
            "cpf" => $cpf,
            "phone" => "(88) 99853-4446",
            "number" => 88,
            "address" => "Av. Francisco Caetano Dantas",
            "cep" => "63430000",
            "district" => "Cidade Nova",
            "city" => "Icó",
            "state" => "CE",
            "country" => "Brasil",
            "complement" => "Suite 202"
        ];

        Contact::factory()->create(['user_id' => $user1->id, 'cpf' => $cpf]);

        $this->actingAs($user1)
            ->postJson('/api/contacts', $payload)
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
            ])
            ->assertJson([ 'success' => false ])
            ->assertJson([ 'message' => 'The cpf has already been taken.' ]);

        $this->actingAs($user2)
            ->postJson('/api/contacts', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'contact'
            ])
            ->assertJson([ 'success' => true ])
            ->assertJson([ 'message' => 'Contact created successfully.' ]);

        $this->assertDatabaseHas('contacts', ['cpf' => $cpf]);
    }

    public function test_show_a_contact()
    {
        $contact = $this->contact();

        $this->getJson("/api/contacts/{$contact->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'contact'
        ])->assertJson(['contact' => ['id' => $contact->id]]);
    }

    public function test_update_a_contact()
    {
        $contact = $this->contact();

        $cpf = '80694657000';

        $payload = [
            "name" => "New Name updated",
            "cpf" => $cpf,
            "phone" => "(88) 99853-4446",
            "number" => 88,
            "address" => "Av. Francisco Caetano Dantas",
            "cep" => "63430000",
            "district" => "Cidade Nova",
            "city" => "Icó",
            "state" => "CE",
            "country" => "Brasil",
            "complement" => "Suite 202"
        ];

        $this->putJson("/api/contacts/{$contact->id}", $payload)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'contact'
        ])
        ->assertJson([ 'success' => true ])
        ->assertJson([ 'message' => 'Contact updated successfully.' ])
        ->assertJson(['contact' => ['cpf' => $cpf]]);
    }

    public function test_delete_a_contact()
    {
        $contact = $this->contact();

        $this->deleteJson("/api/contacts/{$contact->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message'
        ])
        ->assertJson([ 'success' => true ])
        ->assertJson([ 'message' => 'Contact deleted successfully.' ]);
    }

    public function test_delete_a_contact_when_not_found()
    {
        $contact = $this->contact();

        $this->deleteJson("/api/contacts/1231")
        ->assertStatus(500)
        ->assertJsonStructure([
            'success',
            'message'
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'Failed to delete contact.' ]);
    }


    public function test_validates_the_store_payload()
    {
        $this->contact();

        $payload = [
            'name' => '',
            'cpf' => '123',
        ];

        $this->postJson('/api/contacts', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message'
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The name field is required.' ]);
    }

    public function test_validates_the_update_payload()
    {
        $contact = $this->contact();

        $payload = [
            'name' => 'New Name Teste',
            'cpf' => '',
        ];

        $this->putJson("/api/contacts/{$contact->id}", $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message'
        ])
        ->assertJson([ 'success' => false ])
        ->assertJson([ 'message' => 'The cpf field is required.' ]);
    }
}
