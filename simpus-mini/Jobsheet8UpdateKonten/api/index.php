<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan root direktori dengan dirname
$rootDir = dirname(__DIR__);

// Switch/Match Path URL secara eksplisit
switch ($parseUrl) {
    case '/':
    case '':
    case '/index.php':
        require $rootDir . '/index.php';
        break;

    case '/kamar/list.php':
    case '/kamar/list':
        require $rootDir . '/kamar/list.php';
        break;

    case '/kamar/tambah.php':
    case '/kamar/tambah':
        require $rootDir . '/kamar/tambah.php';
        break;

    case '/kamar/proses_tambah.php':
        require $rootDir . '/kamar/proses_tambah.php';
        break;

    case '/penghuni/list.php':
    case '/penghuni/list':
        require $rootDir . '/penghuni/list.php';
        break;

    case '/penghuni/tambah.php':
    case '/penghuni/tambah':
        require $rootDir . '/penghuni/tambah.php';
        break;

    case '/penghuni/proses_tambah.php':
        require $rootDir . '/penghuni/proses_tambah.php';
        break;

    default:
        // Coba panggil file dinamis jika ada
        $targetFile = $rootDir . $parseUrl;
        if (file_exists($targetFile) && is_file($targetFile)) {
            require $targetFile;
        } else {
            http_response_code(404);
            echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl);
        }
        break;
}