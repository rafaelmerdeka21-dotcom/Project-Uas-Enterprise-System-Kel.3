<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Enterprise Management System');
    }

    public function test_admin_can_authenticate_and_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@ems.test',
            'role' => 'admin',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@ems.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_staff_can_authenticate_and_redirects_to_pos(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@ems.test',
            'role' => 'staff',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'staff@ems.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('sales.create'));
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        User::factory()->create([
            'email' => 'inactive@ems.test',
            'role' => 'staff',
            'status' => 'inactive',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@ems.test',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
