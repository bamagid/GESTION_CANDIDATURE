<?php

namespace Tests\Feature;

use Carbon\Factory;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidatTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test : L'utilisateur ne peut pas accéder à la liste des candidats sans s'authentifier.
     *
     * Description :
     * Ce test vérifie que l'accès à la liste des candidats via l'endpoint API est restreint aux utilisateurs
     * authentifiés. L'utilisateur ne doit pas pouvoir voir la liste des candidats s'il n'est pas connecté.
     *
     * Scénario :
     * 1. Effectuez une requête HTTP GET vers l'endpoint API '/api/user'.
     * 2. Vérifiez que la réponse a le code de statut HTTP 200.
     * 3. Vérifiez que la réponse JSON contient le message 'Liste des candidats'.
     * 4. Assurez-vous que l'accès est restreint et que l'utilisateur ne peut pas voir la liste sans authentification.
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être authentifié avant d'exécuter ce test.
     */

    public function test_cannot_see_Candidats()
    {
        $response = $this->get('api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Liste des candidats',
        ]);
    }

    /**
     * Test : L'administrateur peut accéder à la liste des candidats après s'être authentifié.
     *
     * Description :
     * Ce test vérifie que l'administrateur peut accéder à la liste des candidats via l'endpoint API
     * après s'être authentifié. La liste des candidats doit être renvoyée avec succès, et elle peut être
     * vide si aucun candidat n'est enregistré.
     *
     * Scénario :
     * 1. Créez un utilisateur administrateur.
     * 2. Simulez l'authentification de l'administrateur.
     * 3. Effectuez une requête HTTP GET vers l'endpoint API '/api/candidats'.
     * 4. Vérifiez que la réponse a le code de statut HTTP 200.
     * 5. Vérifiez que la réponse JSON contient le message 'La liste des candidats'.
     * 6. Vérifiez que la réponse JSON contient un tableau vide de candidats.
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être authentifié avant d'exécuter ce test.
     * - Vous devez avoir configuré un utilisateur administrateur avec les informations spécifiées.
     *
     */

    public function test_can_see_listeCandidat()
    {
        // Créer un utilisateur 
        $user = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        // Simuler Une authentification 
        $this->actingAs($user, 'api');

        // Effectuer la requête d' 
        $response = $this->get('/api/candidats');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'La liste des candidats',
                'Candidats' => [],
            ]);
    }

    /**
     * Test : Validation de l'unicité de l'email lors de l'inscription d'un candidat.
     *
     * Description :
     * Ce test vérifie que le processus d'inscription d'un candidat échoue correctement lorsque l'email
     * fourni lors de l'inscription est déjà associé à un autre utilisateur dans la base de données.
     * L'unicité de l'email est une règle de validation pour garantir que chaque utilisateur a une adresse
     * email unique.
     *
     * Scénario :
     * 1. Créez un candidat avec une adresse email spécifique.
     * 2. Tentez de créer un nouveau candidat avec la même adresse email.
     * 3. Effectuez une requête HTTP POST vers l'endpoint API '/api/inscription'.
     * 4. Vérifiez que la session contient des erreurs liées à l'adresse email.
     * 5. Vérifiez que la base de données ne contient qu'un seul utilisateur (pas de nouvel enregistrement).
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être enregistré avant d'exécuter ce test.
     */

    public function test_email_unique()
    {
        $candidat = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        $nouveauCandidat =  User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        $response = $this->post('/api/users');
        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::count());
    }

    /**
     * Test : Validation du type du nom lors de l'inscription d'un candidat.
     *
     * Description :
     * Ce test vérifie que le processus d'inscription d'un candidat échoue correctement lorsque le type du nom
     * fourni lors de l'inscription n'est pas une chaîne de caractères. La règle de validation spécifie que le nom
     * doit être une chaîne de caractères, et ce test s'assure que cette règle est respectée.
     *
     * Scénario :
     * 1. Tentez de créer un candidat avec un nom de type non valide (entier).
     * 2. Effectuez une requête HTTP POST vers l'endpoint API '/api/inscription'.
     * 3. Vérifiez que la réponse a le code de statut HTTP 302 (redirection).
     * 4. Vérifiez que la session contient des erreurs liées au champ 'nom'.
     * 5. Vérifiez que la base de données ne contient aucun utilisateur (pas d'enregistrement).
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être enregistré avant d'exécuter ce test.
     */

    public function test_nom_equal_string()
    {
        $Candidat =  User::create([
           'nom' => 127,
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid@gmail.com',
            'password' => Hash::make('Passe12@'),
        ]);

        $response = $this->post('/api/users');
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom']);

        $this->assertEquals(0, User::count());
    }


    /**
     * Test : Authentification d'un administrateur avec succès.
     *
     * Description :
     * Ce test vérifie que le processus d'authentification d'un administrateur fonctionne correctement.
     * L'administrateur doit pouvoir s'authentifier avec succès et recevoir un jeton d'accès valide.
     *
     * Scénario :
     * 1. Créez un utilisateur de type administrateur.
     * 2. Effectuez une requête HTTP POST pour s'authentifier en utilisant les identifiants de l'administrateur.
     * 3. Vérifiez que la connexion a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 4. Vérifiez que le jeton d'accès est inclus dans la réponse JSON.
     * 5. Vérifiez que l'utilisateur est correctement authentifié.
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être authentifié avant d'exécuter ce test.
     */

    public function test_Admin_can_authenticate()
    {
        //Création d'un Utilisateur de type candidat
        $password = 'Passe12@';
        $candidat = User::create([
            'nom' => 'Magid',
            'prenom' => 'Ba',
            'telephone' => '75145221',
            'adresse'=>"dakar",
            'role_id' => 2,
            'email' => 'magid1@gmail.com',
            'password' => Hash::make($password),

        ]);

        // Effectuer une requête HTTP POST pour s'authentifier
        $response = $this->post('/api/login', [
            'email' => $candidat->email,
            'password' => $password,
        ]);

        // Vérifier que la connexion a réussi
        $response->assertStatus(200)
            ->assertJson([
                'message' => "Bravo $candidat->prenom vous êtes connecté en tant que Admin",
            ]);

        // Vérifier que l'utilisateur est connecté
        $this->assertAuthenticatedAs($candidat, 'api');
    }
}
