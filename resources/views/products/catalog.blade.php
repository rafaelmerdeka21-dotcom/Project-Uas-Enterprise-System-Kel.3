@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('page-title', 'Katalog Produk (Kasir & Staf)')
@section('page-subtitle', 'Pengecekan harga jual dan ketersediaan stok fisik barang')

@section('content')
<div class="space-y-6">
    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('products.catalog') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Ketik kode SKU, barcode, atau nama barang..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="category_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                Cari Produk
            </button>
            @if(!empty($search) || !empty($categoryId))
                <a href="{{ route('products.catalog') }}" class="px-3 py-2 text-slate-500 hover:text-slate-700 text-sm">Reset</a>
            @endif
        </form>
    </div>

    <!-- Products Card Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-md transition">
                <div class="h-44 bg-slate-100 flex items-center justify-center overflow-hidden relative">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @endif

                    <div class="absolute top-2.5 right-2.5">
                        @if($product->stock <= 0)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500 text-white shadow-sm">
                                Habis
                            </span>
                        @elseif($product->stock <= $product->min_stock)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white shadow-sm">
                                Menipis ({{ $product->stock }})
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-600 text-white shadow-sm">
                                Stok: {{ $product->stock }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                            <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">{{ $product->code }}</span>
                            <span>{{ $product->category->name ?? '-' }}</span>
                        </div>
                        <h3 class="font-semibold text-slate-800 text-sm line-clamp-2">{{ $product->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $product->description ?? '' }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase tracking-wider">Harga Jual</span>
                            <span class="text-base font-bold text-indigo-600">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400">/ {{ $product->unit }}</span>
                        </div>
                        <a href="{{ route('sales.create') }}?quick_product={{ $product->code }}" 
                           class="p-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition"
                           title="Masukkan ke Kasir">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400 text-sm bg-white rounded-xl border border-slate-200">
                Tidak ada produk ditemukan.
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="p-4 bg-white rounded-xl border border-slate-200">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
