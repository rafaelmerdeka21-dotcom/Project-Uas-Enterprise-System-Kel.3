@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('page-title', 'Edit Data Pelanggan')
@section('page-subtitle', 'Perbarui informasi pelanggan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kode Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code', $customer->code) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition uppercase font-mono">
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor Telepon / WA
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>
            </div>

            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Lengkap
                </label>
                <textarea id="address" name="address" rows="3"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('customers.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Perbarui Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
