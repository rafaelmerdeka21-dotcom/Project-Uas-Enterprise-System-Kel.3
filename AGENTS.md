<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>


# AGENTS.md - Standar Mutu dan Keamanan Sistem

- **Arsitektur Standar:** Wajib menggunakan struktur framework Laravel yang bersih (Controller, Model, Migration, Request Validation).
- **Keamanan Absolut (Security First):**
  - Seluruh input form wajib divalidasi menggunakan Laravel Form Request.
  - Cegah SQL Injection dengan memanfaatkan sepenuhnya Eloquent ORM / Query Builder berparameter (*prepared statements*).
  - Cegah XSS dengan menghindari penggunaan raw output yang tidak disaring; gunakan escape output bawaan Blade (`{{ $variable }}`).
  - Proteksi CSRF aktif secara otomatis pada setiap metode mutasi data (`POST`, `PUT`, `DELETE`).
- **Manajemen Akses Sisi Server:** Pengecekan hak akses (*Role & Permission*) wajib divalidasi di dalam *Middleware* Laravel, bukan sekadar menyembunyikan tombol di tampilan frontend.
- **Integritas Stok Basis Data:** Setiap pengurangan atau penambahan stok barang pada transaksi wajib dibungkus dalam blok transaksi basis data (`DB::transaction`) untuk menghindari inkonsistensi data (race condition).
- **Kode Bersih:** Berikan komentar singkat yang jelas pada logika bisnis yang rumit agar mudah dipelajari.
