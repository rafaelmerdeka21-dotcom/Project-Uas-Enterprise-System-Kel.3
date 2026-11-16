<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $supplierId = $request->input('supplier_id');

        $suppliers = Supplier::orderBy('name')->get();

        $purchases = Purchase::with(['supplier', 'user'])
            ->when($search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'suppliers', 'search', 'status', 'supplierId'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        $nextInvoice = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        return view('purchases.create', compact('suppliers', 'products', 'nextInvoice'));
    }

    public function store(PurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $invoiceNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $purchase = Purchase::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_date' => $validated['purchase_date'],
                'status' => $validated['status'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // Stok bertambah otomatis HANYA jika status adalah 'received'
                if ($purchase->status === 'received') {
                    $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                    $before = $product->stock;
                    $after = $before + $item['quantity'];

                    $product->increment('stock', $item['quantity']);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'in',
                        'quantity' => $item['quantity'],
                        'stock_before' => $before,
                        'stock_after' => $after,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'reason' => "Penerimaan Barang PO #{$purchase->invoice_number}",
                    ]);
                }
            }
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Transaksi pembelian (PO) berhasil dicatat.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'details.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, Purchase $purchase): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,received,canceled',
        ]);

        $newStatus = $request->input('status');

        if ($purchase->status === $newStatus) {
            return back()->with('warning', 'Status transaksi sudah bernilai sama.');
        }

        DB::transaction(function () use ($purchase, $newStatus) {
            $purchase->load('details.product');

            // Jika status sebelumnya pending dan sekarang diterima: tambahkan stok
            if ($purchase->status === 'pending' && $newStatus === 'received') {
                foreach ($purchase->details as $detail) {
                    $product = Product::where('id', $detail->product_id)->lockForUpdate()->first();
                    $before = $product->stock;
                    $after = $before + $detail->quantity;

                    $product->increment('stock', $detail->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'in',
                        'quantity' => $detail->quantity,
                        'stock_before' => $before,
                        'stock_after' => $after,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'reason' => "Penerimaan Barang PO #{$purchase->invoice_number}",
                    ]);
                }
            }
            // Jika status sebelumnya received lalu dibatalkan: kurangi kembali stok
            elseif ($purchase->status === 'received' && $newStatus === 'canceled') {
                foreach ($purchase->details as $detail) {
                    $product = Product::where('id', $detail->product_id)->lockForUpdate()->first();
                    $before = $product->stock;
                    $after = $before - $detail->quantity;

                    $product->decrement('stock', $detail->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $detail->quantity,
                        'stock_before' => $before,
                        'stock_after' => $after,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'reason' => "Pembatalan Penerimaan PO #{$purchase->invoice_number}",
                    ]);
                }
            }

            $purchase->update(['status' => $newStatus]);
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Status pembelian berhasil diubah menjadi: {$newStatus}");
    }
}
