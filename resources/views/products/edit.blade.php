@extends('layouts.app')

@section('title', 'Edit Barang')
@section('page-title', 'Edit Data Barang')
@section('page-subtitle', 'Perbarui informasi dan harga produk')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Current Stock Status Banner -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-semibold tracking-wider text-slate-500">Stok Saat Ini:</span>
                    <span class="text-xl font-bold ml-2 {{ $product->stock <= 0 ? 'text-rose-600' : ($product->stock <= $product->min_stock ? 'text-amber-600' : 'text-slate-800') }}">
                        {{ $product->stock }} {{ $product->unit }}
                    </span>
                </div>
                <div class="text-xs text-slate-500">
                    Penyesuaian stok dilakukan melalui menu <a href="{{ route('stock-movements.create') }}" class="text-indigo-600 font-semibold underline">Stock Opname</a> atau Transaksi Pembelian.
                </div>
            </div>

            <!-- Product Code & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kode SKU / Barcode <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition uppercase font-mono">
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Barang
                    </label>
                    <select id="category_id" name="category_id" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Product Name -->
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Barang <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <!-- Prices & Unit -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="purchase_price" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Harga Beli / Modal (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required min="0"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-mono">
                </div>

                <div>
                    <label for="selling_price" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Harga Jual (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required min="0"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-mono font-semibold text-indigo-700">
                </div>

                <div>
                    <label for="unit" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Satuan Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>
            </div>

            <!-- Min Stock -->
            <div>
                <label for="min_stock" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Batas Minimum Stok <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" required min="0"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Deskripsi & Keterangan Barang
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Product Image -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Foto Produk
                </label>
                @if($product->image)
                    <div class="mb-3 flex items-center gap-3">
                        <img src="{{ asset($product->image) }}" alt="Preview" class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                        <span class="text-xs text-slate-500">Unggah berkas baru untuk mengganti foto ini.</span>
                    </div>
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Perbarui Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
