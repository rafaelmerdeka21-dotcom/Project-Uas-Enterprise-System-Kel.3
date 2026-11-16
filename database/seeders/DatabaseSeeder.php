<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete end-to-end data lifecycle.
     * Idempotent: safe to run multiple times without duplicate key errors.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // 1. USERS (Admin & Staff)
        // ─────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@ems.test'],
            [
                'name'     => 'Administrator EMS',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '081234567890',
                'status'   => 'active',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staf@ems.test'],
            [
                'name'     => 'Staf Kasir',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '081234567891',
                'status'   => 'active',
            ]
        );

        // ─────────────────────────────────────────────
        // 2. CATEGORIES
        // ─────────────────────────────────────────────
        $catFnb = Category::firstOrCreate(
            ['code' => 'CAT-FNB'],
            [
                'name'        => 'Makanan & Minuman',
                'description' => 'Produk konsumsi, makanan ringan, dan berbagai macam minuman.',
            ]
        );

        $catElk = Category::firstOrCreate(
            ['code' => 'CAT-ELK'],
            [
                'name'        => 'Elektronik & Gadget',
                'description' => 'Aksesoris komputer, perangkat pintar, dan gadget modern.',
            ]
        );

        $catAtk = Category::firstOrCreate(
            ['code' => 'CAT-ATK'],
            [
                'name'        => 'Alat Tulis Kantor',
                'description' => 'Perlengkapan alat tulis kantor, buku, dan arsip.',
            ]
        );

        $catHme = Category::firstOrCreate(
            ['code' => 'CAT-HME'],
            [
                'name'        => 'Kebutuhan Rumah',
                'description' => 'Perlengkapan kebersihan dan perabot rumah tangga praktis.',
            ]
        );

        // ─────────────────────────────────────────────
        // 3. SUPPLIERS
        // ─────────────────────────────────────────────
        $sup1 = Supplier::firstOrCreate(
            ['code' => 'SUP-001'],
            [
                'name'    => 'PT Sumber Makmur Sentosa',
                'phone'   => '081234567890',
                'email'   => 'makmur@sumber.test',
                'address' => 'Kawasan Industri Rungkut Blok B-12, Surabaya',
            ]
        );

        $sup2 = Supplier::firstOrCreate(
            ['code' => 'SUP-002'],
            [
                'name'    => 'CV Digital Prima Mandiri',
                'phone'   => '081298765432',
                'email'   => 'sales@digitalprima.test',
                'address' => 'Jl. HR Muhammad No. 45, Surabaya',
            ]
        );

        $sup3 = Supplier::firstOrCreate(
            ['code' => 'SUP-003'],
            [
                'name'    => 'PT Logistik Nusantara Abadi',
                'phone'   => '081377889900',
                'email'   => 'kontak@logistik.test',
                'address' => 'Jl. Ahmad Yani No. 102, Sidoarjo',
            ]
        );

        // ─────────────────────────────────────────────
        // 4. CUSTOMERS
        // ─────────────────────────────────────────────
        $custDefault = Customer::firstOrCreate(
            ['code' => 'CUST-000'],
            [
                'name'    => 'Pelanggan Umum (Walk-in)',
                'phone'   => '08000000000',
                'email'   => 'general@customer.test',
                'address' => 'Transaksi Langsung Toko',
            ]
        );

        $cust1 = Customer::firstOrCreate(
            ['code' => 'CUST-001'],
            [
                'name'    => 'Budi Santoso',
                'phone'   => '081122334455',
                'email'   => 'budi.santoso@gmail.test',
                'address' => 'Jl. Ketintang Baru V No. 12, Surabaya',
            ]
        );

        $cust2 = Customer::firstOrCreate(
            ['code' => 'CUST-002'],
            [
                'name'    => 'Siti Rahmawati',
                'phone'   => '081155667788',
                'email'   => 'siti.rahma@gmail.test',
                'address' => 'Perum Graha Indah Blok C-4, Sidoarjo',
            ]
        );

        // ─────────────────────────────────────────────
        // 5. PRODUCTS (with product images)
        // ─────────────────────────────────────────────
        $prodKopi = Product::firstOrCreate(
            ['code' => 'PRD-0001'],
            [
                'category_id'    => $catFnb->id,
                'name'           => 'Kopi Arabika Premium 250g',
                'description'    => 'Biji kopi pilihan roasted medium dark roasted khas nusantara.',
                'purchase_price' => 45000,
                'selling_price'  => 65000,
                'stock'          => 40,
                'min_stock'      => 10,
                'unit'           => 'pcs',
                'image'          => 'uploads/products/kopi-arabika.svg',
            ]
        );

        $prodMouse = Product::firstOrCreate(
            ['code' => 'PRD-0002'],
            [
                'category_id'    => $catElk->id,
                'name'           => 'Mouse Wireless Ergonomis 2.4G',
                'description'    => 'Mouse optik nirkabel resolusi 1600 DPI hemat daya.',
                'purchase_price' => 75000,
                'selling_price'  => 120000,
                'stock'          => 15,
                'min_stock'      => 5,
                'unit'           => 'unit',
                'image'          => 'uploads/products/mouse-wireless.svg',
            ]
        );

        $prodBuku = Product::firstOrCreate(
            ['code' => 'PRD-0003'],
            [
                'category_id'    => $catAtk->id,
                'name'           => 'Buku Catatan Hardcover A5 Ruled',
                'description'    => 'Buku catatan bersampul tebal 192 halaman kertas 80gsm.',
                'purchase_price' => 18000,
                'selling_price'  => 32000,
                'stock'          => 50,
                'min_stock'      => 10,
                'unit'           => 'buku',
                'image'          => 'uploads/products/buku-catatan.svg',
            ]
        );

        $prodLampu = Product::firstOrCreate(
            ['code' => 'PRD-0004'],
            [
                'category_id'    => $catElk->id,
                'name'           => 'Lampu Meja LED Smart Touch',
                'description'    => 'Lampu belajar dengan 3 temperatur warna dan dimmer sentuh.',
                'purchase_price' => 90000,
                'selling_price'  => 145000,
                'stock'          => 4, // Low stock demo!
                'min_stock'      => 5,
                'unit'           => 'unit',
                'image'          => 'uploads/products/lampu-meja.svg',
            ]
        );

        $prodSabun = Product::firstOrCreate(
            ['code' => 'PRD-0005'],
            [
                'category_id'    => $catHme->id,
                'name'           => 'Sabun Cuci Tangan Organik 500ml',
                'description'    => 'Sabun antibakteri formula alami ekstrak lidah buaya.',
                'purchase_price' => 22000,
                'selling_price'  => 38000,
                'stock'          => 0, // Out of stock demo!
                'min_stock'      => 10,
                'unit'           => 'botol',
                'image'          => 'uploads/products/sabun-cuci.svg',
            ]
        );

        $prodKeyboard = Product::firstOrCreate(
            ['code' => 'PRD-0006'],
            [
                'category_id'    => $catElk->id,
                'name'           => 'Keyboard Mechanical TKL RGB',
                'description'    => 'Keyboard tenkeyless switch blue 87 tuts RGB backlight.',
                'purchase_price' => 180000,
                'selling_price'  => 280000,
                'stock'          => 8,
                'min_stock'      => 3,
                'unit'           => 'unit',
                'image'          => 'uploads/products/keyboard-mechanical.svg',
            ]
        );

        $prodKertas = Product::firstOrCreate(
            ['code' => 'PRD-0007'],
            [
                'category_id'    => $catAtk->id,
                'name'           => 'Kertas HVS A4 80gsm (1 Rim)',
                'description'    => 'Kertas cetak fotokopi putih cerah 500 lembar per rim.',
                'purchase_price' => 42000,
                'selling_price'  => 58000,
                'stock'          => 25,
                'min_stock'      => 5,
                'unit'           => 'rim',
                'image'          => 'uploads/products/kertas-hvs.svg',
            ]
        );

        // ─────────────────────────────────────────────
        // 6. INITIAL STOCK MOVEMENTS (hanya jika belum ada)
        // ─────────────────────────────────────────────
        if (StockMovement::where('reference_type', 'initial')->doesntExist()) {
            $initialProducts = [$prodKopi, $prodMouse, $prodBuku, $prodLampu, $prodKeyboard, $prodKertas];
            foreach ($initialProducts as $p) {
                StockMovement::create([
                    'product_id'     => $p->id,
                    'user_id'        => $admin->id,
                    'type'           => 'in',
                    'quantity'       => $p->stock,
                    'stock_before'   => 0,
                    'stock_after'    => $p->stock,
                    'reference_type' => 'initial',
                    'reference_id'   => null,
                    'reason'         => 'Saldo awal inventaris sistem EMS',
                ]);
            }
        }

        // ─────────────────────────────────────────────
        // 7. SAMPLE PURCHASE ORDER (hanya jika belum ada)
        // ─────────────────────────────────────────────
        if (Purchase::doesntExist()) {
            $purchase = Purchase::create([
                'invoice_number' => 'PO-' . date('Ymd') . '-0001',
                'supplier_id'    => $sup1->id,
                'user_id'        => $admin->id,
                'purchase_date'  => now()->subDays(2),
                'status'         => 'received',
                'total_amount'   => 450000,
                'notes'          => 'Pengadaan stok awal kopi arabika 10 bungkus',
            ]);

            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $prodKopi->id,
                'quantity'    => 10,
                'unit_price'  => 45000,
                'subtotal'    => 450000,
            ]);
        }

        // ─────────────────────────────────────────────
        // 8. SAMPLE SALES TRANSACTIONS (hanya jika belum ada)
        // ─────────────────────────────────────────────
        if (Sale::doesntExist()) {
            // Sale 1: Budi Santoso - Cash
            $sale1 = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-0001',
                'customer_id'    => $cust1->id,
                'user_id'        => $staff->id,
                'sale_date'      => now()->subHours(5),
                'total_amount'   => 130000,
                'paid_amount'    => 150000,
                'change_amount'  => 20000,
                'payment_method' => 'cash',
                'status'         => 'completed',
                'notes'          => 'Transaksi pelanggan langganan',
            ]);

            SaleDetail::create([
                'sale_id'    => $sale1->id,
                'product_id' => $prodKopi->id,
                'quantity'   => 2,
                'unit_price' => 65000,
                'subtotal'   => 130000,
            ]);

            StockMovement::create([
                'product_id'     => $prodKopi->id,
                'user_id'        => $staff->id,
                'type'           => 'out',
                'quantity'       => 2,
                'stock_before'   => 42,
                'stock_after'    => 40,
                'reference_type' => 'sale',
                'reference_id'   => $sale1->id,
                'reason'         => "Penjualan POS Faktur #{$sale1->invoice_number}",
            ]);

            // Sale 2: Walk-in Customer - QRIS
            $sale2 = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-0002',
                'customer_id'    => $custDefault->id,
                'user_id'        => $staff->id,
                'sale_date'      => now()->subHours(2),
                'total_amount'   => 120000,
                'paid_amount'    => 120000,
                'change_amount'  => 0,
                'payment_method' => 'qris',
                'status'         => 'completed',
                'notes'          => 'Pembayaran QRIS scan statis meja kasir',
            ]);

            SaleDetail::create([
                'sale_id'    => $sale2->id,
                'product_id' => $prodMouse->id,
                'quantity'   => 1,
                'unit_price' => 120000,
                'subtotal'   => 120000,
            ]);

            StockMovement::create([
                'product_id'     => $prodMouse->id,
                'user_id'        => $staff->id,
                'type'           => 'out',
                'quantity'       => 1,
                'stock_before'   => 16,
                'stock_after'    => 15,
                'reference_type' => 'sale',
                'reference_id'   => $sale2->id,
                'reason'         => "Penjualan POS Faktur #{$sale2->invoice_number}",
            ]);

            // Sale 3: Siti Rahmawati - Transfer
            $sale3 = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-0003',
                'customer_id'    => $cust2->id,
                'user_id'        => $staff->id,
                'sale_date'      => now()->subMinutes(45),
                'total_amount'   => 96000,
                'paid_amount'    => 96000,
                'change_amount'  => 0,
                'payment_method' => 'transfer',
                'status'         => 'completed',
                'notes'          => 'Transfer bank BCA konfirmasi kasir',
            ]);

            SaleDetail::create([
                'sale_id'    => $sale3->id,
                'product_id' => $prodBuku->id,
                'quantity'   => 3,
                'unit_price' => 32000,
                'subtotal'   => 96000,
            ]);

            StockMovement::create([
                'product_id'     => $prodBuku->id,
                'user_id'        => $staff->id,
                'type'           => 'out',
                'quantity'       => 3,
                'stock_before'   => 53,
                'stock_after'    => 50,
                'reference_type' => 'sale',
                'reference_id'   => $sale3->id,
                'reason'         => "Penjualan POS Faktur #{$sale3->invoice_number}",
            ]);
        }

        // ─────────────────────────────────────────────
        // 9. SAMPLE STOCK OPNAME ADJUSTMENT (hanya jika belum ada)
        // ─────────────────────────────────────────────
        if (StockMovement::where('reference_type', 'adjustment')->doesntExist()) {
            StockMovement::create([
                'product_id'     => $prodLampu->id,
                'user_id'        => $admin->id,
                'type'           => 'adjustment',
                'quantity'       => 1,
                'stock_before'   => 5,
                'stock_after'    => 4,
                'reference_type' => 'adjustment',
                'reference_id'   => null,
                'reason'         => '[Opname Fisik] Barang display etalase mengalami kerusakan switch sentuh',
            ]);
        }
    }
}
