<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Root direktori (folder simpus-mini)
$rootDir = dirname(__DIR__);

// Jika buka root domain utama, arahkan ke index.php utama
if ($parseUrl === '/' || $parseUrl === '') {
    $parseUrl = '/index.php';
}

$targetFile = $rootDir . $parseUrl;

// 1. Penanganan File Statis (CSS, JS, Gambar)
if (file_exists($targetFile) && is_file($targetFile)) {
    $ext = pathinfo($targetFile, PATHINFO_EXTENSION);
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
        readfile($targetFile);
        exit;
    }

    // 2. Jika file yang diminta adalah file PHP (termasuk /Jobsheet9/index.php)
    if ($ext === 'php') {
        require $targetFile;
        exit;
    }
}

// 3. Jika mengakses folder (misal: /Jobsheet9/), otomatis cari index.php di dalam folder tersebut
if (is_dir($targetFile)) {
    $indexPath = rtrim($targetFile, '/') . '/index.php';
    if (file_exists($indexPath)) {
        require $indexPath;
        exit;
    }
}

// 4. Jika file benar-benar tidak ada
http_response_code(404);
echo "404 Not Found - File tidak ditemukan: " . htmlspecialchars($parseUrl);