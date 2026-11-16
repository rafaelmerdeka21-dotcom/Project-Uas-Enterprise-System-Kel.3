@extends('layouts.app')

@section('title', 'Faktur Penjualan ' . $sale->invoice_number)
@section('page-title', 'Faktur Penjualan ' . $sale->invoice_number)
@section('page-subtitle', 'Bukti transaksi resmi penjualan barang kasir')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Action Toolbar (Hidden during Print) -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Daftar Riwayat Penjualan</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                + Transaksi Baru
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Nota Kasir</span>
            </button>
        </div>
    </div>

    <!-- Official Invoice Receipt Sheet -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10 text-slate-800" id="printableInvoice">
        
        <!-- Header -->
        <div class="text-center pb-6 border-b border-dashed border-slate-300">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white font-bold mb-2">
                EMS
            </div>
            <h2 class="text-xl font-bold tracking-tight text-slate-900">EMS DAGANG TERPADU</h2>
            <p class="text-xs text-slate-500">Pusat Grosir & Eceran Barang Dagang Berkualitas</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Jl. Raya Telang No. 1, Kamal, Bangkalan | Telp: 0812-3456-7890</p>
        </div>

        <!-- Transaction Meta -->
        <div class="py-4 border-b border-dashed border-slate-300 grid grid-cols-2 text-xs gap-2">
            <div>
                <p><span class="text-slate-400">No. Faktur:</span> <strong class="font-mono text-slate-900">{{ $sale->invoice_number }}</strong></p>
                <p><span class="text-slate-400">Pelanggan:</span> <strong class="text-slate-800">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</strong></p>
            </div>
            <div class="text-right">
                <p><span class="text-slate-400">Waktu:</span> {{ $sale->sale_date->format('d/m/Y H:i') }}</p>
                <p><span class="text-slate-400">Kasir:</span> {{ $sale->user->name ?? 'Kasir' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-4 border-b border-dashed border-slate-300">
            <table class="w-full text-xs">
                <thead>
                    <tr class="font-semibold text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="text-left pb-2">Item Barang</th>
                        <th class="text-center pb-2 w-16">Qty</th>
                        <th class="text-right pb-2 w-28">Harga</th>
                        <th class="text-right pb-2 w-28">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sale->details as $detail)
                        <tr>
                            <td class="py-2.5">
                                <span class="font-semibold text-slate-800">{{ $detail->product->name }}</span>
                                <span class="block font-mono text-[10px] text-slate-400">{{ $detail->product->code }}</span>
                            </td>
                            <td class="py-2.5 text-center font-semibold text-slate-700">
                                {{ $detail->quantity }}
                            </td>
                            <td class="py-2.5 text-right font-mono text-slate-600">
                                Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-slate-800">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculation & Payment Breakdown -->
        <div class="py-4 border-b border-dashed border-slate-300 space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-500">Total Belanja:</span>
                <span class="font-bold font-mono text-sm text-slate-900">
                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Metode Bayar:</span>
                <span class="font-bold uppercase text-indigo-600">{{ $sale->payment_method }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nominal Diterima:</span>
                <span class="font-mono text-slate-800">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-1 border-t border-slate-100 font-bold">
                <span class="text-slate-700">Kembalian:</span>
                <span class="font-mono text-emerald-600 text-sm">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Receipt Footer -->
        <div class="pt-6 text-center text-xs text-slate-400 space-y-1">
            <p class="font-medium text-slate-600">Terima kasih telah berbelanja di EMS Dagang!</p>
            <p class="text-[10px]">Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan tanpa struk resmi.</p>
        </div>

    </div>

</div>
@endsection
