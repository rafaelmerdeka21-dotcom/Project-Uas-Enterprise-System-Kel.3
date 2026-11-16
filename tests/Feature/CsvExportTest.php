<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_export_mitigates_formula_injection(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        // Customer with a malicious formula payload
        $customer = Customer::create([
            'code' => 'CUST-FORMULA',
            'name' => '=SUM(A1:A100)',
        ]);

        $product = Product::create([
            'code' => 'PRD-MALICIOUS',
            'name' => '@calc.exe',
            'purchase_price' => 5000,
            'selling_price' => 10000,
            'stock' => 10,
            'min_stock' => 2,
            'unit' => 'pcs',
        ]);

        $sale = Sale::create([
            'invoice_number' => '+INV-DANGEROUS-99',
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'sale_date' => now(),
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10000,
            'subtotal' => 10000,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export-sales-csv', [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Formula triggers should be escaped by prefixing with a single quote: '= becomes '=
        $this->assertStringContainsString("'+INV-DANGEROUS-99", $content);
        $this->assertStringContainsString("'=SUM(A1:A100)", $content);
        $this->assertStringContainsString("'@calc.exe", $content);
    }
}
