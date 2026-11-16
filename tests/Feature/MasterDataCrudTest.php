<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    public function test_category_crud(): void
    {
        $response = $this->actingAs($this->admin)->post(route('categories.store'), [
            'code' => 'CAT-NEW',
            'name' => 'Kategori Baru',
            'description' => 'Deskripsi Kategori',
        ]);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['code' => 'CAT-NEW']);

        $category = Category::where('code', 'CAT-NEW')->first();

        $this->actingAs($this->admin)->put(route('categories.update', $category), [
            'code' => 'CAT-NEW',
            'name' => 'Kategori Diperbarui',
            'description' => 'Deskripsi Baru',
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Kategori Diperbarui']);

        $this->actingAs($this->admin)->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_supplier_crud(): void
    {
        $response = $this->actingAs($this->admin)->post(route('suppliers.store'), [
            'code' => 'SUP-NEW',
            'name' => 'Pemasok Mandiri',
            'phone' => '081234567890',
            'email' => 'mandiri@supplier.test',
            'address' => 'Jl. Pemasok No. 1',
        ]);
        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['code' => 'SUP-NEW']);

        $supplier = Supplier::where('code', 'SUP-NEW')->first();

        $this->actingAs($this->admin)->put(route('suppliers.update', $supplier), [
            'code' => 'SUP-NEW',
            'name' => 'Pemasok Mandiri Perkasa',
            'phone' => '081234567899',
            'email' => 'mandiri@supplier.test',
            'address' => 'Jl. Pemasok No. 2',
        ])->assertRedirect(route('suppliers.index'));

        $this->assertDatabaseHas('suppliers', ['name' => 'Pemasok Mandiri Perkasa']);

        $this->actingAs($this->admin)->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_customer_crud(): void
    {
        $response = $this->actingAs($this->admin)->post(route('customers.store'), [
            'code' => 'CUST-NEW',
            'name' => 'Pelanggan Setia',
            'phone' => '081234567888',
            'email' => 'setia@customer.test',
            'address' => 'Jl. Mawar No. 10',
        ]);
        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['code' => 'CUST-NEW']);

        $customer = Customer::where('code', 'CUST-NEW')->first();

        $this->actingAs($this->admin)->put(route('customers.update', $customer), [
            'code' => 'CUST-NEW',
            'name' => 'Pelanggan Sangat Setia',
            'phone' => '081234567888',
            'email' => 'setia@customer.test',
            'address' => 'Jl. Mawar No. 12',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['name' => 'Pelanggan Sangat Setia']);

        $this->actingAs($this->admin)->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_product_crud(): void
    {
        $category = Category::create(['code' => 'CAT-ELK', 'name' => 'Elektronik']);

        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'code' => 'PRD-NEW-01',
            'name' => 'Kabel Type-C 100W',
            'description' => 'Fast charging cable',
            'purchase_price' => 25000,
            'selling_price' => 45000,
            'stock' => 15,
            'min_stock' => 5,
            'unit' => 'pcs',
        ]);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['code' => 'PRD-NEW-01', 'stock' => 15]);

        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => 'initial',
            'quantity' => 15,
            'type' => 'in',
        ]);

        $product = Product::where('code', 'PRD-NEW-01')->first();

        $this->actingAs($this->admin)->put(route('products.update', $product), [
            'category_id' => $category->id,
            'code' => 'PRD-NEW-01',
            'name' => 'Kabel Type-C 100W Braided',
            'purchase_price' => 27000,
            'selling_price' => 50000,
            'min_stock' => 5,
            'unit' => 'pcs',
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['name' => 'Kabel Type-C 100W Braided']);

        $this->actingAs($this->admin)->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_user_management_crud(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Kasir Baru',
            'email' => 'kasirbaru@ems.test',
            'password' => 'password123',
            'role' => 'staff',
            'phone' => '081299990000',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'kasirbaru@ems.test', 'role' => 'staff']);

        $user = User::where('email', 'kasirbaru@ems.test')->first();

        $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => 'Kasir Baru Senior',
            'email' => 'kasirbaru@ems.test',
            'role' => 'admin',
            'phone' => '081299990000',
            'status' => 'active',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['name' => 'Kasir Baru Senior', 'role' => 'admin']);

        // Admin cannot delete himself
        $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin))
            ->assertSessionHas('error');

        // Delete user
        $this->actingAs($this->admin)->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
