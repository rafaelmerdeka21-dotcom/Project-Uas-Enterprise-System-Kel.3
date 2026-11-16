@extends('layouts.app')

@section('title', 'Pelanggan')
@section('page-title', 'Data Pelanggan (Customers)')
@section('page-subtitle', 'Kelola data pelanggan dan riwayat transaksi mereka')

@section('content')
<div class="space-y-6">
    <!-- Header Actions & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('customers.index') }}" method="GET" class="flex-1 flex items-center gap-2 max-w-md">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Cari kode, nama, atau telepon..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition">
                Cari
            </button>
            @if(!empty($search))
                <a href="{{ route('customers.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-700 text-sm">Reset</a>
            @endif
        </form>

        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Pelanggan</span>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Kode Pelanggan</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Telepon</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Alamat</th>
                        <th class="py-3 px-4 text-center">Transaksi</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($customers as $index => $customer)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $customers->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4 font-mono font-medium text-xs text-indigo-600 bg-indigo-50/40 w-fit rounded">
                                {{ $customer->code }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $customer->name }}
                                @if($customer->code === 'CUST-000')
                                    <span class="ml-1 text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">Default</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs">{{ $customer->phone ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs">{{ $customer->email ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-500 text-xs max-w-xs truncate">{{ $customer->address ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                    {{ $customer->sales_count }} transaksi
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                    Edit
                                </a>
                                @if($customer->code !== 'CUST-000')
                                    <span class="text-slate-300">|</span>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada data pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
