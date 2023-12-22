<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CandidatureTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_cannot_see_candidatures()
    {
        $response = $this->get('api/candidatures');
        $response->assertStatus(200)->assertJson([
            'message' => 'Liste des candidatures',
            'candidature' => []
        ]);
    }

    public function test_can_see_candidatures()
    {
        $user = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid3@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        $this->actingAs($user, 'api');

        $response = $this->get('/api/candidatures');

        $response->assertStatus(200)->assertJson([
            'message' => 'Liste des Candidatures',
            'Candidatures' => []
        ]);
    }

    public function test_register_candidature()
    {
        $user = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid4@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        $this->actingAs($user, 'api');

        $response = $this->post('/api/formations/candidatures/1');
        $response->assertStatus(200)->assertJson([
            'message' => 'Candidature ajoutée',
            'candidature' => []
        ]);
    }

    public function test_accept_candidature()
    {
        $user = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid5@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        $this->actingAs($user, 'api');

        $response = $this->put('/api/candidatures/accepter/1');

        $response->assertStatus(200)->assertJson([
            'message' => 'Candidature acceptée'
        ]);
    }

    public function test_data_list_candidature()
    {
        $user = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid6@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);


        $this->actingAs($user, 'api');

        $response = $this->get('/api/candidatures');

        $response->assertStatus(200)->assertJson([
            'message' => 'Liste des Candidatures',
            'Candidatures' => []
        ]);
    }
}
