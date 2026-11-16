<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private Product $product;
    private Customer $customer;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->staff = User::factory()->create(['role' => 'staff', 'status' => 'active']);

        $category = Category::create([
            'code' => 'CAT-TEST',
            'name' => 'Kategori Uji',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'code' => 'PRD-TEST-1',
            'name' => 'Barang Percobaan',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 20,
            'min_stock' => 5,
            'unit' => 'pcs',
        ]);

        $this->customer = Customer::create([
            'code' => 'CUST-001',
            'name' => 'Pelanggan Uji',
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'Pemasok Uji',
        ]);
    }

    public function test_sale_transaction_decreases_stock_and_records_movement(): void
    {
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash',
            'paid_amount' => 50000,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                    'unit_price' => 15000,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sales', [
            'customer_id' => $this->customer->id,
            'total_amount' => 45000,
            'paid_amount' => 50000,
            'change_amount' => 5000,
        ]);

        // Verify product stock reduced from 20 to 17
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 17,
        ]);

        // Verify stock movement out was created
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 3,
            'stock_before' => 20,
            'stock_after' => 17,
            'reference_type' => 'sale',
        ]);
    }

    public function test_sale_transaction_fails_if_quantity_exceeds_available_stock(): void
    {
        // Product only has 20 in stock, attempt to buy 25
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash',
            'paid_amount' => 400000,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 25,
                    'unit_price' => 15000,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Verify stock remains untouched at 20 (not negative!)
        $this->assertEquals(20, $this->product->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_purchase_received_increases_stock_and_records_movement(): void
    {
        $response = $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'status' => 'received',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 10000,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        // Stock increased from 20 to 30
        $this->assertEquals(30, $this->product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 10,
            'stock_before' => 20,
            'stock_after' => 30,
            'reference_type' => 'purchase',
        ]);
    }

    public function test_stock_opname_adjustment_accurately_updates_stock_with_audit_reason(): void
    {
        $response = $this->actingAs($this->admin)->post('/stock-movements', [
            'product_id' => $this->product->id,
            'physical_stock' => 18, // 2 items missing
            'reason' => 'Barang pecah saat pemindahan rak gudang',
        ]);

        $response->assertSessionHasNoErrors();

        // Stock updated to 18
        $this->assertEquals(18, $this->product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'adjustment',
            'quantity' => 2,
            'stock_before' => 20,
            'stock_after' => 18,
            'reference_type' => 'adjustment',
        ]);
    }
}
