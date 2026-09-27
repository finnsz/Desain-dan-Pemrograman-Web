<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan root direktori
$rootDir = dirname(__DIR__);

// Normalisasi URL: Jika URL diawali dengan "/Jobsheet9", hapus prefix tersebut
$cleanUrl = $parseUrl;
if (strpos($cleanUrl, '/Jobsheet9') === 0) {
    $cleanUrl = substr($cleanUrl, strlen('/Jobsheet9'));
    if ($cleanUrl === '' || $cleanUrl === '/') {
        $cleanUrl = '/index.php';
    }
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

// 2. Routing URL ke File PHP
$targetFile = $rootDir . $cleanUrl;

if (file_exists($targetFile) && is_file($targetFile)) {
    require $targetFile;
    exit;
}

// Route default jika membuka root domain
if ($cleanUrl === '/' || $cleanUrl === '' || $cleanUrl === '/index.php') {
    require $rootDir . '/index.php';
    exit;
}

// Jika file tidak ditemukan
http_response_code(404);
echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl);