@extends('layouts.app')

@section('title', 'Penyesuaian Stok (Opname)')
@section('page-title', 'Form Stock Opname Fisik')
@section('page-subtitle', 'Koreksi dan sesuaikan data persediaan fisik gudang dengan sistem')

@section('content')
<div class="max-w-2xl mx-auto" x-data="opnameForm()">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-500">
                Pencatatan opname fisik wajib menyertakan alasan audit untuk transparansi dan kepatuhan sistem.
            </p>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">Audit Trail</span>
        </div>

        <form action="{{ route('stock-movements.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Product Selector -->
            <div>
                <label for="product_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Pilih Barang Yang Di-Opname <span class="text-rose-500">*</span>
                </label>
                <select id="product_id" name="product_id" x-model="selectedProductId" @change="onProductChange()" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}" data-unit="{{ $product->unit }}">
                            {{ $product->code }} - {{ $product->name }} (Stok Sistem: {{ $product->stock }} {{ $product->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Comparison Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" x-show="selectedProductId">
                <div class="text-center p-2 bg-white rounded-lg border border-slate-200">
                    <span class="text-[10px] uppercase font-semibold text-slate-400 block">Stok Sistem</span>
                    <span class="text-xl font-bold font-mono text-slate-700" x-text="currentSystemStock + ' ' + productUnit"></span>
                </div>

                <div class="text-center p-2 bg-white rounded-lg border border-slate-200">
                    <span class="text-[10px] uppercase font-semibold text-slate-400 block">Stok Fisik Aktual</span>
                    <span class="text-xl font-bold font-mono text-indigo-600" x-text="physicalStock + ' ' + productUnit"></span>
                </div>

                <div class="text-center p-2 bg-white rounded-lg border border-slate-200">
                    <span class="text-[10px] uppercase font-semibold text-slate-400 block">Selisih Koreksi</span>
                    <span class="text-xl font-bold font-mono"
                          :class="difference() > 0 ? 'text-emerald-600' : (difference() < 0 ? 'text-rose-600' : 'text-slate-500')"
                          x-text="(difference() > 0 ? '+' : '') + difference()"></span>
                </div>
            </div>

            <!-- Input Physical Stock Count -->
            <div>
                <label for="physical_stock" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Jumlah Stok Fisik Nyata di Gudang / Rak <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="physical_stock" name="physical_stock" x-model.number="physicalStock" min="0" required
                       placeholder="Masukkan hasil perhitungan fisik aktual"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                <p class="text-xs text-slate-400 mt-1">Stok di sistem akan langsung diperbarui menjadi angka ini.</p>
            </div>

            <!-- Mandatory Audit Reason -->
            <div>
                <label for="reason" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alasan Penyesuaian (Audit Reason) <span class="text-rose-500">*</span>
                </label>
                <textarea id="reason" name="reason" rows="3" required minlength="3"
                          placeholder="Contoh: Selisih temuan stok opname bulanan, barang kadaluarsa/rusak 2 unit, atau koreksi salah input nota fisik."
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('reason') }}</textarea>
                <p class="text-xs text-rose-500 mt-1">* Wajib diisi sebagai pertanggungjawaban audit inventaris.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('stock-movements.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" 
                        :disabled="!selectedProductId || physicalStock === currentSystemStock"
                        :class="!selectedProductId || physicalStock === currentSystemStock ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-indigo-600 hover:bg-indigo-700'"
                        class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Penyesuaian Stok
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function opnameForm() {
        return {
            selectedProductId: '',
            currentSystemStock: 0,
            productUnit: 'pcs',
            physicalStock: 0,

            onProductChange() {
                const select = document.getElementById('product_id');
                const opt = select.options[select.selectedIndex];
                if (opt && opt.value) {
                    this.currentSystemStock = parseInt(opt.getAttribute('data-stock') || 0);
                    this.productUnit = opt.getAttribute('data-unit') || 'pcs';
                    this.physicalStock = this.currentSystemStock;
                } else {
                    this.currentSystemStock = 0;
                    this.physicalStock = 0;
                }
            },

            difference() {
                return (this.physicalStock || 0) - this.currentSystemStock;
            }
        }
    }
</script>
@endpush
@endsection
