<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormationTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_Formations()
    {
        $response = $this->get('api/formations');
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Liste des formations'
        ]);
    }
}
