<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan direktori root utama
$rootDir = realpath(__DIR__ . '/..');
chdir($rootDir);

// 1. Jika akses ke root domain (/)
if ($parseUrl === '/' || $parseUrl === '') {
    require $rootDir . '/index.php';
    exit;
}

// Lokasi file fisik yang dituju
$file = $rootDir . $parseUrl;

// 2. Jika file PHP dipanggil langsung (misal: /kamar/list.php)
if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// 3. Jika URL dipanggil tanpa .php (misal: /kamar/list)
if (file_exists($file . '.php') && is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

// 4. Jika merujuk ke file statis (CSS/JS)
if (file_exists($file) && is_file($file)) {
    return false;
}

// 5. Tampilan jika file tidak ditemukan
http_response_code(404);
echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl);