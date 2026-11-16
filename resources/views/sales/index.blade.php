@extends('layouts.app')

@section('title', 'Riwayat Penjualan')
@section('page-title', 'Riwayat Transaksi Penjualan')
@section('page-subtitle', 'Daftar faktur transaksi penjualan kasir dan pencetakan nota')

@section('content')
<div class="space-y-6">
    <!-- Filters & Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('sales.index') }}" method="GET" class="flex-1 flex flex-wrap items-center gap-3">
            <div class="relative min-w-[200px]">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Cari nomor faktur penjualan..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="payment_method" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Metode Bayar</option>
                <option value="cash" {{ ($paymentMethod ?? '') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                <option value="transfer" {{ ($paymentMethod ?? '') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                <option value="qris" {{ ($paymentMethod ?? '') === 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>

            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" title="Dari Tanggal"
                       class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" title="Sampai Tanggal"
                       class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition">
                Filter
            </button>
            @if(!empty($search) || !empty($paymentMethod) || !empty($startDate) || !empty($endDate))
                <a href="{{ route('sales.index') }}" class="px-2 py-2 text-slate-500 hover:text-slate-700 text-sm">Reset</a>
            @endif
        </form>

        <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buka Kasir Baru</span>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">No. Faktur</th>
                        <th class="py-3 px-4">Waktu Transaksi</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4 text-center">Metode</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-medium text-xs text-indigo-600 bg-indigo-50/40 w-fit rounded">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs">
                                {{ $sale->sale_date->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $sale->customer->name ?? 'Pelanggan Umum' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-xs">
                                {{ $sale->user->name ?? 'Kasir' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $sale->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : ($sale->payment_method === 'qris' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800 text-xs">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                    Cetak Nota / Faktur
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada riwayat transaksi penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
