<?php
// Ambil path dari URL
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan lokasi file di direktori utama (root)
$file = __DIR__ . '/..' . $parseUrl;

// Jika akses ke root domain (/), arahkan ke index.php utama
if ($parseUrl === '/' || $parseUrl === '') {
    require __DIR__ . '/../index.php';
    exit;
}

// Jika mengakses file .php langsung (misal: /kamar/list.php)
if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// Jika URL tidak diakhiri .php, coba cari file .php yang sesuai (misal: /kamar/list -> /kamar/list.php)
if (file_exists($file . '.php') && is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

// Muat file statis (CSS/JS) jika ada
if (file_exists($file) && is_file($file)) {
    return false;
}

// Tampilan 404 jika file tidak ditemukan
http_response_code(404);
echo "404 Not Found";