<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->input('product_id');
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $products = Product::orderBy('name')->get();

        $movements = StockMovement::with(['product', 'user'])
            ->when($productId, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock-movements.index', compact('movements', 'products', 'productId', 'type', 'startDate', 'endDate'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        return view('stock-movements.create', compact('products'));
    }

    public function store(StockAdjustmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated) {
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->first();

            $before = $product->stock;
            $physicalStock = (int) $validated['physical_stock'];
            $diff = $physicalStock - $before;

            if ($diff === 0) {
                return [
                    'status' => 'unchanged',
                    'message' => 'Stok fisik yang Anda masukkan sama dengan stok sistem saat ini (tidak ada perubahan).',
                ];
            }

            $product->update(['stock' => $physicalStock]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'quantity' => abs($diff),
                'stock_before' => $before,
                'stock_after' => $physicalStock,
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'reason' => "[Opname Fisik] " . $validated['reason'],
            ]);

            return [
                'status' => 'success',
                'message' => "Stok produk '{$product->name}' berhasil disesuaikan dari {$before} menjadi {$physicalStock} {$product->unit} (Selisih: " . ($diff > 0 ? "+{$diff}" : $diff) . ").",
            ];
        });

        if ($result['status'] === 'unchanged') {
            return back()->with('warning', $result['message'])->withInput();
        }

        return redirect()->route('stock-movements.index')
            ->with('success', $result['message']);
    }
}
