@extends('layouts.app')

@section('title', 'Kasir Penjualan (POS)')
@section('page-title', 'Kasir Penjualan (Point of Sale)')
@section('page-subtitle', 'Pencatatan transaksi penjualan kasir cepat dan cetak faktur otomatis')

@section('content')
<div x-data="posSystem()" class="h-full flex flex-col lg:flex-row gap-6">

    <!-- Left Column: Product Selection & Search -->
    <div class="flex-1 flex flex-col min-w-0 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Search and Quick Category Filter -->
        <div class="p-4 border-b border-slate-100 space-y-3 bg-slate-50/50">
            <div class="relative">
                <input type="text" x-model="search" @input="filterProducts()" 
                       placeholder="Cari nama barang atau ketik / scan kode barcode SKU..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm transition">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <button type="button" @click="selectedCategory = ''; filterProducts()"
                        :class="selectedCategory === '' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                        class="px-3 py-1.5 rounded-lg font-medium transition whitespace-nowrap">
                    Semua Produk
                </button>
                @foreach($products->pluck('category')->unique('id')->filter() as $cat)
                    <button type="button" @click="selectedCategory = '{{ $cat->id }}'; filterProducts()"
                            :class="selectedCategory === '{{ $cat->id }}' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                            class="px-3 py-1.5 rounded-lg font-medium transition whitespace-nowrap">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
            <template x-for="product in filteredProducts" :key="product.id">
                <div @click="addToCart(product)"
                     :class="product.stock <= 0 ? 'opacity-60 cursor-not-allowed bg-slate-50' : 'cursor-pointer hover:border-indigo-400 hover:shadow-md bg-white'"
                     class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between transition group relative">
                    
                    <!-- Stock Badge -->
                    <div class="flex items-center justify-between text-[11px] mb-2">
                        <span class="font-mono text-slate-500 text-[10px]" x-text="product.code"></span>
                        <span :class="product.stock <= 0 ? 'bg-rose-100 text-rose-700' : (product.stock <= product.min_stock ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-800')"
                              class="px-1.5 py-0.5 rounded font-bold text-[10px]"
                              x-text="product.stock <= 0 ? 'Habis' : 'Stok: ' + product.stock"></span>
                    </div>

                    <!-- Product Image / Placeholder -->
                    <div class="h-24 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center mb-2">
                        <template x-if="product.image">
                            <img :src="'/' + product.image" :alt="product.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!product.image">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </template>
                    </div>

                    <div>
                        <h4 class="font-semibold text-slate-800 text-xs line-clamp-2 group-hover:text-indigo-600 transition" x-text="product.name"></h4>
                        <p class="font-bold font-mono text-indigo-600 text-sm mt-1">
                            Rp <span x-text="formatRupiah(product.selling_price)"></span>
                        </p>
                    </div>
                </div>
            </template>
        </div>

    </div>

    <!-- Right Column: Cart & Checkout Payment -->
    <div class="w-full lg:w-96 flex-shrink-0 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
        
        <form action="{{ route('sales.store') }}" method="POST" class="h-full flex flex-col justify-between" id="posForm">
            @csrf

            <!-- Cart Header & Customer Selector -->
            <div class="p-4 border-b border-slate-100 space-y-3 bg-slate-50/40">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Keranjang Belanja</span>
                    </h3>
                    <span class="text-xs font-mono text-slate-400">{{ $nextInvoice }}</span>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">
                        Pilih Pelanggan:
                    </label>
                    <select name="customer_id" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500">
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ $cust->code === 'CUST-000' ? 'selected' : '' }}>
                                {{ $cust->code }} - {{ $cust->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-[180px]">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 py-10">
                        <svg class="w-12 h-12 text-slate-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-xs font-medium">Keranjang masih kosong</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Klik produk di katalog untuk menambahkan</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/70 flex items-center justify-between gap-2 text-xs">
                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.selling_price">
                        <input type="hidden" :name="`items[${index}][quantity]`" :value="item.qty">

                        <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-slate-800 truncate" x-text="item.name"></h5>
                            <p class="text-[11px] text-slate-500 font-mono">Rp <span x-text="formatRupiah(item.selling_price)"></span> / <span x-text="item.unit"></span></p>
                        </div>

                        <!-- Quantity Stepper -->
                        <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-0.5">
                            <button type="button" @click="decreaseQty(index)" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded text-xs font-bold">-</button>
                            <span class="w-8 text-center font-bold text-xs font-mono" x-text="item.qty"></span>
                            <button type="button" @click="increaseQty(index)" class="w-6 h-6 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded text-xs font-bold">+</button>
                        </div>

                        <div class="text-right font-mono font-bold text-slate-800 min-w-[70px]">
                            Rp <span x-text="formatRupiah(item.qty * item.selling_price)"></span>
                        </div>

                        <button type="button" @click="removeFromCart(index)" class="text-slate-400 hover:text-rose-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Payment & Checkout Panel -->
            <div class="p-4 border-t border-slate-200 bg-white space-y-4">
                
                <!-- Payment Method -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Metode Pembayaran:
                    </label>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <label class="flex items-center justify-center p-2 rounded-lg border cursor-pointer transition font-medium"
                               :class="paymentMethod === 'cash' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                            <span>Tunai</span>
                        </label>
                        <label class="flex items-center justify-center p-2 rounded-lg border cursor-pointer transition font-medium"
                               :class="paymentMethod === 'transfer' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="sr-only">
                            <span>Transfer</span>
                        </label>
                        <label class="flex items-center justify-center p-2 rounded-lg border cursor-pointer transition font-medium"
                               :class="paymentMethod === 'qris' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                            <span>QRIS</span>
                        </label>
                    </div>
                </div>

                <!-- Total & Amount Paid -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 uppercase font-semibold">Total Tagihan:</span>
                        <span class="text-xl font-bold font-mono text-indigo-600">
                            Rp <span x-text="formatRupiah(totalAmount())"></span>
                        </span>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-500 font-semibold">Nominal Bayar (Rp):</span>
                            <span class="text-[10px] text-slate-400">Minimal sesuai total</span>
                        </div>
                        <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" required min="0"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:bg-white text-right">
                    </div>

                    <!-- Quick Cash Preset Buttons -->
                    <div class="grid grid-cols-4 gap-1.5 text-[10px]">
                        <button type="button" @click="setExactPaid()" class="p-1 rounded bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700">Uang Pas</button>
                        <button type="button" @click="paidAmount = 50000" class="p-1 rounded bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700">50.000</button>
                        <button type="button" @click="paidAmount = 100000" class="p-1 rounded bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700">100.000</button>
                        <button type="button" @click="paidAmount = 200000" class="p-1 rounded bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700">200.000</button>
                    </div>

                    <!-- Change Calculation -->
                    <div class="flex justify-between items-center text-xs pt-1">
                        <span class="text-slate-500 font-semibold">Kembalian:</span>
                        <span class="font-bold font-mono text-sm" 
                              :class="changeAmount() < 0 ? 'text-rose-500' : 'text-emerald-600'">
                            Rp <span x-text="formatRupiah(changeAmount())"></span>
                        </span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="cart.length === 0 || paidAmount < totalAmount()"
                        :class="cart.length === 0 || paidAmount < totalAmount() ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30'"
                        class="w-full py-3 text-white text-sm font-bold rounded-xl shadow-md transition duration-150 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Bayar & Selesaikan Transaksi</span>
                </button>
            </div>
        </form>

    </div>

</div>

@push('scripts')
<script>
    function posSystem() {
        return {
            products: @json($products),
            filteredProducts: [],
            search: '',
            selectedCategory: '',
            paymentMethod: 'cash',
            paidAmount: 0,
            cart: [],

            init() {
                this.filteredProducts = this.products;
                
                @if($quickProduct)
                    const qp = this.products.find(p => p.id === {{ $quickProduct->id }});
                    if (qp && qp.stock > 0) {
                        this.addToCart(qp);
                    }
                @endif
            },

            filterProducts() {
                const s = this.search.toLowerCase();
                this.filteredProducts = this.products.filter(p => {
                    const matchSearch = p.name.toLowerCase().includes(s) || p.code.toLowerCase().includes(s);
                    const matchCategory = !this.selectedCategory || p.category_id == this.selectedCategory;
                    return matchSearch && matchCategory;
                });
            },

            addToCart(product) {
                if (product.stock <= 0) return;

                const existing = this.cart.find(i => i.id === product.id);
                if (existing) {
                    if (existing.qty < product.stock) {
                        existing.qty++;
                    } else {
                        alert('Jumlah pesanan sudah mencapai batas stok yang tersedia (' + product.stock + ').');
                    }
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        code: product.code,
                        unit: product.unit,
                        selling_price: parseFloat(product.selling_price),
                        stock: product.stock,
                        qty: 1
                    });
                }
                this.setExactPaid();
            },

            increaseQty(index) {
                const item = this.cart[index];
                if (item.qty < item.stock) {
                    item.qty++;
                    this.setExactPaid();
                } else {
                    alert('Jumlah pesanan sudah mencapai batas stok maksimum yang tersedia.');
                }
            },

            decreaseQty(index) {
                if (this.cart[index].qty > 1) {
                    this.cart[index].qty--;
                } else {
                    this.cart.splice(index, 1);
                }
                this.setExactPaid();
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
                this.setExactPaid();
            },

            totalAmount() {
                return this.cart.reduce((sum, item) => sum + (item.qty * item.selling_price), 0);
            },

            setExactPaid() {
                this.paidAmount = this.totalAmount();
            },

            changeAmount() {
                const change = this.paidAmount - this.totalAmount();
                return change >= 0 ? change : 0;
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }
        }
    }
</script>
@endpush
@endsection
