@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Data Pengguna')
@section('page-subtitle', 'Perbarui akun pengguna atau reset kata sandi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor Telepon
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Ganti Kata Sandi (Opsional)
                </label>
                <p class="text-xs text-slate-400 mb-2">Kosongkan jika tidak ingin mengubah kata sandi yang sudah ada.</p>
                <input type="password" id="password" name="password" minlength="6"
                       placeholder="Masukkan kata sandi baru jika ingin diubah"
                       class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                <div>
                    <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Hak Akses (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staf Kasir (Hanya Penjualan)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh)</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif (Dapat Masuk)</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Nonaktif (Diblokir)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Perbarui Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
