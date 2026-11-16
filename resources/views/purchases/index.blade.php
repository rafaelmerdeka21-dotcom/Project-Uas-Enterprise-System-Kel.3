@extends('layouts.app')

@section('title', 'Pembelian (PO)')
@section('page-title', 'Pemesanan Pembelian (Purchase Orders)')
@section('page-subtitle', 'Kelola pemesanan barang dari pemasok dan penerimaan stok')

@section('content')
<div class="space-y-6">
    <!-- Filters & Action -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('purchases.index') }}" method="GET" class="flex-1 flex flex-wrap items-center gap-3">
            <div class="relative min-w-[200px]">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Cari nomor faktur PO..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="supplier_id" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Pemasok</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ ($supplierId ?? '') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Tertunda (Pending)</option>
                <option value="received" {{ ($status ?? '') === 'received' ? 'selected' : '' }}>Diterima (Received)</option>
                <option value="canceled" {{ ($status ?? '') === 'canceled' ? 'selected' : '' }}>Dibatalkan (Canceled)</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition">
                Filter
            </button>
            @if(!empty($search) || !empty($supplierId) || !empty($status))
                <a href="{{ route('purchases.index') }}" class="px-2 py-2 text-slate-500 hover:text-slate-700 text-sm">Reset</a>
            @endif
        </form>

        <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buat Pesanan PO</span>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">No. PO</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Pemasok</th>
                        <th class="py-3 px-4">Dibuat Oleh</th>
                        <th class="py-3 px-4 text-right">Total Nilai</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-medium text-xs text-indigo-600 bg-indigo-50/40 w-fit rounded">
                                {{ $purchase->invoice_number }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs">
                                {{ $purchase->purchase_date->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-xs">
                                {{ $purchase->user->name ?? 'Admin' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800 text-xs">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($purchase->status === 'received')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-100 text-emerald-800">
                                        Diterima (Stok Masuk)
                                    </span>
                                @elseif($purchase->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-100 text-amber-800">
                                        Menunggu Barang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('purchases.show', $purchase) }}" class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                    Rincian PO &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada riwayat pesanan pembelian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
