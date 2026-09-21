<?php
// Ambil path dari URL
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan direktori root proyek (satu tingkat di atas folder /api)
$rootDir = dirname(__DIR__);
$file    = $rootDir . $parseUrl;

// 1. Jika akses ke root domain (/), arahkan ke index.php utama
if ($parseUrl === '/' || $parseUrl === '') {
    require $rootDir . '/index.php';
    exit;
}

// 2. Jika file PHP yang diminta ada secara fisik
if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// 3. Jika URL tanpa ekstensi .php (misal: /kamar/list)
if (file_exists($file . '.php') && is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

// 4. Jika merujuk ke file statis (CSS/JS)
if (file_exists($file) && is_file($file)) {
    return false;
}

// 5. Jika file tidak ditemukan
http_response_code(404);
echo "404 Not Found";