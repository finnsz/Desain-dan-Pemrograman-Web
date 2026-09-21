<?php
// Tangkap path dari URL yang diminta browser
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan direktori root utama proyek (1 tingkat di atas folder /api)
$rootDir = dirname(__DIR__);

// Jika akses ke root domain (/), panggil index.php di root
if ($parseUrl === '/' || $parseUrl === '') {
    require $rootDir . '/index.php';
    exit;
}

// Susun path file fisik lokal yang dituju
$file = $rootDir . $parseUrl;

// 1. Jika URL memanggil file .php langsung (misal: /kamar/list.php)
if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// 2. Jika URL dipanggil tanpa .php (misal: /kamar/list)
if (file_exists($file . '.php') && is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

// 3. Jika merujuk ke file statis (CSS/JS)
if (file_exists($file) && is_file($file)) {
    return false;
}

// 4. Jika file tidak ada sama sekali
http_response_code(404);
echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl);