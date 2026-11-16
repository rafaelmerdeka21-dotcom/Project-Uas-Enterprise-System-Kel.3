<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Sales query
        $sales = Sale::with(['customer', 'user', 'details.product'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->latest('sale_date')
            ->get();

        $totalSalesAmount = $sales->sum('total_amount');
        $totalSalesTransactions = $sales->count();

        // Purchases query
        $purchases = Purchase::with(['supplier', 'user'])
            ->whereDate('purchase_date', '>=', $startDate)
            ->whereDate('purchase_date', '<=', $endDate)
            ->where('status', '!=', 'canceled')
            ->latest('purchase_date')
            ->get();

        $totalPurchasesAmount = $purchases->sum('total_amount');
        $totalPurchasesTransactions = $purchases->count();

        // Estimate COGS (HPP / Modal Produk Terjual)
        $totalCogs = 0;
        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                if ($detail->product) {
                    $totalCogs += $detail->quantity * $detail->product->purchase_price;
                }
            }
        }

        $estimatedGrossProfit = $totalSalesAmount - $totalCogs;

        // Top Selling in Period
        $topProducts = SaleDetail::whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereDate('sale_date', '>=', $startDate)
                  ->whereDate('sale_date', '<=', $endDate);
            })
            ->select('product_id', DB::raw('SUM(quantity) as period_sold'), DB::raw('SUM(subtotal) as period_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('period_sold')
            ->take(8)
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'sales',
            'purchases',
            'totalSalesAmount',
            'totalSalesTransactions',
            'totalPurchasesAmount',
            'totalPurchasesTransactions',
            'totalCogs',
            'estimatedGrossProfit',
            'topProducts'
        ));
    }

    /**
     * Export sales data to CSV protected against Formula Injection.
     */
    public function exportSalesCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $sales = Sale::with(['customer', 'user', 'details.product'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->latest('sale_date')
            ->get();

        $fileName = 'Laporan_Penjualan_' . $startDate . '_sd_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Columns Header
            fputcsv($handle, [
                'No. Faktur',
                'Tanggal Transaksi',
                'Nama Pelanggan',
                'Metode Pembayaran',
                'Kasir',
                'Total Nilai (Rp)',
                'Jumlah Item',
                'Rincian Produk Terjual'
            ]);

            foreach ($sales as $sale) {
                $itemDetails = [];
                foreach ($sale->details as $d) {
                    $itemDetails[] = ($d->product->name ?? 'Produk') . ' (' . $d->quantity . 'x @ ' . $d->unit_price . ')';
                }

                fputcsv($handle, [
                    $this->sanitizeCsvField($sale->invoice_number),
                    $sale->sale_date->format('Y-m-d H:i:s'),
                    $this->sanitizeCsvField($sale->customer->name ?? 'Pelanggan Umum'),
                    $this->sanitizeCsvField(strtoupper($sale->payment_method)),
                    $this->sanitizeCsvField($sale->user->name ?? 'Kasir'),
                    $sale->total_amount,
                    $sale->details->sum('quantity'),
                    $this->sanitizeCsvField(implode('; ', $itemDetails)),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Mitigate CSV Formula Injection by prepending single quote to formula trigger characters.
     */
    private function sanitizeCsvField(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $dangerousTriggers = ['=', '+', '-', '@', "\t", "\r"];
        $firstChar = substr($value, 0, 1);

        if (in_array($firstChar, $dangerousTriggers, true)) {
            return "'" . $value;
        }

        return $value;
    }
}
