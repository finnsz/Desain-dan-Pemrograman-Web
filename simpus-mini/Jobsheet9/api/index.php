<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan direktori root aplikasi (naik 1 tingkat dari folder /api)
$rootDir = dirname(__DIR__);

// Normalisasi URL: Hapus prefix /Jobsheet9 jika ada di URL
$cleanUrl = $parseUrl;
if (strpos($cleanUrl, '/Jobsheet9') === 0) {
    $cleanUrl = substr($cleanUrl, strlen('/Jobsheet9'));
}

// Jika URL bersih adalah kosong atau '/', arahkan ke /index.php
if ($cleanUrl === '' || $cleanUrl === '/') {
    $cleanUrl = '/index.php';
}

// 1. Penanganan File Statis (CSS, JS, Gambar)
$staticFile = $rootDir . $cleanUrl;
if (file_exists($staticFile) && is_file($staticFile)) {
    $ext = pathinfo($staticFile, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml'
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($staticFile);
        exit;
    }
}

// 2. Eksekusi File PHP
$targetFile = $rootDir . $cleanUrl;

if (file_exists($targetFile) && is_file($targetFile)) {
    require $targetFile;
    exit;
}

// 3. Fallback jika masih tidak ditemukan (404)
http_response_code(404);
echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl) . " (Target: " . htmlspecialchars($cleanUrl) . ")";