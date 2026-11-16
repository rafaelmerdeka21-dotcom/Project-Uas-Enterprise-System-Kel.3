@extends('layouts.app')

@section('title', 'Laporan & Ekspor')
@section('page-title', 'Laporan & Analitik Periodik')
@section('page-subtitle', 'Rekapitulasi penjualan, pengadaan pembelian, dan estimasi laba kotor')

@section('content')
<div class="space-y-6">

    <!-- Filter & Export Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200 no-print">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-slate-400 mb-0.5">Dari Tanggal:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500">
                </div>
                <span class="text-xs text-slate-400 mt-3">s/d</span>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-slate-400 mb-0.5">Sampai Tanggal:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <button type="submit" class="mt-3.5 px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition">
                Terapkan Filter
            </button>
        </form>

        <div class="flex items-center gap-3 mt-2 sm:mt-0">
            <!-- CSV Export with formula injection protection -->
            <a href="{{ route('reports.export-sales-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Ekspor CSV (Aman)</span>
            </a>

            <!-- Print Report -->
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Ringkasan</span>
            </button>
        </div>
    </div>

    <!-- Period Report Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Total Omzet Penjualan -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Total Omzet Penjualan</span>
            <h3 class="text-2xl font-bold font-mono text-indigo-600 mt-1">
                Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}
            </h3>
            <span class="text-xs text-slate-400 mt-1 block">{{ $totalSalesTransactions }} transaksi penjualan</span>
        </div>

        <!-- Total Pengadaan Barang (PO) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Total Pembelian (PO)</span>
            <h3 class="text-2xl font-bold font-mono text-slate-800 mt-1">
                Rp {{ number_format($totalPurchasesAmount, 0, ',', '.') }}
            </h3>
            <span class="text-xs text-slate-400 mt-1 block">{{ $totalPurchasesTransactions }} faktur pengadaan</span>
        </div>

        <!-- Estimated Gross Profit -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Estimasi Laba Kotor (Gross Margin)</span>
            <h3 class="text-2xl font-bold font-mono text-emerald-600 mt-1">
                Rp {{ number_format($estimatedGrossProfit, 0, ',', '.') }}
            </h3>
            <span class="text-xs text-slate-400 mt-1 block">Omzet dikurangi modal barang terjual</span>
        </div>

    </div>

    <!-- Breakdown Table: Sales Transactions in Period -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
            <span>Rincian Penjualan Periode ({{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }})</span>
            <span class="text-xs font-normal text-slate-400">{{ $sales->count() }} Data Transaksi</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200 pb-2">
                        <th class="pb-2">No. Faktur</th>
                        <th class="pb-2">Tanggal</th>
                        <th class="pb-2">Pelanggan</th>
                        <th class="pb-2">Metode Bayar</th>
                        <th class="pb-2 text-center">Jumlah Item</th>
                        <th class="pb-2 text-right">Total Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 font-mono font-medium text-indigo-600">{{ $sale->invoice_number }}</td>
                            <td class="py-2.5 text-slate-600">{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 font-semibold text-slate-800">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</td>
                            <td class="py-2.5 uppercase font-bold text-slate-600">{{ $sale->payment_method }}</td>
                            <td class="py-2.5 text-center">{{ $sale->details->sum('quantity') }}</td>
                            <td class="py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">
                                Tidak ada transaksi penjualan pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products in Period -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
            Produk Terlaris Dalam Periode Ini
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($topProducts as $top)
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <span class="font-mono text-[10px] text-slate-400 block">{{ $top->product->code ?? '-' }}</span>
                    <h5 class="font-bold text-slate-800 truncate mt-0.5">{{ $top->product->name ?? 'Produk' }}</h5>
                    <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-indigo-600 font-bold font-mono">{{ $top->period_sold }} {{ $top->product->unit ?? 'unit' }}</span>
                        <span class="text-slate-600 font-mono">Rp {{ number_format($top->period_revenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 col-span-full text-center py-4">Belum ada barang terjual pada periode ini.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
