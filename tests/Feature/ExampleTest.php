<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    /** @test */
    public function homepage_redirects_to_login()
    {
        $response = $this->get('/'); // la route '/' redirige vers /login
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function login_page_is_accessible()
    {
        $response = $this->get('/login'); // route publique réelle
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_dashboard_requires_authentication()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // redirige vers login si pas connecté

        // Tester avec utilisateur connecté
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
