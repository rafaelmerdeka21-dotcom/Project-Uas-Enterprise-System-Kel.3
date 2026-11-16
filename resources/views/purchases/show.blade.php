@extends('layouts.app')

@section('title', 'Rincian PO ' . $purchase->invoice_number)
@section('page-title', 'Rincian Pembelian ' . $purchase->invoice_number)
@section('page-subtitle', 'Faktur pesanan pembelian barang ke pemasok')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar PO</span>
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Faktur PO</span>
            </button>
        </div>
    </div>

    <!-- Status Change Form for Admin -->
    @if($purchase->status !== 'canceled')
        <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
            <div>
                <p class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Perbarui Status Transaksi:</p>
                <p class="text-xs text-indigo-700 mt-0.5">
                    @if($purchase->status === 'pending')
                        Ubah ke "Diterima" saat barang fisik sampai di gudang untuk otomatis menambah stok.
                    @else
                        Status saat ini Diterima (stok telah bertambah otomatis).
                    @endif
                </p>
            </div>
            <form action="{{ route('purchases.update-status', $purchase) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="px-3 py-1.5 bg-white border border-indigo-300 rounded-lg text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pending" {{ $purchase->status === 'pending' ? 'selected' : '' }}>Tertunda (Pending)</option>
                    <option value="received" {{ $purchase->status === 'received' ? 'selected' : '' }}>Diterima (Received)</option>
                    <option value="canceled" {{ $purchase->status === 'canceled' ? 'selected' : '' }}>Dibatalkan (Canceled)</option>
                </select>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    @endif

    <!-- Invoice Sheet Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10">
        
        <!-- Header PO -->
        <div class="flex justify-between items-start pb-8 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">EMS</div>
                    <span class="text-lg font-bold text-slate-800">EMS Dagang Terpadu</span>
                </div>
                <p class="text-xs text-slate-500">Sistem Manajemen Pengadaan & Inventaris</p>
            </div>

            <div class="text-right">
                <span class="text-xs uppercase font-semibold tracking-wider text-slate-400">FAKTUR PEMBELIAN (PO)</span>
                <p class="font-mono text-lg font-bold text-indigo-600 mt-0.5">{{ $purchase->invoice_number }}</p>
                <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $purchase->status === 'received' ? 'bg-emerald-100 text-emerald-800' : ($purchase->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                    Status: {{ strtoupper($purchase->status) }}
                </span>
            </div>
        </div>

        <!-- Supplier & PO Meta Info -->
        <div class="grid grid-cols-2 gap-8 py-6 border-b border-slate-200 text-xs">
            <div>
                <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Mitra Pemasok:</p>
                <p class="text-sm font-bold text-slate-800">{{ $purchase->supplier->name }}</p>
                <p class="text-slate-600 mt-1 font-mono">Kode: {{ $purchase->supplier->code }}</p>
                <p class="text-slate-600">Telp: {{ $purchase->supplier->phone ?? '-' }}</p>
                <p class="text-slate-600">Alamat: {{ $purchase->supplier->address ?? '-' }}</p>
            </div>

            <div class="text-right space-y-1">
                <p><span class="text-slate-400">Tanggal Pesanan:</span> <strong class="text-slate-800">{{ $purchase->purchase_date->format('d F Y') }}</strong></p>
                <p><span class="text-slate-400">Petugas Pembuat:</span> <strong class="text-slate-800">{{ $purchase->user->name ?? 'Admin' }}</strong></p>
                @if($purchase->notes)
                    <p class="mt-2 text-slate-500 italic">"{{ $purchase->notes }}"</p>
                @endif
            </div>
        </div>

        <!-- Details Table -->
        <div class="py-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                        <th class="pb-3">No</th>
                        <th class="pb-3">Kode & Nama Barang</th>
                        <th class="pb-3 text-center">Jumlah</th>
                        <th class="pb-3 text-right">Harga Satuan</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($purchase->details as $index => $detail)
                        <tr>
                            <td class="py-3 text-xs text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3">
                                <span class="font-semibold text-slate-800">{{ $detail->product->name }}</span>
                                <span class="block font-mono text-xs text-slate-400">{{ $detail->product->code }}</span>
                            </td>
                            <td class="py-3 text-center font-bold text-slate-700 text-xs">
                                {{ $detail->quantity }} {{ $detail->product->unit }}
                            </td>
                            <td class="py-3 text-right font-mono text-xs text-slate-600">
                                Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-slate-800 text-xs">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Grand Total Summary -->
        <div class="pt-4 border-t border-slate-200 flex justify-end">
            <div class="w-64 space-y-2 text-right">
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Total Item:</span>
                    <span class="font-bold text-slate-800">{{ $purchase->details->sum('quantity') }} unit</span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-800 pt-2 border-t border-slate-100">
                    <span>Total Bayar:</span>
                    <span class="font-mono text-indigo-600 text-lg">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
