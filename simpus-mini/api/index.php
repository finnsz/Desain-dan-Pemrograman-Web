<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan root direktori
$rootDir = dirname(__DIR__);

// 1. Penanganan File Statis (CSS, JS, Gambar)
$staticFile = $rootDir . $parseUrl;
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

// 2. Routing URL Aplikasi
switch ($parseUrl) {
    case '/':
    case '':
    case '/index.php':
        require $rootDir . '/index.php';
        break;

    // Route Kamar
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

    case '/kamar/edit.php':
    case '/kamar/edit':
        require $rootDir . '/kamar/edit.php';
        break;

    case '/kamar/proses_edit.php':
        require $rootDir . '/kamar/proses_edit.php';
        break;

    case '/kamar/hapus.php':
        require $rootDir . '/kamar/hapus.php';
        break;

    // Route Penghuni
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

    case '/penghuni/edit.php':
    case '/penghuni/edit':
        require $rootDir . '/penghuni/edit.php';
        break;

    case '/penghuni/proses_edit.php':
        require $rootDir . '/penghuni/proses_edit.php';
        break;

    case '/penghuni/hapus.php':
        require $rootDir . '/penghuni/hapus.php';
        break;

    // 3. Fallback Dynamic Routing (Pencarian otomatis untuk Jobsheet & file/folder lain)
    default:
        $targetPath = $rootDir . $parseUrl;

        // Jika path merujuk ke file PHP yang ada langsung
        if (file_exists($targetPath) && is_file($targetPath)) {
            $ext = pathinfo($targetPath, PATHINFO_EXTENSION);
            if ($ext === 'php') {
                require $targetPath;
                exit;
            } elseif ($ext === 'html') {
                readfile($targetPath);
                exit;
            }
        }

        // Jika path merujuk ke folder (misal: /Jobsheet9/), cari index.php atau index.html di dalamnya
        if (is_dir($targetPath)) {
            $indexPathPhp  = rtrim($targetPath, '/') . '/index.php';
            $indexPathHtml = rtrim($targetPath, '/') . '/index.html';

            if (file_exists($indexPathPhp)) {
                require $indexPathPhp;
                exit;
            } elseif (file_exists($indexPathHtml)) {
                readfile($indexPathHtml);
                exit;
            }
        }

        // Jika benar-benar tidak ditemukan
        http_response_code(404);
        echo "404 Not Found - Path file: " . htmlspecialchars($parseUrl);
        break;
}