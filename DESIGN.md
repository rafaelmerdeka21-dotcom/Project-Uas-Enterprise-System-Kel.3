# DESIGN.md - Panduan Visual EMS

## 1. Konsep & Filosofi Desain
Desain mengusung gaya **Modern Executive & Clean Dashboard** menggunakan **Tailwind CSS** (atau Bootstrap 5 jika disesuaikan). Tampilan dibuat elegan, tidak membosankan, menggunakan kombinasi warna kontras profesional yang nyaman dipandang untuk penggunaan operasional jangka panjang.

## 2. Palet Warna (Color Palette)
- **Primary / Dominan (Deep Navy):** `#0F172A` (Slate 900) - Memberikan kesan profesional dan kokoh pada sidebar / header.
- **Accent / Aksi (Royal Indigo/Blue):** `#4F46E5` (Indigo 600) - Digunakan untuk tombol utama, tautan aktif, dan indikator fokus.
- **Success / Status Aman (Emerald):** `#10B981` (Emerald 500) - Indikator stok aman dan transaksi sukses.
- **Warning / Perhatian (Amber):** `#F59E0B` (Amber 500) - Peringatan stok menipis.
- **Danger / Error (Rose):** `#E11D48` (Rose 600) - Tombol hapus, stok habis, dan pesan kesalahan.
- **Background Utama:** `#F8FAFC` (Slate 50) - Latar belakang halaman yang lembut di mata.
- **Card Background:** `#FFFFFF` (White) dengan efek bayangan tipis (`shadow-sm` atau `shadow-md`).

## 3. Tipografi (Typography)
- **Font Family:** `Inter, system-ui, -apple-system, sans-serif` (bersih, tajam, dan modern).
- **Heading 1:** `text-2xl font-bold text-slate-800 tracking-tight`
- **Body Text:** `text-sm font-normal text-slate-600`
- **Table Text:** `text-xs md:text-sm text-slate-700`

## 4. Jarak, Bentuk, & Komponen (Spacing & Layout)
- **Border Radius:** Menggunakan sudut memukau yang konsisten (`rounded-xl` untuk kartu dan kontainer utama, `rounded-lg` untuk tombol dan input form).
- **Spacing:** Menggunakan skala grid Tailwind (`p-6` untuk padding card dashboard, `gap-6` untuk jarak antar grid elemen).
- **Navigasi:** Sidebar responsif di perangkat desktop, dan *bottom navigation / collapsible drawer* yang elegan di perangkat seluler (Mobile-first design).
- **Tabel Data:** Tabel interaktif dengan garis pembatas tipis (`divide-y divide-slate-200`), efek *hover* halus pada baris (`hover:bg-slate-50`), serta pagination yang rapi.
