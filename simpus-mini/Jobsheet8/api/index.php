<?php
// Tangkap path dari URL
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan file lokal yang dituju
$file = __DIR__ . '/..' . $parseUrl;

// Jika mengarah ke direktori root, arahkan ke index.php utama
if ($parseUrl === '/' || $parseUrl === '') {
    require __DIR__ . '/../index.php';
    exit;
}

// Jika file PHP yang diminta ada, muat file tersebut
if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// Jika merujuk ke file statis (CSS/JS)
if (file_exists($file) && is_file($file)) {
    return false;
}

// Tampilan jika route tidak ditemukan
http_response_code(404);
echo "404 Not Found";