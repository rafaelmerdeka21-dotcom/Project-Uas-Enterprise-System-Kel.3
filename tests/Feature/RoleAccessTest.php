<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_protected_pages(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/categories')->assertRedirect(route('login'));
        $this->get('/products')->assertRedirect(route('login'));
        $this->get('/sales')->assertRedirect(route('login'));
    }

    public function test_staff_cannot_access_admin_modules_and_gets_403_forbidden(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);

        // Staff attempts to access admin modules
        $this->actingAs($staff)->get('/dashboard')->assertStatus(403);
        $this->actingAs($staff)->get('/categories')->assertStatus(403);
        $this->actingAs($staff)->get('/products')->assertStatus(403);
        $this->actingAs($staff)->get('/suppliers')->assertStatus(403);
        $this->actingAs($staff)->get('/customers')->assertStatus(403);
        $this->actingAs($staff)->get('/purchases')->assertStatus(403);
        $this->actingAs($staff)->get('/stock-movements')->assertStatus(403);
        $this->actingAs($staff)->get('/reports')->assertStatus(403);
        $this->actingAs($staff)->get('/users')->assertStatus(403);
    }

    public function test_staff_can_access_pos_sales_and_catalog(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->actingAs($staff)->get('/sales/create')->assertStatus(200);
        $this->actingAs($staff)->get('/sales')->assertStatus(200);
        $this->actingAs($staff)->get('/catalog')->assertStatus(200);
    }

    public function test_admin_can_access_all_modules(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/categories')->assertStatus(200);
        $this->actingAs($admin)->get('/products')->assertStatus(200);
        $this->actingAs($admin)->get('/suppliers')->assertStatus(200);
        $this->actingAs($admin)->get('/customers')->assertStatus(200);
        $this->actingAs($admin)->get('/purchases')->assertStatus(200);
        $this->actingAs($admin)->get('/sales/create')->assertStatus(200);
        $this->actingAs($admin)->get('/stock-movements')->assertStatus(200);
        $this->actingAs($admin)->get('/reports')->assertStatus(200);
        $this->actingAs($admin)->get('/users')->assertStatus(200);
    }

    public function test_admin_dashboard_has_five_primary_tabs_and_shortcuts_to_all_modules(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Akses Cepat Semua Modul')
            ->assertSee(route('categories.index'), false)
            ->assertSee(route('suppliers.index'), false)
            ->assertSee(route('customers.index'), false)
            ->assertSee(route('purchases.index'), false)
            ->assertSee(route('stock-movements.index'), false)
            ->assertSee(route('users.index'), false);

        $this->assertSame(5, substr_count($response->getContent(), 'class="tab-item '));
    }

    public function test_staff_navigation_only_shows_relevant_modules(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->get('/sales/create');

        $response->assertOk()
            ->assertSee('Riwayat')
            ->assertSee('Katalog')
            ->assertDontSee('Laporan');

        $this->assertSame(3, substr_count($response->getContent(), 'class="tab-item '));
    }
}
