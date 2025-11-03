<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    public function test_homepage_returns_success()
    {
        // Crée un utilisateur factice pour passer les middleware auth
        $user = User::factory()->create();

        // Accède à la route '/' en simulant l'utilisateur connecté
        $response = $this->actingAs($user)->get('/');

        // Vérifie que la réponse HTTP est 200
        $response->assertStatus(200);
    }

    public function test_public_route()
    {
        // Si tu as une route publique, tu peux tester sans utilisateur
        $response = $this->get('/public-route');
        $response->assertStatus(200);
    }
}
