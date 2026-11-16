@extends('layouts.app')

@section('title', 'Buat Pesanan Pembelian')
@section('page-title', 'Form Pemesanan Pembelian (PO)')
@section('page-subtitle', 'Buat faktur pemesanan barang dari mitra pemasok')

@section('content')
<div class="max-w-4xl mx-auto" x-data="purchaseForm()">
    <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- PO General Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                <span>Informasi Pemesanan</span>
                <span class="font-mono text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $nextInvoice }}</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="supplier_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Pemasok <span class="text-rose-500">*</span>
                    </label>
                    <select id="supplier_id" name="supplier_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">Pilih Pemasok</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="purchase_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tanggal Pesanan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Status Pesanan <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Tertunda (Menunggu Barang)</option>
                        <option value="received" {{ old('status', 'received') === 'received' ? 'selected' : '' }}>Diterima (Langsung Masuk Stok)</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Catatan Pesanan
                </label>
                <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                       placeholder="Nomor surat jalan atau catatan pengiriman..."
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                    Daftar Barang Pesanan
                </h2>
                <button type="button" @click="addItem()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Baris Barang</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="pb-3 w-1/2">Pilih Barang</th>
                            <th class="pb-3 text-center w-24">Jumlah</th>
                            <th class="pb-3 text-right w-44">Harga Beli Satuan (Rp)</th>
                            <th class="pb-3 text-right w-44">Subtotal (Rp)</th>
                            <th class="pb-3 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="py-3 pr-3">
                                    <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="onProductSelect(index)" required
                                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                                {{ $product->code }} - {{ $product->name }} (Stok: {{ $product->stock }} {{ $product->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required
                                           class="w-20 px-2 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-center focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" min="0" required
                                           class="w-36 px-2 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-right font-mono focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                                </td>
                                <td class="py-3 pl-3 text-right font-mono font-bold text-slate-800 text-xs">
                                    Rp <span x-text="formatRupiah(item.quantity * item.unit_price)"></span>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" 
                                            class="text-rose-500 hover:text-rose-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Grand Total Calculation -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs uppercase font-semibold tracking-wider text-slate-500">Total Nilai Pembelian:</span>
                <span class="text-2xl font-bold font-mono text-indigo-600">
                    Rp <span x-text="formatRupiah(grandTotal())"></span>
                </span>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md transition">
                Simpan Transaksi Pembelian
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function purchaseForm() {
        return {
            items: [
                { product_id: '', quantity: 1, unit_price: 0 }
            ],
            addItem() {
                this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            onProductSelect(index) {
                const select = event.target;
                const option = select.options[select.selectedIndex];
                const price = parseFloat(option.getAttribute('data-price') || 0);
                this.items[index].unit_price = price;
            },
            grandTotal() {
                return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.unit_price || 0)), 0);
            },
            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }
        }
    }
</script>
@endpush
@endsection
