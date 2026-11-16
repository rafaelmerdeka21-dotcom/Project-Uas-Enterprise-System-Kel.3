@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Executive Dashboard')
@section('page-subtitle', 'Ringkasan performa penjualan, kondisi stok barang, dan inventaris aktif')

@section('content')
<div class="space-y-6">

    {{-- ─── Stok Alerts ─── --}}
    @if($outOfStockCount > 0 || $lowStockCount > 0)
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @if($outOfStockCount > 0)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-800">Stok Habis!</h4>
                            <p class="text-xs text-rose-600 mt-0.5"><strong>{{ $outOfStockCount }} barang</strong> dengan stok 0.</p>
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}?stock_filter=out"
                       class="shrink-0 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700">
                        Lihat
                    </a>
                </div>
            @endif

            @if($lowStockCount > 0)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-amber-800">Stok Menipis</h4>
                            <p class="text-xs text-amber-700 mt-0.5"><strong>{{ $lowStockCount }} barang</strong> di bawah minimum.</p>
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}?stock_filter=low"
                       class="shrink-0 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-600">
                        Order
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- ─── 4 Metric Cards ─── --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

        {{-- Omzet Hari Ini --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Omzet Hari Ini</p>
                    <h3 class="mt-1 truncate text-lg font-bold text-slate-900 lg:text-xl">
                        Rp&nbsp;{{ number_format($todaySalesAmount, 0, ',', '.') }}
                    </h3>
                    <span class="mt-0.5 block text-xs text-slate-400">{{ $todaySalesCount }} transaksi</span>
                </div>
                <div class="ml-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Penjualan Bulan Ini --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Bulan Ini</p>
                    <h3 class="mt-1 truncate text-lg font-bold text-emerald-600 lg:text-xl">
                        Rp&nbsp;{{ number_format($monthSalesAmount, 0, ',', '.') }}
                    </h3>
                    <span class="mt-0.5 block text-xs text-slate-400">{{ now()->format('M Y') }}</span>
                </div>
                <div class="ml-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pembelian PO --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pembelian (PO)</p>
                    <h3 class="mt-1 truncate text-lg font-bold text-slate-900 lg:text-xl">
                        Rp&nbsp;{{ number_format($monthPurchasesAmount, 0, ',', '.') }}
                    </h3>
                    <span class="mt-0.5 block text-xs text-slate-400">Total pengadaan</span>
                </div>
                <div class="ml-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Barang --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Barang</p>
                    <h3 class="mt-1 text-lg font-bold text-slate-900 lg:text-xl">
                        {{ $totalProducts }} <span class="text-xs font-normal text-slate-400">SKU</span>
                    </h3>
                    <span class="mt-0.5 block text-xs text-slate-400">{{ $totalCategories }} kategori</span>
                </div>
                <div class="ml-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 3 9 4.5v9L12 21l-9-4.5v-9L12 3zm0 9 9-4.5M12 12 3 7.5m9 4.5v9"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── AKSES CEPAT — Pusat Navigasi Modul (Mobile-first Hub) ─── --}}
    {{--
        Ini adalah Grid/Card navigasi utama untuk mobile.
        Di mobile, bottom tab hanya 5 item → modul lain diakses dari sini.
        Di desktop, ini sebagai shortcut tambahan di bawah metric cards.
    --}}
    <div>
        <div class="mb-3 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800">⚡ Akses Cepat Modul</h2>
                <p class="mt-0.5 text-xs text-slate-400">Pintasan ke seluruh fitur sistem</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">

            {{-- Kasir / POS --}}
            <a href="{{ route('sales.create') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-indigo-700 p-4 text-center shadow-sm transition hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7h6m0 0V4m0 3l-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">Kasir</p>
                    <p class="mt-0.5 text-[11px] text-indigo-200">POS Transaksi</p>
                </div>
            </a>

            {{-- Riwayat Penjualan --}}
            <a href="{{ route('sales.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Riwayat</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Transaksi Penjualan</p>
                </div>
            </a>

            {{-- Data Barang --}}
            <a href="{{ route('products.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 3 9 4.5v9L12 21l-9-4.5v-9L12 3zm0 9 9-4.5M12 12 3 7.5m9 4.5v9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Data Barang</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Inventaris &amp; SKU</p>
                </div>
            </a>

            {{-- Pembelian / PO --}}
            <a href="{{ route('purchases.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Pembelian</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Purchase Order</p>
                </div>
            </a>

            {{-- Laporan --}}
            <a href="{{ route('reports.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m0 14h16M8 15v-4m4 4V7m4 8v-6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Laporan</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Ekspor &amp; Analisis</p>
                </div>
            </a>

            {{-- Mutasi Stok --}}
            <a href="{{ route('stock-movements.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Mutasi Stok</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Opname &amp; Adj.</p>
                </div>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('categories.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Kategori</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Master Kategori</p>
                </div>
            </a>

            {{-- Pemasok --}}
            <a href="{{ route('suppliers.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Pemasok</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Data Supplier</p>
                </div>
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('customers.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Pelanggan</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Data Customer</p>
                </div>
            </a>

            {{-- Manajemen Pengguna --}}
            <a href="{{ route('users.index') }}"
               class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-indigo-200 hover:shadow-md hover:scale-[1.02] active:scale-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Pengguna</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Manajemen User</p>
                </div>
            </a>

        </div>
    </div>

    {{-- ─── Transaksi Terbaru & Produk Terlaris ─── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Recent Sales --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div>
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Transaksi Kasir Terbaru</h3>
                        <p class="text-xs text-slate-400">5 aktivitas penjualan terakhir</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto -mx-1 px-1">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-semibold uppercase text-slate-400">
                                <th class="pb-2">Faktur</th>
                                <th class="pb-2">Pelanggan</th>
                                <th class="pb-2 text-center">Metode</th>
                                <th class="pb-2 text-right">Total</th>
                                <th class="hidden pb-2 text-right sm:table-cell">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="py-2.5 font-mono font-medium text-indigo-600">
                                        <a href="{{ route('sales.show', $sale) }}" class="hover:underline">{{ $sale->invoice_number }}</a>
                                    </td>
                                    <td class="py-2.5 font-semibold text-slate-800">{{ $sale->customer->name ?? 'Umum' }}</td>
                                    <td class="py-2.5 text-center">
                                        <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $sale->payment_method === 'cash' ? 'bg-emerald-50 text-emerald-700' : 'bg-purple-50 text-purple-700' }}">
                                            {{ $sale->payment_method }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold text-slate-900">
                                        Rp&nbsp;{{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="hidden py-2.5 text-right text-[11px] text-slate-400 sm:table-cell">
                                        {{ $sale->sale_date->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">Belum ada transaksi penjualan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 border-t border-slate-100 pt-4">
                <a href="{{ route('sales.create') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-50 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buka Mesin Kasir Sekarang
                </a>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Produk Terlaris</h3>
                        <p class="text-xs text-slate-400">Paling banyak terjual</p>
                    </div>
                    <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-700">Top 5</span>
                </div>

                <div class="space-y-2.5">
                    @forelse($topProducts as $top)
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50 p-2.5">
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-xs font-semibold text-slate-800">{{ $top->product->name ?? 'Produk Dihapus' }}</h4>
                                <span class="font-mono text-[10px] text-slate-400">{{ $top->product->code ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-mono text-xs font-bold text-indigo-600">{{ $top->total_sold }} {{ $top->product->unit ?? 'unit' }}</span>
                                <span class="block text-[10px] text-slate-400">Rp&nbsp;{{ number_format($top->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-xs text-slate-400">Belum ada data barang terlaris.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 border-t border-slate-100 pt-4 text-center">
                <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-slate-500 transition hover:text-indigo-600">
                    Lihat Analisis Laporan Lengkap &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
