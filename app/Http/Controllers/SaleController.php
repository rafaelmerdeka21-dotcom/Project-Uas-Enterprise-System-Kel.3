<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $paymentMethod = $request->input('payment_method');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sales = Sale::with(['customer', 'user', 'details.product'])
            ->when($search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($paymentMethod, function ($query, $paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('sale_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('sale_date', '<=', $endDate);
            })
            ->latest('sale_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'paymentMethod', 'startDate', 'endDate'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::orderBy('name')->get();
        $defaultCustomer = Customer::where('code', 'CUST-000')->first() ?? $customers->first();

        // Products for POS items selection
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        $quickProduct = null;
        if ($request->has('quick_product')) {
            $quickProduct = Product::where('code', $request->input('quick_product'))->first();
        }

        $nextInvoice = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        return view('sales.create', compact('customers', 'defaultCustomer', 'products', 'quickProduct', 'nextInvoice'));
    }

    public function store(SaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $sale = DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $paidAmount = (float) $validated['paid_amount'];
            if ($paidAmount < $totalAmount) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Nominal bayar (Rp ' . number_format($paidAmount, 0, ',', '.') . ') kurang dari total belanja (Rp ' . number_format($totalAmount, 0, ',', '.') . ').',
                ]);
            }

            $changeAmount = $paidAmount - $totalAmount;
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'sale_date' => now(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                // Database row locking untuk mencegah race condition & stok minus
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => 'Produk tidak ditemukan.',
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk '{$product->name}' tidak mencukupi. Tersedia: {$product->stock} {$product->unit}, diminta: {$item['quantity']} {$product->unit}.",
                    ]);
                }

                $before = $product->stock;
                $after = $before - $item['quantity'];

                $product->decrement('stock', $item['quantity']);

                $subtotal = $item['quantity'] * $item['unit_price'];

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // Catat mutasi stok keluar
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'reason' => "Penjualan POS Faktur #{$sale->invoice_number}",
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Transaksi berhasil diselesaikan! Faktur: {$sale->invoice_number}");
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'user', 'details.product']);
        return view('sales.show', compact('sale'));
    }
}
