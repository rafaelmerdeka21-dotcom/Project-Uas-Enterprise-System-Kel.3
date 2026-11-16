@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Baru')
@section('page-subtitle', 'Daftarkan kelompok kategori baru untuk produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Kode Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" required
                       placeholder="Contoh: CAT-FNB"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition uppercase font-mono">
                <p class="text-xs text-slate-400 mt-1">Kode identifikasi unik untuk kategori ini.</p>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Makanan & Minuman"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Deskripsi Kategori
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Keterangan singkat mengenai kategori ini..."
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('categories.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
