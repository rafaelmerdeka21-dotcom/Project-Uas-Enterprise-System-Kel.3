<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->format('Y-m-d');
        $startOfMonth = now()->startOfMonth()->format('Y-m-d H:i:s');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d H:i:s');

        // Executive Statistics
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();

        // Omzet & Transaksi
        $todaySalesAmount = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todaySalesCount = Sale::whereDate('sale_date', $today)->count();

        $monthSalesAmount = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
        $monthPurchasesAmount = Purchase::whereBetween('purchase_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->where('status', '!=', 'canceled')
            ->sum('total_amount');

        // Stock Alerts
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->where('stock', '>', 0)
            ->take(5)
            ->get();

        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0)->count();

        // Recent Sales
        $recentSales = Sale::with(['customer', 'user'])
            ->latest('sale_date')
            ->take(5)
            ->get();

        // Top Selling Products
        $topProducts = SaleDetail::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalCustomers',
            'todaySalesAmount',
            'todaySalesCount',
            'monthSalesAmount',
            'monthPurchasesAmount',
            'lowStockProducts',
            'lowStockCount',
            'outOfStockCount',
            'recentSales',
            'topProducts'
        ));
    }
}
