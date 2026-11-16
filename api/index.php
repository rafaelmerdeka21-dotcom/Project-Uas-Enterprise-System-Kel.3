<?php
/**
 * Vercel Serverless Entry Point untuk Laravel.
 *
 * File ini menjadi jembatan antara Vercel PHP runtime
 * dengan bootstrap Laravel yang sesungguhnya.
 */

// ── 1. Arahkan working directory ke root proyek ──────────────
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

// ── 2. Redirect path supaya public/index.php tetap bekerja ───
//    Vercel memanggil file ini, tapi Laravel mengharapkan
//    request diarahkan ke /public — kita simulasikan itu.
$_SERVER['SCRIPT_FILENAME'] = $projectRoot . '/public/index.php';
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_PORT']     = '443';
$_SERVER['HTTPS']           = 'on';

// ── 3. Fix REQUEST_URI: hapus prefix '/api' yang ditambahkan Vercel ─
// Vercel meneruskan request seperti /api/... ke file ini,
// tapi Laravel harus melihat path aslinya.
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = preg_replace('#^/api#', '', $_SERVER['REQUEST_URI']) ?: '/';
}

// ── 4. Bootstrap storage & cache directory (serverless tidak punya /tmp tetap) ──
$storagePath = $projectRoot . '/storage';
foreach ([
    $storagePath . '/app/public',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// ── 5. Autoload & jalankan Laravel ──────────────────────────
require __DIR__ . '/../public/index.php';