<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan root direktori
$rootDir = dirname(__DIR__);

// 1. Penanganan File Statis (CSS, JS, Gambar)
$staticFile = $rootDir . $parseUrl;
if (file_exists($staticFile) && is_file($staticFile)) {
    $ext = pathinfo($staticFile, PATHINFO_EXTENSION);
    
    // Tentukan mime type agar browser membaca CSS/JS dengan benar
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

// 2. Routing URL Aplikasi (Termasuk Handling Path /Jobsheet9)
switch ($parseUrl) {
    // --- BERANDA / DASHBOARD ---
    case '/':
    case '':
    case '/index.php':
    case '/Jobsheet9':
    case '/Jobsheet9/':
    case '/Jobsheet9/index.php':
        require $rootDir . '/index.php';
        break;

    // --- KAMAR ---
    case '/kamar/list.php':
    case '/kamar/list':
    case '/Jobsheet9/kamar/list.php':
    case '/Jobsheet9/kamar/list':
        require $rootDir . '/kamar/list.php';
        break;

    case '/kamar/tambah.php':
    case '/kamar/tambah':
    case '/Jobsheet9/kamar/tambah.php':
    case '/Jobsheet9/kamar/tambah':
        require $rootDir . '/kamar/tambah.php';
        break;

    case '/kamar/proses_tambah.php':
    case '/Jobsheet9/kamar/proses_tambah.php':
        require $rootDir . '/kamar/proses_tambah.php';
        break;

    case '/kamar/edit.php':
    case '/kamar/edit':
    case '/Jobsheet9/kamar/edit.php':
    case '/Jobsheet9/kamar/edit':
        require $rootDir . '/kamar/edit.php';
        break;

    case '/kamar/proses_edit.php':
    case '/Jobsheet9/kamar/proses_edit.php':
        require $rootDir . '/kamar/proses_edit.php';
        break;

    case '/kamar/hapus.php':
    case '/Jobsheet9/kamar/hapus.php':
        require $rootDir . '/kamar/hapus.php';
        break;

    // --- PENGHUNI ---
    case '/penghuni/list.php':
    case '/penghuni/list':
    case '/Jobsheet9/penghuni/list.php':
    case '/Jobsheet9/penghuni/list':
        require $rootDir . '/penghuni/list.php';
        break;

    case '/penghuni/tambah.php':
    case '/penghuni/tambah':
    case '/Jobsheet9/penghuni/tambah.php':
    case '/Jobsheet9/penghuni/tambah':
        require $rootDir . '/penghuni/tambah.php';
        break;

    case '/penghuni/proses_tambah.php':
    case '/Jobsheet9/penghuni/proses_tambah.php':
        require $rootDir . '/penghuni/proses_tambah.php';
        break;

    case '/penghuni/edit.php':
    case '/penghuni/edit':
    case '/Jobsheet9/penghuni/edit.php':
    case '/Jobsheet9/penghuni/edit':
        require $rootDir . '/penghuni/edit.php';
        break;

    case '/penghuni/proses_edit.php':
    case '/Jobsheet9/penghuni/proses_edit.php':
        require $rootDir . '/penghuni/proses_edit.php';
        break;

    case '/penghuni/hapus.php':
    case '/Jobsheet9/penghuni/hapus.php':
        require $rootDir . '/penghuni/hapus.php';
        break;

    // --- DEFAULT 404 ---
    default:
        http_response_code(404);
        echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl);
        break;
}