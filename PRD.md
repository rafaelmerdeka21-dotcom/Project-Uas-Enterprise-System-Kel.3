# PRD.md - Enterprise Management System (EMS)

## 1. Nama Aplikasi
Enterprise Management System (EMS) - Aplikasi Web Terpadu Usaha Dagang.

## 2. Latar Belakang & Masalah
Pencatatan persediaan, penjualan, pembelian, dan laporan pada usaha dagang skala kecil hingga menengah sering kali masih terpisah menggunakan nota kertas dan spreadsheet manual. Hal ini mengakibatkan stok barang tidak akurat, tingginya selisih inventaris, serta keterlambatan pembuatan laporan keuangan operasional.

## 3. Target Pengguna & Role Permission
Sistem menerapkan pembatasan akses ketat di sisi server (Role-Based Access Control):
- **Admin:** Memiliki hak akses penuh ke seluruh 9 modul inti, manajemen pengguna, pengaturan sistem, dan fitur AI.
- **Staf:** Hak akses terbatas hanya pada modul *Penjualan & Faktur* serta melihat data produk dasar.

## 4. Tujuan Utama (Objectives)
- Menyatukan data barang, transaksi, dan stok dalam satu sistem terpusat dengan jejak audit (*audit trail*).
- Mengurangi selisih stok secara otomatis melalui pencatatan mutasi di setiap transaksi pembelian, penjualan, dan penyesuaian (*opname*).
- Menyediakan laporan periodik yang akurat serta fitur cerdas berbasis AI pendukung keputusan.

## 5. Fitur Wajib (Core Modules & CRUD)
1. **Autentikasi & Keamanan:** Login, logout, manajemen sesi aman, proteksi CSRF token, hashing password (Bcrypt), dan pembatasan percobaan login.
2. **Dashboard Interaktif:** Menampilkan ringkasan total produk, peringatan stok menipis/habis, dan total omzet harian/bulanan.
3. **Manajemen Barang (Products):** CRUD lengkap data barang, foto produk, kode unik, harga beli, harga jual, dan batas stok minimum.
4. **Kategori & Pemasok (Categories & Suppliers):** CRUD kategori barang dan pengelolaan data pemasok (kode, telepon, email, alamat).
5. **Pembelian (Purchases):** Alur pemesanan barang ke pemasok dengan status (tertunda/pending, diterima, dibatalkan). Stok bertambah otomatis hanya saat status berubah menjadi diterima.
6. **Penjualan & Faktur (Sales & Invoices):** Transaksi multi-barang, pemilihan pelanggan opsional, metode pembayaran (tunai, transfer, QRIS), pengurangan stok otomatis dengan penguncian baris basis data (*database row locking* agar stok tidak minus), dan cetak faktur.
7. **Mutasi & Penyesuaian Stok (Stock Movements & Opname):** Riwayat keluar-masuk barang dan penyesuaian stok fisik wajib disertai alasan (*audit reason*).
8. **Laporan & Ekspor:** Ringkasan periode transaksi, produk terlaris, cetak PDF, dan ekspor data ke CSV yang dilindungi dari formula injection.
9. **Manajemen Pengguna & Hak Akses:** CRUD pengguna, ubah peran (Admin/Staf), dan reset password.

## 6. Fitur AI (Rencana Tahap Lanjutan)
- **Prediksi Stok AI:** Perkiraan permintaan 7-30 hari ke depan (time series forecasting) dan saran jumlah pesan ulang otomatis.
- **Asisten Data AI (Claude API):** Tanya jawab data operasional bisnis menggunakan bahasa sehari-hari yang diterjemahkan secara aman menjadi kueri SQL khusus baca (*read-only*).

## 7. Kriteria Selesai (Acceptance Criteria)
- A1: Seluruh operasi CRUD pada Barang, Pemasok, dan Transaksi berjalan lancar tanpa celah keamanan.
- A2: Staf tidak dapat mengakses halaman manajemen admin di sisi server (muncul penolakan 403 Forbidden).
- A3: Transaksi penjualan berhasil mengurangi stok secara akurat dan mencatat mutasi keluar.
- A4: Tampilan responsif di desktop maupun perangkat mobile dengan estetika visual yang elegan.

## 8. Di Luar Cakupan (Out of Scope)
- Sistem pembayaran online pihak ketiga yang melibatkan token kartu kredit eksternal langsung.
- Pengolahan data multi-cabang perusahaan yang kompleks (skala enterprise besar).

## 9. Edge Case & Mitigasi Keamanan
- **Stok Minus:** Dicek melalui transaksi berbasis *database transaction* dan *row locking*.
- **XSS & SQL Injection:** Diatasi menggunakan mekanisme *prepared statements* bawaan Laravel (Eloquent ORM / Query Builder) dan pembersihan input otomatis.
- **CSRF Protection:** Diaktifkan secara *default* pada setiap form POST/PUT/DELETE di Laravel.
