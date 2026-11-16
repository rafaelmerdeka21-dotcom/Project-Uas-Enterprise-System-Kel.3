@extends('layouts.app')

@section('title', 'Mutasi & Opname Stok')
@section('page-title', 'Mutasi & Penyesuaian Stok (Audit Trail)')
@section('page-subtitle', 'Riwayat lengkap perpindahan barang masuk, keluar, dan penyesuaian fisik')

@section('content')
<div class="space-y-6">
    <!-- Filter & Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('stock-movements.index') }}" method="GET" class="flex-1 flex flex-wrap items-center gap-3">
            <select name="product_id" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 max-w-xs">
                <option value="">Semua Barang</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ ($productId ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->code }} - {{ $product->name }}
                    </option>
                @endforeach
            </select>

            <select name="type" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Jenis Mutasi</option>
                <option value="in" {{ ($type ?? '') === 'in' ? 'selected' : '' }}>Masuk (In)</option>
                <option value="out" {{ ($type ?? '') === 'out' ? 'selected' : '' }}>Keluar (Out)</option>
                <option value="adjustment" {{ ($type ?? '') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Adjustment)</option>
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
            @if(!empty($productId) || !empty($type) || !empty($startDate) || !empty($endDate))
                <a href="{{ route('stock-movements.index') }}" class="px-2 py-2 text-slate-500 hover:text-slate-700 text-sm">Reset</a>
            @endif
        </form>

        <a href="{{ route('stock-movements.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Stock Opname Fisik</span>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Barang</th>
                        <th class="py-3 px-4 text-center">Jenis Mutasi</th>
                        <th class="py-3 px-4 text-center">Jumlah Perubahan</th>
                        <th class="py-3 px-4 text-center">Sebelum & Sesudah</th>
                        <th class="py-3 px-4">Petugas & Alasan Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-slate-500 text-xs whitespace-nowrap">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800">{{ $movement->product->name }}</span>
                                <span class="block font-mono text-xs text-indigo-600">{{ $movement->product->code }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($movement->type === 'in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                        &darr; Masuk
                                    </span>
                                @elseif($movement->type === 'out')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-800">
                                        &uarr; Keluar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        &harr; Opname Penyesuaian
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-xs {{ $movement->type === 'in' ? 'text-emerald-600' : ($movement->type === 'out' ? 'text-rose-600' : 'text-indigo-600') }}">
                                {{ $movement->type === 'in' ? '+' : ($movement->type === 'out' ? '-' : '') }}{{ $movement->quantity }} {{ $movement->product->unit }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-600">
                                <span>{{ $movement->stock_before }}</span>
                                <span class="text-slate-400 mx-1">&rarr;</span>
                                <strong class="text-slate-900">{{ $movement->stock_after }}</strong>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="font-medium text-slate-700 block">{{ $movement->user->name ?? 'Sistem' }}</span>
                                <span class="text-slate-500 italic">{{ $movement->reason }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada catatan riwayat mutasi stok barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
