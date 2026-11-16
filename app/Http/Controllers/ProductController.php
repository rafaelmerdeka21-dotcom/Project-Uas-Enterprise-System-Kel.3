<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products for Admin.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $stockFilter = $request->input('stock_filter');

        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($stockFilter === 'low', function ($query) {
                $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
            })
            ->when($stockFilter === 'out', function ($query) {
                $query->where('stock', '<=', 0);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'stockFilter'));
    }

    /**
     * Display a read-only catalog for Staff.
     */
    public function catalog(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('products.catalog', compact('products', 'categories', 'search', 'categoryId'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/products');
            
            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }

            $image->move($destination, $filename);
            $validated['image'] = 'uploads/products/' . $filename;
        }

        DB::transaction(function () use ($validated) {
            $stock = (int) ($validated['stock'] ?? 0);
            $product = Product::create($validated);

            if ($stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $stock,
                    'stock_before' => 0,
                    'stock_after' => $stock,
                    'reference_type' => 'initial',
                    'reference_id' => null,
                    'reason' => 'Saldo awal penambahan barang baru',
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', 'Produk barang berhasil ditambahkan ke inventaris.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        // Prevent direct tampering of stock through basic edit; stock is adjusted via Opname or PO/Sale
        unset($validated['stock']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/products');

            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }

            // Remove old image if exists
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            $image->move($destination, $filename);
            $validated['image'] = 'uploads/products/' . $filename;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Data produk barang berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->saleDetails()->count() > 0 || $product->purchaseDetails()->count() > 0) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus dari inventaris.');
    }
}
